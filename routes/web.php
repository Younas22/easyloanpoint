<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\HR\DashboardController as HRDashboard;
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
    });

// ─── HR routes ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        Route::get('/dashboard', [HRDashboard::class, 'index'])->name('dashboard');
    });
