<?php

namespace App\Http\Controllers;

use App\Models\PropertyTransfer;
use App\Models\PropertyTransferItem;
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
            'approvedBy:UserID,FullName',
            'completedBy:UserID,FullName',
            'items.inventoryDetail.unitOfMeasure',
            'items.inventoryDetail.propertyType',
            'items.inventoryDetail.inventory',
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

        return DB::transaction(function () use ($validated, $user, $inventoryDetailIds) {
            $assignedProperties = AssignedProperty::whereIn('InventoryDetailID', $inventoryDetailIds)
                ->lockForUpdate()
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
            }

            $transfer = PropertyTransfer::create([
                'TransferNumber' => PropertyTransfer::generateTransferNumber(),
                'RequestedByUserID' => (int) $user->UserID,
                'FromDepartmentID' => (int) $validated['from_department_id'],
                'ToDepartmentID' => (int) $validated['to_department_id'],
                'FromEndUserID' => (int) $validated['from_end_user_id'],
                'ToEndUserID' => (int) $validated['to_end_user_id'],
                'Reason' => $validated['reason'] ?? null,
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

            $transfer->load([
                'requestedBy:UserID,FullName,DepartmentID',
                'requestedBy.department:DepartmentID,DepartmentName',
                'fromDepartment:DepartmentID,DepartmentName',
                'toDepartment:DepartmentID,DepartmentName',
                'fromEndUser:EndUserID,FullName,DepartmentID',
                'fromEndUser.department:DepartmentID,DepartmentName',
                'toEndUser:EndUserID,FullName,DepartmentID',
                'toEndUser.department:DepartmentID,DepartmentName',
                'items.inventoryDetail.unitOfMeasure',
                'items.inventoryDetail.inventory',
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

        return response()->json(['message' => 'Transfer approved', 'transfer' => $transfer]);
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

        return response()->json(['message' => 'Transfer rejected', 'transfer' => $transfer]);
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

            $completionTime = now();
            $borrowerSignedAt = $validated['borrower_signed_at']
                ? Carbon::parse($validated['borrower_signed_at'])
                : $completionTime;
            $borrowingSignedAt = $validated['borrowing_signed_at']
                ? Carbon::parse($validated['borrowing_signed_at'])
                : $completionTime;

            foreach ($transfer->items as $item) {
                $assignedProperty = AssignedProperty::where('InventoryDetailID', $item->InventoryDetailID)
                    ->lockForUpdate()
                    ->first();

                if (!$assignedProperty) {
                    return response()->json(['message' => 'Assigned property missing for an item'], 422);
                }

                if ((int) $assignedProperty->DepartmentID !== (int) $transfer->FromDepartmentID) {
                    return response()->json(['message' => 'Assigned property department mismatch'], 422);
                }

                $assignedProperty->fill([
                    'EndUserID' => (int) $transfer->ToEndUserID,
                    'DepartmentID' => (int) $transfer->ToDepartmentID,
                    'PropertyCardNumber' => $transfer->TransferNumber,
                    'AssignedDate' => $completionTime->toDateString(),
                    'PropertyStatusID' => 2,
                ]);
                $assignedProperty->save();

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
            ->whereNotIn('assignedproperty.InventoryDetailID', $openTransferIds);

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
