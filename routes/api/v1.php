<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\IntakeController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\DownloadController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    
    // Public Routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Schools (Public)
    Route::get('/schools', [SchoolController::class, 'index']);
    Route::get('/schools/{slug}', [SchoolController::class, 'show']);
    Route::get('/schools/{slug}/programs', [SchoolController::class, 'programs']);
    Route::get('/schools/{slug}/intakes', [SchoolController::class, 'intakes']);

    // Programs (Public)
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{program}', [ProgramController::class, 'show']);
    Route::get('/programs/{program}/requirements', [ProgramController::class, 'requirements']);
    Route::post('/programs/check-eligibility', [ProgramController::class, 'checkEligibility']);

    // Intakes (Public)
    Route::get('/intakes', [IntakeController::class, 'index']);
    Route::get('/intakes/current', [IntakeController::class, 'current']);
    Route::get('/intakes/{intake}', [IntakeController::class, 'show']);

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthController::class, 'updatePassword']);
        Route::post('/auth/verify-2fa', [AuthController::class, 'verifyTwoFactor']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Applications
        Route::get('/applications', [ApplicationController::class, 'index']);
        Route::get('/applications/create', [ApplicationController::class, 'create']);
        Route::post('/applications', [ApplicationController::class, 'store']);
        Route::get('/applications/{application}', [ApplicationController::class, 'show']);
        Route::put('/applications/{application}', [ApplicationController::class, 'update']);
        Route::post('/applications/{application}/submit', [ApplicationController::class, 'submit']);
        Route::get('/applications/current-intake', [ApplicationController::class, 'currentIntake']);

        // Programs - User specific
        Route::get('/departments', [ProgramController::class, 'departments']);

        // Documents
        Route::get('/documents', [DocumentController::class, 'index']);
        Route::post('/documents', [DocumentController::class, 'upload']);
        Route::get('/documents/{document}', [DocumentController::class, 'show']);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

        // Payments
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);
        Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
        Route::get('/payments/{payment}/status', [PaymentController::class, 'status']);
        
        // Payment callbacks (webhook)
        Route::post('/payments/callback', [PaymentController::class, 'callback']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);

        // School Settings
        Route::get('/school/settings', [SchoolController::class, 'settings']);

        // Downloads
        Route::get('/downloads/applications/{application}', [DownloadController::class, 'applicationPdfUrl']);
        Route::get('/downloads/payments/{payment}/receipt', [DownloadController::class, 'paymentReceiptUrl']);
        Route::get('/downloads/letters/{letter}/admission', [DownloadController::class, 'admissionLetterUrl']);
    });
});