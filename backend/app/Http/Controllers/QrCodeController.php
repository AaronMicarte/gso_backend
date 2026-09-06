<?php

namespace App\Http\Controllers;

use App\Models\AssignedProperty;
use App\Models\MasterData\Condition;
use App\Models\PropertyIssuanceDetail;
use App\Models\QrCode;
use App\Http\Resources\Qr\QrAssignedPropertyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QrCodeController extends Controller
{
    // GET /api/qr/properties
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = max(10, (int) $request->query('per_page', 20));
        $search = trim((string) $request->query('search', ''));
        $withQrOnly = $request->boolean('with_qr_only', false);
        $missingQrOnly = $request->boolean('missing_qr_only', false);

        $query = AssignedProperty::query()->select('assignedproperty.*');

        // Add most-recent requester full name as a subquery field.
        // IMPORTANT: In this DB, `assetrequest.RequestedBy` points to the legacy `user`.`UserID` (with `FullName`),
        // not the Laravel `users` table.
        $query->selectRaw('(select u.FullName from propertyissuancedetail as pid inner join propertyissuance as pi on pid.IssuanceID = pi.IssuanceID inner join assetrequest as ar on pi.RequestID = ar.RequestID inner join user as u on ar.RequestedBy = u.UserID where pid.InventoryDetailID = assignedproperty.InventoryDetailID order by pid.IssuanceDetailID desc limit 1) as RequesterFullName');

        // Add most-recent document type as a subquery field
        $query->selectRaw('(select pi.DocumentType from propertyissuancedetail as pid inner join propertyissuance as pi on pid.IssuanceID = pi.IssuanceID where pid.InventoryDetailID = assignedproperty.InventoryDetailID order by pid.IssuanceDetailID desc limit 1) as DocumentType');
        $query->selectRaw("(select u.FullName from user as u inner join userrole as ur on ur.UserRoleID = u.UserRoleID where u.IsActive = 1 and ur.RoleName = 'Department Head' and u.DepartmentID = assignedproperty.DepartmentID order by coalesce(u.UpdatedDate, u.CreatedDate) desc, u.UserID desc limit 1) as CurrentDepartmentHead");

        $query->with([
            'inventoryDetail.drDetail.article',
            'inventoryDetail.unitOfMeasure',
            'endUser.department',
            'department',
            'propertyStatus',
            'condition',
            'qrCode',
        ]);

        // Role-based filtering
        $roleName = $user?->userRole?->RoleName ?? '';
        $isPrivileged = in_array($roleName, ['Admin', 'GSO Staff'], true);
        if (!$isPrivileged) {
            $query->where('DepartmentID', $user?->DepartmentID);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('PropertyCardNumber', 'like', "%{$search}%")
                    ->orWhere('Description', 'like', "%{$search}%")
                    ->orWhereHas('inventoryDetail', function ($iq) use ($search) {
                        $iq->where('PropertyNumber', 'like', "%{$search}%")
                            ->orWhere('SerialNumber', 'like', "%{$search}%")
                            ->orWhere('Brand', 'like', "%{$search}%")
                            ->orWhere('ModelNumber', 'like', "%{$search}%");
                    })
                    ->orWhereHas('endUser', function ($eq) use ($search) {
                        $eq->where('FullName', 'like', "%{$search}%");
                    });
            });
        }

        if ($withQrOnly) {
            $query->whereHas('qrCode');
        }

        if ($missingQrOnly) {
            $query->whereDoesntHave('qrCode');
        }

        $paginated = $query->orderByDesc('PropertyID')->paginate($perPage);
        return QrAssignedPropertyResource::collection($paginated);
    }

    // POST /api/qr/properties/{propertyId}/generate
    public function generate(Request $request, int $propertyId)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        $isPrivileged = in_array($roleName, ['Admin', 'GSO Staff'], true);
        if (!$isPrivileged) {
            return response()->json(['message' => 'Unauthorized to generate QR codes'], 403);
        }

        $assignedProperty = AssignedProperty::with(['inventoryDetail'])->findOrFail($propertyId);

        $baseUrl = rtrim((string) (config('app.url') ?: env('APP_URL', '')), '/');
        if ($baseUrl === '') {
            $baseUrl = 'http://localhost:3000';
        }

        $hash = substr(hash_hmac('sha256', (string) $assignedProperty->PropertyID, (string) config('app.key')), 0, 12);
        $payload = $baseUrl . '/qr/scan/' . $assignedProperty->PropertyID . '?h=' . $hash;

        $qr = QrCode::updateOrCreate(
            ['PropertyID' => (int) $assignedProperty->PropertyID],
            [
                'InventoryDetailID' => $assignedProperty->InventoryDetailID,
                'DepartmentID' => (int) $assignedProperty->DepartmentID,
                'QRCodeData' => $payload,
                'DateGenerated' => now(),
            ]
        );

        return response()->json([
            'message' => 'QR generated',
            'qr' => $qr,
        ], 201);
    }

    // PUT /api/qr/properties/{propertyId}
    public function update(Request $request, int $propertyId)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        $isPrivileged = in_array($roleName, ['Admin', 'GSO Staff'], true);

        $assignedProperty = AssignedProperty::find($propertyId);
        if (!$assignedProperty) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        if (!$isPrivileged && $assignedProperty->DepartmentID !== $user?->DepartmentID) {
            return response()->json(['message' => 'Unauthorized to update this property'], 403);
        }

        $validator = Validator::make($request->all(), [
            'condition_id' => 'required|integer|exists:condition,ConditionID',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $conditionId = (int) $request->input('condition_id');
        $condition = Condition::find($conditionId);

        if (!$condition || !in_array(\strtolower($condition->ConditionName), ['serviceable', 'unserviceable'], true)) {
            return response()->json([
                'message' => 'Condition must be Serviceable or Unserviceable',
            ], 422);
        }

        $assignedProperty->ConditionID = $conditionId;
        $assignedProperty->UpdatedDate = now();
        $assignedProperty->save();

        // Keep source inventory condition in sync with assigned property condition.
        if ($assignedProperty->InventoryDetailID) {
            $inventoryDetail = \App\Models\Inventory\PropertyInventoryDetail::find($assignedProperty->InventoryDetailID);
            if ($inventoryDetail) {
                $inventoryDetail->ConditionID = $conditionId;
                $inventoryDetail->save();
            }
        }

        return response()->json([
            'message' => 'Property condition updated',
            'property' => (new QrAssignedPropertyResource($assignedProperty))->toArray($request),
        ]);
    }

    // GET /api/qr/scan/{propertyId}
    public function scan(Request $request, int $propertyId)
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        $isPrivileged = in_array($roleName, ['Admin', 'GSO Staff'], true);

        $assignedProperty = AssignedProperty::query()
            ->select('assignedproperty.*')
            ->selectRaw('(select u.FullName from propertyissuancedetail as pid inner join propertyissuance as pi on pid.IssuanceID = pi.IssuanceID inner join assetrequest as ar on pi.RequestID = ar.RequestID inner join user as u on ar.RequestedBy = u.UserID where pid.InventoryDetailID = assignedproperty.InventoryDetailID order by pid.IssuanceDetailID desc limit 1) as RequesterFullName')
            ->selectRaw('(select pi.DocumentType from propertyissuancedetail as pid inner join propertyissuance as pi on pid.IssuanceID = pi.IssuanceID where pid.InventoryDetailID = assignedproperty.InventoryDetailID order by pid.IssuanceDetailID desc limit 1) as DocumentType')
            ->selectRaw("(select u.FullName from user as u inner join userrole as ur on ur.UserRoleID = u.UserRoleID where u.IsActive = 1 and ur.RoleName = 'Department Head' and u.DepartmentID = assignedproperty.DepartmentID order by coalesce(u.UpdatedDate, u.CreatedDate) desc, u.UserID desc limit 1) as CurrentDepartmentHead")
            ->with([
            'inventoryDetail.drDetail',
            'inventoryDetail.unitOfMeasure',
            'endUser.department',
            'department',
            'propertyStatus',
            'condition',
            'qrCode',
        ])->findOrFail($propertyId);

        if (!$isPrivileged && $assignedProperty->DepartmentID !== $user?->DepartmentID) {
            return response()->json(['message' => 'Unauthorized to view this property'], 403);
        }

        $latestIssuanceDetail = PropertyIssuanceDetail::with([
            'issuance.issuedBy:UserID,FullName',
            'issuance.request.department:DepartmentID,DepartmentName,DepartmentCode',
        ])
            ->where('InventoryDetailID', $assignedProperty->InventoryDetailID)
            ->orderByDesc('IssuanceDetailID')
            ->first();

        return response()->json([
            'property' => (new QrAssignedPropertyResource($assignedProperty))->toArray($request),
            'latest_issuance_detail' => $latestIssuanceDetail,
        ]);
    }

    // GET or POST /api/qr/lookup
    public function lookup(Request $request)
    {
        $raw = trim((string) ($request->input('query') ?? $request->input('q') ?? $request->input('property_number') ?? $request->input('qr_value') ?? ''));

        if ($raw === '') {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'Please provide a QR code, property number, or property ID.',
            ], 422);
        }

        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';
        $isPrivileged = in_array($roleName, ['Admin', 'GSO Staff'], true);

        $propertyId = null;
        $inventoryDetailId = null;

        if (preg_match('/\/qr\/scan\/(\d+)/i', $raw, $matches)) {
            $propertyId = (int) $matches[1];
        } elseif (preg_match('/detail=(\d+)/i', $raw, $matches)) {
            $inventoryDetailId = (int) $matches[1];
        } elseif (preg_match('/\/inventory\/(\d+)/i', $raw, $matches)) {
            $inventoryDetailId = (int) $matches[1];
        }



        $propertyNumber = $raw;

        $query = AssignedProperty::query()
            ->select('assignedproperty.*')
            ->selectRaw('(select u.FullName from propertyissuancedetail as pid inner join propertyissuance as pi on pid.IssuanceID = pi.IssuanceID inner join assetrequest as ar on pi.RequestID = ar.RequestID inner join user as u on ar.RequestedBy = u.UserID where pid.InventoryDetailID = assignedproperty.InventoryDetailID order by pid.IssuanceDetailID desc limit 1) as RequesterFullName')
            ->selectRaw('(select pi.DocumentType from propertyissuancedetail as pid inner join propertyissuance as pi on pid.IssuanceID = pi.IssuanceID where pid.InventoryDetailID = assignedproperty.InventoryDetailID order by pid.IssuanceDetailID desc limit 1) as DocumentType')
            ->selectRaw("(select u.FullName from user as u inner join userrole as ur on ur.UserRoleID = u.UserRoleID where u.IsActive = 1 and ur.RoleName = 'Department Head' and u.DepartmentID = assignedproperty.DepartmentID order by coalesce(u.UpdatedDate, u.CreatedDate) desc, u.UserID desc limit 1) as CurrentDepartmentHead")
            ->with([
                'inventoryDetail.drDetail.article',
                'inventoryDetail.drDetail.deliveryReceipt.supplier',
                'inventoryDetail.propertyType.propertyClassification',
                'inventoryDetail.unitOfMeasure',
                'endUser.department',
                'department',
                'propertyStatus',
                'condition',
                'qrCode',
            ]);

        $assignedProperty = null;

        if ($propertyId) {
            $assignedProperty = (clone $query)->where('PropertyID', $propertyId)->first();
        }

        if (!$assignedProperty && $inventoryDetailId) {
            $assignedProperty = (clone $query)->where('InventoryDetailID', $inventoryDetailId)->first();
        }

        if (!$assignedProperty && !empty($propertyNumber)) {
            $cleanNumber = trim($propertyNumber);
            $padded = ctype_digit($cleanNumber) ? sprintf('%06d', (int) $cleanNumber) : null;

            $assignedProperty = (clone $query)
                ->where(function ($q) use ($cleanNumber, $padded) {
                    $q->where('PropertyCardNumber', $cleanNumber)
                        ->orWhereHas('inventoryDetail', function ($iq) use ($cleanNumber, $padded) {
                            $iq->where('PropertyNumber', $cleanNumber)
                                ->orWhere('SerialNumber', $cleanNumber);
                            if ($padded) {
                                $iq->orWhere('PropertyNumber', 'like', "%-{$padded}");
                            }
                        });
                    if ($padded) {
                        $q->orWhere('PropertyCardNumber', 'like', "%-{$padded}")
                            ->orWhere('PropertyCardNumber', 'like', "%-" . (int) $cleanNumber);
                    }
                })
                ->first();
        }

        if ($assignedProperty) {
            if (!$isPrivileged && $assignedProperty->DepartmentID !== $user?->DepartmentID) {
                return response()->json([
                    'success' => false,
                    'found' => true,
                    'message' => 'This property belongs to another department. You do not have permission to view its full details.',
                ], 403);
            }

            $latestIssuanceDetail = PropertyIssuanceDetail::with([
                'issuance.issuedBy:UserID,FullName',
                'issuance.request.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
                ->where('InventoryDetailID', $assignedProperty->InventoryDetailID)
                ->orderByDesc('IssuanceDetailID')
                ->first();

            return response()->json([
                'success' => true,
                'found' => true,
                'type' => 'assigned',
                'property' => (new QrAssignedPropertyResource($assignedProperty))->toArray($request),
                'latest_issuance_detail' => $latestIssuanceDetail,
            ]);
        }

        $invQuery = \App\Models\Inventory\PropertyInventoryDetail::query()
            ->with([
                'drDetail.article',
                'drDetail.deliveryReceipt.supplier',
                'propertyType.propertyClassification',
                'unitOfMeasure',
                'condition',
            ]);

        $inventoryDetail = null;

        if ($inventoryDetailId) {
            $inventoryDetail = (clone $invQuery)->where('InventoryDetailID', $inventoryDetailId)->first();
        }

        if (!$inventoryDetail && !empty($propertyNumber)) {
            $cleanNumber = trim($propertyNumber);
            $padded = ctype_digit($cleanNumber) ? sprintf('%06d', (int) $cleanNumber) : null;

            $inventoryDetail = (clone $invQuery)
                ->where(function ($q) use ($cleanNumber, $padded) {
                    $q->where('PropertyNumber', $cleanNumber)
                        ->orWhere('SerialNumber', $cleanNumber);
                    if ($padded) {
                        $q->orWhere('PropertyNumber', 'like', "%-{$padded}");
                    }
                })
                ->first();
        }

        if ($inventoryDetail) {
            return response()->json([
                'success' => true,
                'found' => true,
                'type' => 'inventory_unassigned',
                'inventory_detail' => [
                    'InventoryDetailID' => $inventoryDetail->InventoryDetailID,
                    'PropertyNumber' => $inventoryDetail->PropertyNumber,
                    'Description' => $inventoryDetail->drDetail?->ItemDescription ?? $inventoryDetail->Description,
                    'Brand' => $inventoryDetail->Brand,
                    'ModelNumber' => $inventoryDetail->ModelNumber,
                    'SerialNumber' => $inventoryDetail->SerialNumber,
                    'UnitCost' => $inventoryDetail->UnitCost,
                    'UnitName' => $inventoryDetail->unitOfMeasure?->UnitName,
                    'PropertyName' => $inventoryDetail->propertyType?->PropertyName,
                    'ClassificationName' => $inventoryDetail->propertyType?->propertyClassification?->ClassificationName,
                    'ArticleName' => $inventoryDetail->drDetail?->article?->ArticleName,
                    'ConditionName' => $inventoryDetail->condition?->ConditionName ?? 'Serviceable',
                    'DRNumber' => $inventoryDetail->drDetail?->deliveryReceipt?->DRNumber,
                    'DateAcquired' => $inventoryDetail->drDetail?->deliveryReceipt?->DateReceived,
                    'Supplier' => $inventoryDetail->drDetail?->deliveryReceipt?->supplier?->SupplierName ?? null,
                    'Status' => 'In Inventory (Unassigned)',
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'found' => false,
            'message' => 'Please verify the property number.',
        ], 404);
    }
}
