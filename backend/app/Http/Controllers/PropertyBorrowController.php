<?php

namespace App\Http\Controllers;

use App\Models\PropertyBorrow;
use App\Models\PropertyBorrowItem;
use App\Models\PropertyReturn;
use App\Models\PropertyReturnItem;
use App\Models\PropertyTransferItem;
use App\Models\AssignedProperty;
use App\Models\AssetAuditLog;
use App\Models\EndUser;
use App\Models\MasterData\Department;
use App\Models\MasterData\Condition;
use App\Models\MasterData\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PropertyBorrowController extends Controller
{
    private const ROLE_ADMIN = 'Admin';
    private const ROLE_GSO_STAFF = 'GSO Staff';
    private const ROLE_DEPARTMENT_HEAD = 'Department Head';
    private const PROPERTY_STATUS_ISSUED = 2;

    private const ALLOWED_PROPERTY_TYPE_IDS = [
        2,  // Communication Equipment
        7,  // Information and Communication Technology Equipment
        11, // Office Equipment
        12, // Other Machinery and Equipment
        14, // Technical and Scientific Equipment
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        $query = PropertyBorrow::with([
            'requestedBy:UserID,FullName,DepartmentID',
            'requestedBy.department:DepartmentID,DepartmentName',
            'fromDepartment:DepartmentID,DepartmentName',
            'toDepartment:DepartmentID,DepartmentName',
            'fromEndUser:EndUserID,FullName,DepartmentID',
            'fromEndUser.department:DepartmentID,DepartmentName',
            'toEndUser:EndUserID,FullName,DepartmentID',
            'toEndUser.department:DepartmentID,DepartmentName',
            'releasingReviewedBy:UserID,FullName',
            'approvedBy:UserID,FullName',
        ])->withCount('items');

        if (!$this->isPrivileged($roleName)) {
            if ($roleName !== self::ROLE_DEPARTMENT_HEAD) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $departmentId = (int) ($user?->DepartmentID ?? 0);
            $userId = (int) ($user?->UserID ?? 0);

            $scope = $request->query('scope');
            if ($scope === 'incoming') {
                $query->where('FromDepartmentID', $departmentId)
                    ->where('RequestedByUserID', '!=', $userId);
            } elseif ($scope === 'outgoing') {
                $query->where('RequestedByUserID', $userId);
            } else {
                $query->where(function ($q) use ($userId, $departmentId) {
                    $q->where('RequestedByUserID', $userId)
                        ->orWhere('FromDepartmentID', $departmentId)
                        ->orWhere('ToDepartmentID', $departmentId);
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('Status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('BorrowNumber', 'like', "%{$search}%")
                    ->orWhereHas('fromDepartment', function ($dq) use ($search) {
                        $dq->where('DepartmentName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('toDepartment', function ($dq) use ($search) {
                        $dq->where('DepartmentName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('requestedBy', function ($uq) use ($search) {
                        $uq->where('FullName', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = max(10, (int) $request->query('per_page', 15));
        $borrows = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($borrows);
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        $borrow = PropertyBorrow::with([
            'requestedBy:UserID,FullName,DepartmentID',
            'requestedBy.department:DepartmentID,DepartmentName',
            'fromDepartment:DepartmentID,DepartmentName',
            'toDepartment:DepartmentID,DepartmentName',
            'fromEndUser:EndUserID,FullName,DepartmentID',
            'fromEndUser.department:DepartmentID,DepartmentName',
            'toEndUser:EndUserID,FullName,DepartmentID',
            'toEndUser.department:DepartmentID,DepartmentName',
            'releasingReviewedBy:UserID,FullName',
            'approvedBy:UserID,FullName',
            'returns.returnedBy:UserID,FullName',
            'returns.items.inventoryDetail.unitOfMeasure',
            'returns.items.inventoryDetail.propertyType',
            'returns.items.inventoryDetail.inventory',
            'returns.items.inventoryDetail.drDetail.article',
            'returns.items.condition',
            'returns.items.borrowItem',
            'items.inventoryDetail.unitOfMeasure',
            'items.inventoryDetail.propertyType',
            'items.inventoryDetail.inventory',
            'items.inventoryDetail.drDetail.article',
        ])->findOrFail($id);

        if (!$this->isPrivileged($roleName)) {
            if ($roleName !== self::ROLE_DEPARTMENT_HEAD) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $departmentId = (int) ($user?->DepartmentID ?? 0);
            $isOwner = (int) $borrow->RequestedByUserID === (int) $user->UserID;
            $isDeptInvolved = (int) $borrow->FromDepartmentID === $departmentId
                || (int) $borrow->ToDepartmentID === $departmentId;

            if (!$isOwner && !$isDeptInvolved) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ((int) $borrow->FromDepartmentID === $departmentId) {
                $borrow->ApprovedAt = null;
                $borrow->BorrowedAt = null;
                $borrow->ApprovedByUserID = null;
                $borrow->setRelation('returns', collect());
                $borrow->setRelation('approvedBy', null);
            }
        }

        return response()->json($borrow);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canCreate($roleName)) {
            return response()->json(['message' => 'Unauthorized to create borrow requests'], 403);
        }

        $validated = $request->validate([
            'from_department_id' => 'required|integer|exists:department,DepartmentID',
            'to_department_id' => 'required|integer|different:from_department_id|exists:department,DepartmentID',
            'from_end_user_id' => 'required|integer|exists:enduser,EndUserID',
            'to_end_user_id' => 'required|integer|exists:enduser,EndUserID',
            'purpose' => 'nullable|string|max:2000',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.inventory_detail_id' => 'required|integer|exists:propertyinventorydetail,InventoryDetailID',
            'items.*.quantity' => 'required|integer|in:1',
        ]);

        $requesterDepartmentId = (int) ($user?->DepartmentID ?? 0);
        if ($requesterDepartmentId && (int) $validated['to_department_id'] !== $requesterDepartmentId) {
            return response()->json(['message' => 'Borrowing department must be your department'], 422);
        }

        $requesterName = trim((string) ($user?->FullName ?? ''));
        if ($requesterDepartmentId && $requesterName !== '') {
            $requesterEndUser = EndUser::where('DepartmentID', $requesterDepartmentId)
                ->whereRaw('LOWER(FullName) = ?', [strtolower($requesterName)])
                ->first();

            if (!$requesterEndUser) {
                return response()->json([
                    'message' => 'Requester must have a matching End User record in their department before creating a borrow request.'
                ], 422);
            }

            $validated['to_end_user_id'] = (int) $requesterEndUser->EndUserID;
            $validated['to_department_id'] = $requesterDepartmentId;
        }

        $fromEndUser = EndUser::find((int) $validated['from_end_user_id']);
        $toEndUser = EndUser::find((int) $validated['to_end_user_id']);

        if ($fromEndUser && (int) $fromEndUser->DepartmentID !== (int) $validated['from_department_id']) {
            return response()->json(['message' => 'Releasing custodian must belong to the releasing department'], 422);
        }

        if ($toEndUser && (int) $toEndUser->DepartmentID !== (int) $validated['to_department_id']) {
            return response()->json(['message' => 'Borrowing custodian must belong to the borrowing department'], 422);
        }

        $dueDate = Carbon::parse((string) $validated['due_date'])->timezone('Asia/Manila')->startOfDay();
        $today = Carbon::now('Asia/Manila')->startOfDay();

        if ($dueDate->lessThanOrEqualTo($today)) {
            return response()->json(['message' => 'Due date must be a future date'], 422);
        }

        $inventoryDetailIds = collect($validated['items'])->pluck('inventory_detail_id')->map(fn ($id) => (int) $id)->values();

        $openTransferExists = PropertyTransferItem::whereIn('InventoryDetailID', $inventoryDetailIds)
            ->whereHas('transfer', function ($q) {
                $q->whereIn('Status', ['pending', 'approved']);
            })
            ->exists();

        if ($openTransferExists) {
            return response()->json(['message' => 'One or more items already have an active transfer request'], 422);
        }

        $openBorrowExists = PropertyBorrowItem::whereIn('InventoryDetailID', $inventoryDetailIds)
            ->whereHas('borrow', function ($q) {
                $q->whereIn('Status', ['pending', 'approved', 'borrowed', 'partially_returned']);
            })
            ->exists();

        if ($openBorrowExists) {
            return response()->json(['message' => 'One or more items already have an active borrow request'], 422);
        }

        return DB::transaction(function () use ($validated, $user, $inventoryDetailIds, $dueDate) {
            $assignedProperties = AssignedProperty::query()
                ->join('propertyinventorydetail as pid', 'assignedproperty.InventoryDetailID', '=', 'pid.InventoryDetailID')
                ->whereIn('assignedproperty.InventoryDetailID', $inventoryDetailIds)
                ->lockForUpdate()
                ->select([
                    'assignedproperty.*',
                    'pid.PropertyStatusID as InventoryPropertyStatusID',
                    'pid.PropertyTypeID',
                ])
                ->get();

            if ($assignedProperties->count() !== $inventoryDetailIds->count()) {
                return response()->json(['message' => 'Some items are not assigned and cannot be borrowed'], 422);
            }

            foreach ($assignedProperties as $assignedProperty) {
                if ((int) $assignedProperty->DepartmentID !== (int) $validated['from_department_id']) {
                    return response()->json(['message' => 'All items must belong to the releasing department'], 422);
                }
                if ((int) $assignedProperty->EndUserID !== (int) $validated['from_end_user_id']) {
                    return response()->json(['message' => 'All items must belong to the selected releasing custodian'], 422);
                }
                if ((int) $assignedProperty->PropertyStatusID !== self::PROPERTY_STATUS_ISSUED
                    || (int) ($assignedProperty->InventoryPropertyStatusID ?? 0) !== self::PROPERTY_STATUS_ISSUED) {
                    return response()->json(['message' => 'Only issued items can be borrowed'], 422);
                }
                if (!in_array((int) $assignedProperty->PropertyTypeID, self::ALLOWED_PROPERTY_TYPE_IDS, true)) {
                    return response()->json(['message' => 'One or more items are not eligible for borrowing'], 422);
                }
            }

            $borrow = PropertyBorrow::create([
                'BorrowNumber' => PropertyBorrow::generateBorrowNumber(),
                'RequestedByUserID' => (int) $user->UserID,
                'FromDepartmentID' => (int) $validated['from_department_id'],
                'ToDepartmentID' => (int) $validated['to_department_id'],
                'FromEndUserID' => (int) $validated['from_end_user_id'],
                'ToEndUserID' => (int) $validated['to_end_user_id'],
                'Purpose' => $validated['purpose'] ?? null,
                'RequestedItemsCount' => (int) $inventoryDetailIds->count(),
                'DueDate' => $dueDate->toDateString(),
                'ReleasingDecision' => 'pending',
                'Status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                PropertyBorrowItem::create([
                    'BorrowID' => $borrow->BorrowID,
                    'InventoryDetailID' => (int) $item['inventory_detail_id'],
                    'Quantity' => (int) $item['quantity'],
                    'Remarks' => null,
                ]);
            }

            AssetAuditLog::createLog(
                'property_borrow',
                (int) $borrow->BorrowID,
                'created',
                (int) $user->UserID,
                [
                    'borrow_number' => $borrow->BorrowNumber,
                    'from_department_id' => (int) $borrow->FromDepartmentID,
                    'to_department_id' => (int) $borrow->ToDepartmentID,
                    'due_date' => $borrow->DueDate?->toDateString(),
                    'items' => $inventoryDetailIds->all(),
                ]
            );

            foreach ($inventoryDetailIds as $inventoryDetailId) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $inventoryDetailId,
                    'borrow_requested',
                    (int) $user->UserID,
                    [
                        'borrow_id' => (int) $borrow->BorrowID,
                        'borrow_number' => $borrow->BorrowNumber,
                        'from_department_id' => (int) $borrow->FromDepartmentID,
                        'to_department_id' => (int) $borrow->ToDepartmentID,
                        'from_end_user_id' => (int) $borrow->FromEndUserID,
                        'to_end_user_id' => (int) $borrow->ToEndUserID,
                    ]
                );
            }

            $borrow->load([
                'requestedBy:UserID,FullName,DepartmentID',
                'requestedBy.department:DepartmentID,DepartmentName',
                'fromDepartment:DepartmentID,DepartmentName',
                'toDepartment:DepartmentID,DepartmentName',
                'fromEndUser:EndUserID,FullName,DepartmentID',
                'fromEndUser.department:DepartmentID,DepartmentName',
                'toEndUser:EndUserID,FullName,DepartmentID',
                'toEndUser.department:DepartmentID,DepartmentName',
                'releasingReviewedBy:UserID,FullName',
                'items.inventoryDetail.unitOfMeasure',
                'items.inventoryDetail.inventory',
                'items.inventoryDetail.drDetail.article',
                'items.inventoryDetail.propertyType',
            ]);

            return response()->json([
                'message' => 'Borrow request submitted',
                'borrow' => $borrow,
            ], 201);
        });
    }

    public function approve(int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Unauthorized to approve borrow requests'], 403);
        }

        $borrow = PropertyBorrow::findOrFail($id);
        if ($borrow->Status !== 'pending') {
            return response()->json(['message' => 'Only pending borrow requests can be approved'], 422);
        }

        if (($borrow->ReleasingDecision ?? 'pending') !== 'approved') {
            return response()->json(['message' => 'Releasing department must approve first before GSO/Admin approval'], 422);
        }

        $borrow->update([
            'Status' => 'borrowed',
            'ApprovedByUserID' => (int) $user->UserID,
            'ApprovedAt' => now(),
            'BorrowedAt' => now(),
        ]);

        AssetAuditLog::createLog(
            'property_borrow',
            (int) $borrow->BorrowID,
            'approved',
            (int) $user->UserID,
            ['borrow_number' => $borrow->BorrowNumber]
        );

        $borrow->loadMissing('items');
        foreach ($borrow->items as $item) {
            AssetAuditLog::createLog(
                'inventory_detail',
                (int) $item->InventoryDetailID,
                'borrow_approved',
                (int) $user->UserID,
                [
                    'borrow_id' => (int) $borrow->BorrowID,
                    'borrow_number' => $borrow->BorrowNumber,
                ]
            );
        }

        AssetAuditLog::createLog(
            'property_borrow',
            (int) $borrow->BorrowID,
            'borrowed',
            (int) $user->UserID,
            ['borrow_number' => $borrow->BorrowNumber]
        );

        return response()->json(['message' => 'Borrow approved and marked as borrowed', 'borrow' => $borrow]);
    }

    public function releasingReview(Request $request, int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if ($roleName !== self::ROLE_DEPARTMENT_HEAD) {
            return response()->json(['message' => 'Only Department Head can perform releasing review'], 403);
        }

        return DB::transaction(function () use ($request, $id, $user) {
            $borrow = PropertyBorrow::with(['items.inventoryDetail'])->lockForUpdate()->findOrFail($id);

            if ((int) ($user?->DepartmentID ?? 0) !== (int) $borrow->FromDepartmentID) {
                return response()->json(['message' => 'Only releasing department can review this request'], 403);
            }

            if ($borrow->Status !== 'pending') {
                return response()->json(['message' => 'Only pending borrows can be reviewed by releasing department'], 422);
            }

            if (($borrow->ReleasingDecision ?? 'pending') !== 'pending') {
                return response()->json(['message' => 'Releasing review already completed for this borrow'], 422);
            }

            $validated = $request->validate([
                'decision' => 'required|in:approved,rejected',
                'approved_quantities' => 'nullable|array',
                'approved_quantities.*' => 'integer|min:0',
                'reason' => 'nullable|string|max:2000',
            ]);

            $reason = trim((string) ($validated['reason'] ?? ''));
            $requestedCount = (int) $borrow->items->sum(function ($item) {
                return (int) ($item->Quantity ?? 0);
            });

            if ($validated['decision'] === 'rejected') {
                if ($reason === '') {
                    return response()->json(['message' => 'Reason is required when releasing department rejects the request'], 422);
                }

                $borrow->update([
                    'ReleasingDecision' => 'rejected',
                    'ReleasingApprovedCount' => 0,
                    'ReleasingDecisionReason' => $reason,
                    'ReleasingReviewedByUserID' => (int) $user->UserID,
                    'ReleasingReviewedAt' => now(),
                    'Status' => 'rejected',
                ]);

                AssetAuditLog::createLog(
                    'property_borrow',
                    (int) $borrow->BorrowID,
                    'releasing_rejected',
                    (int) $user->UserID,
                    [
                        'borrow_number' => $borrow->BorrowNumber,
                        'reason' => $reason,
                    ]
                );

                return response()->json(['message' => 'Borrow request rejected', 'borrow' => $borrow]);
            }

            $approvedCount = 0;
            $approvedQuantities = $validated['approved_quantities'] ?? [];
            foreach ($borrow->items as $item) {
                $requestedQty = (int) ($item->Quantity ?? 0);
                $approvedQty = (int) ($approvedQuantities[$item->BorrowItemID] ?? $requestedQty);
                $approvedQty = max(0, min($approvedQty, $requestedQty));
                $approvedCount += $approvedQty;
            }

            if ($approvedCount < 1) {
                $autoRejectReason = $reason !== ''
                    ? $reason
                    : 'Auto-rejected: releasing department set all provided quantities to 0.';

                $borrow->update([
                    'ReleasingDecision' => 'rejected',
                    'ReleasingApprovedCount' => 0,
                    'ReleasingDecisionReason' => $autoRejectReason,
                    'ReleasingReviewedByUserID' => (int) $user->UserID,
                    'ReleasingReviewedAt' => now(),
                    'Status' => 'rejected',
                ]);

                AssetAuditLog::createLog(
                    'property_borrow',
                    (int) $borrow->BorrowID,
                    'releasing_rejected',
                    (int) $user->UserID,
                    [
                        'borrow_number' => $borrow->BorrowNumber,
                        'requested_count' => $requestedCount,
                        'approved_count' => 0,
                        'reason' => $autoRejectReason,
                    ]
                );

                return response()->json([
                    'message' => 'Borrow request auto-rejected because provided quantity is 0',
                    'borrow' => $borrow,
                ]);
            }

            if ($approvedCount > $requestedCount) {
                return response()->json(['message' => 'Approved quantity cannot exceed requested quantity'], 422);
            }

            if ($approvedCount < $requestedCount && $reason === '') {
                return response()->json(['message' => 'Reason is required when provided quantity is less than requested'], 422);
            }

            $borrow->update([
                'ReleasingDecision' => 'approved',
                'ReleasingApprovedCount' => $approvedCount,
                'ReleasingDecisionReason' => $reason ?: null,
                'ReleasingReviewedByUserID' => (int) $user->UserID,
                'ReleasingReviewedAt' => now(),
            ]);

            AssetAuditLog::createLog(
                'property_borrow',
                (int) $borrow->BorrowID,
                'releasing_approved',
                (int) $user->UserID,
                [
                    'borrow_number' => $borrow->BorrowNumber,
                    'requested_count' => $requestedCount,
                    'approved_count' => $approvedCount,
                    'reason' => $reason ?: null,
                ]
            );

            return response()->json(['message' => 'Releasing review submitted', 'borrow' => $borrow]);
        });
    }

    public function reject(Request $request, int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Unauthorized to reject borrow requests'], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        $borrow = PropertyBorrow::findOrFail($id);
        if ($borrow->Status !== 'pending') {
            return response()->json(['message' => 'Only pending borrows can be rejected'], 422);
        }

        $borrow->update([
            'Status' => 'rejected',
            'GsoNotes' => $validated['notes'] ?? $borrow->GsoNotes,
        ]);

        AssetAuditLog::createLog(
            'property_borrow',
            (int) $borrow->BorrowID,
            'rejected',
            (int) $user->UserID,
            ['borrow_number' => $borrow->BorrowNumber]
        );

        return response()->json(['message' => 'Borrow rejected', 'borrow' => $borrow]);
    }

    public function markReturned(Request $request, int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Unauthorized to mark returns'], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.borrow_item_id' => 'required|integer|exists:propertyborrowitems,BorrowItemID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition_id' => 'nullable|integer|exists:condition,ConditionID',
        ]);

        return DB::transaction(function () use ($validated, $user, $id) {
            $borrow = PropertyBorrow::with(['items', 'returns.items'])
                ->lockForUpdate()
                ->findOrFail($id);

            if (!in_array($borrow->Status, ['borrowed', 'partially_returned'], true)) {
                return response()->json(['message' => 'Only borrowed items can be returned'], 422);
            }

            $borrowItems = $borrow->items->keyBy('BorrowItemID');
            $alreadyReturnedByItem = [];

            foreach ($borrow->returns as $returnRecord) {
                foreach ($returnRecord->items as $returnItem) {
                    $itemId = (int) $returnItem->BorrowItemID;
                    $alreadyReturnedByItem[$itemId] = ($alreadyReturnedByItem[$itemId] ?? 0)
                        + (int) $returnItem->QuantityReturned;
                }
            }

            $returnItems = [];
            $totalNewReturned = 0;
            $payloadTotals = [];
            $payloadRows = [];

            foreach ($validated['items'] as $itemPayload) {
                $borrowItemId = (int) $itemPayload['borrow_item_id'];
                $quantity = (int) $itemPayload['quantity'];
                $conditionId = isset($itemPayload['condition_id']) ? (int) $itemPayload['condition_id'] : null;

                if (!$borrowItems->has($borrowItemId)) {
                    return response()->json(['message' => 'Return item does not belong to this borrow'], 422);
                }

                $borrowItem = $borrowItems->get($borrowItemId);

                $payloadTotals[$borrowItemId] = ($payloadTotals[$borrowItemId] ?? 0) + $quantity;
                $totalNewReturned += $quantity;
                $payloadRows[] = [
                    'BorrowItemID' => $borrowItemId,
                    'InventoryDetailID' => (int) $borrowItem->InventoryDetailID,
                    'QuantityReturned' => $quantity,
                    'ConditionID' => $conditionId ?: null,
                ];
            }

            foreach ($payloadTotals as $borrowItemId => $quantity) {
                $borrowItem = $borrowItems->get($borrowItemId);
                $requestedQty = (int) ($borrowItem->Quantity ?? 0);
                $returnedQty = (int) ($alreadyReturnedByItem[$borrowItemId] ?? 0);
                $remainingQty = $requestedQty - $returnedQty;

                if ($remainingQty < 1) {
                    return response()->json(['message' => 'Item already fully returned'], 422);
                }

                if ($quantity > $remainingQty) {
                    return response()->json(['message' => 'Return quantity exceeds remaining quantity'], 422);
                }
            }

            if ($totalNewReturned < 1) {
                return response()->json(['message' => 'Return quantity must be at least 1'], 422);
            }

            $now = Carbon::now('Asia/Manila');
            $returnRecord = PropertyReturn::create([
                'ReturnNumber' => PropertyReturn::generateReturnNumber(),
                'BorrowID' => (int) $borrow->BorrowID,
                'ReturnedByUserID' => (int) $user->UserID,
                'ReturnedAt' => $now,
                'Notes' => $validated['notes'] ?? null,
            ]);

            foreach ($payloadRows as $returnItemData) {
                PropertyReturnItem::create([
                    'ReturnID' => (int) $returnRecord->ReturnID,
                    'BorrowItemID' => (int) $returnItemData['BorrowItemID'],
                    'InventoryDetailID' => (int) $returnItemData['InventoryDetailID'],
                    'QuantityReturned' => (int) $returnItemData['QuantityReturned'],
                    'ConditionID' => $returnItemData['ConditionID'],
                    'Remarks' => null,
                ]);
            }

            $totalBorrowed = (int) $borrow->items->sum(fn ($item) => (int) ($item->Quantity ?? 0));
            $previousReturned = array_sum($alreadyReturnedByItem);
            $totalReturned = $previousReturned + $totalNewReturned;
            $newStatus = $totalReturned >= $totalBorrowed ? 'returned' : 'partially_returned';

            $borrow->update([
                'Status' => $newStatus,
            ]);

            AssetAuditLog::createLog(
                'property_return',
                (int) $returnRecord->ReturnID,
                'created',
                (int) $user->UserID,
                [
                    'borrow_id' => (int) $borrow->BorrowID,
                    'borrow_number' => $borrow->BorrowNumber,
                    'items_returned' => $totalNewReturned,
                ]
            );

            AssetAuditLog::createLog(
                'property_borrow',
                (int) $borrow->BorrowID,
                $newStatus === 'returned' ? 'returned' : 'partially_returned',
                (int) $user->UserID,
                [
                    'borrow_number' => $borrow->BorrowNumber,
                    'items_returned' => $totalNewReturned,
                ]
            );

            foreach ($payloadRows as $returnItemData) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $returnItemData['InventoryDetailID'],
                    'borrow_returned',
                    (int) $user->UserID,
                    [
                        'borrow_id' => (int) $borrow->BorrowID,
                        'borrow_number' => $borrow->BorrowNumber,
                        'return_id' => (int) $returnRecord->ReturnID,
                        'quantity' => (int) $returnItemData['QuantityReturned'],
                        'condition_id' => $returnItemData['ConditionID'],
                    ]
                );
            }

            $borrow->load([
                'requestedBy:UserID,FullName,DepartmentID',
                'requestedBy.department:DepartmentID,DepartmentName',
                'fromDepartment:DepartmentID,DepartmentName',
                'toDepartment:DepartmentID,DepartmentName',
                'fromEndUser:EndUserID,FullName,DepartmentID',
                'fromEndUser.department:DepartmentID,DepartmentName',
                'toEndUser:EndUserID,FullName,DepartmentID',
                'toEndUser.department:DepartmentID,DepartmentName',
                'releasingReviewedBy:UserID,FullName',
                'approvedBy:UserID,FullName',
                'returns.returnedBy:UserID,FullName',
                'returns.items.inventoryDetail.unitOfMeasure',
                'returns.items.inventoryDetail.propertyType',
                'returns.items.inventoryDetail.inventory',
                'returns.items.inventoryDetail.drDetail.article',
                'returns.items.condition',
                'returns.items.borrowItem',
                'items.inventoryDetail.unitOfMeasure',
                'items.inventoryDetail.propertyType',
                'items.inventoryDetail.inventory',
                'items.inventoryDetail.drDetail.article',
            ]);

            return response()->json(['message' => 'Return recorded', 'borrow' => $borrow]);
        });
    }

    public function availableItems(Request $request)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canCreate($roleName) && !$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = max(10, (int) $request->query('per_page', 15));
        $search = trim((string) $request->query('search', ''));
        $departmentId = $request->query('from_department_id');
        $endUserId = $request->query('from_end_user_id');

        $openTransferIds = PropertyTransferItem::whereHas('transfer', function ($q) {
            $q->whereIn('Status', ['pending', 'approved']);
        })->pluck('InventoryDetailID');

        $openBorrowIds = PropertyBorrowItem::whereHas('borrow', function ($q) {
            $q->whereIn('Status', ['pending', 'approved', 'borrowed', 'partially_returned']);
        })->pluck('InventoryDetailID');

        $query = AssignedProperty::query()
            ->leftJoin('propertyinventorydetail as pid', 'assignedproperty.InventoryDetailID', '=', 'pid.InventoryDetailID')
            ->leftJoin('propertytype as pt', 'pid.PropertyTypeID', '=', 'pt.PropertyTypeID')
            ->leftJoin('deliveryreceiptdetail as drd', 'pid.SourceDRDetailID', '=', 'drd.DRDetailID')
            ->leftJoin('articles as a', 'drd.ArticleID', '=', 'a.ArticleID')
            ->leftJoin('department as d', 'assignedproperty.DepartmentID', '=', 'd.DepartmentID')
            ->leftJoin('enduser as eu', 'assignedproperty.EndUserID', '=', 'eu.EndUserID')
            ->leftJoin('unitofmeasure as uom', 'pid.UnitOfMeasureID', '=', 'uom.UnitOfMeasureID')
            ->select([
                'assignedproperty.PropertyID',
                'assignedproperty.InventoryDetailID',
                'assignedproperty.DepartmentID',
                'assignedproperty.EndUserID',
                'assignedproperty.PropertyCardNumber',
                'assignedproperty.Description as AssignedDescription',
                'pid.PropertyNumber',
                'pid.Description as InventoryDescription',
                'pid.Brand',
                'pid.ModelNumber',
                'pid.SerialNumber',
                'pid.UnitCost',
                'pid.PropertyTypeID',
                'pt.PropertyName as PropertyTypeName',
                'drd.ItemDescription as ItemDescription',
                'a.ArticleName as ArticleName',
                'uom.UnitName',
                'd.DepartmentName',
                'eu.FullName as EndUserName',
            ])
            ->where('assignedproperty.PropertyStatusID', self::PROPERTY_STATUS_ISSUED)
            ->where('pid.PropertyStatusID', self::PROPERTY_STATUS_ISSUED)
            ->whereIn('pid.PropertyTypeID', self::ALLOWED_PROPERTY_TYPE_IDS)
            ->whereNotIn('assignedproperty.InventoryDetailID', $openTransferIds)
            ->whereNotIn('assignedproperty.InventoryDetailID', $openBorrowIds);

        if ($departmentId) {
            $query->where('assignedproperty.DepartmentID', (int) $departmentId);
        }

        if ($endUserId) {
            $query->where('assignedproperty.EndUserID', (int) $endUserId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('assignedproperty.PropertyCardNumber', 'like', "%{$search}%")
                    ->orWhere('assignedproperty.Description', 'like', "%{$search}%")
                    ->orWhere('pid.PropertyNumber', 'like', "%{$search}%")
                    ->orWhere('pid.SerialNumber', 'like', "%{$search}%")
                    ->orWhere('pid.Brand', 'like', "%{$search}%")
                    ->orWhere('pid.ModelNumber', 'like', "%{$search}%")
                    ->orWhere('drd.ItemDescription', 'like', "%{$search}%")
                    ->orWhere('a.ArticleName', 'like', "%{$search}%")
                    ->orWhere('eu.FullName', 'like', "%{$search}%");
            });
        }

        $items = $query->orderByDesc('assignedproperty.PropertyID')->paginate($perPage);

        return response()->json($items);
    }

    public function helperDepartments()
    {
        $departments = Department::query()
            ->where('IsActive', 1)
            ->orderBy('DepartmentName')
            ->get(['DepartmentID', 'DepartmentName', 'DepartmentCode']);

        return response()->json($departments);
    }

    public function helperEndUsers(Request $request)
    {
        $departmentId = $request->query('department_id');
        $excludeGso = filter_var($request->query('exclude_gso'), FILTER_VALIDATE_BOOLEAN);

        $query = EndUser::query()
            ->leftJoin('department as d', 'enduser.DepartmentID', '=', 'd.DepartmentID')
            ->where('enduser.IsActive', 1)
            ->orderBy('enduser.FullName')
            ->select([
                'enduser.EndUserID',
                'enduser.FullName',
                'enduser.DepartmentID',
                'd.DepartmentName',
            ]);

        if ($departmentId) {
            $query->where('enduser.DepartmentID', (int) $departmentId);
        }

        if ($excludeGso) {
            $query->where(function ($q) {
                $q->whereNull('d.DepartmentCode')
                    ->orWhere('d.DepartmentCode', '!=', 'GSO');
            })->where(function ($q) {
                $q->whereNull('d.DepartmentName')
                    ->orWhere('d.DepartmentName', '!=', 'GSO');
            });
        }

        $endUsers = $query->get();

        return response()->json($endUsers);
    }

    public function helperConditions()
    {
        $conditions = Condition::query()
            ->orderBy('ConditionName')
            ->get(['ConditionID', 'ConditionName']);

        return response()->json($conditions);
    }

    public function allowedPropertyTypes()
    {
        $types = PropertyType::query()
            ->whereIn('PropertyTypeID', self::ALLOWED_PROPERTY_TYPE_IDS)
            ->orderBy('PropertyName')
            ->get(['PropertyTypeID', 'PropertyName']);

        return response()->json($types);
    }

    private function isPrivileged(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true);
    }

    private function canCreate(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF, self::ROLE_DEPARTMENT_HEAD], true);
    }
}
