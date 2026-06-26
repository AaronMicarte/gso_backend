<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\RequisitionIssueSlip;
use App\Models\RequisitionIssueSlipItem;
use App\Models\RequisitionIssueSlipSignature;
use App\Models\AssetRequest;
use App\Models\AssetRequestSignature;
use App\Models\Auth\User;
use App\Services\Notifications\RoleNotificationService;

class RequisitionIssueSlipController extends Controller
{
    /**
     * List RIS records.
     */
    public function index(Request $request)
    {
        if (!Schema::hasTable('requisition_issue_slips')) {
            return response()->json([
                'message' => 'RIS tables are not installed. Run migrations to create requisition_issue_slips tables.',
            ], 500);
        }

        $user = Auth::user();
        $roleName = $user->userRole->RoleName ?? '';

        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        // NOTE: eager-load the SAME relations buildRisPayload() needs, so list rows
        // carry request.requester / signatures.received_by / supplier info just like show().
        // Without this, the frontend's requester/supplier fallbacks always render '-' on the list page.
        $query = RequisitionIssueSlip::with([
            'items.requestDetail',
            'items.inventoryDetail.drDetail.article',
            'items.inventoryDetail.drDetail.deliveryReceipt.supplier:SupplierID,SupplierName',
            'items.inventoryDetail.unitOfMeasure',
            'assetRequest.issuer:UserID,FullName',
            'assetRequest.reviewer:UserID,FullName',
            'assetRequest.requester:UserID,FullName,DepartmentID',
            'assetRequest.department:DepartmentID,DepartmentName,DepartmentCode',
        ])->orderByDesc('id');

        // Non-admins can only see RIS records for their department
        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            $query->whereHas('assetRequest', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('ris_number', 'like', "%{$search}%")
                    ->orWhereHas('assetRequest', function ($rq) use ($search) {
                        $rq->where('RequestNumber', 'like', "%{$search}%");
                    });
            });
        }

        $paginated = $query->paginate($perPage);

        // Re-shape each row through buildRisPayload() so list and detail responses
        // share the same JSON shape (request.requester, signatures.received_by, etc.)
        $paginated->setCollection(
            $paginated->getCollection()->map(fn (RequisitionIssueSlip $ris) => $this->buildRisPayload($ris))
        );

        return response()->json($paginated);
    }

    /**
     * Create (or update) RIS header + item snapshots for an approved request.
     */
    public function store(Request $request)
    {
        if (!Schema::hasTable('requisition_issue_slips')) {
            return response()->json([
                'message' => 'RIS tables are not installed. Run migrations to create requisition_issue_slips tables.',
            ], 500);
        }

        $validated = $request->validate([
            'asset_request_id' => 'required|integer|exists:assetrequest,RequestID',
            'ris_date' => 'required|date',
            'entity_name' => 'required|string|max:255',
            'fund_cluster' => 'required|string|max:50',
            'division' => 'required|string|max:255',
            'responsibility_center_code' => 'required|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        $user = Auth::user();
        $roleName = $user->userRole->RoleName ?? '';

        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assetRequest = AssetRequest::with([
            'details.inventoryDetail.unitOfMeasure',
            'details.inventoryDetail.drDetail.article',
            'signatures',
            'department',
        ])
            ->findOrFail((int) $validated['asset_request_id']);

        // IMPORTANT: Include ALL request details in the RIS.
        // Unchecked / not-approved items must still appear, but with Stock Available? = No.
        $allDetails = $assetRequest->details;

        if (!in_array($assetRequest->Status, ['approved', 'waiting_for_issuance', 'issued', 'received'], true)) {
            return response()->json([
                'message' => 'RIS can only be prepared for approved/waiting_for_issuance/issued/received requests.',
                'current_status' => $assetRequest->Status,
            ], 422);
        }

        $requesterSig = $assetRequest->signatures->firstWhere('role', 'requester');
        $approverSig = $assetRequest->signatures->firstWhere('role', 'approver');

        if (!$requesterSig || !$approverSig) {
            return response()->json([
                'message' => 'RIS cannot be prepared: missing requester/approver signatures on the request.',
                'missing' => [
                    'requester' => !$requesterSig,
                    'approver' => !$approverSig,
                ],
            ], 422);
        }

        $ris = DB::transaction(function () use ($assetRequest, $validated, $user, $allDetails) {
            $existing = RequisitionIssueSlip::where('asset_request_id', $assetRequest->RequestID)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->update([
                    'entity_name' => $validated['entity_name'],
                    'fund_cluster' => $validated['fund_cluster'],
                    'division' => $validated['division'],
                    'responsibility_center_code' => $validated['responsibility_center_code'],
                    'remarks' => $validated['remarks'] ?? null,
                ]);

                // Re-snapshot items so RIS stays in sync with review decisions (availability) and admin notes.
                RequisitionIssueSlipItem::where('ris_id', $existing->id)->delete();

                foreach ($allDetails as $detail) {
                    $inventoryDetail = $detail->inventoryDetail;
                    $stockNo = trim((string) ($inventoryDetail?->PropertyNumber ?? '')) ?: 'N/A';

                    // Prefer article name/description when available (from delivery receipt origin).
                    $article = $inventoryDetail?->drDetail?->article ?? null;
                    if ($article) {
                        $unitName = trim((string) ($inventoryDetail?->unitOfMeasure?->UnitName ?? $detail->Unit ?? '')) ?: 'N/A';
                        $description = trim((string) ($article->ArticleName ?? $article->Description ?? '')) ?: 'N/A';
                        // Use delivery receipt's requested qty when available, else fall back to request detail.
                        $qtyRequested = (int) ($inventoryDetail->drDetail->QuantityRequested ?? $detail->QuantityRequested);
                        $brand = trim((string) ($inventoryDetail->drDetail->Brand ?? $inventoryDetail->Brand ?? ''));
                        $model = trim((string) ($inventoryDetail->drDetail->ModelNumber ?? $inventoryDetail->ModelNumber ?? ''));
                        $serial = trim((string) ($inventoryDetail->drDetail->SerialNumber ?? $inventoryDetail->SerialNumber ?? ''));

                        // Group by article + brand + model + serial
                        $groupKey = implode('|', [
                            (string) ($article->ArticleID ?? '0'),
                            $brand,
                            $model,
                            $serial,
                        ]);

                        // Accumulate grouped items in a temp array on the existing RIS object to later insert
                        $existingGrouped = $existing->tempGroupedItems ?? [];
                        if (!isset($existingGrouped[$groupKey])) {
                            $existingGrouped[$groupKey] = [
                                'ris_id' => $existing->id,
                                'asset_request_detail_id' => (int) $detail->RequestDetailID,
                                'inventory_detail_id' => $detail->PropertyInventoryDetailID ? (int) $detail->PropertyInventoryDetailID : null,
                                'stock_no' => $stockNo,
                                'unit' => $unitName,
                                'description' => $description,
                                'quantity_requested' => $qtyRequested,
                                'stock_available' => (bool) ($detail->IsApprovedForIssuance ?? false),
                                'quantity_issued' => 0,
                                'issue_remarks' => trim((string) ($detail->AdminNotes ?? '')),
                            ];
                        } else {
                            $existingGrouped[$groupKey]['quantity_requested'] += (int) $qtyRequested;
                        }

                        $existing->tempGroupedItems = $existingGrouped;
                        continue;
                    }

                    // Fallback: create individual item when no article/drDetail is available
                    $unitName = trim((string) ($inventoryDetail?->unitOfMeasure?->UnitName ?? $detail->Unit ?? '')) ?: 'N/A';
                    $description = trim((string) ($detail->ItemDescription ?? $inventoryDetail?->Description ?? '')) ?: 'N/A';
                    $qtyRequested = (int) $detail->QuantityRequested;
                    $stockAvailable = (bool) ($detail->IsApprovedForIssuance ?? false);
                    $issueRemarks = trim((string) ($detail->AdminNotes ?? ''));

                    RequisitionIssueSlipItem::create([
                        'ris_id' => $existing->id,
                        'asset_request_detail_id' => (int) $detail->RequestDetailID,
                        'inventory_detail_id' => $detail->PropertyInventoryDetailID ? (int) $detail->PropertyInventoryDetailID : null,
                        'stock_no' => $stockNo,
                        'unit' => $unitName,
                        'description' => $description,
                        'quantity_requested' => $qtyRequested,
                        'stock_available' => $stockAvailable,
                        'quantity_issued' => 0,
                        'issue_remarks' => $issueRemarks,
                    ]);
                }

                // Persist any grouped items accumulated during snapshotting
                $existingGrouped = $existing->tempGroupedItems ?? [];
                foreach ($existingGrouped as $group) {
                    RequisitionIssueSlipItem::create($group);
                }

                // Clean temp storage
                unset($existing->tempGroupedItems);

                return $existing;
            }

            // Generate sequential RIS number (YYYY-MM-XXXX format)
            $maxRis = RequisitionIssueSlip::lockForUpdate()
                ->orderByDesc('id')
                ->value('ris_number');

            $nextSerial = 1;
            if ($maxRis) {
                // Extract the last 4 digits and increment
                $parts = explode('-', $maxRis);
                if (count($parts) === 3) {
                    $nextSerial = (int) $parts[2] + 1;
                }
            }

            $risDate = Carbon::parse($validated['ris_date'], 'Asia/Manila');
            $risNumber = sprintf('%04d-%02d-%04d', $risDate->year, $risDate->month, $nextSerial);

            $ris = RequisitionIssueSlip::create([
                'asset_request_id' => $assetRequest->RequestID,
                'ris_number' => $risNumber,
                'entity_name' => $validated['entity_name'],
                'fund_cluster' => $validated['fund_cluster'],
                'division' => $validated['division'],
                'responsibility_center_code' => $validated['responsibility_center_code'],
                'purpose' => (string) ($assetRequest->Purpose ?? ''),
                'status' => 'for_issuance',
                'requested_by_user_id' => (int) $assetRequest->RequestedBy,
                'requested_at' => $assetRequest->RequestDate ?? now(),
                'approved_by_user_id' => (int) ($assetRequest->ReviewedBy ?? $assetRequest->RequestedBy),
                'approved_at' => $assetRequest->ReviewedAt ?? now(),
                'prepared_by_user_id' => (int) $user->UserID,
                'prepared_at' => now(),
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $grouped = [];
            foreach ($allDetails as $detail) {
                $inventoryDetail = $detail->inventoryDetail;
                $stockNo = trim((string) ($inventoryDetail?->PropertyNumber ?? '')) ?: 'N/A';

                // Prefer article name/description when available (from delivery receipt origin).
                $article = $inventoryDetail?->drDetail?->article ?? null;
                if ($article) {
                    $unitName = trim((string) ($inventoryDetail?->unitOfMeasure?->UnitName ?? $detail->Unit ?? '')) ?: 'N/A';
                    $description = trim((string) ($article->ArticleName ?? $article->Description ?? '')) ?: 'N/A';
                    $qtyRequested = (int) ($inventoryDetail->drDetail->QuantityRequested ?? $detail->QuantityRequested);
                    $brand = trim((string) ($inventoryDetail->drDetail->Brand ?? $inventoryDetail->Brand ?? ''));
                    $model = trim((string) ($inventoryDetail->drDetail->ModelNumber ?? $inventoryDetail->ModelNumber ?? ''));
                    $serial = trim((string) ($inventoryDetail->drDetail->SerialNumber ?? $inventoryDetail->SerialNumber ?? ''));

                    $groupKey = implode('|', [
                        (string) ($article->ArticleID ?? '0'),
                        $brand,
                        $model,
                        $serial,
                    ]);

                    if (!isset($grouped[$groupKey])) {
                        $grouped[$groupKey] = [
                            'ris_id' => $ris->id,
                            'asset_request_detail_id' => (int) $detail->RequestDetailID,
                            'inventory_detail_id' => $detail->PropertyInventoryDetailID ? (int) $detail->PropertyInventoryDetailID : null,
                            'stock_no' => $stockNo,
                            'unit' => $unitName,
                            'description' => $description,
                            'quantity_requested' => $qtyRequested,
                            'stock_available' => (bool) ($detail->IsApprovedForIssuance ?? false),
                            'quantity_issued' => 0,
                            'issue_remarks' => trim((string) ($detail->AdminNotes ?? '')),
                        ];
                    } else {
                        $grouped[$groupKey]['quantity_requested'] += (int) $qtyRequested;
                    }

                    continue;
                }

                // Fallback: create individual item when no article/drDetail is available
                $unitName = trim((string) ($inventoryDetail?->unitOfMeasure?->UnitName ?? $detail->Unit ?? '')) ?: 'N/A';
                $description = trim((string) ($detail->ItemDescription ?? $inventoryDetail?->Description ?? '')) ?: 'N/A';
                $qtyRequested = (int) $detail->QuantityRequested;
                $stockAvailable = (bool) ($detail->IsApprovedForIssuance ?? false);
                $issueRemarks = trim((string) ($detail->AdminNotes ?? ''));

                RequisitionIssueSlipItem::create([
                    'ris_id' => $ris->id,
                    'asset_request_detail_id' => (int) $detail->RequestDetailID,
                    'inventory_detail_id' => $detail->PropertyInventoryDetailID ? (int) $detail->PropertyInventoryDetailID : null,
                    'stock_no' => $stockNo,
                    'unit' => $unitName,
                    'description' => $description,
                    'quantity_requested' => $qtyRequested,
                    'stock_available' => $stockAvailable,
                    'quantity_issued' => 0,
                    'issue_remarks' => $issueRemarks,
                ]);
            }

            // Persist grouped items
            foreach ($grouped as $g) {
                RequisitionIssueSlipItem::create($g);
            }

            return $ris;
        });

        // Signatures are not used in this deployment; skipping copying request signatures.

        $recipientUserIds = $this->getRisNotificationUserIds(
            (int) ($assetRequest->DepartmentID ?? 0),
            (int) ($user->UserID ?? 0)
        );

        app(RoleNotificationService::class)->notifyUsers(
            $recipientUserIds,
            'ris.prepared',
            'Requisition Issue Slip Prepared',
            sprintf('RIS %s was prepared for request %s.', (string) $ris->ris_number, (string) $assetRequest->RequestNumber),
            [
                'ris_id' => (string) $ris->id,
                'ris_number' => (string) $ris->ris_number,
                'request_id' => (string) $assetRequest->RequestID,
                'request_number' => (string) $assetRequest->RequestNumber,
                'status' => (string) $ris->status,
                'link' => '/ris/' . $ris->id,
            ]
        );

        if ((int) ($assetRequest->RequestedBy ?? 0) > 0) {
            app(RoleNotificationService::class)->notifyUser(
                (int) $assetRequest->RequestedBy,
                'ris.prepared',
                'RIS Prepared For Your Request',
                sprintf('RIS %s was prepared for your request %s.', (string) $ris->ris_number, (string) $assetRequest->RequestNumber),
                [
                    'ris_id' => (string) $ris->id,
                    'ris_number' => (string) $ris->ris_number,
                    'request_id' => (string) $assetRequest->RequestID,
                    'request_number' => (string) $assetRequest->RequestNumber,
                    'status' => (string) $ris->status,
                    'link' => '/ris/' . $ris->id,
                ]
            );
        }

        return response()->json([
            'message' => 'RIS prepared successfully',
            'ris' => $this->buildRisPayload($ris->fresh()),
        ], 201);
    }

    public function show($id)
    {
        if (!Schema::hasTable('requisition_issue_slips')) {
            return response()->json([
                'message' => 'RIS tables are not installed. Run migrations to create requisition_issue_slips tables.',
            ], 500);
        }

        $user = Auth::user();
        $roleName = $user->userRole->RoleName ?? '';

        $ris = RequisitionIssueSlip::with([
            'items.requestDetail',
            'items.inventoryDetail.drDetail.article',
            'items.inventoryDetail.drDetail.deliveryReceipt.supplier:SupplierID,SupplierName',
            'items.inventoryDetail.unitOfMeasure',
            'assetRequest.issuer:UserID,FullName',
            'assetRequest.reviewer:UserID,FullName',
            'assetRequest.requester:UserID,FullName,DepartmentID',
            'assetRequest.department:DepartmentID,DepartmentName,DepartmentCode',
        ])->findOrFail((int) $id);

        if (!in_array($roleName, ['Admin', 'GSO Staff'], true)) {
            if (($ris->assetRequest?->DepartmentID ?? null) !== $user->DepartmentID) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return response()->json($this->buildRisPayload($ris));
    }

    public function showByRequest($requestId)
    {
        $ris = RequisitionIssueSlip::where('asset_request_id', (int) $requestId)->first();

        if (!$ris) {
            return response()->json([
                'exists' => false,
                'message' => 'RIS not prepared yet.',
                'request_id' => (int) $requestId,
            ]);
        }

        return $this->show($ris->id);
    }

    /**
     * @return array<int>
     */
    private function getRisNotificationUserIds(int $departmentId, ?int $excludeUserId = null): array
    {
        $query = User::query()
            ->select('UserID')
            ->where('IsActive', true)
            ->where(function ($q) use ($departmentId) {
                $q->whereHas('userRole', function ($roleQ) {
                    $roleQ->whereIn('RoleName', ['Admin', 'GSO Staff']);
                })->orWhere(function ($deptHeadQ) use ($departmentId) {
                    $deptHeadQ->whereHas('userRole', function ($roleQ) {
                        $roleQ->where('RoleName', 'Department Head');
                    });

                    if ($departmentId > 0) {
                        $deptHeadQ->where('DepartmentID', $departmentId);
                    } else {
                        $deptHeadQ->whereRaw('1 = 0');
                    }
                });
            });

        if (!empty($excludeUserId)) {
            $query->where('UserID', '!=', (int) $excludeUserId);
        }

        return $query->pluck('UserID')->map(fn ($id) => (int) $id)->all();
    }

    private function syncSignatureFromRequest(int $requestId, int $risId, string $requestRole, string $risRole): void
    {
        $sig = AssetRequestSignature::where('request_id', $requestId)
            ->where('role', $requestRole)
            ->first();

        if (!$sig) {
            return;
        }

        RequisitionIssueSlipSignature::updateOrCreate(
            ['ris_id' => $risId, 'role' => $risRole],
            [
                'signed_by_user_id' => (int) $sig->signed_by_user_id,
                'source' => (string) ($sig->source ?? 'linked'),
                'storage_disk' => (string) ($sig->storage_disk ?? 'public'),
                'storage_path' => (string) ($sig->storage_path ?? ''),
                'url' => (string) ($sig->url ?? ''),
                'mime' => (string) ($sig->mime ?? 'image/png'),
                'width' => $sig->width,
                'height' => $sig->height,
                'size_bytes' => $sig->size_bytes,
                'sha256_hash' => (string) ($sig->sha256_hash ?? hash('sha256', (string) $sig->storage_path)),
                'ip_address' => $sig->ip_address,
                'user_agent' => $sig->user_agent,
            ]
        );
    }

    private function buildRisPayload(RequisitionIssueSlip $ris): array
    {
        // Signatures are not used in this deployment; do not attempt to sync issuer signature.

        $assetRequest = $ris->assetRequest;

        // Issued quantities based on direct RIS item linkage.
        $issuedByRisItem = DB::table('propertyissuancedetail as pid')
            ->join('propertyissuance as pi', 'pid.IssuanceID', '=', 'pi.IssuanceID')
            ->whereNull('pid.deleted_at')
            ->whereNull('pi.deleted_at')
            ->where('pi.RequestID', $ris->asset_request_id)
            ->whereNotNull('pid.ris_item_id')
            ->groupBy('pid.ris_item_id')
            ->selectRaw('pid.ris_item_id as ris_item_id, SUM(pid.QuantityIssued) as qty_issued')
            ->pluck('qty_issued', 'ris_item_id');

        // Backward-compatible fallback for older rows without ris_item_id.
        $issuedByDetail = DB::table('propertyissuancedetail as pid')
            ->join('propertyissuance as pi', 'pid.IssuanceID', '=', 'pi.IssuanceID')
            ->whereNull('pid.deleted_at')
            ->whereNull('pi.deleted_at')
            ->where('pi.RequestID', $ris->asset_request_id)
            ->groupBy('pid.RequestDetailID')
            ->selectRaw('pid.RequestDetailID as request_detail_id, SUM(pid.QuantityIssued) as qty_issued')
            ->pluck('qty_issued', 'request_detail_id');

        // Latest acknowledged issuance for received signature
        $ack = DB::table('propertyissuance')
            ->where('RequestID', $ris->asset_request_id)
            ->where('Status', 'acknowledged')
            ->orderByDesc('AcknowledgedAt')
            ->first(['IssuedTo', 'AcknowledgedAt', 'AcknowledgmentSignature', 'IssuedToName']);

        // Signatures table is not present in this deployment; provide empty signatures collection.
        $signatures = collect();

        return [
            'id' => $ris->id,
            'asset_request_id' => $ris->asset_request_id,
            'ris_number' => $ris->ris_number,
            'entity_name' => $ris->entity_name,
            'fund_cluster' => $ris->fund_cluster,
            'division' => $ris->division,
            'responsibility_center_code' => $ris->responsibility_center_code,
            'purpose' => $ris->purpose,
            'status' => $ris->status,
            'remarks' => $ris->remarks,

            'request' => $assetRequest ? [
                'RequestID' => $assetRequest->RequestID,
                'RequestNumber' => $assetRequest->RequestNumber,
                'RequestDate' => $assetRequest->RequestDate,
                'Status' => $assetRequest->Status,
                'department' => $assetRequest->department,
                'requester' => $assetRequest->requester,
                'reviewer' => $assetRequest->reviewer,
                'issuer' => $assetRequest->issuer,
               'supplier_name' => $ris->items->map(fn($item) =>
                $item->inventoryDetail?->drDetail?->deliveryReceipt?->supplier?->SupplierName
            )->filter()->first(),
            ] : null,

            'items' => (function () use ($ris, $issuedByDetail, $issuedByRisItem) {
                $groups = [];

                foreach ($ris->items as $item) {
                    $inv = $item->inventoryDetail ?? null;
                    $drd = $inv?->drDetail ?? null;
                    $article = $drd?->article ?? null;

                    if ($article) {
                        $brand = trim((string) ($drd->Brand ?? $inv->Brand ?? ''));
                        $model = trim((string) ($drd->ModelNumber ?? $inv->ModelNumber ?? ''));
                        $serial = trim((string) ($drd->SerialNumber ?? $inv->SerialNumber ?? ''));
                        $desc = trim((string) ($article->ArticleName ?? $article->Description ?? $item->description ?? '')) ?: 'N/A';
                        $unit = trim((string) ($inv?->unitOfMeasure?->UnitName ?? $item->unit ?? '')) ?: 'N/A';
                        $unitCost = (float) ($drd->UnitCost ?? 0);

                        $key = implode('|', [(string) ($article->ArticleID ?? '0'), $brand, $model, $serial, $unit]);
                    } else {
                        $desc = trim((string) ($item->description ?? '')) ?: 'N/A';
                        $brand = $model = $serial = '';
                        $unit = trim((string) ($item->unit ?? '')) ?: 'N/A';
                        $unitCost = (float) ($drd->UnitCost ?? 0);
                        $key = implode('|', [$desc, $unit, (string) ($item->stock_no ?? '')]);
                    }

                    $qtyReq = (int) $item->quantity_requested;
                    $qtyIssued = isset($issuedByRisItem[(int) $item->id]) ? (int) $issuedByRisItem[(int) $item->id] : (int) ($issuedByDetail[(int) $item->asset_request_detail_id] ?? 0);

                    if (!isset($groups[$key])) {
                        $groups[$key] = [
                            'id' => null,
                            'asset_request_detail_id' => $item->asset_request_detail_id,
                            'stock_no' => $item->stock_no,
                            'unit' => $unit,
                            'description' => $desc,
                            'brand' => $brand,
                            'model' => $model,
                            'serial' => $serial,
                                'property_numbers' => $item->stock_no ? [trim((string) $item->stock_no)] : [],
                            'quantity_requested' => $qtyReq,
                            'unit_cost' => $unitCost,
                            'stock_available' => (bool) $item->stock_available,
                            'quantity_issued' => $qtyIssued,
                            'issue_remarks' => trim((string) ($item->issue_remarks ?? '')),
                        ];
                    } else {
                        $groups[$key]['quantity_requested'] += $qtyReq;
                        $groups[$key]['quantity_issued'] += $qtyIssued;
                        // keep stock_available true if any grouped item is available
                        $groups[$key]['stock_available'] = $groups[$key]['stock_available'] || (bool) $item->stock_available;
                            // collect unique property numbers
                            $prop = $item->stock_no ? trim((string) $item->stock_no) : null;
                            if ($prop && !in_array($prop, $groups[$key]['property_numbers'], true)) {
                                $groups[$key]['property_numbers'][] = $prop;
                            }
                    }
                }

                // Return grouped items as indexed array
                return array_values($groups);
            })(),

            'signatures' => [
                'requested_by' => $signatures->get('requested_by') ? [
                    'url' => $signatures->get('requested_by')->url,
                    'user' => $signatures->get('requested_by')->signedByUser,
                ] : null,
                'approved_by' => $signatures->get('approved_by') ? [
                    'url' => $signatures->get('approved_by')->url,
                    'user' => $signatures->get('approved_by')->signedByUser,
                ] : null,
                'issued_by' => $signatures->get('issued_by') ? [
                    'url' => $signatures->get('issued_by')->url,
                    'user' => $signatures->get('issued_by')->signedByUser,
                ] : null,
                'received_by' => $ack ? [
                    'acknowledged_by' => $ack->IssuedTo,
                    'acknowledged_at' => $ack->AcknowledgedAt,
                    'signature' => $ack->AcknowledgmentSignature,
                    'issued_to_name' => $ack->IssuedToName,
                ] : null,
            ],
        ];
    }
}