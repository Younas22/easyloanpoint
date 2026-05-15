<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\HrController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerAssignmentController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\HR\DashboardController as HRDashboard;
use App\Http\Controllers\HR\LoanController as HRLoanController;
use Illuminate\Support\Facades\Route;

// ─── Redirect root ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Auth routes (guest only) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

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
        Route::post('loans/{loan}/documents',                        [AdminLoanController::class, 'uploadDocument'])->name('loans.upload-document');
        Route::patch('loans/{loan}/documents/{document}/verify',     [AdminLoanController::class, 'verifyDocument'])->name('loans.verify-document');
    });

// ─── HR routes ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        Route::get('/dashboard', [HRDashboard::class, 'index'])->name('dashboard');

        // Loan Management (HR sees only assigned loans)
        Route::get('/loans',                             [HRLoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/{loan}',                      [HRLoanController::class, 'show'])->name('loans.show');
        Route::patch('/loans/{loan}/status',             [HRLoanController::class, 'updateStatus'])->name('loans.update-status');
        Route::patch('/loans/{loan}/documents/verify',   [HRLoanController::class, 'verifyDocument'])->name('loans.verify-document');
    });
