<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\LoginAuditController;
use App\Http\Controllers\Auth\MFAController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\MasterData\CategoryController as MasterDataCategoryController;
use App\Http\Controllers\Admin\MasterData\ArticleController;
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
use App\Http\Controllers\Admin\MasterData\VendorController;
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
use App\Http\Controllers\RsmiReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AssetServiceRecordController;
use App\Http\Controllers\PropertyWasteController;
use App\Http\Controllers\RpcInventoryController;

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

// Mobile token login (for native/mobile clients)
Route::post('/auth/mobile-login', [AuthController::class, 'mobileLogin']);

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

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('', [NotificationController::class, 'index']);
        Route::delete('', [NotificationController::class, 'destroyAll']);
        Route::patch('read-all', [NotificationController::class, 'markAllAsRead']);
        Route::patch('{id}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('{id}', [NotificationController::class, 'destroy']);
        Route::post('push-token', [NotificationController::class, 'registerPushToken']);
        Route::delete('push-token', [NotificationController::class, 'removePushToken']);
    });

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
        // Admin-only routes
        Route::middleware(\App\Http\Middleware\EnsureAdmin::class)->group(function () {
            // User Management (Admin only)
            Route::get('users', [UserManagementController::class, 'index']);
            Route::get('users/{id}', [UserManagementController::class, 'show']);
            Route::post('users', [UserManagementController::class, 'store']);
            Route::patch('users/{id}', [UserManagementController::class, 'update']);
            Route::patch('users/{id}/active', [UserManagementController::class, 'toggleActive']);
            Route::patch('users/{id}/mfa', [UserManagementController::class, 'toggleMfa']);
            Route::post('users/{id}/resend-activation', [UserManagementController::class, 'resendActivation']);

            // Note: Procurement routes (PR/PO) have been removed per project requirements
            // Inventory routes (admin-only actions)
            Route::prefix('inventory')->group(function () {
                Route::get('pending-ingestion', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'pendingIngestion']);
                Route::post('ingest-from-iar', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'ingestFromIar']);
                Route::post('backfill-qr-codes', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'backfillQrCodes']);
                Route::post('details/{id}/generate-qr', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'generateQr']);
                Route::post('issuances', [\App\Http\Controllers\Admin\Inventory\IssuanceController::class, 'store']);
                    // Admin helper: flattened unserviceable items for IIRUP
                    Route::get('property-unserviceable', [\App\Http\Controllers\PropertyWasteController::class, 'adminUnserviceable']);
            });

                // Expose unserviceable listing to authenticated users; Department Heads will be scoped to their department
                Route::get('property-unserviceable', [\App\Http\Controllers\PropertyWasteController::class, 'adminUnserviceable']);

            // Database backups (Admin only): list, create, download, restore, delete, status, settings
            Route::prefix('backups')->group(function () {
                Route::get('', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'index']);
                Route::get('status', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'status']);
                Route::get('settings', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'settings']);
                Route::post('settings', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'updateSettings']);
                Route::post('trigger', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'triggerScheduledBackup']);
                Route::post('', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'store']);
                Route::get('{name}', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'download']);
                Route::post('{name}/restore', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'restore']);
                Route::delete('{name}', [\App\Http\Controllers\Admin\DatabaseBackupController::class, 'destroy']);
            });
        });

        // Master Data routes (shared with authenticated users)
        Route::prefix('master-data')->group(function () {
            // Categories
            Route::apiResource('categories', MasterDataCategoryController::class)->parameters(['categories' => 'id']);
            Route::post('categories/{id}/restore', [MasterDataCategoryController::class, 'restore']);

            // Articles
            Route::apiResource('articles', ArticleController::class)->parameters(['articles' => 'id']);
            Route::post('articles/{id}/restore', [ArticleController::class, 'restore']);
            Route::get('articles-active', [ArticleController::class, 'active']);

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

            // Vendors (Repair/Service)
            Route::apiResource('vendors', VendorController::class)->parameters(['vendors' => 'id']);
            Route::post('vendors/{id}/restore', [VendorController::class, 'restore']);

            // Units of Measure
            Route::apiResource('units-of-measure', UnitOfMeasureController::class)->parameters(['units-of-measure' => 'id']);
            Route::post('units-of-measure/{id}/restore', [UnitOfMeasureController::class, 'restore']);

            // User Roles
            Route::apiResource('user-roles', UserRoleController::class)->parameters(['user-roles' => 'id']);
            Route::post('user-roles/{id}/restore', [UserRoleController::class, 'restore']);
        });
    });

    // Procurement routes (available to authenticated users)
    Route::prefix('procurement')->group(function () {
        Route::get('delivery-receipts', [DeliveryReceiptController::class, 'index']);
        Route::post('delivery-receipts', [DeliveryReceiptController::class, 'store']);
        Route::get('delivery-receipts/{id}', [DeliveryReceiptController::class, 'show']);
        Route::put('delivery-receipts/{id}', [DeliveryReceiptController::class, 'update']);
        Route::delete('delivery-receipts/{id}', [DeliveryReceiptController::class, 'destroy']);
        Route::post('delivery-receipts/{id}/restore', [DeliveryReceiptController::class, 'restore']);
        Route::get('delivery-receipts-helpers/suppliers', [DeliveryReceiptController::class, 'getSuppliers']);
        Route::get('delivery-receipts-helpers/property-types', [DeliveryReceiptController::class, 'getPropertyTypes']);
        Route::get('delivery-receipts-helpers/requesters', [DeliveryReceiptController::class, 'getRequesters']);

        // IAR routes (Inspection & Acceptance Report)
        Route::get('inspection-acceptance-reports', [InspectionAcceptanceReportController::class, 'index']);
        Route::post('inspection-acceptance-reports', [InspectionAcceptanceReportController::class, 'store']);
        Route::get('inspection-acceptance-reports/{id}', [InspectionAcceptanceReportController::class, 'show']);
        Route::put('inspection-acceptance-reports/{id}', [InspectionAcceptanceReportController::class, 'update']);
        Route::delete('inspection-acceptance-reports/{id}', [InspectionAcceptanceReportController::class, 'destroy']);
        Route::post('inspection-acceptance-reports/{id}/signatures', [IarSignatureController::class, 'store']);
        // Stream signature file (fallback when storage URL isn't directly accessible)
        Route::get('inspection-acceptance-reports/{id}/signatures/{role}', [IarSignatureController::class, 'show']);
        Route::get('inspection-acceptance-reports-helpers/conditions', [InspectionAcceptanceReportController::class, 'getConditions']);
        Route::get('inspection-acceptance-reports-helpers/users', [InspectionAcceptanceReportController::class, 'getUsers']);
    });

    // Inventory routes accessible to authenticated users
    Route::prefix('inventory')->group(function () {
        Route::get('all-items', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'allItems']);
        Route::get('details/{id}/history', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'detailHistory']);
        Route::get('details/{id}/property-card', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'propertyCard']);
        Route::get('', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'index']);
        Route::get('{id}', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'show']);
        Route::put('details/{id}', [\App\Http\Controllers\Admin\Inventory\InventoryController::class, 'updateDetail']);
    });

    // Expose unserviceable listing to authenticated users (Department Heads will be scoped to their department)
    Route::get('property-unserviceable', [PropertyWasteController::class, 'adminUnserviceable']);

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
        Route::post('ensure-issuances', [\App\Http\Controllers\PropertyIssuanceController::class, 'ensureIssuancesForInspection']);
        Route::get('requests-for-issuance', [\App\Http\Controllers\PropertyIssuanceController::class, 'getRequestsForIssuance']);
        Route::get('{id}', [\App\Http\Controllers\PropertyIssuanceController::class, 'show']);
        Route::get('{id}/document/{type}', [\App\Http\Controllers\PropertyIssuanceController::class, 'generateDocument']);
        // Assignments for splitting quantities to receivers
        Route::get('{id}/assignments', [\App\Http\Controllers\PropertyIssuanceAssignmentController::class, 'index']);
        Route::post('{id}/assignments', [\App\Http\Controllers\PropertyIssuanceAssignmentController::class, 'store']);
        Route::post('{id}/acknowledge', [\App\Http\Controllers\PropertyIssuanceController::class, 'acknowledge']);
    });

    // Property Transfer (PTR)
    Route::prefix('property-transfers')->group(function () {
        Route::get('', [\App\Http\Controllers\PropertyTransferController::class, 'index']);
        Route::post('', [\App\Http\Controllers\PropertyTransferController::class, 'store']);
        Route::get('available-items', [\App\Http\Controllers\PropertyTransferController::class, 'availableItems']);
        Route::get('helpers/departments', [\App\Http\Controllers\PropertyTransferController::class, 'helperDepartments']);
        Route::get('helpers/end-users', [\App\Http\Controllers\PropertyTransferController::class, 'helperEndUsers']);
        Route::get('{id}', [\App\Http\Controllers\PropertyTransferController::class, 'show']);
        Route::post('{id}/releasing-review', [\App\Http\Controllers\PropertyTransferController::class, 'releasingReview']);
        Route::post('{id}/sign-releasing', [\App\Http\Controllers\PropertyTransferController::class, 'signReleasing']);
        Route::post('{id}/sign-receiving', [\App\Http\Controllers\PropertyTransferController::class, 'signReceiving']);
        Route::post('{id}/approve', [\App\Http\Controllers\PropertyTransferController::class, 'approve']);
        Route::post('{id}/reject', [\App\Http\Controllers\PropertyTransferController::class, 'reject']);
        Route::post('{id}/complete', [\App\Http\Controllers\PropertyTransferController::class, 'complete']);
        Route::get('{id}/receiver-assignments', [\App\Http\Controllers\PropertyTransferController::class, 'receiverAssignments']);
        Route::post('{id}/receiver-assignments', [\App\Http\Controllers\PropertyTransferController::class, 'saveReceiverAssignments']);
    });

    // Property Borrowing (PBR)
    Route::prefix('property-borrows')->group(function () {
        Route::get('', [\App\Http\Controllers\PropertyBorrowController::class, 'index']);
        Route::post('', [\App\Http\Controllers\PropertyBorrowController::class, 'store']);
        Route::get('available-items', [\App\Http\Controllers\PropertyBorrowController::class, 'availableItems']);
        Route::get('allowed-property-types', [\App\Http\Controllers\PropertyBorrowController::class, 'allowedPropertyTypes']);
        Route::get('helpers/departments', [\App\Http\Controllers\PropertyBorrowController::class, 'helperDepartments']);
        Route::get('helpers/end-users', [\App\Http\Controllers\PropertyBorrowController::class, 'helperEndUsers']);
        Route::get('helpers/conditions', [\App\Http\Controllers\PropertyBorrowController::class, 'helperConditions']);
        Route::get('{id}', [\App\Http\Controllers\PropertyBorrowController::class, 'show']);
        Route::post('{id}/releasing-review', [\App\Http\Controllers\PropertyBorrowController::class, 'releasingReview']);
        Route::post('{id}/approve', [\App\Http\Controllers\PropertyBorrowController::class, 'approve']);
        Route::post('{id}/deliver', [\App\Http\Controllers\PropertyBorrowController::class, 'deliver']);
        Route::post('{id}/reject', [\App\Http\Controllers\PropertyBorrowController::class, 'reject']);
        Route::post('{id}/return', [\App\Http\Controllers\PropertyBorrowController::class, 'markReturned']);
        Route::post('{id}/complete', [\App\Http\Controllers\PropertyBorrowController::class, 'complete']);
    });

    // Service & Repair
    Route::prefix('asset-services')->group(function () {
        Route::get('', [AssetServiceRecordController::class, 'index']);
        Route::post('', [AssetServiceRecordController::class, 'store']);
        Route::get('statuses', [AssetServiceRecordController::class, 'statuses']);
        Route::get('helpers/vendors', [AssetServiceRecordController::class, 'helperVendors']);
        Route::get('helpers/departments', [AssetServiceRecordController::class, 'helperDepartments']);
        Route::get('helpers/department-heads', [AssetServiceRecordController::class, 'helperDepartmentHeads']);
        Route::get('helpers/end-users', [AssetServiceRecordController::class, 'helperEndUsers']);
        Route::get('helpers/inventory', [AssetServiceRecordController::class, 'helperInventory']);
        Route::get('{id}', [AssetServiceRecordController::class, 'show']);
        Route::post('{id}', [AssetServiceRecordController::class, 'update']);
    });

    // Property Waste (Disposal Workflow)
    Route::prefix('property-wastes')->group(function () {
        Route::get('', [PropertyWasteController::class, 'index']);
        Route::get('statuses', [PropertyWasteController::class, 'statuses']);
        Route::get('helpers/disposal-methods', [PropertyWasteController::class, 'helperDisposalMethods']);
        Route::get('{id}', [PropertyWasteController::class, 'show']);
        Route::post('{id}', [PropertyWasteController::class, 'update']);
    });

    // Requisition and Issue Slip (RIS)
    Route::prefix('requisition-issue-slips')->group(function () {
        Route::get('', [RequisitionIssueSlipController::class, 'index']);
        Route::post('', [RequisitionIssueSlipController::class, 'store']);
        Route::get('by-request/{requestId}', [RequisitionIssueSlipController::class, 'showByRequest']);
        Route::get('{id}', [RequisitionIssueSlipController::class, 'show']);
    });

    // Report of Supplies and Materials Issued (RSMI)
    Route::prefix('rsmi-reports')->group(function () {
        Route::get('', [RsmiReportController::class, 'index']);
        Route::get('{id}', [RsmiReportController::class, 'show']);
    });

    // QR management (assignedproperty + qrcode)
    Route::prefix('qr')->group(function () {
        Route::get('properties', [QrCodeController::class, 'index']);
        Route::post('properties/{propertyId}/generate', [QrCodeController::class, 'generate']);
        Route::put('properties/{propertyId}', [QrCodeController::class, 'update']);
        Route::get('scan/{propertyId}', [QrCodeController::class, 'scan']);
    });

    // RPCPPE / RPCSEP inventory cycles and scanning
    Route::prefix('rpc-inventory')->group(function () {
        Route::get('cycles', [RpcInventoryController::class, 'index']);
        Route::post('cycles/current', [RpcInventoryController::class, 'createCurrentCycle']);
        Route::get('cycles/{id}', [RpcInventoryController::class, 'show']);
        Route::get('cycles/{id}/scans', [RpcInventoryController::class, 'scans']);
        Route::post('cycles/{id}/scan-preview', [RpcInventoryController::class, 'previewScan']);
        Route::post('cycles/{id}/scans', [RpcInventoryController::class, 'storeScan']);
        Route::post('cycles/{id}/scans/bulk', [RpcInventoryController::class, 'bulkStoreScan']);
        Route::get('cycles/{id}/report', [RpcInventoryController::class, 'report']);
        Route::get('cycles/{id}/audit-logs', [RpcInventoryController::class, 'auditLogs']);

        // Close and Reopen endpoints (Admin only)
        Route::get('cycles/{id}/closure-summary', [RpcInventoryController::class, 'closureSummary']);
        Route::post('cycles/{id}/close', [RpcInventoryController::class, 'closeCycle']);
        Route::post('cycles/{id}/reopen-request', [RpcInventoryController::class, 'reopenRequest']);
        Route::post('cycles/{id}/reopen', [RpcInventoryController::class, 'reopenCycle']);

        Route::patch('scans/{scanId}', [RpcInventoryController::class, 'updateScan']);
        Route::delete('scans/{scanId}', [RpcInventoryController::class, 'destroyScan']);

        Route::get('helpers/conditions', [RpcInventoryController::class, 'conditions']);
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

    // Security / Activity Logs routes
    Route::prefix('security')->group(function () {
        Route::prefix('activity-logs')->group(function () {
            Route::get('', [AuditLogsController::class, 'getActivityLogs']);
            Route::get('entity-types', [AuditLogsController::class, 'getEntityTypes']);
        });

        Route::prefix('login-audit')->group(function () {
            Route::get('', [LoginAuditController::class, 'getLoginAudits']);
            Route::get('statuses', [LoginAuditController::class, 'getLoginStatuses']);
        });
    });
});

// Public authentication routes - with CSRF protection
Route::middleware('web')->group(function () {
    // Moved to web.php
});
