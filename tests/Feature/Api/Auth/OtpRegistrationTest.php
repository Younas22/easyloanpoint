<?php

namespace Tests\Feature\Api\Auth;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OtpRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private const MOBILE = '9876543210';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.fast2sms.api_key' => 'test-api-key']);
    }

    private function fakeFast2SmsSuccess(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::response([
                'return'     => true,
                'request_id' => 'abc123',
                'message'    => ['Message sent successfully'],
            ], 200),
        ]);
    }

    private function fakeFast2SmsFailure(): void
    {
        Http::fake([
            'www.fast2sms.com/*' => Http::response([
                'return'  => false,
                'message' => 'Invalid Authentication',
            ], 200),
        ]);
    }

    private function latestOtp(string $purpose = 'register'): OtpVerification
    {
        return OtpVerification::where('phone', self::MOBILE)
            ->where('purpose', $purpose)
            ->latest('id')
            ->firstOrFail();
    }

    /** Read the plain OTP that was actually sent, by inspecting the faked HTTP request. */
    private function plainOtpFromLastSms(): string
    {
        $sent = null;
        Http::assertSent(function ($request) use (&$sent) {
            if (str_contains($request->url(), 'fast2sms.com')) {
                $sent = $request['message'] ?? null;
            }

            return true;
        });

        preg_match('/\b(\d{6})\b/', (string) $sent, $m);

        return $m[1] ?? '';
    }

    // ── 1. Send OTP ──────────────────────────────────────────────────────────

    public function test_send_otp_successfully(): void
    {
        $this->fakeFast2SmsSuccess();

        $response = $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);

        $response->assertOk()->assertJson(['status' => true]);
        $this->assertDatabaseCount('otp_verifications', 1);

        // OTP must never be present in the response.
        $response->assertJsonMissingPath('data.otp');
        $this->assertStringNotContainsString('"otp"', $response->getContent());
    }

    // ── 2. Invalid phone ─────────────────────────────────────────────────────

    public function test_send_otp_rejects_invalid_phone(): void
    {
        $response = $this->postJson('/api/v1/auth/send-otp', ['mobile' => '12345']);

        $response->assertStatus(422);
        $this->assertDatabaseCount('otp_verifications', 0);
    }

    // ── 3. Duplicate phone (already verified account) ───────────────────────

    public function test_send_otp_rejects_already_registered_phone(): void
    {
        User::factory()->create([
            'phone'             => self::MOBILE,
            'role'              => 'customer',
            'phone_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);

        $response->assertStatus(409);
    }

    // ── 4. Resend cooldown ────────────────────────────────────────────────────

    public function test_send_otp_enforces_resend_cooldown(): void
    {
        $this->fakeFast2SmsSuccess();

        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE])->assertOk();
        $response = $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);

        $response->assertStatus(429);
        $response->assertJsonPath('errors.retry_after_seconds', fn ($v) => $v > 0);
        $this->assertDatabaseCount('otp_verifications', 1);
    }

    // ── 5. Rate limit (route throttle) ───────────────────────────────────────

    public function test_send_otp_is_rate_limited(): void
    {
        $this->fakeFast2SmsSuccess();

        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/v1/auth/send-otp', ['mobile' => '98765432' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)]);
        }

        $response = $this->postJson('/api/v1/auth/send-otp', ['mobile' => '9876543299']);

        $response->assertStatus(429);
    }

    // ── 6. Fast2SMS success ──────────────────────────────────────────────────

    public function test_send_otp_calls_fast2sms_with_correct_route(): void
    {
        $this->fakeFast2SmsSuccess();

        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE])->assertOk();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fast2sms.com/dev/bulkV2')
                && $request['route'] === 'q'
                && $request['numbers'] === self::MOBILE
                && $request->hasHeader('authorization');
        });
    }

    // ── 7. Fast2SMS failure ───────────────────────────────────────────────────

    public function test_send_otp_handles_fast2sms_failure(): void
    {
        $this->fakeFast2SmsFailure();

        $response = $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);

        $response->assertStatus(502);
        // Failed send must not leave a row that blocks retry via cooldown.
        $this->assertDatabaseCount('otp_verifications', 0);
    }

    // ── 8. Incorrect OTP ──────────────────────────────────────────────────────

    public function test_verify_otp_rejects_incorrect_code(): void
    {
        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);

        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => self::MOBILE,
            'otp'    => '000000',
        ]);

        // extremely unlikely the real OTP is 000000; guard just in case
        if ($this->plainOtpFromLastSms() === '000000') {
            $this->markTestSkipped('Random OTP collided with test value.');
        }

        $response->assertStatus(422);
        $response->assertJsonPath('errors.attempts_remaining', fn ($v) => $v >= 0);
    }

    // ── 9. Correct OTP ────────────────────────────────────────────────────────

    public function test_verify_otp_accepts_correct_code_and_returns_token(): void
    {
        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);
        $code = $this->plainOtpFromLastSms();

        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => self::MOBILE,
            'otp'    => $code,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'message', 'data' => ['verification_token']]);
        $this->assertNotEmpty($response->json('data.verification_token'));
    }

    // ── 10. Expired OTP ───────────────────────────────────────────────────────

    public function test_verify_otp_rejects_expired_code(): void
    {
        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);
        $code = $this->plainOtpFromLastSms();

        $this->latestOtp()->update(['expires_at' => now()->subMinute()]);

        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'mobile' => self::MOBILE,
            'otp'    => $code,
        ]);

        $response->assertStatus(422);
    }

    // ── 11. Maximum OTP attempts ─────────────────────────────────────────────

    public function test_verify_otp_locks_out_after_max_attempts(): void
    {
        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/verify-otp', ['mobile' => self::MOBILE, 'otp' => '111111']);
        }

        $code = $this->plainOtpFromLastSms();
        $response = $this->postJson('/api/v1/auth/verify-otp', ['mobile' => self::MOBILE, 'otp' => $code]);

        $response->assertStatus(429);
    }

    // ── 12. OTP reuse ─────────────────────────────────────────────────────────

    public function test_otp_cannot_be_reused_after_verification(): void
    {
        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);
        $code = $this->plainOtpFromLastSms();

        $this->postJson('/api/v1/auth/verify-otp', ['mobile' => self::MOBILE, 'otp' => $code])->assertOk();

        $response = $this->postJson('/api/v1/auth/verify-otp', ['mobile' => self::MOBILE, 'otp' => $code]);

        $response->assertStatus(422);
    }

    // ── 13. Verification token generation ────────────────────────────────────

    public function test_verification_token_is_hashed_in_database(): void
    {
        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE]);
        $code = $this->plainOtpFromLastSms();

        $response = $this->postJson('/api/v1/auth/verify-otp', ['mobile' => self::MOBILE, 'otp' => $code]);
        $token = $response->json('data.verification_token');

        $record = $this->latestOtp();
        $this->assertNotEquals($token, $record->getRawOriginal('verification_token_hash'));
        $this->assertEquals(hash('sha256', $token), $record->getRawOriginal('verification_token_hash'));
    }

    // ── 14. Expired verification token ───────────────────────────────────────

    public function test_registration_rejects_expired_verification_token(): void
    {
        $token = $this->verifiedMobileToken();
        $this->latestOtp()->update(['verification_token_expires_at' => now()->subMinute()]);

        $response = $this->postJson('/api/v1/auth/register', $this->registerPayload($token));

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['phone' => self::MOBILE]);
    }

    // ── 15. Verification token reuse ─────────────────────────────────────────

    public function test_verification_token_cannot_be_reused(): void
    {
        $token = $this->verifiedMobileToken();

        $this->postJson('/api/v1/auth/register', $this->registerPayload($token))->assertCreated();

        $response = $this->postJson('/api/v1/auth/register', $this->registerPayload($token, mobile: '9876500001'));

        $response->assertStatus(422);
    }

    // ── 16. Registration without OTP verification ────────────────────────────

    public function test_registration_fails_without_verification_token(): void
    {
        $payload = $this->registerPayload('');
        unset($payload['verification_token']);

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['phone' => self::MOBILE]);
    }

    // ── 17. Registration with fake verification token ────────────────────────

    public function test_registration_fails_with_fake_verification_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', $this->registerPayload('not-a-real-token'));

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['phone' => self::MOBILE]);
    }

    // ── 18. Token belonging to another phone ─────────────────────────────────

    public function test_registration_fails_when_token_belongs_to_another_phone(): void
    {
        $token = $this->verifiedMobileToken('9876500002');

        $response = $this->postJson('/api/v1/auth/register', $this->registerPayload($token, mobile: self::MOBILE));

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['phone' => self::MOBILE]);
    }

    // ── 19. Registration with changed phone number ───────────────────────────

    public function test_registration_fails_when_phone_changed_after_verification(): void
    {
        $token = $this->verifiedMobileToken(self::MOBILE);

        $response = $this->postJson('/api/v1/auth/register', $this->registerPayload($token, mobile: '9876511111'));

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['phone' => '9876511111']);
    }

    // ── 20. Successful verified registration ─────────────────────────────────

    public function test_successful_registration_after_otp_verification(): void
    {
        $token = $this->verifiedMobileToken();

        $response = $this->postJson('/api/v1/auth/register', $this->registerPayload($token));

        $response->assertCreated();
        $response->assertJsonStructure(['status', 'message', 'data' => ['token', 'token_type', 'user']]);

        $this->assertDatabaseHas('users', [
            'phone' => self::MOBILE,
            'role'  => 'customer',
        ]);

        $user = User::where('phone', self::MOBILE)->first();
        $this->assertNotNull($user->phone_verified_at);
    }

    // ── 21. Token consumption after registration ─────────────────────────────

    public function test_verification_token_is_consumed_after_registration(): void
    {
        $token = $this->verifiedMobileToken();

        $this->postJson('/api/v1/auth/register', $this->registerPayload($token))->assertCreated();

        $this->assertNotNull($this->latestOtp()->consumed_at);
    }

    // ── 22. Duplicate registration after successful verification ─────────────

    public function test_duplicate_registration_after_success_is_rejected(): void
    {
        $token = $this->verifiedMobileToken();
        $this->postJson('/api/v1/auth/register', $this->registerPayload($token))->assertCreated();

        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => self::MOBILE])->assertStatus(409);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function verifiedMobileToken(string $mobile = self::MOBILE): string
    {
        $this->fakeFast2SmsSuccess();
        $this->postJson('/api/v1/auth/send-otp', ['mobile' => $mobile]);

        $record = OtpVerification::where('phone', $mobile)->where('purpose', 'register')->latest('id')->firstOrFail();

        $sent = null;
        Http::assertSent(function ($request) use (&$sent) {
            if (str_contains($request->url(), 'fast2sms.com')) {
                $sent = $request['message'] ?? null;
            }

            return true;
        });
        preg_match('/\b(\d{6})\b/', (string) $sent, $m);
        $code = $m[1] ?? '';

        $response = $this->postJson('/api/v1/auth/verify-otp', ['mobile' => $mobile, 'otp' => $code]);

        return $response->json('data.verification_token');
    }

    private function registerPayload(string $token, string $mobile = self::MOBILE): array
    {
        return [
            'name'                  => 'Test User',
            'mobile'                => $mobile,
            'email'                 => 'test' . uniqid() . '@example.com',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
            'verification_token'    => $token,
        ];
    }
}
