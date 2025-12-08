<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\MFAController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Protected routes - requires session-based authentication (httpOnly cookie)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard routes
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/activity', [DashboardController::class, 'getRecentActivity']);

    // MFA Management routes (require authentication)
    Route::prefix('mfa')->group(function () {
        Route::get('/status', [MFAController::class, 'getMFAStatus']);
        Route::post('/generate-secret', [MFAController::class, 'generateSecret']);
        Route::post('/enable', [MFAController::class, 'enableMFA']);
        Route::post('/disable', [MFAController::class, 'disableMFA']);
        Route::get('/trusted-devices', [MFAController::class, 'getTrustedDevices']);
        Route::post('/revoke-device', [MFAController::class, 'revokeTrustedDevice']);
    });
});

// Public authentication routes - with CSRF protection
Route::middleware('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/mfa/verify', [AuthController::class, 'completeMFALogin']);

    // Public MFA setup routes (for login flow when MFA setup is required)
    Route::post('/mfa/setup/generate', [AuthController::class, 'generateMFASecretForLogin']);
    Route::post('/mfa/setup/enable', [AuthController::class, 'enableMFADuringLogin']);
});
