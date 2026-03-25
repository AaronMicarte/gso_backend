<?php

namespace App\Http\Controllers;

use App\Models\AssetAuditLog;
use App\Models\AssetServiceRecord;
use App\Models\AssetServiceRecordDetail;
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

        $validated = $request->validate([
            'date_reported' => 'required|date',
            'report_source' => 'required|in:cce_user,inventory_check,audit,other',
            'submitted_by_name' => 'nullable|string|max:150',
            'submitted_by_end_user_id' => 'nullable|integer|exists:enduser,EndUserID',
            'reporting_department_id' => 'nullable|integer|exists:department,DepartmentID',
            'status' => 'nullable|in:pending,under_review,work_approved,repair_in_progress,verification,closed,unrepairable',
            'record_notes' => 'nullable|string|max:4000',
            'details' => 'required|array|min:1',
            'details.*.inventory_detail_id' => 'required|integer|exists:propertyinventorydetail,InventoryDetailID',
            'details.*.problem_description' => 'required|string|max:4000',
            'details.*.handling_decision' => 'nullable|in:warranty_claim,vendor_repair,replacement_evaluation',
            'details.*.budget_status' => 'nullable|in:pending_check,warranty_covered,budget_available,budget_unavailable',
            'details.*.status' => 'nullable|in:pending,under_review,work_approved,repair_in_progress,verification,closed,unrepairable',
            'details.*.assigned_vendor_id' => 'nullable|integer|exists:vendor,VendorID',
            'details.*.estimated_cost' => 'nullable|numeric|min:0',
            'details.*.actual_cost' => 'nullable|numeric|min:0',
            'details.*.warranty_covered' => 'nullable|boolean',
            'details.*.warranty_outcome' => 'nullable|string|max:255',
            'details.*.completion_notes' => 'nullable|string|max:4000',
            'details.*.verification_notes' => 'nullable|string|max:4000',
            'details.*.invoice_attachment' => 'nullable|string|max:255',
            'details.*.supporting_attachments' => 'nullable|array',
            'details.*.supporting_attachments.*' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $defaultStatus = $validated['status'] ?? 'pending';

            $record = AssetServiceRecord::create([
                'ServiceNumber' => AssetServiceRecord::generateServiceNumber(),
                'DateReported' => $validated['date_reported'],
                'ReportSource' => $validated['report_source'],
                'SubmittedByName' => $validated['submitted_by_name'] ?? null,
                'SubmittedByEndUserID' => $validated['submitted_by_end_user_id'] ?? null,
                'ReportingDepartmentID' => $validated['reporting_department_id'] ?? null,
                'Status' => $defaultStatus,
                'RecordNotes' => $validated['record_notes'] ?? null,
                'CreatedByUserID' => (int) $user->UserID,
                'UpdatedByUserID' => (int) $user->UserID,
            ]);

            $detailStatuses = [];
            foreach ($validated['details'] as $detailInput) {
                $inventoryDetail = \App\Models\Inventory\PropertyInventoryDetail::findOrFail((int) $detailInput['inventory_detail_id']);

                $detail = AssetServiceRecordDetail::create([
                    'ServiceRecordID' => (int) $record->ServiceRecordID,
                    'InventoryDetailID' => (int) $detailInput['inventory_detail_id'],
                    'PropertyNumber' => $inventoryDetail->PropertyNumber,
                    'ItemDescription' => $inventoryDetail->Description,
                    'ProblemDescription' => $detailInput['problem_description'],
                    'HandlingDecision' => $detailInput['handling_decision'] ?? null,
                    'BudgetStatus' => $detailInput['budget_status'] ?? 'pending_check',
                    'Status' => $detailInput['status'] ?? $defaultStatus,
                    'AssignedVendorID' => $detailInput['assigned_vendor_id'] ?? null,
                    'EstimatedCost' => $detailInput['estimated_cost'] ?? null,
                    'ActualCost' => $detailInput['actual_cost'] ?? null,
                    'WarrantyCovered' => (bool) ($detailInput['warranty_covered'] ?? false),
                    'WarrantyOutcome' => $detailInput['warranty_outcome'] ?? null,
                    'CompletionNotes' => $detailInput['completion_notes'] ?? null,
                    'VerificationNotes' => $detailInput['verification_notes'] ?? null,
                    'InvoiceAttachment' => $detailInput['invoice_attachment'] ?? null,
                    'SupportingAttachments' => array_values(array_filter($detailInput['supporting_attachments'] ?? [])),
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

        $validated = $request->validate([
            'date_reported' => 'sometimes|required|date',
            'report_source' => 'sometimes|required|in:cce_user,inventory_check,audit,other',
            'submitted_by_name' => 'nullable|string|max:150',
            'submitted_by_end_user_id' => 'nullable|integer|exists:enduser,EndUserID',
            'reporting_department_id' => 'nullable|integer|exists:department,DepartmentID',
            'status' => 'nullable|in:pending,under_review,work_approved,repair_in_progress,verification,closed,unrepairable',
            'record_notes' => 'nullable|string|max:4000',
            'details' => 'nullable|array|min:1',
            'details.*.service_record_detail_id' => 'nullable|integer|exists:asset_service_records_details,ServiceRecordDetailID',
            'details.*.inventory_detail_id' => 'required_with:details|integer|exists:propertyinventorydetail,InventoryDetailID',
            'details.*.problem_description' => 'required_with:details|string|max:4000',
            'details.*.handling_decision' => 'nullable|in:warranty_claim,vendor_repair,replacement_evaluation',
            'details.*.budget_status' => 'nullable|in:pending_check,warranty_covered,budget_available,budget_unavailable',
            'details.*.status' => 'nullable|in:pending,under_review,work_approved,repair_in_progress,verification,closed,unrepairable',
            'details.*.assigned_vendor_id' => 'nullable|integer|exists:vendor,VendorID',
            'details.*.estimated_cost' => 'nullable|numeric|min:0',
            'details.*.actual_cost' => 'nullable|numeric|min:0',
            'details.*.warranty_covered' => 'nullable|boolean',
            'details.*.warranty_outcome' => 'nullable|string|max:255',
            'details.*.completion_notes' => 'nullable|string|max:4000',
            'details.*.verification_notes' => 'nullable|string|max:4000',
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

                    $detail->InventoryDetailID = (int) $detailInput['inventory_detail_id'];
                    $detail->PropertyNumber = $inventoryDetail->PropertyNumber;
                    $detail->ItemDescription = $inventoryDetail->Description;
                    $detail->ProblemDescription = $detailInput['problem_description'];
                    $detail->HandlingDecision = $detailInput['handling_decision'] ?? $detail->HandlingDecision;
                    $detail->BudgetStatus = $detailInput['budget_status'] ?? $detail->BudgetStatus ?? 'pending_check';
                    $detail->Status = $newDetailStatus;
                    $detail->AssignedVendorID = $detailInput['assigned_vendor_id'] ?? null;
                    $detail->EstimatedCost = $detailInput['estimated_cost'] ?? null;
                    $detail->ActualCost = $detailInput['actual_cost'] ?? null;
                    $detail->WarrantyCovered = (bool) ($detailInput['warranty_covered'] ?? false);
                    $detail->WarrantyOutcome = $detailInput['warranty_outcome'] ?? null;
                    $detail->CompletionNotes = $detailInput['completion_notes'] ?? null;
                    $detail->VerificationNotes = $detailInput['verification_notes'] ?? null;
                    $detail->InvoiceAttachment = $detailInput['invoice_attachment'] ?? null;
                    $detail->SupportingAttachments = array_values(array_filter($detailInput['supporting_attachments'] ?? []));
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
            ->get(['VendorID', 'VendorName', 'ContactPerson', 'ContactNumber', 'Email']);

        return response()->json(['data' => $vendors]);
    }

    public function helperDepartments(): \Illuminate\Http\JsonResponse
    {
        $departments = \App\Models\MasterData\Department::query()
            ->where('IsActive', true)
            ->orderBy('DepartmentName')
            ->get(['DepartmentID', 'DepartmentName', 'DepartmentCode']);

        return response()->json(['data' => $departments]);
    }

    public function helperEndUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = \App\Models\EndUser::query()
            ->where('IsActive', true)
            ->orderBy('FullName');

        if ($request->filled('department_id')) {
            $query->where('DepartmentID', (int) $request->query('department_id'));
        }

        $rows = $query->get(['EndUserID', 'DepartmentID', 'FullName']);

        return response()->json(['data' => $rows]);
    }

    public function helperInventory(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = \App\Models\Inventory\PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyName',
                'condition:ConditionID,ConditionName',
                'inventory:InventoryID,InventoryNumber,DateReceived',
            ])
            ->orderByDesc('InventoryDetailID');

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

        $rows = $query->limit(200)->get();

        return response()->json(['data' => $rows]);
    }

    private function canView(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF, self::ROLE_DEPARTMENT_HEAD], true);
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
