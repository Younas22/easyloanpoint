<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OtpService $otpService) {}

    // ── 1. Register ───────────────────────────────────────────────────────────

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->mobile,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'customer',
            'status'   => true,
        ]);

        $this->otpService->generate($user->phone, 'register');

        return $this->success(
            'Registration successful. OTP sent to your mobile number.',
            ['mobile' => $user->phone],
            201
        );
    }

    // ── 2. Verify OTP (post-registration) ─────────────────────────────────────

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        if (! $user) {
            return $this->notFound('Mobile number not registered.');
        }

        if ($user->phone_verified_at) {
            return $this->error('Mobile number is already verified. Please login.', 409);
        }

        if (! $this->otpService->verify($request->mobile, $request->otp, 'register')) {
            return $this->error('Invalid or expired OTP. Please request a new one.', 422);
        }

        $user->update(['phone_verified_at' => now()]);

        $token = $user->createToken('flutter-app', ['role:customer'])->plainTextToken;

        return $this->success('Mobile verified successfully.', [
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->userPayload($user),
        ]);
    }

    // ── 3. Login ──────────────────────────────────────────────────────────────

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Invalid mobile number or password.', 401);
        }

        if (! $user->phone_verified_at) {
            $this->otpService->generate($user->phone, 'register');

            return $this->error(
                'Account not verified. A new OTP has been sent to your mobile number.',
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

    // ── 4. Logout ─────────────────────────────────────────────────────────────

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success('Logged out successfully.');
    }

    // ── 5. Forgot Password (send OTP) ────────────────────────────────────────

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        // Always return success to avoid phone enumeration attacks
        if ($user && $user->status) {
            $this->otpService->generate($user->phone, 'forgot_password');
        }

        return $this->success(
            'If this mobile number is registered, an OTP has been sent.',
            ['mobile' => $request->mobile]
        );
    }

    // ── 6. Reset Password (verify OTP + set new password) ────────────────────

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        if (! $user) {
            return $this->notFound('Mobile number not registered.');
        }

        if (! $this->otpService->verify($request->mobile, $request->otp, 'forgot_password')) {
            return $this->error('Invalid or expired OTP.', 422);
        }

        $user->update(['password' => $request->password]);

        // Revoke all tokens so user must login fresh
        $user->tokens()->delete();

        return $this->success('Password reset successfully. Please login with your new password.');
    }

    // ── 7. Resend OTP ────────────────────────────────────────────────────────

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile'  => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'purpose' => ['required', 'in:register,forgot_password'],
        ]);

        $user = User::where('phone', $request->mobile)->where('role', 'customer')->first();

        if (! $user) {
            return $this->notFound('Mobile number not registered.');
        }

        if ($request->purpose === 'register' && $user->phone_verified_at) {
            return $this->error('Mobile number is already verified.', 409);
        }

        $this->otpService->generate($user->phone, $request->purpose);

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
