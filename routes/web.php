<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\HrController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerAssignmentController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\HR\DashboardController as HRDashboard;
use App\Http\Controllers\HR\LoanController as HRLoanController;
use App\Http\Controllers\HR\CustomerController as HRCustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\LoanTypeController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\SystemToolsController;
use Illuminate\Support\Facades\Route;

// ─── Public homepage ──────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/download/app', [HomeController::class, 'downloadApk'])->name('download.apk');

// ─── Auth routes (guest only) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Notifications (shared: admin + HR) ──────────────────────────────────────
Route::middleware('auth')
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/',                           [NotificationController::class, 'index'])->name('index');
        Route::get('/dropdown',                   [NotificationController::class, 'dropdown'])->name('dropdown');
        Route::get('/unread-count',               [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::patch('/mark-all-read',            [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::patch('/{notification}/read',      [NotificationController::class, 'markRead'])->name('mark-read');
        Route::delete('/{notification}',          [NotificationController::class, 'destroy'])->name('destroy');
    });

// ─── Admin routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard',         [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart',   [AdminDashboard::class, 'chartData'])->name('dashboard.chart');

        // HR Management
        Route::resource('hr', HrController::class)->except(['show']);
        Route::patch('hr/{hr}/toggle-status', [HrController::class, 'toggleStatus'])->name('hr.toggle-status');

        // Customer Management
        Route::resource('customers', CustomerController::class);
        Route::patch('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
        Route::post('customers/{customer}/assign-hr',      [CustomerController::class, 'assignHr'])->name('customers.assign-hr');
        Route::patch('customers/{customer}/note',          [CustomerController::class, 'updateNote'])->name('customers.update-note');

        // Customer Assignments
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/',                   [CustomerAssignmentController::class, 'index'])->name('index');
            Route::post('/',                  [CustomerAssignmentController::class, 'store'])->name('store');
            Route::get('/hr-stats',           [CustomerAssignmentController::class, 'hrStats'])->name('hr-stats');
            Route::get('/{customer}/history', [CustomerAssignmentController::class, 'history'])->name('history');
        });

        // Loan Management
        Route::resource('loans', AdminLoanController::class);
        Route::patch('loans/{loan}/status',                          [AdminLoanController::class, 'updateStatus'])->name('loans.update-status');
        Route::patch('loans/{loan}/approve-payment',                 [AdminLoanController::class, 'approvePayment'])->name('loans.approve-payment');
        Route::post('loans/{loan}/documents',                        [AdminLoanController::class, 'uploadDocument'])->name('loans.upload-document');
        Route::patch('loans/{loan}/documents/{document}/verify',     [AdminLoanController::class, 'verifyDocument'])->name('loans.verify-document');

        // Loan Types
        Route::resource('loan-types', LoanTypeController::class)->except(['show']);
        Route::patch('loan-types/{loanType}/toggle-status', [LoanTypeController::class, 'toggleStatus'])->name('loan-types.toggle-status');

        // Reports & Analytics
        Route::get('/reports',        [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/ajax',   [ReportController::class, 'ajax'])->name('reports.ajax');
        Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
        Route::get('/reports/print',  [ReportController::class, 'printReport'])->name('reports.print');
        Route::get('/settings',          [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/{group}', [SettingController::class, 'update'])->name('settings.update');

        // System Tools (super_admin only — controller enforces it)
        Route::prefix('settings/system')->name('settings.system.')->group(function () {
            Route::get('/migrations/pending', [SystemToolsController::class, 'pendingMigrations'])->name('migrations.pending');
            Route::post('/migrate',           [SystemToolsController::class, 'migrate'])->name('migrate');
            Route::post('/migrate-one',       [SystemToolsController::class, 'migrateOne'])->name('migrate-one');
            Route::post('/composer-install',  [SystemToolsController::class, 'composerInstall'])->name('composer-install');
            Route::get('/logs/recent',        [SystemToolsController::class, 'recentLogs'])->name('logs.recent');
            Route::post('/cache-clear',       [SystemToolsController::class, 'cacheClear'])->name('cache-clear');
        });
        Route::get('/logs',      [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('/logs/ajax', [ActivityLogController::class, 'ajax'])->name('logs.ajax');

        // Permissions (super_admin only — controller enforces it)
        Route::get('/permissions',                          [PermissionsController::class, 'index'])->name('permissions.index');
        Route::patch('/permissions/{permission}/toggle',    [PermissionsController::class, 'toggle'])->name('permissions.toggle');
    });

// ─── Profile routes (shared: admin + HR) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',           [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile',           [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password',  [ProfileController::class, 'showPasswordForm'])->name('profile.password');
    Route::put('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// ─── HR routes ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        Route::get('/dashboard', [HRDashboard::class, 'index'])->name('dashboard');

        // My Customers (HR sees only assigned customers)
        Route::get('/customers',                         [HRCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}',              [HRCustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{customer}/remark',      [HRCustomerController::class, 'addRemark'])->name('customers.remark');

        // Loan Management (HR sees only assigned loans)
        Route::get('/loans',                             [HRLoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/{loan}',                      [HRLoanController::class, 'show'])->name('loans.show');
        Route::patch('/loans/{loan}/status',             [HRLoanController::class, 'updateStatus'])->name('loans.update-status');
        Route::patch('/loans/{loan}/documents/verify',   [HRLoanController::class, 'verifyDocument'])->name('loans.verify-document');
    });
