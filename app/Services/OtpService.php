<?php

namespace App\Services;

use App\Exceptions\OtpVerificationException;
use App\Models\OtpVerification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Handles the full lifecycle of a mobile OTP: generation, hashing, delivery
 * (via Fast2SmsService), expiry, resend cooldown, attempt limiting,
 * verification, and issuing/consuming the short-lived verification token
 * that authorizes a follow-up action (e.g. account registration).
 *
 * Security invariants:
 *  - The OTP is never stored in plain text (HMAC-SHA256, keyed with APP_KEY).
 *  - The verification token is never stored in plain text (SHA-256).
 *  - The verification token is single-use and tied to one phone + purpose.
 *  - Comparisons use hash_equals() to avoid timing side-channels.
 */
class OtpService
{
    public function __construct(private readonly Fast2SmsService $sms) {}

    // ── Send ──────────────────────────────────────────────────────────────────

    /**
     * Generate and dispatch a new OTP for the given phone + purpose.
     *
     * Invalidates any previous unconsumed OTP for the same phone/purpose,
     * enforces the resend cooldown, and delivers the code via Fast2SMS.
     *
     * @throws OtpVerificationException on cooldown or delivery failure
     */
    public function sendOtp(string $phone, string $purpose, ?string $ip = null): void
    {
        DB::transaction(function () use ($phone, $purpose, $ip) {
            $existing = OtpVerification::where('phone', $phone)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $cooldown = (int) config('otp.resend_cooldown_seconds');

            if ($existing && $existing->isWithinCooldown($cooldown)) {
                throw OtpVerificationException::cooldownActive(
                    $existing->cooldownSecondsRemaining($cooldown)
                );
            }

            $code = $this->generateCode();

            $record = OtpVerification::create([
                'phone'        => $phone,
                'otp_hash'     => $this->hashOtp($code),
                'purpose'      => $purpose,
                'attempts'     => 0,
                'expires_at'   => Carbon::now()->addMinutes((int) config('otp.expiry_minutes')),
                'last_sent_at' => Carbon::now(),
                'ip_address'   => $ip,
            ]);

            // Any earlier unconsumed OTP for this phone/purpose is superseded.
            if ($existing) {
                $existing->update(['consumed_at' => Carbon::now()]);
            }

            $sent = $this->sms->send($phone, $this->messageFor($purpose, $code));

            if (! $sent) {
                // Roll back so the cooldown doesn't lock the user out after a
                // delivery failure that wasn't their fault.
                $record->delete();

                throw OtpVerificationException::sendFailed();
            }
        });
    }

    // ── Verify (returns a single-use verification token) ────────────────────

    /**
     * Verify an OTP and, on success, issue a short-lived verification token
     * proving this exact phone number completed OTP verification for this
     * purpose. The plain token is returned once and only its hash is stored.
     *
     * @throws OtpVerificationException on missing/expired/wrong/locked OTP
     */
    public function verifyOtp(string $phone, string $code, string $purpose): string
    {
        // A wrong guess must still record the failed attempt in the database
        // even though the caller ultimately sees an error — so the "invalid
        // code" case returns a sentinel and commits, and we throw *after*
        // the transaction closes instead of inside it (throwing inside would
        // roll back the attempts increment along with everything else).
        $outcome = DB::transaction(function () use ($phone, $code, $purpose) {
            $record = $this->lockLatestActive($phone, $purpose);

            $this->assertUsable($record);

            if (! $this->codeMatches($record, $code)) {
                $record->increment('attempts');

                return ['ok' => false, 'remaining' => max(0, (int) config('otp.max_attempts') - $record->attempts)];
            }

            $token = Str::random(64);

            $record->update([
                'verified_at'                   => Carbon::now(),
                'verification_token_hash'       => $this->hashToken($token),
                'verification_token_expires_at' => Carbon::now()->addMinutes(
                    (int) config('otp.verification_token_ttl_minutes')
                ),
            ]);

            return ['ok' => true, 'token' => $token];
        });

        if (! $outcome['ok']) {
            throw OtpVerificationException::invalidCode($outcome['remaining']);
        }

        return $outcome['token'];
    }

    /**
     * Verify an OTP and immediately consume it in one step, without issuing
     * a verification token. Used for flows where the OTP and the resulting
     * action (e.g. password reset) are submitted together in a single
     * trusted server-side request.
     *
     * @throws OtpVerificationException on missing/expired/wrong/locked OTP
     */
    public function verifyAndConsume(string $phone, string $code, string $purpose): void
    {
        // See verifyOtp() for why the failure case commits a sentinel instead
        // of throwing inside the transaction.
        $outcome = DB::transaction(function () use ($phone, $code, $purpose) {
            $record = $this->lockLatestActive($phone, $purpose);

            $this->assertUsable($record);

            if (! $this->codeMatches($record, $code)) {
                $record->increment('attempts');

                return ['ok' => false, 'remaining' => max(0, (int) config('otp.max_attempts') - $record->attempts)];
            }

            $record->update([
                'verified_at' => Carbon::now(),
                'consumed_at' => Carbon::now(),
            ]);

            return ['ok' => true];
        });

        if (! $outcome['ok']) {
            throw OtpVerificationException::invalidCode($outcome['remaining']);
        }
    }

    // ── Verification token ───────────────────────────────────────────────────

    /**
     * Validate a verification token for the exact phone + purpose and return
     * the locked, still-open OtpVerification row so the caller can perform
     * its dependent action (e.g. create the user) and then consume the token
     * within the same database transaction.
     *
     * @throws OtpVerificationException when the token is missing/expired/used/mismatched
     */
    public function lockValidToken(string $phone, string $token, string $purpose): OtpVerification
    {
        $record = OtpVerification::where('verification_token_hash', $this->hashToken($token))
            ->where('purpose', $purpose)
            ->lockForUpdate()
            ->first();

        if (! $record || ! $record->isVerified() || $record->isConsumed()) {
            throw OtpVerificationException::invalidOrExpiredToken();
        }

        if ($record->isTokenExpired()) {
            throw OtpVerificationException::invalidOrExpiredToken();
        }

        if (! hash_equals($record->phone, $phone)) {
            throw OtpVerificationException::tokenPhoneMismatch();
        }

        return $record;
    }

    /**
     * Mark the verification token as consumed so it can never be reused.
     * Must be called within the same transaction as the dependent action.
     */
    public function consumeToken(OtpVerification $record): void
    {
        $record->update(['consumed_at' => Carbon::now()]);
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    private function lockLatestActive(string $phone, string $purpose): ?OtpVerification
    {
        return OtpVerification::where('phone', $phone)
            ->where('purpose', $purpose)
            // Not yet consumed AND not yet verified — an OTP that was already
            // verified once (whether or not its resulting token/action has
            // been consumed) can never be verified again.
            ->whereNull('consumed_at')
            ->whereNull('verified_at')
            ->lockForUpdate()
            ->latest('id')
            ->first();
    }

    /**
     * @throws OtpVerificationException
     */
    private function assertUsable(?OtpVerification $record): void
    {
        if (! $record) {
            throw OtpVerificationException::notFound();
        }

        if ($record->isExpired()) {
            throw OtpVerificationException::expired();
        }

        if ($record->hasExceededAttempts((int) config('otp.max_attempts'))) {
            throw OtpVerificationException::maxAttemptsReached();
        }
    }

    private function codeMatches(OtpVerification $record, string $code): bool
    {
        return hash_equals($record->otp_hash, $this->hashOtp($code));
    }

    /**
     * Cryptographically secure numeric OTP of the configured length.
     */
    private function generateCode(): string
    {
        $length = (int) config('otp.length', 6);
        $max    = (10 ** $length) - 1;

        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }

    /**
     * HMAC-SHA256 the OTP with the application key so a leaked database
     * alone is not enough to recover or brute-force the code offline.
     */
    private function hashOtp(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    private function messageFor(string $purpose, string $code): string
    {
        // Matches, as closely as possible, the exact wording confirmed to
        // pass Fast2SMS's Quick ("q") route spam filter in production
        // ("otp is: 090909") — a brand name prefix alone was enough to get
        // "EasyLoanPoint OTP is: 123456" flagged as spam_sms and rejected.
        return "otp is: {$code}";
    }
}
