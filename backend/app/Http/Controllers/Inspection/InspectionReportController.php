<?php

namespace App\Http\Controllers\Inspection;

use App\Http\Controllers\Controller;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Procurement\IARDetail;
use App\Models\MasterData\Condition;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\PropertyStatus;
use App\Models\AssignedProperty;
use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\EndUser;
use App\Models\QrCode;
use App\Models\AssetAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InspectionReportController extends Controller
{
    private const ROLE_ADMIN = 1;
    private const ROLE_GSO_STAFF = 2;
    private const ROLE_INSPECTION_OFFICER = 6;

    private function requireInspectionOfficer(): ?JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (! in_array($user->UserRoleID, [self::ROLE_ADMIN, self::ROLE_INSPECTION_OFFICER], true)) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return null;
    }

    private function requireReportViewer(): ?JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (! in_array($user->UserRoleID, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF, self::ROLE_INSPECTION_OFFICER], true)) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return null;
    }

    private function normalizeField($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function resolveConditionMode(?string $conditionName): string
    {
        $name = strtolower(trim((string) $conditionName));
        if (str_contains($name, 'unserviceable') || str_contains($name, 'unservicable')) {
            return 'reject';
        }
        if (str_contains($name, 'serviceable')) {
            return 'accept';
        }
        return 'reject';
    }

    public function pendingItems(Request $request): JsonResponse
    {
        if ($resp = $this->requireInspectionOfficer()) {
            return $resp;
        }

        $query = DeliveryReceipt::with([
            'details.propertyType',
            'details.unitOfMeasure',
            'requestedByUser:UserID,FullName,DepartmentID',
            'requestedByUser.department:DepartmentID,DepartmentName',
            'iar.details.condition',
            'iar.details.drDetail.unitOfMeasure',
            'iar.details.propertyType',
        ])
            ->where('InspectionStatus', 'pending')
            ->orderBy('CreatedDate', 'desc');

        $perPage = (int) $request->input('per_page', 15);
        $reports = $query->paginate($perPage);

        $reports->getCollection()->transform(function ($dr) {
            $dr->inspection_status = $dr->InspectionStatus ?? 'pending';
            $dr->RequestedByName = $dr->requestedByUser->FullName ?? null;
            $dr->DepartmentName = $dr->requestedByUser->department->DepartmentName ?? null;
            $dr->DepartmentID = $dr->requestedByUser->DepartmentID ?? null;
            return $dr;
        });

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        if ($resp = $this->requireReportViewer()) {
            return $resp;
        }

        $query = InspectionAcceptanceReport::with([
            'deliveryReceipt' => function ($q) {
                $q->with(['requestedByUser:UserID,FullName,DepartmentID', 'requestedByUser.department:DepartmentID,DepartmentName']);
            },
            'details.drDetail.unitOfMeasure',
            'details.propertyType',
            'details.condition',
            'inspectedByUser',
            'acceptedByUser',
        ])->orderBy('CreatedDate', 'desc');

        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->whereHas('deliveryReceipt', function ($q) use ($status) {
                $q->where('InspectionStatus', $status);
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $reports = $query->paginate($perPage);

        $reports->getCollection()->transform(function ($report) {
            $report->Status = $report->deliveryReceipt->InspectionStatus ?? 'pending';
            return $report;
        });

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    public function show($id): JsonResponse
    {
        if ($resp = $this->requireReportViewer()) {
            return $resp;
        }

        $report = InspectionAcceptanceReport::with([
            'deliveryReceipt' => function ($q) {
                $q->with(['requestedByUser:UserID,FullName,DepartmentID', 'requestedByUser.department:DepartmentID,DepartmentName']);
            },
            'details.drDetail.unitOfMeasure',
            'details.propertyType',
            'details.condition',
            'inspectedByUser',
            'acceptedByUser',
        ])->findOrFail($id);

        $report->Status = $report->deliveryReceipt->InspectionStatus ?? 'pending';

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if ($resp = $this->requireInspectionOfficer()) {
            return $resp;
        }

        $validator = Validator::make($request->all(), [
            'dr_id' => 'required|integer|exists:deliveryreceipt,DRID',
            'inspector_name' => 'required|string|max:150',
            'date_inspected' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.dr_detail_id' => 'required|integer|exists:deliveryreceiptdetail,DRDetailID',
            'items.*.condition_id' => 'required|integer|exists:condition,ConditionID',
            'items.*.quantity_accepted' => 'required|integer|min:0',
            'items.*.quantity_rejected' => 'required|integer|min:0',
            'items.*.verified_brand' => 'nullable|string|max:100',
            'items.*.verified_model' => 'nullable|string|max:100',
            'items.*.verified_serial' => 'nullable|string|max:100',
            'items.*.no_serial' => 'boolean',
            'items.*.remarks' => 'nullable|string',
            'items.*.change_reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $normalizedName = strtolower(preg_replace('/\s+/', ' ', trim((string) $request->inspector_name)));
        $expectedName = strtolower(preg_replace('/\s+/', ' ', trim((string) ($user->FullName ?? ''))));
        if ($normalizedName === '' || $normalizedName !== $expectedName) {
            return response()->json([
                'success' => false,
                'message' => 'Inspector name must match your account name',
                'errors' => ['inspector_name' => ['Inspector name must match your account name']],
            ], 422);
        }

        $dr = DeliveryReceipt::with('details')->findOrFail((int) $request->dr_id);
        if (($dr->InspectionStatus ?? 'pending') === 'inspected') {
            return response()->json([
                'success' => false,
                'message' => 'Delivery receipt already inspected',
            ], 409);
        }

        $existing = InspectionAcceptanceReport::where('DRID', $dr->DRID)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection report already exists for this delivery receipt',
            ], 409);
        }

        $detailIds = $dr->details->pluck('DRDetailID')->all();
        $incomingIds = collect($request->items)->pluck('dr_detail_id')->unique()->all();

        $missing = array_diff($detailIds, $incomingIds);
        $extra = array_diff($incomingIds, $detailIds);
        if (!empty($missing) || !empty($extra)) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection items must include every delivery receipt item',
                'errors' => ['items' => ['Inspection items must include every delivery receipt item']],
            ], 422);
        }

        $detailsById = $dr->details->keyBy('DRDetailID');
        $validationErrors = [];
        $conditionIds = collect($request->items)->pluck('condition_id')->filter()->unique()->all();
        $conditionMap = Condition::whereIn('ConditionID', $conditionIds)->pluck('ConditionName', 'ConditionID');

        $totalsByDetail = [];
        $verificationByDetail = [];

        $totalsByDetail = [];
        $verificationByDetail = [];

        foreach ($request->items as $index => $item) {
            $detail = $detailsById->get($item['dr_detail_id']);
            if (! $detail) {
                continue;
            }

            $brand = $this->normalizeField($item['verified_brand'] ?? $detail->Brand);
            $modelNumber = $this->normalizeField($item['verified_model'] ?? $detail->ModelNumber);
            $serialNumber = $this->normalizeField($item['verified_serial'] ?? $detail->SerialNumber);
            $noSerial = filter_var($item['no_serial'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if (! $brand || ! $modelNumber) {
                $validationErrors["items.$index"][] = 'Brand and model are required for inspection.';
            }
            if (! $noSerial && ! $serialNumber) {
                $validationErrors["items.$index"][] = 'Serial number is required or mark no serial.';
            }

            $accepted = (int) ($item['quantity_accepted'] ?? 0);
            $rejected = (int) ($item['quantity_rejected'] ?? 0);
            $delivered = (int) $detail->QuantityDelivered;

            $mode = $this->resolveConditionMode($conditionMap->get($item['condition_id']) ?? null);
            if ($accepted + $rejected <= 0) {
                $validationErrors["items.$index.quantity_accepted"][] = 'Quantity must be greater than 0.';
            }
            if ($mode === 'accept') {
                if ($rejected > 0) {
                    $validationErrors["items.$index.quantity_rejected"][] = 'Rejected quantity must be 0 for acceptable conditions.';
                }
                if ($accepted <= 0) {
                    $validationErrors["items.$index.quantity_accepted"][] = 'Accepted quantity is required for acceptable conditions.';
                }
            } else {
                if ($accepted > 0) {
                    $validationErrors["items.$index.quantity_accepted"][] = 'Accepted quantity must be 0 for reject-only conditions.';
                }
                if ($rejected <= 0) {
                    $validationErrors["items.$index.quantity_rejected"][] = 'Rejected quantity is required for reject-only conditions.';
                }
            }

            if ($rejected > 0 && $this->normalizeField($item['remarks'] ?? null) === null) {
                $validationErrors["items.$index.remarks"][] = 'Reject reason is required when rejecting quantity.';
            }

            $detailId = (int) $detail->DRDetailID;
            if (! isset($totalsByDetail[$detailId])) {
                $totalsByDetail[$detailId] = ['accepted' => 0, 'rejected' => 0, 'delivered' => $delivered];
            }
            $totalsByDetail[$detailId]['accepted'] += $accepted;
            $totalsByDetail[$detailId]['rejected'] += $rejected;

            $verificationKey = [$brand, $modelNumber, $serialNumber, $noSerial ? 1 : 0];
            if (! isset($verificationByDetail[$detailId])) {
                $verificationByDetail[$detailId] = $verificationKey;
            } elseif ($verificationByDetail[$detailId] !== $verificationKey) {
                $validationErrors["items.$index"][] = 'Verified brand/model/serial must be consistent for the same item.';
            }

            $changes = [];
            if ($brand !== $this->normalizeField($detail->Brand)) {
                $changes['Brand'] = [$detail->Brand, $brand];
            }
            if ($modelNumber !== $this->normalizeField($detail->ModelNumber)) {
                $changes['ModelNumber'] = [$detail->ModelNumber, $modelNumber];
            }
            if ($serialNumber !== $this->normalizeField($detail->SerialNumber)) {
                $changes['SerialNumber'] = [$detail->SerialNumber, $serialNumber];
            }

            if (! empty($changes)) {
                $reason = trim((string) ($item['change_reason'] ?? ''));
                if ($reason === '') {
                    $validationErrors["items.$index.change_reason"][] = 'Reason is required when updating brand/model/serial.';
                }
            }
        }

        foreach ($totalsByDetail as $detailId => $totals) {
            if (($totals['accepted'] + $totals['rejected']) !== $totals['delivered']) {
                $validationErrors["items.$detailId"][] = 'Accepted + rejected must equal delivered quantity.';
            }
        }

        if (! empty($validationErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validationErrors,
            ], 422);
        }

        DB::beginTransaction();
        try {
            $iarNumber = 'IAR-' . date('Y') . '-' . str_pad(InspectionAcceptanceReport::count() + 1, 4, '0', STR_PAD_LEFT);
            $conditionIds = collect($request->items)->pluck('condition_id')->filter()->unique()->all();
            $conditionMap = Condition::whereIn('ConditionID', $conditionIds)->pluck('ConditionName', 'ConditionID');
            $report = InspectionAcceptanceReport::create([
                'IARNumber' => $iarNumber,
                'DRID' => $dr->DRID,
                'InspectedBy' => $user->UserID,
                'AcceptedBy' => null,
                'DateInspected' => $request->date_inspected,
                'DateAccepted' => null,
                'Remarks' => $request->remarks,
            ]);

            foreach ($request->items as $item) {
                $detail = $detailsById->get($item['dr_detail_id']);
                $brand = $this->normalizeField($item['verified_brand'] ?? $detail?->Brand);
                $modelNumber = $this->normalizeField($item['verified_model'] ?? $detail?->ModelNumber);
                $serialNumber = $this->normalizeField($item['verified_serial'] ?? $detail?->SerialNumber);
                $noSerial = filter_var($item['no_serial'] ?? false, FILTER_VALIDATE_BOOLEAN);

                IARDetail::create([
                    'IARID' => $report->IARID,
                    'DRDetailID' => $item['dr_detail_id'],
                    'PropertyTypeID' => $detail?->PropertyTypeID,
                    'ConditionID' => $item['condition_id'],
                    'QuantityAccepted' => $item['quantity_accepted'],
                    'QuantityRejected' => $item['quantity_rejected'],
                    'VerifiedBrand' => $brand,
                    'VerifiedModel' => $modelNumber,
                    'VerifiedSerial' => $serialNumber,
                    'NoSerial' => $noSerial,
                    'Remarks' => $item['remarks'] ?? null,
                    'change_reason' => $this->normalizeField($item['change_reason'] ?? null),
                ]);

                if ($detail) {
                    $changes = [];
                    if ($brand !== $this->normalizeField($detail->Brand)) {
                        $changes['Brand'] = [$detail->Brand, $brand];
                    }
                    if ($modelNumber !== $this->normalizeField($detail->ModelNumber)) {
                        $changes['ModelNumber'] = [$detail->ModelNumber, $modelNumber];
                    }
                    if ($serialNumber !== $this->normalizeField($detail->SerialNumber)) {
                        $changes['SerialNumber'] = [$detail->SerialNumber, $serialNumber];
                    }

                    if (! empty($changes)) {
                        $detail->update([
                            'Brand' => $brand,
                            'ModelNumber' => $modelNumber,
                            'SerialNumber' => $serialNumber,
                        ]);

                        AssetAuditLog::createLog(
                            'deliveryreceiptdetail',
                            (int) $detail->DRDetailID,
                            'Inspection update',
                            (int) $user->UserID,
                            [
                                'dr_id' => $dr->DRID,
                                'iar_id' => $report->IARID,
                                'reason' => trim((string) ($item['change_reason'] ?? '')),
                                'changes' => $changes,
                            ]
                        );
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save inspection report',
                'error' => $e->getMessage(),
            ], 500);
        }

        $report->load([
            'deliveryReceipt',
            'details.drDetail',
            'details.condition',
            'details.propertyType',
            'inspectedByUser',
            'acceptedByUser',
        ]);

        $report->Status = $report->deliveryReceipt->InspectionStatus ?? 'pending';

        return response()->json([
            'success' => true,
            'message' => 'Inspection report saved',
            'data' => $report,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        if ($resp = $this->requireInspectionOfficer()) {
            return $resp;
        }

        $report = InspectionAcceptanceReport::with('details')->findOrFail($id);
        if ($report->DateAccepted) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection report already accepted',
            ], 409);
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.dr_detail_id' => 'required|integer|exists:deliveryreceiptdetail,DRDetailID',
            'items.*.condition_id' => 'required|integer|exists:condition,ConditionID',
            'items.*.quantity_accepted' => 'required|integer|min:0',
            'items.*.quantity_rejected' => 'required|integer|min:0',
            'items.*.verified_brand' => 'nullable|string|max:100',
            'items.*.verified_model' => 'nullable|string|max:100',
            'items.*.verified_serial' => 'nullable|string|max:100',
            'items.*.no_serial' => 'boolean',
            'items.*.remarks' => 'nullable|string',
            'items.*.change_reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dr = DeliveryReceipt::with('details')->findOrFail($report->DRID);
        $detailsById = $dr->details->keyBy('DRDetailID');
        $detailIds = $dr->details->pluck('DRDetailID')->all();
        $incomingIds = collect($request->items)->pluck('dr_detail_id')->unique()->all();

        $missing = array_diff($detailIds, $incomingIds);
        $extra = array_diff($incomingIds, $detailIds);
        if (!empty($missing) || !empty($extra)) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection items must include every delivery receipt item',
                'errors' => ['items' => ['Inspection items must include every delivery receipt item']],
            ], 422);
        }
        $validationErrors = [];
        $conditionIds = collect($request->items)->pluck('condition_id')->filter()->unique()->all();
        $conditionMap = Condition::whereIn('ConditionID', $conditionIds)->pluck('ConditionName', 'ConditionID');
        $totalsByDetail = [];
        $verificationByDetail = [];

        foreach ($request->items as $index => $item) {
            $detail = $detailsById->get($item['dr_detail_id']);
            if (! $detail) {
                continue;
            }

            $brand = $this->normalizeField($item['verified_brand'] ?? $detail->Brand);
            $modelNumber = $this->normalizeField($item['verified_model'] ?? $detail->ModelNumber);
            $serialNumber = $this->normalizeField($item['verified_serial'] ?? $detail->SerialNumber);
            $noSerial = filter_var($item['no_serial'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if (! $brand || ! $modelNumber) {
                $validationErrors["items.$index"][] = 'Brand and model are required for inspection.';
            }
            if (! $noSerial && ! $serialNumber) {
                $validationErrors["items.$index"][] = 'Serial number is required or mark no serial.';
            }

            $accepted = (int) ($item['quantity_accepted'] ?? 0);
            $rejected = (int) ($item['quantity_rejected'] ?? 0);
            $delivered = (int) $detail->QuantityDelivered;

            $mode = $this->resolveConditionMode($conditionMap->get($item['condition_id']) ?? null);
            if ($accepted + $rejected <= 0) {
                $validationErrors["items.$index.quantity_accepted"][] = 'Quantity must be greater than 0.';
            }
            if ($mode === 'accept') {
                if ($rejected > 0) {
                    $validationErrors["items.$index.quantity_rejected"][] = 'Rejected quantity must be 0 for acceptable conditions.';
                }
                if ($accepted <= 0) {
                    $validationErrors["items.$index.quantity_accepted"][] = 'Accepted quantity is required for acceptable conditions.';
                }
            } else {
                if ($accepted > 0) {
                    $validationErrors["items.$index.quantity_accepted"][] = 'Accepted quantity must be 0 for reject-only conditions.';
                }
                if ($rejected <= 0) {
                    $validationErrors["items.$index.quantity_rejected"][] = 'Rejected quantity is required for reject-only conditions.';
                }
            }

            if ($rejected > 0 && $this->normalizeField($item['remarks'] ?? null) === null) {
                $validationErrors["items.$index.remarks"][] = 'Reject reason is required when rejecting quantity.';
            }

            $detailId = (int) $detail->DRDetailID;
            if (! isset($totalsByDetail[$detailId])) {
                $totalsByDetail[$detailId] = ['accepted' => 0, 'rejected' => 0, 'delivered' => $delivered];
            }
            $totalsByDetail[$detailId]['accepted'] += $accepted;
            $totalsByDetail[$detailId]['rejected'] += $rejected;

            $verificationKey = [$brand, $modelNumber, $serialNumber, $noSerial ? 1 : 0];
            if (! isset($verificationByDetail[$detailId])) {
                $verificationByDetail[$detailId] = $verificationKey;
            } elseif ($verificationByDetail[$detailId] !== $verificationKey) {
                $validationErrors["items.$index"][] = 'Verified brand/model/serial must be consistent for the same item.';
            }

            $changes = [];
            if ($brand !== $this->normalizeField($detail->Brand)) {
                $changes['Brand'] = [$detail->Brand, $brand];
            }
            if ($modelNumber !== $this->normalizeField($detail->ModelNumber)) {
                $changes['ModelNumber'] = [$detail->ModelNumber, $modelNumber];
            }
            if ($serialNumber !== $this->normalizeField($detail->SerialNumber)) {
                $changes['SerialNumber'] = [$detail->SerialNumber, $serialNumber];
            }

            if (! empty($changes)) {
                $reason = trim((string) ($item['change_reason'] ?? ''));
                if ($reason === '') {
                    $validationErrors["items.$index.change_reason"][] = 'Reason is required when updating brand/model/serial.';
                }
            }
        }

        foreach ($totalsByDetail as $detailId => $totals) {
            if (($totals['accepted'] + $totals['rejected']) !== $totals['delivered']) {
                $validationErrors["items.$detailId"][] = 'Accepted + rejected must equal delivered quantity.';
            }
        }

        if (! empty($validationErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validationErrors,
            ], 422);
        }
        DB::beginTransaction();
        try {
            $report->update([
                'Remarks' => $request->remarks,
            ]);

            $report->details()->delete();

            foreach ($request->items as $item) {
                $detail = $detailsById->get($item['dr_detail_id']);
                $brand = $this->normalizeField($item['verified_brand'] ?? $detail?->Brand);
                $modelNumber = $this->normalizeField($item['verified_model'] ?? $detail?->ModelNumber);
                $serialNumber = $this->normalizeField($item['verified_serial'] ?? $detail?->SerialNumber);
                $noSerial = filter_var($item['no_serial'] ?? false, FILTER_VALIDATE_BOOLEAN);

                IARDetail::create([
                    'IARID' => $report->IARID,
                    'DRDetailID' => $item['dr_detail_id'],
                    'PropertyTypeID' => $detail?->PropertyTypeID,
                    'ConditionID' => $item['condition_id'],
                    'QuantityAccepted' => $item['quantity_accepted'],
                    'QuantityRejected' => $item['quantity_rejected'],
                    'VerifiedBrand' => $brand,
                    'VerifiedModel' => $modelNumber,
                    'VerifiedSerial' => $serialNumber,
                    'NoSerial' => $noSerial,
                    'Remarks' => $item['remarks'] ?? null,
                    'change_reason' => $this->normalizeField($item['change_reason'] ?? null),
                ]);

                if ($detail) {
                    $changes = [];
                    if ($brand !== $this->normalizeField($detail->Brand)) {
                        $changes['Brand'] = [$detail->Brand, $brand];
                    }
                    if ($modelNumber !== $this->normalizeField($detail->ModelNumber)) {
                        $changes['ModelNumber'] = [$detail->ModelNumber, $modelNumber];
                    }
                    if ($serialNumber !== $this->normalizeField($detail->SerialNumber)) {
                        $changes['SerialNumber'] = [$detail->SerialNumber, $serialNumber];
                    }

                    if (! empty($changes)) {
                        $detail->update([
                            'Brand' => $brand,
                            'ModelNumber' => $modelNumber,
                            'SerialNumber' => $serialNumber,
                        ]);

                        AssetAuditLog::createLog(
                            'deliveryreceiptdetail',
                            (int) $detail->DRDetailID,
                            'Inspection update',
                            (int) $user->UserID,
                            [
                                'dr_id' => $report->DRID,
                                'iar_id' => $report->IARID,
                                'reason' => trim((string) ($item['change_reason'] ?? '')),
                                'changes' => $changes,
                            ]
                        );
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inspection report',
                'error' => $e->getMessage(),
            ], 500);
        }

        $report->load(['details.drDetail', 'details.condition', 'details.propertyType', 'deliveryReceipt', 'inspectedByUser', 'acceptedByUser']);

        $report->Status = $report->deliveryReceipt->InspectionStatus ?? 'pending';

        return response()->json([
            'success' => true,
            'message' => 'Inspection report updated',
            'data' => $report,
        ]);
    }

    public function complete(Request $request, $id): JsonResponse
    {
        if ($resp = $this->requireInspectionOfficer()) {
            return $resp;
        }

        $report = InspectionAcceptanceReport::with(['details.drDetail', 'deliveryReceipt.requestedByUser'])->findOrFail($id);
        if (($report->deliveryReceipt->InspectionStatus ?? 'pending') === 'inspected') {
            return response()->json([
                'success' => false,
                'message' => 'Inspection report already completed',
            ], 409);
        }

        $completionErrors = [];
        $totalsByDrDetail = [];
        $iarIdsByDrDetail = [];
        foreach ($report->details as $item) {
            $detail = $item->drDetail;
            if (! $detail) {
                $completionErrors[$item->IARDetailID][] = 'Missing DR detail reference.';
                continue;
            }

            $brand = $this->normalizeField($item->VerifiedBrand ?? $detail->Brand);
            $modelNumber = $this->normalizeField($item->VerifiedModel ?? $detail->ModelNumber);
            $serialNumber = $this->normalizeField($item->VerifiedSerial ?? $detail->SerialNumber);
            $noSerial = (bool) $item->NoSerial;

            if (! $brand) {
                $completionErrors[$item->IARDetailID][] = 'Brand is required.';
            }
            if (! $modelNumber) {
                $completionErrors[$item->IARDetailID][] = 'Model number is required.';
            }
            if (! $noSerial && ! $serialNumber) {
                $completionErrors[$item->IARDetailID][] = 'Serial number is required or mark no serial.';
            }

            if (! $item->ConditionID) {
                $completionErrors[$item->IARDetailID][] = 'Condition is required.';
            }

            $drDetailId = (int) $item->DRDetailID;
            $iarIdsByDrDetail[$drDetailId][] = (int) $item->IARDetailID;
            if (! isset($totalsByDrDetail[$drDetailId])) {
                $totalsByDrDetail[$drDetailId] = [
                    'accepted' => 0,
                    'rejected' => 0,
                    'delivered' => (int) $detail->QuantityDelivered,
                ];
            }
            $totalsByDrDetail[$drDetailId]['accepted'] += (int) $item->QuantityAccepted;
            $totalsByDrDetail[$drDetailId]['rejected'] += (int) $item->QuantityRejected;
        }

        foreach ($totalsByDrDetail as $drDetailId => $totals) {
            if ($totals['accepted'] + $totals['rejected'] !== $totals['delivered']) {
                foreach ($iarIdsByDrDetail[$drDetailId] as $iarDetailId) {
                    $completionErrors[$iarDetailId][] = 'Accepted + rejected must equal delivered quantity for the DR item.';
                }
            }
        }

        if (! empty($completionErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Inspection items are incomplete. Fix the errors before completion.',
                'errors' => ['items' => $completionErrors],
            ], 422);
        }

        DB::beginTransaction();
        try {
            DeliveryReceipt::where('DRID', $report->DRID)->update([
                'InspectionStatus' => 'inspected',
                'InspectionCompletedAt' => now(),
            ]);

            DeliveryReceiptDetail::where('DRID', $report->DRID)
                ->update(['InspectionStatus' => 'inspected']);

            $this->ingestInventoryFromInspection($report);
            $this->createIssuancesFromInspection($report);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete inspection report',
                'error' => $e->getMessage(),
            ], 500);
        }

        $report->load(['details.drDetail', 'details.condition', 'details.propertyType', 'deliveryReceipt', 'inspectedByUser', 'acceptedByUser']);

        $report->Status = $report->deliveryReceipt->InspectionStatus ?? 'pending';

        return response()->json([
            'success' => true,
            'message' => 'Inspection completed. Items are pending acceptance.',
            'data' => $report,
        ]);
    }

    private function ingestInventoryFromInspection(InspectionAcceptanceReport $report): void
    {
        if (PropertyInventory::where('IARID', $report->IARID)->exists()) {
            return;
        }

        $details = $report->details;
        if ($details->isEmpty()) {
            return;
        }

        $deliveryReceipt = $report->deliveryReceipt;
        if (! $deliveryReceipt) {
            return;
        }

        $endUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);

        $inventory = PropertyInventory::create([
            'InventoryNumber' => 'INV-' . date('Ymd-His') . '-' . $report->IARID,
            'IARID' => $report->IARID,
            'DateReceived' => $report->DateInspected ?? now(),
            'Remarks' => 'Auto-ingested from IAR #' . $report->IARNumber,
            'CreatedDate' => now(),
        ]);

        $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'To be issued')
            ->orWhere('StatusName', 'For issuance')
            ->orWhere('StatusName', 'For Issuance')
            ->value('PropertyStatusID');

        if (! $toBeIssuedStatusId) {
            $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'Available')
                ->value('PropertyStatusID') ?? 1;
        }

        $year = date('Y');
        $prefix = $year . '-';
        $latestItem = PropertyInventoryDetail::where('PropertyNumber', 'like', $prefix . '%')
            ->orderBy('PropertyNumber', 'desc')
            ->withTrashed()
            ->first();

        $currentSequence = 0;
        if ($latestItem && preg_match('/-(\d+)$/', (string) $latestItem->PropertyNumber, $matches)) {
            $currentSequence = (int) $matches[1];
        }

        foreach ($details as $detail) {
            $acceptedQty = (int) ($detail->QuantityAccepted ?? 0);
            if ($acceptedQty <= 0) {
                continue;
            }

            $drDetail = $detail->drDetail;
            if (! $drDetail) {
                continue;
            }

            $brand = $detail->VerifiedBrand ?? $drDetail->Brand;
            $model = $detail->VerifiedModel ?? $drDetail->ModelNumber;
            $serial = $detail->NoSerial ? null : ($detail->VerifiedSerial ?? $drDetail->SerialNumber);

            for ($i = 0; $i < $acceptedQty; $i++) {
                $currentSequence++;
                $propertyNumber = $prefix . str_pad((string) $currentSequence, 6, '0', STR_PAD_LEFT);

                $inventoryDetail = PropertyInventoryDetail::create([
                    'InventoryID' => $inventory->InventoryID,
                    'PropertyTypeID' => $detail->PropertyTypeID,
                    'PropertyNumber' => $propertyNumber,
                    'ConditionID' => $detail->ConditionID,
                    'Brand' => $brand,
                    'ModelNumber' => $model,
                    'SerialNumber' => $serial,
                    'Description' => $drDetail->ItemDescription ?? $detail->Remarks,
                    'Quantity' => 1,
                    'QuantityAvailable' => 1,
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'UnitOfMeasureID' => $drDetail->UnitOfMeasureID,
                    'UnitCost' => $drDetail->UnitCost,
                    'TotalCost' => $drDetail->UnitCost,
                    'SourceIARDetailID' => $detail->IARDetailID,
                    'SourceDRDetailID' => $detail->DRDetailID,
                    'Remarks' => $detail->Remarks,
                    'CreatedDate' => now(),
                ]);

                $actorId = (int) (Auth::id() ?? 0);
                if ($actorId > 0) {
                    AssetAuditLog::createLog(
                        'inventory_detail',
                        (int) $inventoryDetail->InventoryDetailID,
                        'created_from_inspection',
                        $actorId,
                        [
                            'inventory_id' => (int) $inventory->InventoryID,
                            'iar_detail_id' => (int) $detail->IARDetailID,
                            'dr_detail_id' => (int) $detail->DRDetailID,
                            'property_number' => $propertyNumber,
                        ]
                    );
                }

                $assignedPayload = [
                    'InventoryDetailID' => $inventoryDetail->InventoryDetailID,
                    'EndUserID' => $endUser->EndUserID,
                    'DepartmentID' => $endUser->DepartmentID,
                    'PropertyCardNumber' => null,
                    'Description' => $drDetail->ItemDescription ?? $detail->Remarks,
                    'AssignedDate' => now()->toDateString(),
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'ConditionID' => $detail->ConditionID,
                    'Remarks' => 'Auto-created on inspection',
                ];

                $assignedProperty = AssignedProperty::where('InventoryDetailID', $inventoryDetail->InventoryDetailID)->first();
                if ($assignedProperty) {
                    $assignedProperty->fill($assignedPayload);
                    $assignedProperty->save();
                } else {
                    $assignedProperty = AssignedProperty::create($assignedPayload);
                }

                $qrPayload = $this->buildQrPayloadForProperty((int) $assignedProperty->PropertyID);
                QrCode::updateOrCreate(
                    ['InventoryDetailID' => $inventoryDetail->InventoryDetailID],
                    [
                        'PropertyID' => (int) $assignedProperty->PropertyID,
                        'DepartmentID' => (int) $assignedProperty->DepartmentID,
                        'QRCodeData' => $qrPayload,
                        'DateGenerated' => now(),
                    ]
                );
            }
        }
    }

    private function createIssuancesFromInspection(InspectionAcceptanceReport $report): void
    {
        $iarDetailIds = $report->details->pluck('IARDetailID')->filter()->values()->all();
        if (empty($iarDetailIds)) {
            return;
        }

        $deliveryReceipt = $report->deliveryReceipt;
        if (! $deliveryReceipt) {
            return;
        }

        $inventoryDetails = PropertyInventoryDetail::with([
                'drDetail',
                'propertyType.propertyClassification',
                'unitOfMeasure',
            ])
            ->whereIn('SourceIARDetailID', $iarDetailIds)
            ->get();

        if ($inventoryDetails->isEmpty()) {
            return;
        }

        $resolvedEndUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);
        $existingIssuances = PropertyIssuance::query()
            ->where('DRID', (int) $deliveryReceipt->DRID)
            ->get()
            ->keyBy(function ($issuance) {
                return (string) $issuance->DocumentType;
            });

        $groupedByType = $inventoryDetails->groupBy(function ($detail) {
            $docType = $detail->propertyType?->propertyClassification?->DocumentType ?? null;
            return $docType ?: 'ICS';
        });

        $issuedToName = $deliveryReceipt->requestedByUser?->FullName ?? $resolvedEndUser->FullName ?? null;
        $issuedAt = $report->DateInspected ?? now();

        foreach ($groupedByType as $documentType => $details) {
            $documentType = (string) $documentType;
            $issuance = $existingIssuances->get($documentType);

            if (! $issuance) {
                $documentNumber = $this->generateDocumentNumber($documentType);
                $issuance = PropertyIssuance::create([
                    'RequestID' => null,
                    'DRID' => (int) $deliveryReceipt->DRID,
                    'IssuedBy' => Auth::id() ?: (int) ($report->InspectedBy ?? 0),
                    'IssuerSignature' => null,
                    'IssuerSignatureSource' => null,
                    'IssuedTo' => (int) $resolvedEndUser->EndUserID,
                    'IssuedToName' => $issuedToName,
                    'DocumentType' => $documentType,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $issuedAt,
                    'Remarks' => 'Auto-created from inspection completion',
                    'Status' => 'pending_acknowledgment',
                    'AcknowledgedBy' => null,
                    'AcknowledgedAt' => null,
                    'CreatedDate' => now(),
                ]);

                $existingIssuances->put($documentType, $issuance);
            }

            foreach ($details as $detail) {
                $alreadyExists = PropertyIssuanceDetail::where('IssuanceID', $issuance->IssuanceID)
                    ->where('InventoryDetailID', $detail->InventoryDetailID)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $itemDescription = $detail->drDetail?->ItemDescription ?? $detail->Description ?? 'Item';
                $unitName = $detail->unitOfMeasure?->UnitName ?? null;

                PropertyIssuanceDetail::create([
                    'IssuanceID' => $issuance->IssuanceID,
                    'RequestDetailID' => null,
                    'InventoryDetailID' => $detail->InventoryDetailID,
                    'ItemDescription' => $itemDescription,
                    'Unit' => $unitName,
                    'QuantityIssued' => 1,
                    'Remarks' => $issuance->Remarks,
                ]);
            }
        }
    }

    private function resolveEndUserForDeliveryReceipt(DeliveryReceipt $deliveryReceipt): EndUser
    {
        $requester = $deliveryReceipt->requestedByUser;
        $departmentId = (int) ($requester?->DepartmentID ?? 0);
        $fullName = $requester?->FullName ?? null;

        if (! $departmentId || ! $fullName) {
            throw new \Exception('Unable to resolve EndUser for inspection inventory creation.');
        }

        $existing = EndUser::where('FullName', $fullName)
            ->where('DepartmentID', $departmentId)
            ->first();

        if ($existing) {
            return $existing;
        }

        return EndUser::create([
            'PositionID' => 2,
            'DepartmentID' => $departmentId,
            'FullName' => $fullName,
            'IsActive' => 1,
        ]);
    }

    private function buildQrPayloadForProperty(int $propertyId): string
    {
        $baseUrl = rtrim((string) config('gso.frontend_url'), '/');
        if ($baseUrl === '') {
            $baseUrl = 'http://localhost:3000';
        }

        $key = (string) config('app.key');
        $hash = substr(hash_hmac('sha256', (string) $propertyId, $key), 0, 12);

        return $baseUrl . '/qr/scan/' . $propertyId . '?h=' . $hash;
    }

    private function generateDocumentNumber(string $type): string
    {
        $year = date('Y');
        $prefix = $type === 'PAR' ? "PAR-{$year}-" : "ICS-{$year}-";

        $lastIssuance = PropertyIssuance::where('DocumentType', $type)
            ->where('DocumentNumber', 'like', $prefix . '%')
            ->orderBy('IssuanceID', 'desc')
            ->first();

        if ($lastIssuance) {
            $lastNumber = (int) substr($lastIssuance->DocumentNumber, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function conditions(): JsonResponse
    {
        if ($resp = $this->requireInspectionOfficer()) {
            return $resp;
        }

        $conditions = Condition::query()
            ->whereNull('deleted_at')
            ->orderBy('ConditionName')
            ->get(['ConditionID', 'ConditionName', 'Description']);

        return response()->json([
            'success' => true,
            'data' => $conditions,
        ]);
    }
}
