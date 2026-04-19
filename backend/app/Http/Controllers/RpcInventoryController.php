<?php

namespace App\Http\Controllers;

use App\Models\AssignedProperty;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\Condition;
use App\Models\MasterData\PropertyType;
use App\Models\RpcInventoryCycle;
use App\Models\RpcInventoryScan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RpcInventoryController extends Controller
{
    private const ROLE_ADMIN = 'Admin';
    private const ROLE_GSO_STAFF = 'GSO Staff';

    private const CLASSIFICATION_SEMI_EXPENDABLE = 1;
    private const CLASSIFICATION_NON_EXPENDABLE = 2;

    public function index(Request $request): JsonResponse
    {
        $this->authorizeGso();

        $query = RpcInventoryCycle::query()
            ->withCount('scans')
            ->withCount([
                'scans as rpcppe_count' => fn ($q) => $q->where('rpc_type', 'RPCPPE'),
                'scans as rpcsep_count' => fn ($q) => $q->where('rpc_type', 'RPCSEP'),
            ])
            ->orderByDesc('start_year');

        if ($request->filled('year')) {
            $year = (int) $request->query('year');
            if ($year >= 2000 && $year <= 9999) {
                $query->where(function ($q) use ($year) {
                    $q->where('start_year', $year)->orWhere('end_year', $year);
                });
            }
        }

        $today = Carbon::now();
        $currentStartYear = (int) $today->year;
        $currentEndYear = $currentStartYear + 1;

        return response()->json([
            'data' => $query->get(),
            'current_cycle' => [
                'start_year' => $currentStartYear,
                'end_year' => $currentEndYear,
                'label' => $this->cycleLabel($currentStartYear, $currentEndYear),
            ],
        ]);
    }

    public function createCurrentCycle(): JsonResponse
    {
        $user = $this->authorizeGso();

        $startYear = (int) now()->year;
        $endYear = $startYear + 1;

        $cycle = RpcInventoryCycle::withTrashed()
            ->where('start_year', $startYear)
            ->where('end_year', $endYear)
            ->first();

        if ($cycle && $cycle->trashed()) {
            $cycle->restore();
        }

        if (!$cycle) {
            $cycle = RpcInventoryCycle::create([
                'cycle_label' => $this->cycleLabel($startYear, $endYear),
                'start_year' => $startYear,
                'end_year' => $endYear,
                'status' => 'active',
                'started_at' => now(),
                'created_by_user_id' => (int) $user->UserID,
            ]);
        }

        return response()->json([
            'message' => 'Inventory cycle ready',
            'cycle' => $cycle,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorizeGso();

        $cycle = RpcInventoryCycle::withCount('scans')
            ->withCount([
                'scans as rpcppe_count' => fn ($q) => $q->where('rpc_type', 'RPCPPE'),
                'scans as rpcsep_count' => fn ($q) => $q->where('rpc_type', 'RPCSEP'),
            ])
            ->findOrFail($id);

        return response()->json($cycle);
    }

    public function scans(Request $request, int $id): JsonResponse
    {
        $this->authorizeGso();
        $cycle = RpcInventoryCycle::findOrFail($id);

        $perPage = max(10, min(100, (int) $request->query('per_page', 25)));
        $query = RpcInventoryScan::query()
            ->where('rpc_inventory_cycle_id', (int) $cycle->id)
            ->leftJoin('propertyinventorydetail as invd', 'invd.InventoryDetailID', '=', 'rpc_inventory_scans.inventory_detail_id')
            ->leftJoin('propertytype as pt', 'pt.PropertyTypeID', '=', 'invd.PropertyTypeID')
            ->select('rpc_inventory_scans.*')
            ->selectRaw('pt.PropertyName as property_type')
            ->with(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName'])
            ->orderByDesc('scanned_at')
            ->orderByDesc('id');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('property_number', 'like', "%{$search}%")
                    ->orWhere('article', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('accountable_officer', 'like', "%{$search}%");
            });
        }

        $type = strtoupper((string) $request->query('type', ''));
        if (in_array($type, ['RPCPPE', 'RPCSEP'], true)) {
            $query->where('rpc_type', $type);
        }

        return response()->json($query->paginate($perPage));
    }

    public function previewScan(Request $request, int $id): JsonResponse
    {
        $this->authorizeGso();
        $cycle = RpcInventoryCycle::findOrFail($id);

        $validated = $request->validate([
            'property_id' => 'nullable|integer|min:1',
            'property_number' => 'nullable|string|max:100',
            'qr_value' => 'nullable|string|max:2048',
        ]);

        $propertyId = isset($validated['property_id']) ? (int) $validated['property_id'] : null;
        $propertyNumber = isset($validated['property_number'])
            ? trim((string) $validated['property_number'])
            : null;

        if (!$propertyId && !empty($validated['qr_value'])) {
            $propertyId = $this->extractPropertyId((string) $validated['qr_value']);
            if (!$propertyId && !$propertyNumber) {
                $propertyNumber = trim((string) $validated['qr_value']);
            }
        }

        if (!$propertyId && !$propertyNumber) {
            return response()->json(['message' => 'Unable to determine property from scan input'], 422);
        }

        $mapped = null;

        if ($propertyId) {
            $assignedProperty = $this->resolveAssignedProperty($propertyId);
            if ($assignedProperty) {
                $mapped = $this->mapAssignedProperty($assignedProperty);
            } else {
                $inventoryDetail = $this->resolveInventoryDetailById($propertyId);
                if ($inventoryDetail) {
                    $mapped = $this->mapInventoryDetail($inventoryDetail);
                }
            }
        } elseif ($propertyNumber) {
            $assignedProperty = $this->resolveAssignedPropertyByPropertyNumber((string) $propertyNumber);
            if ($assignedProperty) {
                $mapped = $this->mapAssignedProperty($assignedProperty);
            } else {
                $inventoryDetail = $this->resolveInventoryDetailByPropertyNumber((string) $propertyNumber);
                if ($inventoryDetail) {
                    $mapped = $this->mapInventoryDetail($inventoryDetail);
                }
            }
        }

        if (!$mapped) {
            return response()->json(['message' => 'Property not found for the provided input'], 404);
        }

        $duplicate = RpcInventoryScan::query()
            ->where('rpc_inventory_cycle_id', (int) $cycle->id)
            ->where('inventory_detail_id', (int) $mapped['inventory_detail_id'])
            ->first();

        return response()->json([
            'asset' => $mapped,
            'already_scanned' => (bool) $duplicate,
            'existing_scan_id' => $duplicate ? (int) $duplicate->id : null,
        ]);
    }

    public function storeScan(Request $request, int $id): JsonResponse
    {
        $user = $this->authorizeGso();
        $cycle = RpcInventoryCycle::findOrFail($id);

        $validated = $request->validate([
            'property_id' => 'nullable|integer|min:1',
            'inventory_detail_id' => 'nullable|integer|min:1',
            'condition_id' => 'required|integer|exists:condition,ConditionID',
            'quantity_per_count' => 'nullable|integer|in:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        $propertyId = isset($validated['property_id']) ? (int) $validated['property_id'] : null;
        $inventoryDetailId = isset($validated['inventory_detail_id']) ? (int) $validated['inventory_detail_id'] : null;

        if (!$propertyId && !$inventoryDetailId) {
            return response()->json(['message' => 'Property ID or Inventory Detail ID is required'], 422);
        }

        $mapped = null;

        if ($propertyId) {
            $assignedProperty = $this->resolveAssignedProperty($propertyId);
            if (!$assignedProperty) {
                return response()->json(['message' => 'Property not found'], 404);
            }
            $mapped = $this->mapAssignedProperty($assignedProperty);
        } elseif ($inventoryDetailId) {
            $inventoryDetail = $this->resolveInventoryDetailById($inventoryDetailId);
            if (!$inventoryDetail) {
                return response()->json(['message' => 'Inventory item not found'], 404);
            }
            $mapped = $this->mapInventoryDetail($inventoryDetail);
        }

        try {
            $scan = RpcInventoryScan::create([
                'rpc_inventory_cycle_id' => (int) $cycle->id,
                'inventory_detail_id' => (int) $mapped['inventory_detail_id'],
                'property_id' => (int) $mapped['property_id'],
                'rpc_type' => (string) $mapped['rpc_type'],
                'property_classification_id' => $mapped['property_classification_id'],
                'condition_id' => (int) $validated['condition_id'],
                'scanned_at' => now(),
                'scanned_by_user_id' => (int) $user->UserID,
                'end_user_id' => $mapped['end_user_id'],
                'department_id' => $mapped['department_id'],
                'article' => $mapped['article'],
                'description' => $mapped['description'],
                'property_number' => $mapped['property_number'],
                'unit_of_measure' => $mapped['unit_of_measure'],
                'unit_value' => $mapped['unit_value'],
                'quantity_per_card' => 1,
                'quantity_per_count' => 1,
                'remarks' => $validated['remarks'] ?? null,
                'accountable_officer' => $mapped['accountable_officer'],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                return response()->json([
                    'message' => 'This asset is already scanned in this cycle.',
                ], 409);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Asset scan recorded.',
            'scan' => $scan->load(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName']),
        ], 201);
    }

    public function updateScan(Request $request, int $scanId): JsonResponse
    {
        $this->authorizeGso();

        $scan = RpcInventoryScan::with(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName'])
            ->findOrFail($scanId);

        $validated = $request->validate([
            'condition_id' => 'required|integer|exists:condition,ConditionID',
            'quantity_per_count' => 'nullable|integer|in:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        $scan->condition_id = (int) $validated['condition_id'];
        $scan->quantity_per_count = 1;
        if (array_key_exists('remarks', $validated)) {
            $scan->remarks = $validated['remarks'];
        }
        $scan->save();

        return response()->json([
            'message' => 'Scanned asset updated.',
            'scan' => $scan->fresh(['condition:ConditionID,ConditionName', 'scannedBy:UserID,FullName']),
        ]);
    }

    public function destroyScan(int $scanId): JsonResponse
    {
        $this->authorizeGso();

        $scan = RpcInventoryScan::findOrFail($scanId);
        $scan->delete();

        return response()->json(['message' => 'Scanned asset removed.']);
    }

    public function report(int $id): JsonResponse
    {
        $this->authorizeGso();
        $cycle = RpcInventoryCycle::findOrFail($id);

        $scans = RpcInventoryScan::query()
            ->where('rpc_inventory_cycle_id', (int) $cycle->id)
            ->with(['condition:ConditionID,ConditionName'])
            ->get();

        $assigned = AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->get();

        $inventoryDetails = PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
                'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->whereHas('propertyType', function ($query) {
                $query->whereIn('PropertyClassificationID', [
                    self::CLASSIFICATION_NON_EXPENDABLE,
                    self::CLASSIFICATION_SEMI_EXPENDABLE,
                ]);
            })
            ->get();

        $scannedByDetailId = $scans->keyBy('inventory_detail_id');

        $rowsByType = [
            'RPCPPE' => [],
            'RPCSEP' => [],
        ];

        $mappedByInventoryDetail = [];

        foreach ($assigned as $property) {
            $mapped = $this->mapAssignedProperty($property);
            $rpcType = (string) $mapped['rpc_type'];
            $inventoryDetailId = (int) ($mapped['inventory_detail_id'] ?? 0);

            if (!in_array($rpcType, ['RPCPPE', 'RPCSEP'], true) || $inventoryDetailId <= 0) {
                continue;
            }

            $mappedByInventoryDetail[$inventoryDetailId] = $mapped;
        }

        foreach ($inventoryDetails as $detail) {
            $inventoryDetailId = (int) ($detail->InventoryDetailID ?? 0);
            if ($inventoryDetailId <= 0 || isset($mappedByInventoryDetail[$inventoryDetailId])) {
                continue;
            }

            $mapped = $this->mapInventoryDetail($detail);
            $rpcType = (string) $mapped['rpc_type'];
            if (!in_array($rpcType, ['RPCPPE', 'RPCSEP'], true)) {
                continue;
            }

            $mappedByInventoryDetail[$inventoryDetailId] = $mapped;
        }

        foreach ($mappedByInventoryDetail as $mapped) {
            $rpcType = (string) $mapped['rpc_type'];

            $scan = $scannedByDetailId->get((int) $mapped['inventory_detail_id']);
            $quantityCard = 1;
            $quantityCount = $scan ? (int) $scan->quantity_per_count : 0;
            $shortageOverageQty = $quantityCount - $quantityCard;

            $rowsByType[$rpcType][] = [
                'property_type' => $mapped['property_type_name'] ?? '-',
                'article' => $mapped['article'] ?? '-',
                'description' => $mapped['description'] ?? '-',
                'property_number' => $mapped['property_number'] ?? '-',
                'unit_of_measure' => $mapped['unit_of_measure'] ?? '-',
                'unit_value' => $mapped['unit_value'] !== null ? (float) $mapped['unit_value'] : 0.0,
                'quantity_per_card' => $quantityCard,
                'quantity_per_count' => $quantityCount,
                'shortage_overage_qty' => $shortageOverageQty,
                'shortage_overage_value' => round($shortageOverageQty * (float) ($mapped['unit_value'] ?? 0), 2),
                'remarks' => $scan?->remarks ?? ($scan ? null : 'not found'),
                'accountable_officer' => $mapped['accountable_officer'] ?? '-',
                'department' => $mapped['department_name'] ?? '-',
                'condition' => $scan?->condition?->ConditionName,
                'is_scanned' => (bool) $scan,
            ];
        }

        foreach (['RPCPPE', 'RPCSEP'] as $type) {
            usort($rowsByType[$type], static function (array $a, array $b) {
                $byType = strcmp((string) ($a['property_type'] ?? ''), (string) ($b['property_type'] ?? ''));
                if ($byType !== 0) {
                    return $byType;
                }

                return strcmp((string) $a['property_number'], (string) $b['property_number']);
            });
        }

        $propertyTypesByRpcType = [
            'RPCPPE' => PropertyType::query()
                ->where('PropertyClassificationID', self::CLASSIFICATION_NON_EXPENDABLE)
                ->orderBy('PropertyName')
                ->pluck('PropertyName')
                ->map(static fn ($name) => (string) $name)
                ->values()
                ->all(),
            'RPCSEP' => PropertyType::query()
                ->where('PropertyClassificationID', self::CLASSIFICATION_SEMI_EXPENDABLE)
                ->orderBy('PropertyName')
                ->pluck('PropertyName')
                ->map(static fn ($name) => (string) $name)
                ->values()
                ->all(),
        ];

        return response()->json([
            'cycle' => $cycle,
            'generated_at' => now()->toIso8601String(),
            'rpcppe' => [
                'rows' => $rowsByType['RPCPPE'],
                'property_types' => $propertyTypesByRpcType['RPCPPE'],
                'scanned_count' => count(array_filter($rowsByType['RPCPPE'], static fn ($row) => $row['is_scanned'])),
                'total_count' => count($rowsByType['RPCPPE']),
            ],
            'rpcsep' => [
                'rows' => $rowsByType['RPCSEP'],
                'property_types' => $propertyTypesByRpcType['RPCSEP'],
                'scanned_count' => count(array_filter($rowsByType['RPCSEP'], static fn ($row) => $row['is_scanned'])),
                'total_count' => count($rowsByType['RPCSEP']),
            ],
        ]);
    }

    public function conditions(): JsonResponse
    {
        $this->authorizeGso();

        $conditions = Condition::query()
            ->orderBy('ConditionName')
            ->get(['ConditionID', 'ConditionName'])
            ->map(static function ($condition) {
                return [
                    'ConditionID' => (int) $condition->ConditionID,
                    'ConditionName' => (string) $condition->ConditionName,
                ];
            })
            ->values();

        return response()->json(['data' => $conditions]);
    }

    private function authorizeGso()
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true)) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }

        return $user;
    }

    private function cycleLabel(int $startYear, int $endYear): string
    {
        return sprintf('Inventory Cycle %d-%d', $startYear, $endYear);
    }

    private function extractPropertyId(string $raw): ?int
    {
        $value = trim($raw);
        if ($value === '') {
            return null;
        }

        if (ctype_digit($value)) {
            $id = (int) $value;
            return $id > 0 ? $id : null;
        }

        if (preg_match('/\/qr\/scan\/(\d+)/', $value, $matches) === 1 && isset($matches[1])) {
            $id = (int) $matches[1];
            return $id > 0 ? $id : null;
        }

        return null;
    }

    private function resolveAssignedProperty(int $propertyId): ?AssignedProperty
    {
        return AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->find($propertyId);
    }

    private function resolveAssignedPropertyByPropertyNumber(string $propertyNumber): ?AssignedProperty
    {
        $normalized = trim($propertyNumber);
        if ($normalized === '') {
            return null;
        }

        return AssignedProperty::query()
            ->with([
                'inventoryDetail.propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'inventoryDetail.unitOfMeasure:UnitOfMeasureID,UnitName',
                'endUser:EndUserID,FullName,DepartmentID',
                'department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->whereHas('inventoryDetail', function ($query) use ($normalized) {
                $query->where('PropertyNumber', $normalized);
            })
            ->orderByDesc('PropertyID')
            ->first();
    }

    private function resolveInventoryDetailById(int $inventoryDetailId): ?PropertyInventoryDetail
    {
        return PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
                'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->find($inventoryDetailId);
    }

    private function resolveInventoryDetailByPropertyNumber(string $propertyNumber): ?PropertyInventoryDetail
    {
        $normalized = trim($propertyNumber);
        if ($normalized === '') {
            return null;
        }

        return PropertyInventoryDetail::query()
            ->with([
                'propertyType:PropertyTypeID,PropertyClassificationID,PropertyName',
                'drDetail.article:ArticleID,ArticleName',
                'unitOfMeasure:UnitOfMeasureID,UnitName',
                'assignedProperties.endUser:EndUserID,FullName,DepartmentID',
                'assignedProperties.department:DepartmentID,DepartmentName,DepartmentCode',
            ])
            ->where('PropertyNumber', $normalized)
            ->orderByDesc('InventoryDetailID')
            ->first();
    }

    /**
     * @return array<string, int|string|float|null>
     */
    private function mapAssignedProperty(AssignedProperty $property): array
    {
        $detail = $property->inventoryDetail;
        $propertyType = $detail?->propertyType;
        $classificationId = $propertyType?->PropertyClassificationID
            ? (int) $propertyType->PropertyClassificationID
            : null;

        $rpcType = $this->determineRpcType($classificationId);
        $fallback = $this->resolveCustodianFallbackByInventoryDetailId((int) ($detail?->InventoryDetailID ?? 0));

        return [
            'property_id' => (int) $property->PropertyID,
            'inventory_detail_id' => (int) ($property->InventoryDetailID ?? 0),
            'property_classification_id' => $classificationId,
            'property_type_name' => $propertyType?->PropertyName,
            'rpc_type' => $rpcType,
            'article' => $detail?->drDetail?->article?->ArticleName
                ?? $detail?->drDetail?->ItemDescription
                ?? $detail?->Description
                ?? $property->Description,
            'description' => $detail?->Description ?? $property->Description,
            'property_number' => $detail?->PropertyNumber,
            'unit_of_measure' => $detail?->unitOfMeasure?->UnitName,
            'unit_value' => $detail?->UnitCost !== null ? (float) $detail->UnitCost : null,
            'accountable_officer' => $property->endUser?->FullName ?: ($fallback['name'] ?? null),
            'end_user_id' => $property->EndUserID ? (int) $property->EndUserID : null,
            'department_id' => $property->DepartmentID ? (int) $property->DepartmentID : ($fallback['department_id'] ?? null),
            'department_name' => $property->department?->DepartmentName ?: ($fallback['department_name'] ?? null),
        ];
    }

    /**
     * @return array<string, int|string|float|null>
     */
    private function mapInventoryDetail(PropertyInventoryDetail $detail): array
    {
        $propertyType = $detail->propertyType;
        $classificationId = $propertyType?->PropertyClassificationID
            ? (int) $propertyType->PropertyClassificationID
            : null;

        $rpcType = $this->determineRpcType($classificationId);
        $latestAssigned = $detail->assignedProperties
            ->sortByDesc('PropertyID')
            ->first();
        $fallback = $this->resolveCustodianFallbackByInventoryDetailId((int) ($detail->InventoryDetailID ?? 0));

        return [
            'property_id' => $latestAssigned ? (int) $latestAssigned->PropertyID : null,
            'inventory_detail_id' => (int) ($detail->InventoryDetailID ?? 0),
            'property_classification_id' => $classificationId,
            'property_type_name' => $propertyType?->PropertyName,
            'rpc_type' => $rpcType,
            'article' => $detail->drDetail?->article?->ArticleName
                ?? $detail->drDetail?->ItemDescription
                ?? $detail->Description,
            'description' => $detail->Description,
            'property_number' => $detail->PropertyNumber,
            'unit_of_measure' => $detail->unitOfMeasure?->UnitName,
            'unit_value' => $detail->UnitCost !== null ? (float) $detail->UnitCost : null,
            'accountable_officer' => $latestAssigned?->endUser?->FullName ?: ($fallback['name'] ?? null),
            'end_user_id' => $latestAssigned?->EndUserID ? (int) $latestAssigned->EndUserID : null,
            'department_id' => $latestAssigned?->DepartmentID ? (int) $latestAssigned->DepartmentID : ($fallback['department_id'] ?? null),
            'department_name' => $latestAssigned?->department?->DepartmentName ?: ($fallback['department_name'] ?? null),
        ];
    }

    /**
     * @return array{name: string|null, department_id: int|null, department_name: string|null}
     */
    private function resolveCustodianFallbackByInventoryDetailId(int $inventoryDetailId): array
    {
        if ($inventoryDetailId <= 0) {
            return [
                'name' => null,
                'department_id' => null,
                'department_name' => null,
            ];
        }

        $issuance = DB::table('propertyissuancedetail as pidet')
            ->join('propertyissuance as pi', 'pi.IssuanceID', '=', 'pidet.IssuanceID')
            ->leftJoin('assetrequest as ar', 'ar.RequestID', '=', 'pi.RequestID')
            ->leftJoin('user as reqU', 'reqU.UserID', '=', 'ar.RequestedBy')
            ->leftJoin('enduser as eu', 'eu.EndUserID', '=', 'pi.IssuedTo')
            ->leftJoin('department as dep', 'dep.DepartmentID', '=', 'eu.DepartmentID')
            ->leftJoin('user as u', 'u.UserID', '=', 'pi.IssuedTo')
            ->leftJoin('department as depU', 'depU.DepartmentID', '=', 'u.DepartmentID')
            ->leftJoin('department as depReq', 'depReq.DepartmentID', '=', 'ar.DepartmentID')
            ->where('pidet.InventoryDetailID', $inventoryDetailId)
            ->whereNull('pi.deleted_at')
            ->whereNull('pidet.deleted_at')
            ->whereIn('pi.Status', ['pending_acknowledgment', 'acknowledged'])
            ->orderByDesc('pi.DateIssued')
            ->orderByDesc('pi.IssuanceID')
            ->select([
                'pi.DocumentType as document_type',
                'reqU.FullName as request_user_name',
                'eu.FullName as end_user_name',
                'u.FullName as issued_to_user_name',
                'pi.IssuedToName as issued_to_name',
                'dep.DepartmentID as dep_end_user_id',
                'dep.DepartmentName as dep_end_user_name',
                'depU.DepartmentID as dep_user_id',
                'depU.DepartmentName as dep_user_name',
                'depReq.DepartmentID as dep_request_id',
                'depReq.DepartmentName as dep_request_name',
            ])
            ->first();

        if ($issuance) {
            $name = null;
            if ((string) $issuance->document_type === 'ICS') {
                $name = $issuance->request_user_name ?: null;
            }

            if (!$name) {
                $name = $issuance->end_user_name
                    ?: $issuance->issued_to_user_name
                    ?: $issuance->issued_to_name
                    ?: $issuance->request_user_name
                    ?: null;
            }

            $departmentId = $issuance->dep_end_user_id
                ?: $issuance->dep_user_id
                ?: $issuance->dep_request_id
                ?: null;

            $departmentName = $issuance->dep_end_user_name
                ?: $issuance->dep_user_name
                ?: $issuance->dep_request_name
                ?: null;

            return [
                'name' => $name ? (string) $name : null,
                'department_id' => $departmentId ? (int) $departmentId : null,
                'department_name' => $departmentName ? (string) $departmentName : null,
            ];
        }

        $fromDelivery = DB::table('propertyinventorydetail as invd')
            ->join('deliveryreceiptdetail as drd', 'drd.DRDetailID', '=', 'invd.SourceDRDetailID')
            ->join('deliveryreceipt as dr', 'dr.DRID', '=', 'drd.DRID')
            ->leftJoin('user as reqU', 'reqU.UserID', '=', 'dr.RequestedBy')
            ->leftJoin('department as depReq', 'depReq.DepartmentID', '=', 'reqU.DepartmentID')
            ->leftJoin('user as recU', 'recU.UserID', '=', 'dr.ReceivedBy')
            ->leftJoin('department as depRec', 'depRec.DepartmentID', '=', 'recU.DepartmentID')
            ->where('invd.InventoryDetailID', $inventoryDetailId)
            ->select([
                'reqU.FullName as requested_by_name',
                'recU.FullName as received_by_name',
                'depReq.DepartmentID as dep_requested_id',
                'depReq.DepartmentName as dep_requested_name',
                'depRec.DepartmentID as dep_received_id',
                'depRec.DepartmentName as dep_received_name',
            ])
            ->first();

        if ($fromDelivery) {
            $name = $fromDelivery->requested_by_name ?: ($fromDelivery->received_by_name ?: null);
            $departmentId = $fromDelivery->dep_requested_id ?: ($fromDelivery->dep_received_id ?: null);
            $departmentName = $fromDelivery->dep_requested_name ?: ($fromDelivery->dep_received_name ?: null);

            return [
                'name' => $name ? (string) $name : null,
                'department_id' => $departmentId ? (int) $departmentId : null,
                'department_name' => $departmentName ? (string) $departmentName : null,
            ];
        }

        return [
            'name' => null,
            'department_id' => null,
            'department_name' => null,
        ];
    }

    private function determineRpcType(?int $classificationId): string
    {
        if ($classificationId === self::CLASSIFICATION_NON_EXPENDABLE) {
            return 'RPCPPE';
        }

        if ($classificationId === self::CLASSIFICATION_SEMI_EXPENDABLE) {
            return 'RPCSEP';
        }

        return 'RPCSEP';
    }
}
