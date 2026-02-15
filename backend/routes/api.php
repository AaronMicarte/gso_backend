<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\MFAController;
use App\Http\Controllers\Admin\UserManagementController;
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
use App\Http\Controllers\Admin\Procurement\IarSignatureController;
use App\Http\Controllers\Inspection\InspectionReportController;
use App\Http\Controllers\department_head\DashboardController as DepartmentHeadDashboardController;
use App\Http\Controllers\department_head\ReceivedItemsController as DepartmentHeadReceivedItemsController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RequisitionIssueSlipController;

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

// Public account activation routes
Route::post('/auth/activate-account', [AuthController::class, 'activateAccount']);

// Public MFA routes (for login flow)
Route::post('/mfa/verify', [MFAController::class, 'verifyCode']);
Route::post('/mfa/setup/generate', [AuthController::class, 'generateMFASecretForLogin']);
Route::post('/mfa/setup/enable', [AuthController::class, 'enableMFADuringLogin']);

// Public signature stream for local/dev disk (allows simple dev access to private files)
Route::get('/signatures/{iarId}/{role}', [IarSignatureController::class, 'showPublic']);

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
        // User Management (Admin only)
        Route::get('users', [UserManagementController::class, 'index']);
        Route::post('users', [UserManagementController::class, 'store']);
        Route::patch('users/{id}/active', [UserManagementController::class, 'toggleActive']);
        Route::post('users/{id}/resend-activation', [UserManagementController::class, 'resendActivation']);

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
            Route::get('delivery-receipts-helpers/requesters', [DeliveryReceiptController::class, 'getRequesters']);

            // IAR routes (Inspection & Acceptance Report)
            Route::apiResource('inspection-acceptance-reports', InspectionAcceptanceReportController::class)->parameters(['inspection-acceptance-reports' => 'id']);
            Route::post('inspection-acceptance-reports/{id}/signatures', [IarSignatureController::class, 'store']);
            // Stream signature file (fallback when storage URL isn't directly accessible)
            Route::get('inspection-acceptance-reports/{id}/signatures/{role}', [IarSignatureController::class, 'show']);
            Route::get('inspection-acceptance-reports-helpers/conditions', [InspectionAcceptanceReportController::class, 'getConditions']);
            Route::get('inspection-acceptance-reports-helpers/users', [InspectionAcceptanceReportController::class, 'getUsers']);
        });

        // Note: Procurement routes (PR/PO) have been removed per project requirements
            // Inventory routes
            Route::prefix('inventory')->group(function () {
                Route::get('pending-ingestion', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'pendingIngestion']);
                Route::post('ingest-from-iar', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'ingestFromIar']);
                Route::post('backfill-qr-codes', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'backfillQrCodes']);
                Route::get('all-items', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'allItems']);
                Route::get('', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'index']);
                Route::get('{id}', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'show']);
                Route::put('details/{id}', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'updateDetail']);
                Route::post('details/{id}/generate-qr', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'generateQr']);
                Route::put('details/{id}', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'updateDetail']);
                Route::post('issuances', [\App\Http\Controllers\Admin\Inventory\IssuanceController::class, 'store']);
            });
    });

    // Department Head routes
    Route::middleware('auth:sanctum')->prefix('department-head')->group(function () {
        Route::get('dashboard/stats', [DepartmentHeadDashboardController::class, 'getStats']);
        Route::get('dashboard/activities', [DepartmentHeadDashboardController::class, 'getActivities']);
        Route::get('received-items', [DepartmentHeadReceivedItemsController::class, 'index']);
    });

    // Asset Request & Issuance routes (available to all authenticated users with role-based access)
    Route::prefix('asset-requests')->group(function () {
        Route::get('', [\App\Http\Controllers\AssetRequestController::class, 'index']);
        Route::post('', [\App\Http\Controllers\AssetRequestController::class, 'store']);
        Route::get('approved', [\App\Http\Controllers\PropertyIssuanceController::class, 'getApprovedRequests']);
        Route::get('available-inventory', [\App\Http\Controllers\AssetRequestController::class, 'getAvailableInventory']);
        Route::get('end-users', [\App\Http\Controllers\AssetRequestController::class, 'getEndUsers']);
        Route::get('departments', [\App\Http\Controllers\AssetRequestController::class, 'getDepartments']);
        Route::get('{id}', [\App\Http\Controllers\AssetRequestController::class, 'show']);
        Route::post('{id}/review', [\App\Http\Controllers\AssetRequestController::class, 'saveReview']);
        Route::post('{id}/approve', [\App\Http\Controllers\AssetRequestController::class, 'approve']);
        Route::post('{id}/reject', [\App\Http\Controllers\AssetRequestController::class, 'reject']);
        Route::post('{id}/cancel', [\App\Http\Controllers\AssetRequestController::class, 'cancel']);
        Route::post('{id}/receive', [\App\Http\Controllers\AssetRequestController::class, 'receive']);
        Route::post('{id}/issue', [\App\Http\Controllers\PropertyIssuanceController::class, 'store']);
    });

    Route::prefix('property-issuances')->group(function () {
        Route::get('', [\App\Http\Controllers\PropertyIssuanceController::class, 'index']);
        Route::post('', [\App\Http\Controllers\PropertyIssuanceController::class, 'store']);
        Route::get('stats', [\App\Http\Controllers\PropertyIssuanceController::class, 'getStats']);
        Route::get('available-inventory', [\App\Http\Controllers\PropertyIssuanceController::class, 'getAvailableInventory']);
        Route::get('delivery-receipts', [\App\Http\Controllers\PropertyIssuanceController::class, 'getDeliveryReceiptsForIssuance']);
    Route::post('acknowledge-iar', [\App\Http\Controllers\PropertyIssuanceController::class, 'acknowledgeIarFromInspection']);
        Route::get('requests-for-issuance', [\App\Http\Controllers\PropertyIssuanceController::class, 'getRequestsForIssuance']);
        Route::get('{id}', [\App\Http\Controllers\PropertyIssuanceController::class, 'show']);
        Route::get('{id}/document/{type}', [\App\Http\Controllers\PropertyIssuanceController::class, 'generateDocument']);
        Route::post('{id}/acknowledge', [\App\Http\Controllers\PropertyIssuanceController::class, 'acknowledge']);
    });

    // Requisition and Issue Slip (RIS)
    Route::prefix('requisition-issue-slips')->group(function () {
        Route::get('', [RequisitionIssueSlipController::class, 'index']);
        Route::post('', [RequisitionIssueSlipController::class, 'store']);
        Route::get('by-request/{requestId}', [RequisitionIssueSlipController::class, 'showByRequest']);
        Route::get('{id}', [RequisitionIssueSlipController::class, 'show']);
    });

    // QR management (assignedproperty + qrcode)
    Route::prefix('qr')->group(function () {
        Route::get('properties', [QrCodeController::class, 'index']);
        Route::post('properties/{propertyId}/generate', [QrCodeController::class, 'generate']);
        Route::get('scan/{propertyId}', [QrCodeController::class, 'scan']);
    });

    // Inspection Officer routes
    Route::prefix('inspection')->group(function () {
        Route::get('pending-items', [InspectionReportController::class, 'pendingItems']);
        Route::get('conditions', [InspectionReportController::class, 'conditions']);
        Route::get('reports', [InspectionReportController::class, 'index']);
        Route::get('reports/{id}', [InspectionReportController::class, 'show']);
        Route::post('reports', [InspectionReportController::class, 'store']);
        Route::patch('reports/{id}', [InspectionReportController::class, 'update']);
        Route::post('reports/{id}/complete', [InspectionReportController::class, 'complete']);
    });
});

// Public authentication routes - with CSRF protection
Route::middleware('web')->group(function () {
    // Moved to web.php
});
