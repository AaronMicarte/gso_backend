<?php

namespace App\Http\Controllers;

use App\Models\PropertyTransfer;
use App\Models\PropertyTransferItem;
use App\Models\PropertyBorrowItem;
use App\Models\AssignedProperty;
use App\Models\AssetAuditLog;
use App\Models\QrCode;
use App\Models\EndUser;
use App\Models\MasterData\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PropertyTransferController extends Controller
{
    private const ROLE_ADMIN = 'Admin';
    private const ROLE_GSO_STAFF = 'GSO Staff';
    private const ROLE_DEPARTMENT_HEAD = 'Department Head';
    private const PROPERTY_STATUS_FOR_ISSUANCE = 1;
    private const PROPERTY_STATUS_ISSUED = 2;

    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        $query = PropertyTransfer::with([
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
            'completedBy:UserID,FullName',
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
                $q->where('TransferNumber', 'like', "%{$search}%")
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
        $transfers = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($transfers);
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        $transfer = PropertyTransfer::with([
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
            'completedBy:UserID,FullName',
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
            $isOwner = (int) $transfer->RequestedByUserID === (int) $user->UserID;
            $isDeptInvolved = (int) $transfer->FromDepartmentID === $departmentId
                || (int) $transfer->ToDepartmentID === $departmentId;

            if (!$isOwner && !$isDeptInvolved) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ((int) $transfer->FromDepartmentID === $departmentId) {
                $transfer->ApprovedAt = null;
                $transfer->BorrowerSignedAt = null;
                $transfer->BorrowingSignedAt = null;
                $transfer->CompletedAt = null;
                $transfer->ApprovedByUserID = null;
                $transfer->CompletedByUserID = null;
                $transfer->setRelation('approvedBy', null);
                $transfer->setRelation('completedBy', null);
            }
        }

        return response()->json($transfer);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canCreate($roleName)) {
            return response()->json(['message' => 'Unauthorized to create transfer requests'], 403);
        }

        $validated = $request->validate([
            'from_department_id' => 'required|integer|exists:department,DepartmentID',
            'to_department_id' => 'required|integer|different:from_department_id|exists:department,DepartmentID',
            'from_end_user_id' => 'required|integer|exists:enduser,EndUserID',
            'to_end_user_id' => 'required|integer|exists:enduser,EndUserID',
            'reason' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.inventory_detail_id' => 'required|integer|exists:propertyinventorydetail,InventoryDetailID',
            'items.*.quantity' => 'required|integer|in:1',
        ]);

        $requesterDepartmentId = (int) ($user?->DepartmentID ?? 0);
        if ($requesterDepartmentId && (int) $validated['to_department_id'] !== $requesterDepartmentId) {
            return response()->json(['message' => 'Receiving department must be your department'], 422);
        }

        $requesterName = trim((string) ($user?->FullName ?? ''));
        if ($requesterDepartmentId && $requesterName !== '') {
            $requesterEndUser = EndUser::where('DepartmentID', $requesterDepartmentId)
                ->whereRaw('LOWER(FullName) = ?', [strtolower($requesterName)])
                ->first();

            if (!$requesterEndUser) {
                return response()->json([
                    'message' => 'Requester must have a matching End User record in their department before creating a transfer.'
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
            return response()->json(['message' => 'Receiving custodian must belong to the receiving department'], 422);
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
                $q->whereIn('Status', ['pending', 'approved', 'borrowed']);
            })
            ->exists();

        if ($openBorrowExists) {
            return response()->json(['message' => 'One or more items are currently under an active borrow request'], 422);
        }

        return DB::transaction(function () use ($validated, $user, $inventoryDetailIds) {
            $assignedProperties = AssignedProperty::query()
                ->join('propertyinventorydetail as pid', 'assignedproperty.InventoryDetailID', '=', 'pid.InventoryDetailID')
                ->whereIn('assignedproperty.InventoryDetailID', $inventoryDetailIds)
                ->lockForUpdate()
                ->select([
                    'assignedproperty.*',
                    'pid.PropertyStatusID as InventoryPropertyStatusID',
                ])
                ->get();

            if ($assignedProperties->count() !== $inventoryDetailIds->count()) {
                return response()->json(['message' => 'Some items are not assigned and cannot be transferred'], 422);
            }

            foreach ($assignedProperties as $assignedProperty) {
                if ((int) $assignedProperty->DepartmentID !== (int) $validated['from_department_id']) {
                    return response()->json(['message' => 'All items must belong to the releasing department'], 422);
                }
                if ((int) $assignedProperty->EndUserID !== (int) $validated['from_end_user_id']) {
                    return response()->json(['message' => 'All items must belong to the selected borrower (current holder)'], 422);
                }
                if ((int) $assignedProperty->PropertyStatusID !== self::PROPERTY_STATUS_ISSUED
                    || (int) ($assignedProperty->InventoryPropertyStatusID ?? 0) !== self::PROPERTY_STATUS_ISSUED) {
                    return response()->json(['message' => 'Only issued items can be transferred'], 422);
                }
            }

            $transfer = PropertyTransfer::create([
                'TransferNumber' => PropertyTransfer::generateTransferNumber(),
                'RequestedByUserID' => (int) $user->UserID,
                'FromDepartmentID' => (int) $validated['from_department_id'],
                'ToDepartmentID' => (int) $validated['to_department_id'],
                'FromEndUserID' => (int) $validated['from_end_user_id'],
                'ToEndUserID' => (int) $validated['to_end_user_id'],
                'Reason' => $validated['reason'] ?? null,
                'RequestedItemsCount' => (int) $inventoryDetailIds->count(),
                'ReleasingDecision' => 'pending',
                'Status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                PropertyTransferItem::create([
                    'TransferID' => $transfer->TransferID,
                    'InventoryDetailID' => (int) $item['inventory_detail_id'],
                    'Quantity' => (int) $item['quantity'],
                    'Remarks' => null,
                ]);
            }

            AssetAuditLog::createLog(
                'property_transfer',
                (int) $transfer->TransferID,
                'created',
                (int) $user->UserID,
                [
                    'transfer_number' => $transfer->TransferNumber,
                    'from_department_id' => (int) $transfer->FromDepartmentID,
                    'to_department_id' => (int) $transfer->ToDepartmentID,
                    'items' => $inventoryDetailIds->all(),
                ]
            );

            foreach ($inventoryDetailIds as $inventoryDetailId) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $inventoryDetailId,
                    'transfer_requested',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                        'from_department_id' => (int) $transfer->FromDepartmentID,
                        'to_department_id' => (int) $transfer->ToDepartmentID,
                        'from_end_user_id' => (int) $transfer->FromEndUserID,
                        'to_end_user_id' => (int) $transfer->ToEndUserID,
                    ]
                );
            }

            $transfer->load([
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
            ]);

            return response()->json([
                'message' => 'Transfer request submitted',
                'transfer' => $transfer,
            ], 201);
        });
    }

    public function approve(int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Unauthorized to approve transfer requests'], 403);
        }

        $transfer = PropertyTransfer::findOrFail($id);
        if ($transfer->Status !== 'pending') {
            return response()->json(['message' => 'Only pending transfers can be approved'], 422);
        }

        if (($transfer->ReleasingDecision ?? 'pending') !== 'approved') {
            return response()->json(['message' => 'Releasing department must approve first before GSO/Admin approval'], 422);
        }

        $transfer->update([
            'Status' => 'approved',
            'ApprovedByUserID' => (int) $user->UserID,
            'ApprovedAt' => now(),
        ]);

        AssetAuditLog::createLog(
            'property_transfer',
            (int) $transfer->TransferID,
            'approved',
            (int) $user->UserID,
            ['transfer_number' => $transfer->TransferNumber]
        );

            $transfer->loadMissing('items');
            foreach ($transfer->items as $item) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $item->InventoryDetailID,
                    'transfer_approved',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                    ]
                );
            }

        return response()->json(['message' => 'Transfer approved', 'transfer' => $transfer]);
    }

    public function releasingReview(Request $request, int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if ($roleName !== self::ROLE_DEPARTMENT_HEAD) {
            return response()->json(['message' => 'Only Department Head can perform releasing review'], 403);
        }

        return DB::transaction(function () use ($request, $id, $user) {
            $transfer = PropertyTransfer::with(['items.inventoryDetail'])->lockForUpdate()->findOrFail($id);

            if ((int) ($user?->DepartmentID ?? 0) !== (int) $transfer->FromDepartmentID) {
                return response()->json(['message' => 'Only releasing department can review this request'], 403);
            }

            if ($transfer->Status !== 'pending') {
                return response()->json(['message' => 'Only pending transfers can be reviewed by releasing department'], 422);
            }

            if (($transfer->ReleasingDecision ?? 'pending') !== 'pending') {
                return response()->json(['message' => 'Releasing review already completed for this transfer'], 422);
            }

            $validated = $request->validate([
                'decision' => 'required|in:approved,rejected',
                'approved_quantities' => 'nullable|array',
                'approved_quantities.*' => 'integer|min:0',
                'reason' => 'nullable|string|max:2000',
            ]);

            $reason = trim((string) ($validated['reason'] ?? ''));
            $requestedCount = (int) $transfer->items->sum(function ($item) {
                return (int) ($item->Quantity ?? 0);
            });

            if ($validated['decision'] === 'rejected') {
                if ($reason === '') {
                    return response()->json(['message' => 'Reason is required when releasing department rejects the request'], 422);
                }

                $transfer->update([
                    'ReleasingDecision' => 'rejected',
                    'ReleasingApprovedCount' => 0,
                    'ReleasingDecisionReason' => $reason,
                    'ReleasingReviewedByUserID' => (int) $user->UserID,
                    'ReleasingReviewedAt' => now(),
                    'Status' => 'rejected',
                ]);

                AssetAuditLog::createLog(
                    'property_transfer',
                    (int) $transfer->TransferID,
                    'releasing_rejected',
                    (int) $user->UserID,
                    [
                        'transfer_number' => $transfer->TransferNumber,
                        'requested_count' => $requestedCount,
                        'approved_count' => 0,
                        'reason' => $reason,
                    ]
                );

                foreach ($transfer->items as $item) {
                    AssetAuditLog::createLog(
                        'inventory_detail',
                        (int) $item->InventoryDetailID,
                        'transfer_releasing_rejected',
                        (int) $user->UserID,
                        [
                            'transfer_id' => (int) $transfer->TransferID,
                            'transfer_number' => $transfer->TransferNumber,
                            'reason' => $reason,
                        ]
                    );
                }

                return response()->json([
                    'message' => 'Transfer rejected by releasing department',
                    'transfer' => $transfer->fresh([
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
                        'completedBy:UserID,FullName',
                        'items.inventoryDetail.unitOfMeasure',
                        'items.inventoryDetail.propertyType',
                        'items.inventoryDetail.inventory',
                        'items.inventoryDetail.drDetail.article',
                    ]),
                ]);
            }

            $approvedQuantitiesRaw = collect($validated['approved_quantities'] ?? [])
                ->mapWithKeys(fn ($qty, $itemId) => [(int) $itemId => (int) $qty]);

            $approvedCount = 0;
            $hasShortfall = false;
            foreach ($transfer->items as $item) {
                $itemId = (int) $item->TransferItemID;
                $requestedQty = (int) ($item->Quantity ?? 0);
                $approvedQty = $approvedQuantitiesRaw->has($itemId)
                    ? (int) $approvedQuantitiesRaw->get($itemId)
                    : $requestedQty;

                $assignedCount = AssignedProperty::where('InventoryDetailID', (int) $item->InventoryDetailID)
                    ->where('DepartmentID', (int) $transfer->FromDepartmentID)
                    ->where('EndUserID', (int) $transfer->FromEndUserID)
                    ->where('PropertyStatusID', self::PROPERTY_STATUS_ISSUED)
                    ->count();

                $availableFromInventory = (int) (($item->inventoryDetail?->QuantityAvailable
                    ?? $item->inventoryDetail?->Quantity
                    ?? 0));

                $maxByInventory = $availableFromInventory > 0 ? $availableFromInventory : $assignedCount;
                $maxReleasable = $assignedCount > 0
                    ? min($requestedQty, $assignedCount, $maxByInventory)
                    : 0;

                if ($approvedQty < 0) {
                    return response()->json(['message' => 'Approved quantity cannot be negative'], 422);
                }

                if ($approvedQty > $requestedQty) {
                    return response()->json(['message' => 'Approved quantity cannot exceed requested quantity for any item'], 422);
                }

                if ($approvedQty > $maxReleasable) {
                    return response()->json([
                        'message' => 'Approved quantity exceeds releasable inventory for one or more requested items'
                    ], 422);
                }

                $approvedCount += $approvedQty;
                if ($approvedQty < $requestedQty) {
                    $hasShortfall = true;
                }
            }

            if ($approvedCount < 1) {
                $autoRejectReason = $reason !== ''
                    ? $reason
                    : 'Auto-rejected: releasing department set all provided quantities to 0.';

                $transfer->update([
                    'ReleasingDecision' => 'rejected',
                    'ReleasingApprovedCount' => 0,
                    'ReleasingDecisionReason' => $autoRejectReason,
                    'ReleasingReviewedByUserID' => (int) $user->UserID,
                    'ReleasingReviewedAt' => now(),
                    'Status' => 'rejected',
                ]);

                AssetAuditLog::createLog(
                    'property_transfer',
                    (int) $transfer->TransferID,
                    'releasing_rejected',
                    (int) $user->UserID,
                    [
                        'transfer_number' => $transfer->TransferNumber,
                        'requested_count' => $requestedCount,
                        'approved_count' => 0,
                        'reason' => $autoRejectReason,
                    ]
                );

                foreach ($transfer->items as $item) {
                    AssetAuditLog::createLog(
                        'inventory_detail',
                        (int) $item->InventoryDetailID,
                        'transfer_releasing_rejected',
                        (int) $user->UserID,
                        [
                            'transfer_id' => (int) $transfer->TransferID,
                            'transfer_number' => $transfer->TransferNumber,
                            'reason' => $autoRejectReason,
                        ]
                    );
                }

                return response()->json([
                    'message' => 'Transfer auto-rejected because provided quantity is 0',
                    'transfer' => $transfer->fresh([
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
                        'completedBy:UserID,FullName',
                        'items.inventoryDetail.unitOfMeasure',
                        'items.inventoryDetail.propertyType',
                        'items.inventoryDetail.inventory',
                        'items.inventoryDetail.drDetail.article',
                    ]),
                ]);
            }

            if ($approvedCount > $requestedCount) {
                return response()->json(['message' => 'Approved count cannot exceed requested count'], 422);
            }

            if ($hasShortfall && $reason === '') {
                return response()->json(['message' => 'Reason is required when approved count is less than requested count'], 422);
            }

            foreach ($transfer->items as $item) {
                $itemId = (int) $item->TransferItemID;
                $requestedQty = (int) ($item->Quantity ?? 0);
                $approvedQty = $approvedQuantitiesRaw->has($itemId)
                    ? (int) $approvedQuantitiesRaw->get($itemId)
                    : $requestedQty;

                if ($approvedQty === $requestedQty) {
                    continue;
                }

                $existingRemarks = trim((string) ($item->Remarks ?? ''));
                $newRemarks = $existingRemarks;
                if ($reason !== '') {
                    $newRemarks = trim(($existingRemarks !== '' ? $existingRemarks . ' | ' : '') . 'Shortfall reason: ' . $reason);
                }

                if ($approvedQty <= 0) {
                    $item->Remarks = $newRemarks !== '' ? $newRemarks : null;
                    $item->save();
                    $item->delete();
                    continue;
                }

                $item->Quantity = $approvedQty;
                $item->Remarks = $newRemarks !== '' ? $newRemarks : null;
                $item->save();
            }

            $transfer->update([
                'ReleasingDecision' => 'approved',
                'ReleasingApprovedCount' => $approvedCount,
                'ReleasingDecisionReason' => $reason !== '' ? $reason : null,
                'ReleasingReviewedByUserID' => (int) $user->UserID,
                'ReleasingReviewedAt' => now(),
            ]);

            AssetAuditLog::createLog(
                'property_transfer',
                (int) $transfer->TransferID,
                'releasing_approved',
                (int) $user->UserID,
                [
                    'transfer_number' => $transfer->TransferNumber,
                    'requested_count' => $requestedCount,
                    'approved_count' => $approvedCount,
                    'approved_quantities' => $approvedQuantitiesRaw->all(),
                    'reason' => $reason !== '' ? $reason : null,
                ]
            );

            foreach ($transfer->items as $item) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $item->InventoryDetailID,
                    'transfer_releasing_approved',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                        'approved_count' => $approvedCount,
                    ]
                );
            }

            return response()->json([
                'message' => 'Transfer reviewed by releasing department',
                'transfer' => $transfer->fresh([
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
                    'completedBy:UserID,FullName',
                    'items.inventoryDetail.unitOfMeasure',
                    'items.inventoryDetail.propertyType',
                    'items.inventoryDetail.inventory',
                    'items.inventoryDetail.drDetail.article',
                ]),
            ]);
        });
    }

    public function reject(Request $request, int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Unauthorized to reject transfer requests'], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        $transfer = PropertyTransfer::findOrFail($id);
        if ($transfer->Status !== 'pending') {
            return response()->json(['message' => 'Only pending transfers can be rejected'], 422);
        }

        $transfer->update([
            'Status' => 'rejected',
            'ApprovedByUserID' => (int) $user->UserID,
            'ApprovedAt' => now(),
            'GsoNotes' => $validated['notes'] ?? null,
        ]);

        AssetAuditLog::createLog(
            'property_transfer',
            (int) $transfer->TransferID,
            'rejected',
            (int) $user->UserID,
            ['transfer_number' => $transfer->TransferNumber]
        );

            $transfer->loadMissing('items');
            foreach ($transfer->items as $item) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $item->InventoryDetailID,
                    'transfer_rejected',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                        'notes' => $validated['notes'] ?? null,
                    ]
                );
            }

        return response()->json(['message' => 'Transfer rejected', 'transfer' => $transfer]);
    }

    public function signReleasing(int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Only GSO/Admin can confirm releasing signature'], 403);
        }

        return DB::transaction(function () use ($id, $user) {
            $transfer = PropertyTransfer::lockForUpdate()->findOrFail($id);

            if ($transfer->Status !== 'approved') {
                return response()->json(['message' => 'Releasing signature is only allowed for approved transfers'], 422);
            }

            if ($transfer->BorrowerSignedAt) {
                return response()->json(['message' => 'Releasing signature already confirmed'], 422);
            }

            $transfer->update([
                'BorrowerSignedAt' => now(),
            ]);

            AssetAuditLog::createLog(
                'property_transfer',
                (int) $transfer->TransferID,
                'releasing_signed',
                (int) $user->UserID,
                ['transfer_number' => $transfer->TransferNumber]
            );

            $transfer->loadMissing('items');
            foreach ($transfer->items as $item) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $item->InventoryDetailID,
                    'transfer_releasing_signed',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                    ]
                );
            }

            return response()->json(['message' => 'Releasing signature confirmed', 'transfer' => $transfer]);
        });
    }

    public function signReceiving(int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if ($roleName !== self::ROLE_DEPARTMENT_HEAD) {
            return response()->json(['message' => 'Only receiving department can confirm receipt'], 403);
        }

        return DB::transaction(function () use ($id, $user) {
            $transfer = PropertyTransfer::lockForUpdate()->findOrFail($id);

            if ((int) ($user?->DepartmentID ?? 0) !== (int) $transfer->ToDepartmentID) {
                return response()->json(['message' => 'Only the receiving department can confirm receipt'], 403);
            }

            if ($transfer->Status !== 'approved') {
                return response()->json(['message' => 'Receiving signature is only allowed for approved transfers'], 422);
            }

            if (!$transfer->BorrowerSignedAt) {
                return response()->json(['message' => 'GSO releasing signature is required before receiving can sign'], 422);
            }

            if ($transfer->BorrowingSignedAt) {
                return response()->json(['message' => 'Receiving signature already confirmed'], 422);
            }

            $transfer->update([
                'BorrowingSignedAt' => now(),
            ]);

            AssetAuditLog::createLog(
                'property_transfer',
                (int) $transfer->TransferID,
                'receiving_signed',
                (int) $user->UserID,
                ['transfer_number' => $transfer->TransferNumber]
            );

            $transfer->loadMissing('items');
            foreach ($transfer->items as $item) {
                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $item->InventoryDetailID,
                    'transfer_receiving_signed',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                    ]
                );
            }

            $completionTime = now();
            foreach ($transfer->items as $item) {
                $assignedProperty = AssignedProperty::where('InventoryDetailID', $item->InventoryDetailID)
                    ->where('DepartmentID', (int) $transfer->FromDepartmentID)
                    ->where('EndUserID', (int) $transfer->FromEndUserID)
                    ->where('PropertyStatusID', self::PROPERTY_STATUS_ISSUED)
                    ->lockForUpdate()
                    ->first();

                if (!$assignedProperty) {
                    return response()->json(['message' => 'Assigned issued property missing for an item or custody mismatch'], 422);
                }

                $inventoryDetail = DB::table('propertyinventorydetail')
                    ->where('InventoryDetailID', (int) $item->InventoryDetailID)
                    ->lockForUpdate()
                    ->first(['InventoryDetailID', 'PropertyStatusID']);

                if (!$inventoryDetail || (int) ($inventoryDetail->PropertyStatusID ?? 0) !== self::PROPERTY_STATUS_ISSUED) {
                    return response()->json(['message' => 'Only issued items can be transferred'], 422);
                }

                $assignedProperty->fill([
                    'EndUserID' => (int) $transfer->ToEndUserID,
                    'DepartmentID' => (int) $transfer->ToDepartmentID,
                    'PropertyCardNumber' => $transfer->TransferNumber,
                    'AssignedDate' => $completionTime->toDateString(),
                    'PropertyStatusID' => self::PROPERTY_STATUS_ISSUED,
                ]);
                $assignedProperty->save();

                DB::table('propertyinventorydetail')
                    ->where('InventoryDetailID', (int) $item->InventoryDetailID)
                    ->update(['PropertyStatusID' => self::PROPERTY_STATUS_ISSUED]);

                $this->updateQrCodeForAssignedProperty($assignedProperty);

                AssetAuditLog::createLog(
                    'property_transfer_item',
                    (int) $item->TransferItemID,
                    'transferred',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                        'inventory_detail_id' => (int) $item->InventoryDetailID,
                        'from_department_id' => (int) $transfer->FromDepartmentID,
                        'to_department_id' => (int) $transfer->ToDepartmentID,
                    ]
                );

                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $item->InventoryDetailID,
                    'transfer_completed',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                        'from_department_id' => (int) $transfer->FromDepartmentID,
                        'to_department_id' => (int) $transfer->ToDepartmentID,
                        'from_end_user_id' => (int) $transfer->FromEndUserID,
                        'to_end_user_id' => (int) $transfer->ToEndUserID,
                    ]
                );
            }

            $transfer->update([
                'Status' => 'completed',
                'CompletedByUserID' => (int) $user->UserID,
                'CompletedAt' => $completionTime,
            ]);

            AssetAuditLog::createLog(
                'property_transfer',
                (int) $transfer->TransferID,
                'completed',
                (int) $user->UserID,
                ['transfer_number' => $transfer->TransferNumber]
            );

            return response()->json([
                'message' => 'Receiving signature confirmed and transfer completed automatically',
                'transfer' => $transfer,
            ]);
        });
    }

    public function complete(Request $request, int $id)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->isPrivileged($roleName)) {
            return response()->json(['message' => 'Unauthorized to complete transfers'], 403);
        }

        $validated = $request->validate([
            'borrower_signed_at' => 'nullable|date',
            'borrowing_signed_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        return DB::transaction(function () use ($validated, $user, $id) {
            $transfer = PropertyTransfer::with(['items'])->lockForUpdate()->findOrFail($id);

            if ($transfer->Status !== 'approved') {
                return response()->json(['message' => 'Only approved transfers can be completed'], 422);
            }

            if (!$transfer->BorrowerSignedAt) {
                return response()->json(['message' => 'GSO releasing signature must be confirmed before completion'], 422);
            }

            if (!$transfer->BorrowingSignedAt) {
                return response()->json(['message' => 'Receiving signature must be confirmed before completion'], 422);
            }

            $completionTime = now();
            $borrowerSignedAt = Carbon::parse((string) $transfer->BorrowerSignedAt);
            $borrowingSignedAt = Carbon::parse((string) $transfer->BorrowingSignedAt);

            foreach ($transfer->items as $item) {
                $assignedProperty = AssignedProperty::where('InventoryDetailID', $item->InventoryDetailID)
                    ->where('DepartmentID', (int) $transfer->FromDepartmentID)
                    ->where('EndUserID', (int) $transfer->FromEndUserID)
                    ->where('PropertyStatusID', self::PROPERTY_STATUS_ISSUED)
                    ->lockForUpdate()
                    ->first();

                if (!$assignedProperty) {
                    return response()->json(['message' => 'Assigned issued property missing for an item or custody mismatch'], 422);
                }

                $inventoryDetail = DB::table('propertyinventorydetail')
                    ->where('InventoryDetailID', (int) $item->InventoryDetailID)
                    ->lockForUpdate()
                    ->first(['InventoryDetailID', 'PropertyStatusID']);

                if (!$inventoryDetail || (int) ($inventoryDetail->PropertyStatusID ?? 0) !== self::PROPERTY_STATUS_ISSUED) {
                    return response()->json(['message' => 'Only issued items can be transferred'], 422);
                }

                $assignedProperty->fill([
                    'EndUserID' => (int) $transfer->ToEndUserID,
                    'DepartmentID' => (int) $transfer->ToDepartmentID,
                    'PropertyCardNumber' => $transfer->TransferNumber,
                    'AssignedDate' => $completionTime->toDateString(),
                    'PropertyStatusID' => self::PROPERTY_STATUS_ISSUED,
                ]);
                $assignedProperty->save();

                DB::table('propertyinventorydetail')
                    ->where('InventoryDetailID', (int) $item->InventoryDetailID)
                    ->update(['PropertyStatusID' => self::PROPERTY_STATUS_ISSUED]);

                $this->updateQrCodeForAssignedProperty($assignedProperty);

                AssetAuditLog::createLog(
                    'property_transfer_item',
                    (int) $item->TransferItemID,
                    'transferred',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                        'inventory_detail_id' => (int) $item->InventoryDetailID,
                        'from_department_id' => (int) $transfer->FromDepartmentID,
                        'to_department_id' => (int) $transfer->ToDepartmentID,
                    ]
                );

                AssetAuditLog::createLog(
                    'inventory_detail',
                    (int) $item->InventoryDetailID,
                    'transfer_completed',
                    (int) $user->UserID,
                    [
                        'transfer_id' => (int) $transfer->TransferID,
                        'transfer_number' => $transfer->TransferNumber,
                        'from_department_id' => (int) $transfer->FromDepartmentID,
                        'to_department_id' => (int) $transfer->ToDepartmentID,
                        'from_end_user_id' => (int) $transfer->FromEndUserID,
                        'to_end_user_id' => (int) $transfer->ToEndUserID,
                    ]
                );
            }

            $transfer->update([
                'Status' => 'completed',
                'CompletedByUserID' => (int) $user->UserID,
                'CompletedAt' => $completionTime,
                'BorrowerSignedAt' => $borrowerSignedAt,
                'BorrowingSignedAt' => $borrowingSignedAt,
                'GsoNotes' => $validated['notes'] ?? $transfer->GsoNotes,
            ]);

            AssetAuditLog::createLog(
                'property_transfer',
                (int) $transfer->TransferID,
                'completed',
                (int) $user->UserID,
                ['transfer_number' => $transfer->TransferNumber]
            );

            return response()->json(['message' => 'Transfer completed', 'transfer' => $transfer]);
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
            $q->whereIn('Status', ['pending', 'approved', 'borrowed']);
        })->pluck('InventoryDetailID');

        $query = AssignedProperty::query()
            ->leftJoin('propertyinventorydetail as pid', 'assignedproperty.InventoryDetailID', '=', 'pid.InventoryDetailID')
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
                'drd.ItemDescription as ItemDescription',
                'a.ArticleName as ArticleName',
                'uom.UnitName',
                'd.DepartmentName',
                'eu.FullName as EndUserName',
            ])
            ->where('assignedproperty.PropertyStatusID', self::PROPERTY_STATUS_ISSUED)
            ->where('pid.PropertyStatusID', self::PROPERTY_STATUS_ISSUED)
            ->whereNotIn('assignedproperty.InventoryDetailID', $openTransferIds);

        if ($openBorrowIds->isNotEmpty()) {
            $query->whereNotIn('assignedproperty.InventoryDetailID', $openBorrowIds);
        }

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

    private function isPrivileged(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true);
    }

    private function canCreate(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF, self::ROLE_DEPARTMENT_HEAD], true);
    }

    private function updateQrCodeForAssignedProperty(AssignedProperty $assignedProperty): void
    {
        $baseUrl = rtrim((string) (config('app.url') ?: env('APP_URL', '')), '/');
        if ($baseUrl === '') {
            $baseUrl = 'http://localhost:3000';
        }

        $hash = substr(hash_hmac('sha256', (string) $assignedProperty->PropertyID, (string) config('app.key')), 0, 12);
        $payload = $baseUrl . '/qr/scan/' . $assignedProperty->PropertyID . '?h=' . $hash;

        QrCode::updateOrCreate(
            ['InventoryDetailID' => $assignedProperty->InventoryDetailID],
            [
                'PropertyID' => (int) $assignedProperty->PropertyID,
                'DepartmentID' => (int) $assignedProperty->DepartmentID,
                'QRCodeData' => $payload,
                'DateGenerated' => now(),
            ]
        );
    }
}
