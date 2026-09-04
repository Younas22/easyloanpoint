<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\OtpVerificationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OtpService $otpService) {}

    // ── 1. Send OTP (pre-registration) ───────────────────────────────────────
    //
    // Generates and sends an OTP for the given mobile number. No account is
    // created here — this only proves, in step 2, that the requester owns
    // the phone number they are about to register with.

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $mobile = $request->mobile;

        if (User::where('phone', $mobile)->whereNotNull('phone_verified_at')->exists()) {
            return $this->error('This mobile number is already registered. Please login instead.', 409);
        }

        try {
            $this->otpService->sendOtp($mobile, 'register', $request->ip());
        } catch (OtpVerificationException $e) {
            return $this->error($e->getMessage(), $e->statusCode, $e->meta ?: null);
        }

        return $this->success('OTP sent successfully.', ['mobile' => $mobile]);
    }

    // ── 2. Verify OTP → issue a temporary verification token ─────────────────
    //
    // Does NOT create or authenticate any account. On success it returns a
    // short-lived, single-use verification_token proving this exact mobile
    // number completed OTP verification for registration.

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $token = $this->otpService->verifyOtp($request->mobile, $request->otp, 'register');
        } catch (OtpVerificationException $e) {
            return $this->error($e->getMessage(), $e->statusCode, $e->meta ?: null);
        }

        return $this->success('Mobile number verified successfully.', [
            'verification_token' => $token,
        ]);
    }

    // ── 3. Register (requires a valid verification_token) ────────────────────
    //
    // The account is created ONLY after the verification token — proof of
    // server-side OTP verification for this exact phone number — is
    // validated. A client-supplied "phone_verified" style flag is never
    // trusted; verification state lives solely in otp_verifications.

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            [$user, $token] = DB::transaction(function () use ($request) {
                $otpRecord = $this->otpService->lockValidToken(
                    $request->mobile,
                    $request->verification_token,
                    'register'
                );

                // Self-heal: a previous registration attempt for this exact
                // number that never completed OTP verification does not
                // block a fresh, now OTP-verified attempt. Only a completed
                // (phone-verified) account can already own this number —
                // RegisterRequest already rejects that case before we get here.
                User::where('phone', $request->mobile)
                    ->where('role', 'customer')
                    ->whereNull('phone_verified_at')
                    ->delete();

                $user = User::create([
                    'name'              => $request->name,
                    'phone'             => $request->mobile,
                    'email'             => $request->email,
                    'password'          => $request->password,
                    'role'              => 'customer',
                    'status'            => true,
                    'phone_verified_at' => now(),
                ]);

                $this->otpService->consumeToken($otpRecord);

                $accessToken = $user->createToken('flutter-app', ['role:customer'])->plainTextToken;

                return [$user, $accessToken];
            });
        } catch (OtpVerificationException $e) {
            return $this->error($e->getMessage(), $e->statusCode, $e->meta ?: null);
        } catch (\Throwable $e) {
            Log::error('Registration failed.', ['error' => $e->getMessage()]);

            return $this->error('Unable to complete registration. Please try again.', 500);
        }

        return $this->success('Registration successful.', [
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->userPayload($user),
        ], 201);
    }

    // ── 4. Login ──────────────────────────────────────────────────────────────

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Invalid mobile number or password.', 401);
        }

        if (! $user->phone_verified_at) {
            // Legacy/incomplete account from before mobile verification was
            // required at registration. Ask the user to verify and complete
            // registration again through the normal send-otp/verify-otp flow.
            return $this->error(
                'This account never completed mobile verification. Please register again.',
                403,
                ['needs_verification' => true, 'mobile' => $user->phone]
            );
        }

        if (! $user->status) {
            return $this->error('Your account has been deactivated. Please contact support.', 403);
        }

        // Revoke old tokens to keep only 1 active session per device
        $user->tokens()->delete();

        $token = $user->createToken('flutter-app', ['role:customer'])->plainTextToken;

        return $this->success('Login successful.', [
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->userPayload($user),
        ]);
    }

    // ── 5. Logout ─────────────────────────────────────────────────────────────

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success('Logged out successfully.');
    }

    // ── 6. Forgot Password (send OTP) ────────────────────────────────────────

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        // Always return success to avoid phone enumeration attacks — including
        // when the underlying send hits a cooldown or delivery failure.
        if ($user && $user->status) {
            try {
                $this->otpService->sendOtp($user->phone, 'forgot_password', $request->ip());
            } catch (OtpVerificationException $e) {
                Log::info('Forgot-password OTP not sent.', ['reason' => $e->getMessage()]);
            }
        }

        return $this->success(
            'If this mobile number is registered, an OTP has been sent.',
            ['mobile' => $request->mobile]
        );
    }

    // ── 7. Reset Password (verify OTP + set new password) ────────────────────

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        if (! $user) {
            return $this->notFound('Mobile number not registered.');
        }

        try {
            $this->otpService->verifyAndConsume($request->mobile, $request->otp, 'forgot_password');
        } catch (OtpVerificationException $e) {
            return $this->error($e->getMessage(), $e->statusCode, $e->meta ?: null);
        }

        $user->update(['password' => $request->password]);

        // Revoke all tokens so user must login fresh
        $user->tokens()->delete();

        return $this->success('Password reset successfully. Please login with your new password.');
    }

    // ── 8. Resend OTP ────────────────────────────────────────────────────────

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile'  => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'purpose' => ['required', 'in:register,forgot_password'],
        ]);

        if ($request->purpose === 'register') {
            if (User::where('phone', $request->mobile)->whereNotNull('phone_verified_at')->exists()) {
                return $this->error('This mobile number is already registered. Please login instead.', 409);
            }
        } else {
            $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

            if (! $user) {
                return $this->notFound('Mobile number not registered.');
            }
        }

        try {
            $this->otpService->sendOtp($request->mobile, $request->purpose, $request->ip());
        } catch (OtpVerificationException $e) {
            return $this->error($e->getMessage(), $e->statusCode, $e->meta ?: null);
        }

        return $this->success('OTP resent to your mobile number.');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function userPayload(User $user): array
    {
        return [
            'id'                 => $user->id,
            'name'               => $user->name,
            'mobile'             => $user->phone,
            'email'              => $user->email,
            'phone_verified'     => $user->phone_verified_at !== null,
            'profile_image'      => $user->profile_image
                ? asset('public/uploads/profiles/' . $user->profile_image)
                : null,
            'member_since'       => $user->created_at->format('d M Y'),
        ];
    }
}
