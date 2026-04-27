<?php

use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\Tenant\AuthController as TenantAuthController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('super-admin')->name('super-admin.')->group(function () {
    Route::middleware('guest:super_admin')->group(function () {
        Route::get('/login', [SuperAdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [SuperAdminAuthController::class, 'login']);
        Route::get('/forgot-password', [SuperAdminAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [SuperAdminAuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [SuperAdminAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [SuperAdminAuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware(['auth:super_admin', 'role:super_admin'])->group(function () {
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    });
});

Route::middleware(['web', 'tenant'])->prefix('{school}')->name('tenant.')->group(function () {
    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [TenantAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [TenantAuthController::class, 'login']);
        Route::get('/register', [TenantAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [TenantAuthController::class, 'register']);
        Route::get('/forgot-password', [TenantAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [TenantAuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [TenantAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [TenantAuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [TenantAuthController::class, 'logout'])->name('logout');
    });
});

Route::middleware(['web'])->group(function () {
    Route::get('/domain/{school:slug}', function (\App\Models\School $school) {
        return redirect()->to('https://' . ($school->subdomain 
            ? $school->subdomain . '.' . config('app.base_domain')
            : $school->domain));
    })->name('school.domain.redirect');
});