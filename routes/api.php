<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── Health check ───────────────────────────────────────────────────────────────
Route::get('/ping', fn () => response()->json([
    'status'  => true,
    'message' => 'EasyLoanPoint API is running.',
    'version' => '1.0.0',
]));

// ── Public Auth routes ────────────────────────────────────────────────────────
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/register',        [AuthController::class, 'register'])->name('register');
    Route::post('/verify-otp',      [AuthController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/login',           [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password',  [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::post('/resend-otp',      [AuthController::class, 'resendOtp'])->name('resend-otp');
});

// ── Protected routes (Sanctum token required) ─────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    // Authenticated user info
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'status' => true,
            'data'   => [
                'id'             => $user->id,
                'name'           => $user->name,
                'mobile'         => $user->phone,
                'email'          => $user->email,
                'phone_verified' => $user->phone_verified_at !== null,
                'member_since'   => $user->created_at->format('d M Y'),
            ],
        ]);
    });
});
