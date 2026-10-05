<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProgramEligibilityController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/broadcasting/auth', function (Request $request) {
        return Broadcast::auth($request);
    });
});

Route::post('/program/eligibility', [ProgramEligibilityController::class, 'check'])->name('api.program.eligibility');
Route::get('/program/{programId}/requirements', [ProgramEligibilityController::class, 'getRequirements']);

// API v1 Routes
require_once __DIR__ . '/api/v1.php';
