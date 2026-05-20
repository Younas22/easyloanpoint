<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Customer\LoanController;
use App\Http\Controllers\Api\Customer\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\Customer\ProfileController;
use App\Http\Controllers\Api\LoanTypeController;
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

// ── Public: Loan types (active, visible to app) ───────────────────────────────
Route::get('/loan-types', [LoanTypeController::class, 'index'])->name('api.loan-types');

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

    // ── Customer Mobile App routes ────────────────────────────────────────────
    Route::prefix('customer')->name('api.customer.')->group(function () {

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Loans
        Route::post('/loans/apply',                      [LoanController::class, 'apply'])->name('loans.apply');
        Route::get('/loans',                             [LoanController::class, 'history'])->name('loans.history');
        Route::get('/loans/{loanId}/status',             [LoanController::class, 'statusTracking'])->name('loans.status');
        Route::post('/loans/{loanId}/payment',           [LoanController::class, 'submitPayment'])->name('loans.payment.submit');
        Route::get('/loans/{loanId}/payment',            [LoanController::class, 'getPayment'])->name('loans.payment.get');
        Route::get('/settings/payment-bank',             [LoanController::class, 'paymentBank'])->name('settings.payment-bank');

        // Document uploads  (type constrained to aadhaar|pan|selfie)
        Route::post('/loans/{loanId}/documents/{type}', [LoanController::class, 'uploadDocument'])
             ->where('type', 'aadhaar|pan|selfie')
             ->name('loans.docs.upload');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',              [ApiNotificationController::class, 'index'])->name('index');
            Route::get('/unread-count',  [ApiNotificationController::class, 'unreadCount'])->name('unread-count');
            Route::patch('/mark-all-read', [ApiNotificationController::class, 'markAllRead'])->name('mark-all-read');
            Route::patch('/{id}/read',   [ApiNotificationController::class, 'markRead'])->name('mark-read');
        });
    });
});
