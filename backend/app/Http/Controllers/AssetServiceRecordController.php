<?php

namespace App\Http\Controllers;

use App\Models\AssetAuditLog;
use App\Models\AssetServiceRecord;
use App\Models\AssetServiceRecordDetail;
use App\Models\Auth\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetServiceRecordController extends Controller
{
    private const ROLE_ADMIN = 'Admin';
    private const ROLE_GSO_STAFF = 'GSO Staff';
    private const ROLE_DEPARTMENT_HEAD = 'Department Head';

    private const STATUS_FLOW = [
        'pending',
        'under_review',
        'work_approved',
        'repair_in_progress',
        'verification',
        'closed',
    ];

    private const HANDLING_DECISIONS = [
        'warranty_claim',
        'vendor_repair',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canView($roleName)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = AssetServiceRecord::query()
            ->with([
                'details.inventoryDetail.propertyType:PropertyTypeID,PropertyName',
                'details.inventoryDetail.condition:ConditionID,ConditionName',
                'details.inventoryDetail.inventory:InventoryID,InventoryNumber,DateReceived',
                'details.vendor:VendorID,VendorName,ContactPerson,ContactNumber,Email',
                'details.supplier:SupplierID,SupplierName,ContactPerson,ContactNumber,Email',
                'reportingDepartment:DepartmentID,DepartmentName',
                'submittedByEndUser:EndUserID,FullName,DepartmentID',
                'createdBy:UserID,FullName',
                'updatedBy:UserID,FullName',
            ]);

        if ($roleName === self::ROLE_DEPARTMENT_HEAD) {
            $departmentId = (int) ($user?->DepartmentID ?? 0);
            $query->where('ReportingDepartmentID', $departmentId);
        }

        if ($request->filled('status')) {
            $status = (string) $request->query('status');
            $query->where(function ($q) use ($status) {
                $q->where('Status', $status)
                    ->orWhereHas('details', function ($dq) use ($status) {
                        $dq->where('Status', $status);
                    });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('ReportingDepartmentID', (int) $request->query('department_id'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ServiceNumber', 'like', "%{$search}%")
                    ->orWhereHas('details', function ($dq) use ($search) {
                        $dq->where('PropertyNumber', 'like', "%{$search}%")
                            ->orWhere('ItemDescription', 'like', "%{$search}%")
                            ->orWhere('ProblemDescription', 'like', "%{$search}%")
                            ->orWhereHas('vendor', function ($vq) use ($search) {
                                $vq->where('VendorName', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('reportingDepartment', function ($dq) use ($search) {
                        $dq->where('DepartmentName', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = max(10, min(100, (int) $request->query('per_page', 15)));
        $rows = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($rows);
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canView($roleName)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $record = AssetServiceRecord::with([
            'details.inventoryDetail.propertyType:PropertyTypeID,PropertyName',
            'details.inventoryDetail.condition:ConditionID,ConditionName',
            'details.inventoryDetail.inventory:InventoryID,InventoryNumber,DateReceived',
            'details.inventoryDetail.drDetail:DRDetailID,DRID,ItemDescription',
            'details.vendor:VendorID,VendorName,Address,ContactPerson,ContactNumber,Email',
            'details.supplier:SupplierID,SupplierName,ContactPerson,ContactNumber,Email',
            'reportingDepartment:DepartmentID,DepartmentName',
            'submittedByEndUser:EndUserID,FullName,DepartmentID',
            'createdBy:UserID,FullName',
            'updatedBy:UserID,FullName',
        ])->findOrFail($id);

        if ($roleName === self::ROLE_DEPARTMENT_HEAD) {
            $departmentId = (int) ($user?->DepartmentID ?? 0);
            if ((int) $record->ReportingDepartmentID !== $departmentId) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return response()->json($this->withAttachmentUrls($record));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canManage($roleName)) {
            return response()->json(['message' => 'Unauthorized to create service records'], 403);
        }

        $today = now()->toDateString();
        $oneYearAgo = now()->subYear()->toDateString();

        $validated = $request->validate([
            'date_reported' => "required|date|after_or_equal:{$oneYearAgo}|before_or_equal:{$today}",
            'report_source' => 'nullable|in:cce_user,inventory_check,audit,other',
            'submitted_by_name' => 'nullable|string|max:150',
            'submitted_by_end_user_id' => 'nullable|integer|exists:enduser,EndUserID',
            'reporting_department_id' => 'required|integer|exists:department,DepartmentID',
            'reporting_department_head_user_id' => 'nullable|integer|exists:user,UserID',
            'record_notes' => 'nullable|string|max:4000',
            'details' => 'required|array|min:1',
            'details.*.inventory_detail_id' => 'required|integer|exists:propertyinventorydetail,InventoryDetailID',
            'details.*.problem_description' => 'required|string|max:4000',
            'details.*.handling_decision' => 'nullable|in:warranty_claim,vendor_repair',
            'details.*.budget_status' => 'nullable|in:pending_check,warranty_covered,budget_available',
            'details.*.status' => 'nullable|in:pending,under_review,work_approved,repair_in_progress,verification,closed,unrepairable',
            'details.*.assigned_vendor_id' => 'nullable|integer|exists:vendor,VendorID',
            'details.*.assigned_supplier_id' => 'nullable|integer|exists:supplier,SupplierID',
            'details.*.actual_cost' => 'nullable|numeric|min:0',
            'details.*.invoice_attachment' => 'nullable|string|max:255',
            'details.*.supporting_attachments' => 'nullable|array',
            'details.*.supporting_attachments.*' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $defaultStatus = 'pending';
            $reportingHead = !empty($validated['reporting_department_head_user_id'])
                ? User::with('userRole')->find($validated['reporting_department_head_user_id'])
                : null;

            if ($reportingHead && ($reportingHead->userRole?->RoleName !== self::ROLE_DEPARTMENT_HEAD || (int) $reportingHead->DepartmentID !== (int) $validated['reporting_department_id'])) {
                return response()->json([
                    'message' => 'Selected reporting department head is invalid for the department.',
                ], 422);
            }

            $record = AssetServiceRecord::create([
                'ServiceNumber' => AssetServiceRecord::generateServiceNumber(),
                'DateReported' => $validated['date_reported'],
                'ReportSource' => $validated['report_source'] ?? 'other',
                'SubmittedByName' => $reportingHead?->FullName,
                'SubmittedByEndUserID' => null,
                'ReportingDepartmentID' => $validated['reporting_department_id'] ?? null,
                'Status' => $defaultStatus,
                'RecordNotes' => $validated['record_notes'] ?? null,
                'CreatedByUserID' => (int) $user->UserID,
                'UpdatedByUserID' => (int) $user->UserID,
            ]);

            $detailStatuses = [];
            foreach ($validated['details'] as $detailInput) {
                $inventoryDetail = \App\Models\Inventory\PropertyInventoryDetail::findOrFail((int) $detailInput['inventory_detail_id']);
                $this->assertInventoryBelongsToDepartment($inventoryDetail, (int) $validated['reporting_department_id']);

                $normalizedDetail = $this->normalizeAndValidateDetailInput($detailInput, $defaultStatus, true);

                $detail = AssetServiceRecordDetail::create([
                    'ServiceRecordID' => (int) $record->ServiceRecordID,
                    'InventoryDetailID' => (int) $detailInput['inventory_detail_id'],
                    'PropertyNumber' => $inventoryDetail->PropertyNumber,
                    'ItemDescription' => $inventoryDetail->Description,
                    'ProblemDescription' => $normalizedDetail['problem_description'],
                    'HandlingDecision' => $normalizedDetail['handling_decision'],
                    'BudgetStatus' => $normalizedDetail['budget_status'],
                    'Status' => $normalizedDetail['status'],
                    'AssignedVendorID' => $normalizedDetail['assigned_vendor_id'],
                    'AssignedSupplierID' => $normalizedDetail['assigned_supplier_id'],
                    'EstimatedCost' => $normalizedDetail['estimated_cost'],
                    'ActualCost' => $normalizedDetail['actual_cost'],
                    'WarrantyClaimNumber' => $normalizedDetail['warranty_claim_number'],
                    'RepairOrderNumber' => $normalizedDetail['repair_order_number'],
                    'InvoiceAttachment' => $normalizedDetail['invoice_attachment'],
                    'SupportingAttachments' => $normalizedDetail['supporting_attachments'],
                    'CreatedByUserID' => (int) $user->UserID,
                    'UpdatedByUserID' => (int) $user->UserID,
                ]);

                $detailStatuses[] = (string) $detail->Status;

                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $detail->InventoryDetailID,
                    'service_record_created',
                    (int) $user->UserID,
                    [
                        'service_record_id' => (int) $record->ServiceRecordID,
                        'service_record_detail_id' => (int) $detail->ServiceRecordDetailID,
                        'service_number' => $record->ServiceNumber,
                        'status' => $detail->Status,
                    ]
                );
            }

            $record->Status = $this->computeOverallStatus($detailStatuses);
            $record->save();

            AssetAuditLog::createLog(
                'asset_service_record',
                (int) $record->ServiceRecordID,
                'created',
                (int) $user->UserID,
                [
                    'service_number' => $record->ServiceNumber,
                    'status' => $record->Status,
                    'details_count' => count($detailStatuses),
                    'reporting_department_id' => $record->ReportingDepartmentID,
                ]
            );

            return response()->json([
                'message' => 'Service record created successfully',
                'record' => $this->withAttachmentUrls($record->fresh()->load([
                    'details.inventoryDetail.propertyType:PropertyTypeID,PropertyName',
                    'details.inventoryDetail.condition:ConditionID,ConditionName',
                    'details.inventoryDetail.inventory:InventoryID,InventoryNumber,DateReceived',
                    'details.vendor:VendorID,VendorName,ContactPerson,ContactNumber,Email',
                    'details.supplier:SupplierID,SupplierName,ContactPerson,ContactNumber,Email',
                    'reportingDepartment:DepartmentID,DepartmentName',
                    'submittedByEndUser:EndUserID,FullName,DepartmentID',
                    'createdBy:UserID,FullName',
                    'updatedBy:UserID,FullName',
                ])),
            ], 201);
        });
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canManage($roleName)) {
            return response()->json(['message' => 'Unauthorized to update service records'], 403);
        }

        $record = AssetServiceRecord::findOrFail($id);
        $oldStatus = (string) $record->Status;

        $today = now()->toDateString();
        $oneYearAgo = now()->subYear()->toDateString();

        $validated = $request->validate([
            'date_reported' => "sometimes|required|date|after_or_equal:{$oneYearAgo}|before_or_equal:{$today}",
            'report_source' => 'sometimes|nullable|in:cce_user,inventory_check,audit,other',
            'submitted_by_name' => 'nullable|string|max:150',
            'submitted_by_end_user_id' => 'nullable|integer|exists:enduser,EndUserID',
            'reporting_department_id' => 'nullable|integer|exists:department,DepartmentID',
            'reporting_department_head_user_id' => 'nullable|integer|exists:user,UserID',
            'status' => 'nullable|in:pending,under_review,work_approved,repair_in_progress,verification,closed,unrepairable',
            'record_notes' => 'nullable|string|max:4000',
            'details' => 'nullable|array|min:1',
            'details.*.service_record_detail_id' => 'nullable|integer|exists:asset_service_records_details,ServiceRecordDetailID',
            'details.*.inventory_detail_id' => 'required_with:details|integer|exists:propertyinventorydetail,InventoryDetailID',
            'details.*.problem_description' => 'required_with:details|string|max:4000',
            'details.*.handling_decision' => 'nullable|in:warranty_claim,vendor_repair',
            'details.*.budget_status' => 'nullable|in:pending_check,warranty_covered,budget_available',
            'details.*.status' => 'nullable|in:pending,under_review,work_approved,repair_in_progress,verification,closed,unrepairable',
            'details.*.assigned_vendor_id' => 'nullable|integer|exists:vendor,VendorID',
            'details.*.assigned_supplier_id' => 'nullable|integer|exists:supplier,SupplierID',
            'details.*.actual_cost' => 'nullable|numeric|min:0',
            'details.*.invoice_attachment' => 'nullable|string|max:255',
            'details.*.supporting_attachments' => 'nullable|array',
            'details.*.supporting_attachments.*' => 'nullable|string|max:255',
        ]);

        if (array_key_exists('status', $validated) && !$this->isStatusTransitionAllowed($oldStatus, (string) $validated['status'])) {
            return response()->json([
                'message' => 'Invalid status transition',
                'current_status' => $oldStatus,
                'next_status' => $validated['status'],
            ], 422);
        }

        if (array_key_exists('reporting_department_head_user_id', $validated) && !empty($validated['reporting_department_head_user_id'])) {
            $head = User::with('userRole')->find($validated['reporting_department_head_user_id']);
            $departmentId = (int) ($validated['reporting_department_id'] ?? $record->ReportingDepartmentID);

            if (!$head || $head->userRole?->RoleName !== self::ROLE_DEPARTMENT_HEAD || (int) $head->DepartmentID !== $departmentId) {
                return response()->json([
                    'message' => 'Selected reporting department head is invalid for the department.',
                ], 422);
            }

            $validated['submitted_by_name'] = $head->FullName;
            $validated['submitted_by_end_user_id'] = null;
        }

        return DB::transaction(function () use ($validated, $record, $user, $oldStatus) {
            $detailStatuses = [];

            if (!empty($validated['details'])) {
                foreach ($validated['details'] as $detailInput) {
                    $detail = null;
                    if (!empty($detailInput['service_record_detail_id'])) {
                        $detail = AssetServiceRecordDetail::where('ServiceRecordID', (int) $record->ServiceRecordID)
                            ->findOrFail((int) $detailInput['service_record_detail_id']);
                    }

                    $inventoryDetail = \App\Models\Inventory\PropertyInventoryDetail::findOrFail((int) $detailInput['inventory_detail_id']);
                    $departmentId = (int) ($validated['reporting_department_id'] ?? $record->ReportingDepartmentID);
                    $this->assertInventoryBelongsToDepartment($inventoryDetail, $departmentId);

                    if (!$detail) {
                        $detail = new AssetServiceRecordDetail();
                        $detail->ServiceRecordID = (int) $record->ServiceRecordID;
                        $detail->CreatedByUserID = (int) $user->UserID;
                    }

                    $oldDetailStatus = (string) ($detail->Status ?? 'pending');
                    $newDetailStatus = (string) ($detailInput['status'] ?? $detail->Status ?? $record->Status);

                    if (!$this->isStatusTransitionAllowed($oldDetailStatus, $newDetailStatus)) {
                        return response()->json([
                            'message' => 'Invalid detail status transition',
                            'detail_id' => $detail->ServiceRecordDetailID ?? null,
                            'current_status' => $oldDetailStatus,
                            'next_status' => $newDetailStatus,
                        ], 422);
                    }

                    $normalizedDetail = $this->normalizeAndValidateDetailInput(
                        array_merge($detailInput, ['status' => $newDetailStatus]),
                        (string) $record->Status,
                        false
                    );

                    $detail->InventoryDetailID = (int) $detailInput['inventory_detail_id'];
                    $detail->PropertyNumber = $inventoryDetail->PropertyNumber;
                    $detail->ItemDescription = $inventoryDetail->Description;
                    $detail->ProblemDescription = $normalizedDetail['problem_description'];
                    $detail->HandlingDecision = $normalizedDetail['handling_decision'];
                    $detail->BudgetStatus = $normalizedDetail['budget_status'];
                    $detail->Status = $normalizedDetail['status'];
                    $detail->AssignedVendorID = $normalizedDetail['assigned_vendor_id'];
                    $detail->AssignedSupplierID = $normalizedDetail['assigned_supplier_id'];
                    $detail->EstimatedCost = $normalizedDetail['estimated_cost'];
                    $detail->ActualCost = $normalizedDetail['actual_cost'];
                    $detail->WarrantyClaimNumber = $normalizedDetail['warranty_claim_number'];
                    $detail->RepairOrderNumber = $normalizedDetail['repair_order_number'];
                    $detail->InvoiceAttachment = $normalizedDetail['invoice_attachment'];
                    $detail->SupportingAttachments = $normalizedDetail['supporting_attachments'];
                    $detail->UpdatedByUserID = (int) $user->UserID;
                    $detail->save();

                    $detailStatuses[] = (string) $detail->Status;

                    AssetAuditLog::createLog(
                        'asset_service_record_detail',
                        (int) $detail->ServiceRecordDetailID,
                        'updated',
                        (int) $user->UserID,
                        [
                            'service_record_id' => (int) $record->ServiceRecordID,
                            'old_status' => $oldDetailStatus,
                            'new_status' => $detail->Status,
                            'inventory_detail_id' => (int) $detail->InventoryDetailID,
                        ]
                    );
                }
            }

            if (empty($detailStatuses)) {
                $detailStatuses = AssetServiceRecordDetail::where('ServiceRecordID', (int) $record->ServiceRecordID)
                    ->pluck('Status')
                    ->map(static fn ($value) => (string) $value)
                    ->all();
            }

            $resolvedStatus = $this->computeOverallStatus($detailStatuses);
            if (array_key_exists('status', $validated)) {
                $resolvedStatus = (string) $validated['status'];
            }

            $record->fill([
                'DateReported' => $validated['date_reported'] ?? $record->DateReported,
                'ReportSource' => $validated['report_source'] ?? $record->ReportSource,
                'SubmittedByName' => array_key_exists('submitted_by_name', $validated) ? $validated['submitted_by_name'] : $record->SubmittedByName,
                'SubmittedByEndUserID' => array_key_exists('submitted_by_end_user_id', $validated) ? $validated['submitted_by_end_user_id'] : $record->SubmittedByEndUserID,
                'ReportingDepartmentID' => array_key_exists('reporting_department_id', $validated) ? $validated['reporting_department_id'] : $record->ReportingDepartmentID,
                'Status' => $resolvedStatus,
                'RecordNotes' => array_key_exists('record_notes', $validated) ? $validated['record_notes'] : $record->RecordNotes,
                'UpdatedByUserID' => (int) $user->UserID,
            ]);

            $record->save();

            AssetAuditLog::createLog(
                'asset_service_record',
                (int) $record->ServiceRecordID,
                'updated',
                (int) $user->UserID,
                [
                    'service_number' => $record->ServiceNumber,
                    'old_status' => $oldStatus,
                    'new_status' => $record->Status,
                    'details_count' => AssetServiceRecordDetail::where('ServiceRecordID', (int) $record->ServiceRecordID)->count(),
                ]
            );

            if ($oldStatus !== $record->Status) {
                AssetAuditLog::createLog(
                    'asset_service_record',
                    (int) $record->ServiceRecordID,
                    'status_changed',
                    (int) $user->UserID,
                    [
                        'service_number' => $record->ServiceNumber,
                        'old_status' => $oldStatus,
                        'new_status' => $record->Status,
                    ]
                );
            }

            return response()->json([
                'message' => 'Service record updated successfully',
                'record' => $this->withAttachmentUrls($record->fresh()->load([
                    'details.inventoryDetail.propertyType:PropertyTypeID,PropertyName',
                    'details.inventoryDetail.condition:ConditionID,ConditionName',
                    'details.inventoryDetail.inventory:InventoryID,InventoryNumber,DateReceived',
                    'details.vendor:VendorID,VendorName,ContactPerson,ContactNumber,Email',
                    'details.supplier:SupplierID,SupplierName,ContactPerson,ContactNumber,Email',
                    'reportingDepartment:DepartmentID,DepartmentName',
                    'submittedByEndUser:EndUserID,FullName,DepartmentID',
                    'createdBy:UserID,FullName',
                    'updatedBy:UserID,FullName',
                ])),
            ]);
        });
    }

    public function statuses(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => [
                'pending',
                'under_review',
                'work_approved',
                'repair_in_progress',
                'verification',
                'closed',
                'unrepairable',
            ],
        ]);
    }

    public function helperVendors(): \Illuminate\Http\JsonResponse
    {
        $vendors = \App\Models\MasterData\Vendor::query()
            ->where('IsActive', true)
            ->orderBy('VendorName')
            ->get(['VendorID', 'VendorName', 'ContactPerson', 'ContactNumber', 'Email'])
            ->map(static function ($vendor) {
                return [
                    'ProviderType' => 'vendor',
                    'ProviderID' => (int) $vendor->VendorID,
                    'ProviderName' => $vendor->VendorName,
                    'ContactPerson' => $vendor->ContactPerson,
                    'ContactNumber' => $vendor->ContactNumber,
                    'Email' => $vendor->Email,
                ];
            });

        $suppliers = \App\Models\MasterData\Supplier::query()
            ->where('IsActive', true)
            ->orderBy('SupplierName')
            ->get(['SupplierID', 'SupplierName', 'ContactPerson', 'ContactNumber', 'Email'])
            ->map(static function ($supplier) {
                return [
                    'ProviderType' => 'supplier',
                    'ProviderID' => (int) $supplier->SupplierID,
                    'ProviderName' => $supplier->SupplierName,
                    'ContactPerson' => $supplier->ContactPerson,
                    'ContactNumber' => $supplier->ContactNumber,
                    'Email' => $supplier->Email,
                ];
            });

        return response()->json(['data' => $vendors->concat($suppliers)->values()]);
    }

    public function helperDepartments(): \Illuminate\Http\JsonResponse
    {
        $departmentIds = User::query()
            ->where('IsActive', true)
            ->whereHas('userRole', function ($q) {
                $q->where('RoleName', self::ROLE_DEPARTMENT_HEAD);
            })
            ->pluck('DepartmentID')
            ->filter()
            ->unique()
            ->values();

        $departments = \App\Models\MasterData\Department::query()
            ->where('IsActive', true)
            ->when($departmentIds->isNotEmpty(), function ($q) use ($departmentIds) {
                $q->whereIn('DepartmentID', $departmentIds->all());
            })
            ->orderBy('DepartmentName')
            ->get(['DepartmentID', 'DepartmentName', 'DepartmentCode']);

        return response()->json(['data' => $departments]);
    }

    public function helperDepartmentHeads(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = User::query()
            ->where('IsActive', true)
            ->whereHas('userRole', function ($q) {
                $q->where('RoleName', self::ROLE_DEPARTMENT_HEAD);
            })
            ->with('department:DepartmentID,DepartmentName')
            ->orderBy('FullName');

        if ($request->filled('department_id')) {
            $query->where('DepartmentID', (int) $request->query('department_id'));
        }

        $rows = $query->get(['UserID', 'DepartmentID', 'FullName'])
            ->map(static function (User $user) {
                return [
                    'EndUserID' => (int) $user->UserID,
                    'DepartmentID' => (int) $user->DepartmentID,
                    'FullName' => $user->FullName,
                    'DepartmentName' => $user->department?->DepartmentName,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function helperEndUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = User::query()
            ->where('IsActive', true)
            ->whereHas('userRole', function ($q) {
                $q->where('RoleName', self::ROLE_DEPARTMENT_HEAD);
            })
            ->orderBy('FullName');

        if ($request->filled('department_id')) {
            $query->where('DepartmentID', (int) $request->query('department_id'));
        }

        $rows = $query->get(['UserID', 'DepartmentID', 'FullName'])
            ->map(static function (User $user) {
                return [
                    'EndUserID' => (int) $user->UserID,
                    'DepartmentID' => (int) $user->DepartmentID,
                    'FullName' => $user->FullName,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function helperInventory(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        $query = \App\Models\Inventory\PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyName',
                'condition:ConditionID,ConditionName',
                'inventory:InventoryID,InventoryNumber,DateReceived',
                'drDetail:DRDetailID,DRID,ArticleID,ItemDescription',
                'drDetail.article:ArticleID,ArticleName',
                'drDetail.deliveryReceipt:DRID,SupplierID',
                'drDetail.deliveryReceipt.supplier:SupplierID,SupplierName',
            ])
            ->orderByDesc('InventoryDetailID');

        $departmentId = null;
        if ($roleName === self::ROLE_DEPARTMENT_HEAD) {
            $departmentId = (int) ($user?->DepartmentID ?? 0);
        } elseif ($request->filled('department_id')) {
            $departmentId = (int) $request->query('department_id');
        }

        if (!empty($departmentId)) {
            $query->whereHas('assignedProperties', function ($q) use ($departmentId) {
                $q->where('DepartmentID', $departmentId);
            });
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('PropertyNumber', 'like', "%{$search}%")
                    ->orWhere('Description', 'like', "%{$search}%")
                    ->orWhere('Brand', 'like', "%{$search}%")
                    ->orWhere('ModelNumber', 'like', "%{$search}%")
                    ->orWhere('SerialNumber', 'like', "%{$search}%");
            });
        }

        if ($request->filled('property_status_id')) {
            $query->where('PropertyStatusID', (int) $request->query('property_status_id'));
        }

        $rows = $query->limit(200)->get()->map(static function ($item) {
            $supplier = $item->drDetail?->deliveryReceipt?->supplier;
            $articleName = $item->drDetail?->article?->ArticleName;

            return [
                'InventoryDetailID' => (int) $item->InventoryDetailID,
                'PropertyNumber' => $item->PropertyNumber,
                'dr_article_name' => $item->drDetail?->article?->ArticleName
                    ?? $item->drDetail?->ItemDescription
                    ?? $articleName
                    ?? null,
                'Description' => $item->Description,
                'Brand' => $item->Brand,
                'ModelNumber' => $item->ModelNumber,
                'SerialNumber' => $item->SerialNumber,
                'UnitCost' => $item->UnitCost,
                'SupplierID' => $supplier?->SupplierID ? (int) $supplier->SupplierID : null,
                'SupplierName' => $supplier?->SupplierName,
                'propertyType' => $item->propertyType,
                'condition' => $item->condition,
                'inventory' => $item->inventory,
            ];
        })->values();

        return response()->json(['data' => $rows]);
    }

    private function canView(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF, self::ROLE_DEPARTMENT_HEAD], true);
    }

    private function assertInventoryBelongsToDepartment(\App\Models\Inventory\PropertyInventoryDetail $inventoryDetail, int $departmentId): void
    {
        if ($departmentId <= 0) {
            return;
        }

        $belongsToDepartment = $inventoryDetail->assignedProperties()
            ->where('DepartmentID', $departmentId)
            ->exists();

        if (!$belongsToDepartment) {
            throw new HttpResponseException(response()->json([
                'message' => 'Inventory item is not assigned to the selected reporting department.',
                'inventory_detail_id' => (int) $inventoryDetail->InventoryDetailID,
            ], 422));
        }
    }

    private function normalizeAndValidateDetailInput(array $detailInput, string $defaultStatus, bool $forcePending): array
    {
        $handlingDecision = isset($detailInput['handling_decision']) ? (string) $detailInput['handling_decision'] : null;
        if (!empty($handlingDecision) && !in_array($handlingDecision, self::HANDLING_DECISIONS, true)) {
            throw new HttpResponseException(response()->json(['message' => 'Invalid handling decision provided.'], 422));
        }

        if (empty($handlingDecision)) {
            throw new HttpResponseException(response()->json(['message' => 'Handling decision is required.'], 422));
        }

        $status = $forcePending
            ? 'pending'
            : (string) ($detailInput['status'] ?? $defaultStatus ?: 'pending');

        $warrantyCovered = $handlingDecision === 'warranty_claim';
        $actualCost = array_key_exists('actual_cost', $detailInput) ? $detailInput['actual_cost'] : null;
        $assignedVendorId = !empty($detailInput['assigned_vendor_id']) ? (int) $detailInput['assigned_vendor_id'] : null;
        $assignedSupplierId = !empty($detailInput['assigned_supplier_id']) ? (int) $detailInput['assigned_supplier_id'] : null;

        if ($assignedVendorId && $assignedSupplierId) {
            throw new HttpResponseException(response()->json(['message' => 'Assign only one service provider: vendor or supplier.'], 422));
        }

        if (!$assignedVendorId && !$assignedSupplierId) {
            throw new HttpResponseException(response()->json(['message' => 'Selecting a vendor or supplier is required.'], 422));
        }

        $isCompleted = in_array($status, ['closed', 'unrepairable'], true);
        if (!$isCompleted && ($actualCost !== null && $actualCost !== '')) {
            throw new HttpResponseException(response()->json(['message' => 'Actual cost can only be set when detail status is completed.'], 422));
        }

        if ($isCompleted && !$warrantyCovered && ($actualCost === null || $actualCost === '')) {
            throw new HttpResponseException(response()->json(['message' => 'Actual cost is required when detail status is completed.'], 422));
        }

        if ($handlingDecision === 'vendor_repair') {
            $budgetStatus = 'budget_available';
        } else {
            $budgetStatus = 'warranty_covered';
        }

        return [
            'problem_description' => (string) ($detailInput['problem_description'] ?? ''),
            'handling_decision' => $handlingDecision,
            'budget_status' => $budgetStatus,
            'status' => $status,
            'assigned_vendor_id' => $assignedVendorId,
            'assigned_supplier_id' => $assignedSupplierId,
            'estimated_cost' => null,
            'actual_cost' => ($actualCost === null || $actualCost === '') ? null : (float) $actualCost,
            'warranty_claim_number' => null,
            'repair_order_number' => null,
            'invoice_attachment' => $detailInput['invoice_attachment'] ?? null,
            'supporting_attachments' => array_values(array_filter($detailInput['supporting_attachments'] ?? [])),
        ];
    }

    private function canManage(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true);
    }

    private function isStatusTransitionAllowed(string $currentStatus, string $nextStatus): bool
    {
        if ($currentStatus === $nextStatus) {
            return true;
        }

        if ($nextStatus === 'unrepairable') {
            return in_array($currentStatus, ['pending', 'under_review', 'work_approved', 'repair_in_progress', 'verification'], true);
        }

        if ($currentStatus === 'unrepairable' || $currentStatus === 'closed') {
            return false;
        }

        $currentIndex = array_search($currentStatus, self::STATUS_FLOW, true);
        $nextIndex = array_search($nextStatus, self::STATUS_FLOW, true);

        if ($currentIndex === false || $nextIndex === false) {
            return false;
        }

        return $nextIndex >= $currentIndex && $nextIndex - $currentIndex <= 1;
    }

    private function withAttachmentUrls(AssetServiceRecord $record): AssetServiceRecord
    {
        $record->setRelation('details', $record->details->map(function (AssetServiceRecordDetail $detail) {
            $attachments = (array) ($detail->SupportingAttachments ?? []);
            $detail->setAttribute('invoice_url', $detail->InvoiceAttachment ? Storage::disk('public')->url($detail->InvoiceAttachment) : null);
            $detail->setAttribute('supporting_attachment_urls', array_map(static fn (string $path) => Storage::disk('public')->url($path), $attachments));
            $detail->setAttribute('assigned_provider_type', $detail->AssignedVendorID ? 'vendor' : ($detail->AssignedSupplierID ? 'supplier' : null));
            $detail->setAttribute('assigned_provider_id', $detail->AssignedVendorID ?: $detail->AssignedSupplierID);
            $detail->setAttribute('assigned_provider_name', $detail->vendor?->VendorName ?: $detail->supplier?->SupplierName);

            return $detail;
        }));

        return $record;
    }

    private function computeOverallStatus(array $detailStatuses): string
    {
        $statuses = array_values(array_filter(array_map('strval', $detailStatuses)));
        if (empty($statuses)) {
            return 'pending';
        }

        $allClosed = count(array_filter($statuses, static fn (string $s) => $s === 'closed')) === count($statuses);
        if ($allClosed) {
            return 'closed';
        }

        $allUnrepairable = count(array_filter($statuses, static fn (string $s) => $s === 'unrepairable')) === count($statuses);
        if ($allUnrepairable) {
            return 'unrepairable';
        }

        if (in_array('verification', $statuses, true)) {
            return 'verification';
        }
        if (in_array('repair_in_progress', $statuses, true)) {
            return 'repair_in_progress';
        }
        if (in_array('work_approved', $statuses, true)) {
            return 'work_approved';
        }
        if (in_array('under_review', $statuses, true)) {
            return 'under_review';
        }

        return 'pending';
    }
}
