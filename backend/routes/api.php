<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\MFAController;
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
use App\Http\Controllers\Admin\Procurement\DeliveryReceiptController;
use App\Http\Controllers\Admin\Procurement\InspectionAcceptanceReportController;

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

// Public routes
Route::get('/test-connection', function () {
    return response()->json(['message' => 'API is working']);
});

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
        // Master Data routes (structured approach)
        Route::prefix('master-data')->group(function () {
            // Categories
            Route::apiResource('categories', MasterDataCategoryController::class)->parameters(['categories' => 'id']);
            Route::post('categories/{id}/restore', [MasterDataCategoryController::class, 'restore']);

            // Classifications
            Route::apiResource('classifications', ClassificationController::class)->parameters(['classifications' => 'id']);
            Route::post('classifications/{id}/restore', [ClassificationController::class, 'restore']);

            // Conditions
            Route::apiResource('conditions', ConditionController::class)->parameters(['conditions' => 'id']);
            Route::post('conditions/{id}/restore', [ConditionController::class, 'restore']);

            // Departments
            Route::apiResource('departments', MasterDataDepartmentController::class)->parameters(['departments' => 'id']);
            Route::post('departments/{id}/restore', [MasterDataDepartmentController::class, 'restore']);

            // Disposal Methods
            Route::apiResource('disposal-methods', DisposalMethodController::class)->parameters(['disposal-methods' => 'id']);
            Route::post('disposal-methods/{id}/restore', [DisposalMethodController::class, 'restore']);

            // End Users
            Route::apiResource('end-users', EndUserController::class)->parameters(['end-users' => 'id']);
            Route::post('end-users/{id}/restore', [EndUserController::class, 'restore']);

            // Positions
            Route::apiResource('positions', PositionController::class)->parameters(['positions' => 'id']);
            Route::post('positions/{id}/restore', [PositionController::class, 'restore']);

            // Property Classifications
            Route::apiResource('property-classifications', PropertyClassificationController::class)->parameters(['property-classifications' => 'id']);
            Route::post('property-classifications/{id}/restore', [PropertyClassificationController::class, 'restore']);

            // Property Statuses
            Route::apiResource('property-statuses', PropertyStatusController::class)->parameters(['property-statuses' => 'id']);
            Route::post('property-statuses/{id}/restore', [PropertyStatusController::class, 'restore']);

            // Property Types
            Route::apiResource('property-types', PropertyTypeController::class)->parameters(['property-types' => 'id']);
            Route::post('property-types/{id}/restore', [PropertyTypeController::class, 'restore']);

            // Suppliers
            Route::apiResource('suppliers', SupplierController::class)->parameters(['suppliers' => 'id']);
            Route::post('suppliers/{id}/restore', [SupplierController::class, 'restore']);

            // Units of Measure
            Route::apiResource('units-of-measure', UnitOfMeasureController::class)->parameters(['units-of-measure' => 'id']);
            Route::post('units-of-measure/{id}/restore', [UnitOfMeasureController::class, 'restore']);

            // User Roles
            Route::apiResource('user-roles', UserRoleController::class)->parameters(['user-roles' => 'id']);
            Route::post('user-roles/{id}/restore', [UserRoleController::class, 'restore']);
        });

        // Delivery Receipt routes (simplified workflow - starts here)
        Route::prefix('procurement')->group(function () {
            Route::apiResource('delivery-receipts', DeliveryReceiptController::class)->parameters(['delivery-receipts' => 'id']);
            Route::post('delivery-receipts/{id}/restore', [DeliveryReceiptController::class, 'restore']);
            Route::get('delivery-receipts-helpers/suppliers', [DeliveryReceiptController::class, 'getSuppliers']);
            Route::get('delivery-receipts-helpers/property-types', [DeliveryReceiptController::class, 'getPropertyTypes']);

            // IAR routes (Inspection & Acceptance Report)
            Route::apiResource('inspection-acceptance-reports', InspectionAcceptanceReportController::class)->parameters(['inspection-acceptance-reports' => 'id']);
            Route::get('inspection-acceptance-reports-helpers/conditions', [InspectionAcceptanceReportController::class, 'getConditions']);
            Route::get('inspection-acceptance-reports-helpers/users', [InspectionAcceptanceReportController::class, 'getUsers']);
        });

        // Note: Procurement routes (PR/PO) have been removed per project requirements
    });
});

// Public authentication routes - with CSRF protection
Route::middleware('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/mfa/verify', [AuthController::class, 'completeMFALogin']);

    // Public MFA setup routes (for login flow when MFA setup is required)
    Route::post('/mfa/setup/generate', [AuthController::class, 'generateMFASecretForLogin']);
    Route::post('/mfa/setup/enable', [AuthController::class, 'enableMFADuringLogin']);
});
