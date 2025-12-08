<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\MFAController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MasterData\CategoryController as MasterDataCategoryController;
use App\Http\Controllers\Admin\MasterData\ClassificationController;
use App\Http\Controllers\Admin\MasterData\ConditionController;
use App\Http\Controllers\Admin\MasterData\DepartmentController as MasterDataDepartmentController;
use App\Http\Controllers\Admin\MasterData\DisposalMethodController;
use App\Http\Controllers\Admin\MasterData\EndUserController;
use App\Http\Controllers\Admin\MasterData\PositionController;
use App\Http\Controllers\Admin\MasterData\PropertyClassificationController;
use App\Http\Controllers\Admin\MasterData\PropertyStatusController;
use App\Http\Controllers\Admin\MasterData\PropertyTypeController;
use App\Http\Controllers\Admin\MasterData\SupplierController;
use App\Http\Controllers\Admin\MasterData\UnitOfMeasureController;
use App\Http\Controllers\Admin\MasterData\UserRoleController;

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

    // Admin routes
    Route::prefix('admin')->group(function () {
        // Legacy category route (keep for backward compatibility)
        Route::apiResource('categories', CategoryController::class);

        // Master Data routes (new structured approach)
        Route::prefix('master-data')->group(function () {
            // Categories
            Route::apiResource('categories', MasterDataCategoryController::class);
            Route::post('categories/{id}/restore', [MasterDataCategoryController::class, 'restore']);

            // Classifications
            Route::apiResource('classifications', ClassificationController::class);
            Route::post('classifications/{id}/restore', [ClassificationController::class, 'restore']);

            // Conditions
            Route::apiResource('conditions', ConditionController::class);
            Route::post('conditions/{id}/restore', [ConditionController::class, 'restore']);

            // Departments
            Route::apiResource('departments', MasterDataDepartmentController::class);
            Route::post('departments/{id}/restore', [MasterDataDepartmentController::class, 'restore']);

            // Disposal Methods
            Route::apiResource('disposal-methods', DisposalMethodController::class);
            Route::post('disposal-methods/{id}/restore', [DisposalMethodController::class, 'restore']);

            // End Users
            Route::apiResource('end-users', EndUserController::class);
            Route::post('end-users/{id}/restore', [EndUserController::class, 'restore']);

            // Positions
            Route::apiResource('positions', PositionController::class);
            Route::post('positions/{id}/restore', [PositionController::class, 'restore']);

            // Property Classifications
            Route::apiResource('property-classifications', PropertyClassificationController::class);
            Route::post('property-classifications/{id}/restore', [PropertyClassificationController::class, 'restore']);

            // Property Statuses
            Route::apiResource('property-statuses', PropertyStatusController::class);
            Route::post('property-statuses/{id}/restore', [PropertyStatusController::class, 'restore']);

            // Property Types
            Route::apiResource('property-types', PropertyTypeController::class);
            Route::post('property-types/{id}/restore', [PropertyTypeController::class, 'restore']);

            // Suppliers
            Route::apiResource('suppliers', SupplierController::class);
            Route::post('suppliers/{id}/restore', [SupplierController::class, 'restore']);

            // Units of Measure
            Route::apiResource('units-of-measure', UnitOfMeasureController::class);
            Route::post('units-of-measure/{id}/restore', [UnitOfMeasureController::class, 'restore']);

            // User Roles
            Route::apiResource('user-roles', UserRoleController::class);
            Route::post('user-roles/{id}/restore', [UserRoleController::class, 'restore']);
        });
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
