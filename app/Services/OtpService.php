<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private const OTP_TTL_MINUTES = 10;

    public function generate(string $phone, string $purpose): Otp
    {
        // Invalidate any existing unused OTPs for this phone + purpose
        Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->delete();

        $otp = Otp::create([
            'phone'      => $phone,
            'otp'        => $this->makeCode(),
            'purpose'    => $purpose,
            'expires_at' => Carbon::now()->addMinutes(self::OTP_TTL_MINUTES),
        ]);

        $this->send($phone, $otp->otp, $purpose);

        return $otp;
    }

    public function verify(string $phone, string $code, string $purpose): bool
    {
        $record = Otp::where('phone', $phone)
            ->where('otp', $code)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $record || ! $record->isValid()) {
            return false;
        }

        $record->markAsUsed();

        return true;
    }

    private function makeCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function send(string $phone, string $code, string $purpose): void
    {
        $label = $purpose === 'register' ? 'registration' : 'password reset';

        // ── Development: log OTP to laravel.log ──────────────────────────────
        Log::info("OTP [{$purpose}] for {$phone}: {$code} (expires in " . self::OTP_TTL_MINUTES . " min)");

        // ── Production: plug in your SMS provider below ───────────────────────
        // Example: MSG91, Fast2SMS, Twilio
        //
        // $message = "Your EasyLoanPoint {$label} OTP is: {$code}. Valid for " . self::OTP_TTL_MINUTES . " minutes. Do not share with anyone.";
        //
        // Fast2SMS example:
        // Http::withHeaders(['authorization' => config('services.fast2sms.key')])
        //     ->post('https://www.fast2sms.com/dev/bulkV2', [
        //         'route'   => 'q',
        //         'message' => $message,
        //         'numbers' => $phone,
        //     ]);
    }
}
