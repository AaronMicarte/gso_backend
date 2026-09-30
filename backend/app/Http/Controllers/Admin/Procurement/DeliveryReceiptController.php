<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\Article;
use App\Models\MasterData\PropertyType;
use App\Models\MasterData\PropertyStatus;
use App\Models\Auth\User;
use App\Models\MasterData\Supplier;
use App\Models\AssetAuditLog;
use App\Models\AssignedProperty;
use App\Models\EndUser;
use App\Models\MasterData\Position;
use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\QrCode;
use App\Services\Notifications\RoleNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class DeliveryReceiptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DeliveryReceipt::with([
                    'supplier:SupplierID,SupplierName',
                    'details.article',
                    'details.propertyType',
                    'details.unitOfMeasure',
                    'details.inventoryDetails:InventoryDetailID,SourceDRDetailID,PropertyNumber',
                    'requestedByUser:UserID,FullName,DepartmentID',
                    'requestedByUser.department:DepartmentID,DepartmentName',
                ])
                ->leftJoin('user', 'deliveryreceipt.ReceivedBy', '=', 'user.UserID')
                ->select('deliveryreceipt.*', 'user.FullName as received_by_name')
                ;

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('DateReceived', [$request->start_date, $request->end_date]);
            }

            // Filter by supplier
            if ($request->has('supplier_id')) {
                $query->where('SupplierID', (int) $request->supplier_id);
            } elseif ($request->has('supplier_name')) {
                $query->whereHas('supplier', function ($q) use ($request) {
                    $q->where('SupplierName', 'like', '%' . $request->supplier_name . '%');
                });
            }

            // Filter by PO number
            if ($request->has('po_number')) {
                $query->where('PONumber', 'like', '%' . $request->po_number . '%');
            }

            // Generic search across DR number, IAR number, PO number, and supplier
            if ($request->has('query')) {
                $search = trim((string) $request->query('query', ''));
                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('deliveryreceipt.DRNumber', 'like', '%' . $search . '%')
                            ->orWhere('deliveryreceipt.IARNumber', 'like', '%' . $search . '%')
                            ->orWhere('deliveryreceipt.PONumber', 'like', '%' . $search . '%')
                            ->orWhereHas('supplier', function ($sq) use ($search) {
                                $sq->where('SupplierName', 'like', '%' . $search . '%');
                            });
                    });
                }
            }

            // Workflow status filtering for delivery receipts
            // Pending inspection is intentionally excluded from this listing.
            $status = trim((string) $request->input('status', ''));
            $hasSearchQuery = trim((string) $request->query('query', '')) !== '';
            $statusProvided = $request->filled('status');

            if ($status === 'to_be_issued') {
                $query->where('deliveryreceipt.InspectionStatus', 'inspected')
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('propertyissuance')
                            ->whereColumn('propertyissuance.DRID', 'deliveryreceipt.DRID')
                            ->where('propertyissuance.Status', 'acknowledged')
                            ->whereNull('propertyissuance.deleted_at');
                    });
            } elseif ($status === 'acknowledged') {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('propertyissuance')
                        ->whereColumn('propertyissuance.DRID', 'deliveryreceipt.DRID')
                        ->where('propertyissuance.Status', 'acknowledged')
                        ->whereNull('propertyissuance.deleted_at');
                });
            } elseif (!$statusProvided && !$hasSearchQuery) {
                // Default listing: inspected receipts only (issuance workflow view).
                $query->where('deliveryreceipt.InspectionStatus', 'inspected');
            }

            $perPage = (int) $request->input('per_page', 15);
            $perPage = max(1, min($perPage, 100));

            $deliveryReceipts = $query->orderBy('CreatedDate', 'desc')->paginate($perPage);

            // Transform to include workflow info
              $deliveryReceipts->getCollection()->transform(function ($dr) {
                $this->applyWorkflowStatus($dr);

                // Resolve RIS (synthetic request pattern) for each row.
                $syntheticRequestNumber = 'REQ-' . date('Y') . '-DR-' . str_pad((string) $dr->DRID, 4, '0', STR_PAD_LEFT);
                $ris = \App\Models\RequisitionIssueSlip::query()
                    ->whereHas('assetRequest', function ($q) use ($syntheticRequestNumber) {
                        $q->where('RequestNumber', $syntheticRequestNumber);
                    })
                    ->first(['id', 'ris_number', 'status']);

                $dr->ris_id = $ris?->id;
                $dr->ris_number = $ris?->ris_number;
                $dr->ris_status = $ris?->status;

                return $dr;
            });

            return response()->json([
                'success' => true,
                'data' => $deliveryReceipts
            ]);
        } catch (\Exception $e) {
            Log::error('DeliveryReceiptController@index failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query' => $request->query(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch delivery receipts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // determine min allowed date (configurable years back) in Asia/Manila
            $backdateYears = (int) config('gso.dr_backdate_years', 5);
            $minDate = Carbon::now('Asia/Manila')->subYears($backdateYears)->toDateString();

            // enforce backdate window in Asia/Manila
            $dateRules = 'required|date|before_or_equal:today|after_or_equal:' . $minDate;

            $isTemporaryDr = filter_var($request->input('is_temporary_dr', false), FILTER_VALIDATE_BOOLEAN)
                || empty(trim((string) $request->input('dr_number', '')));

            $validator = Validator::make($request->all(), [
                'dr_number' => $isTemporaryDr ? 'nullable|string|max:50|unique:deliveryreceipt,DRNumber' : 'required|string|max:50|unique:deliveryreceipt,DRNumber',
                'is_temporary_dr' => 'nullable|boolean',
                'supplier_id' => 'required|integer|exists:supplier,SupplierID',
                'requested_by_user_id' => 'required|integer|exists:user,UserID',
                'pr_number' => 'required|string|max:50',
                'po_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('deliveryreceipt', 'PONumber')->where(function($q) use ($request) {
                        return $q->where('SupplierID', (int) $request->supplier_id);
                    }),
                ],
                'invoice_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('deliveryreceipt', 'InvoiceNumber')->where(function($q) use ($request) {
                        return $q->where('SupplierID', (int) $request->supplier_id);
                    }),
                ],
                'date_received' => $dateRules,

                'remarks' => 'nullable|string',
                'dr_picture' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'details' => 'required|array|min:1',
                'details.*.article_id' => 'required|integer|exists:articles,ArticleID',
                'details.*.item_description' => 'required|string|max:255',
                'details.*.brand' => 'nullable|string|max:100',
                'details.*.model_number' => 'nullable|string|max:100',
                'details.*.serial_number' => 'nullable|string|max:100',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
                'details.*.quantity_requested' => 'required|integer|min:1',
                'details.*.change_reason' => 'nullable|string|max:255',
                'details.*.unit_of_measure_id' => 'nullable|integer|exists:unitofmeasure,UnitOfMeasureID',
                'details.*.unit_cost' => 'required|numeric|min:0',
                'items_still_pending_delivery' => 'nullable|boolean',
                'assignments' => 'nullable',
            ], [
                'dr_number.unique' => 'DR number already exists',
                'po_number.unique' => 'PO number already exists for this supplier',
                'invoice_number.unique' => 'Invoice number already exists for this supplier',
                'dr_picture.file' => 'DR picture must be a valid file',
                'dr_picture.mimes' => 'DR picture must be a JPEG, PNG, JPG, or GIF file',
                'dr_picture.max' => 'DR picture must not exceed 2MB',
            ]);

            $validator->after(function ($validator) use ($request) {
                $details = $request->input('details', []);
                $articleIds = collect($details)->pluck('article_id')->filter()->unique()->values();
                $articleMap = Article::whereIn('ArticleID', $articleIds)
                    ->get(['ArticleID', 'PropertyTypeID'])
                    ->keyBy('ArticleID');

                // Load all existing serial numbers from DB
                $rawDrSerials = DB::table('deliveryreceiptdetail')
                    ->whereNotNull('SerialNumber')
                    ->where('SerialNumber', '!=', '')
                    ->pluck('SerialNumber');

                $rawInvSerials = DB::table('propertyinventorydetail')
                    ->whereNotNull('SerialNumber')
                    ->where('SerialNumber', '!=', '')
                    ->pluck('SerialNumber');

                $existingUsedSerials = [];
                foreach ($rawDrSerials->concat($rawInvSerials) as $raw) {
                    foreach (explode(',', (string) $raw) as $s) {
                        $trimmed = strtolower(trim($s));
                        if ($trimmed !== '') {
                            $existingUsedSerials[$trimmed] = true;
                        }
                    }
                }

                $seenInPayload = [];

                foreach ($details as $index => $detail) {
                    $serial = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
                    $serialParts = [];
                    if ($serial !== '') {
                        $serialParts = array_values(array_filter(array_map('trim', explode(',', $serial)), fn($v) => $v !== ''));
                    }
                    $serialCount = count($serialParts);

                    foreach ($serialParts as $sPart) {
                        $lowerPart = strtolower($sPart);
                        if (isset($seenInPayload[$lowerPart])) {
                            $validator->errors()->add("details.$index.serial_number", "Serial number '{$sPart}' is duplicated within this delivery receipt.");
                        } else {
                            $seenInPayload[$lowerPart] = true;
                        }

                        if (isset($existingUsedSerials[$lowerPart])) {
                            $validator->errors()->add("details.$index.serial_number", "Serial number '{$sPart}' is already used in another Delivery Receipt.");
                        }
                    }

                    $quantity = (int) ($detail['quantity_delivered'] ?? 0);
                    if ($serialCount > 0 && $quantity !== $serialCount) {
                        $validator->errors()->add("details.$index.quantity_delivered", 'Quantity must equal the number of serial numbers provided');
                    }

                    $requested = (int) ($detail['quantity_requested'] ?? 0);
                    if ($serialCount > 0 && $requested !== $serialCount) {
                        $validator->errors()->add("details.$index.quantity_requested", 'Quantity must equal the number of serial numbers provided');
                    }

                    $changeReason = isset($detail['change_reason']) ? trim((string) $detail['change_reason']) : '';
                    if ($quantity !== $requested && $changeReason === '') {
                        $validator->errors()->add("details.$index.change_reason", 'Change reason is required when delivered differs from requested quantity');
                    }

                    $articleId = (int) ($detail['article_id'] ?? 0);
                    $article = $articleMap->get($articleId);
                    if ($article && (int) ($detail['property_type_id'] ?? 0) !== (int) $article->PropertyTypeID) {
                        $validator->errors()->add("details.$index.property_type_id", 'Property type must match the selected article.');
                    }
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle optional DR picture upload
            $drPicturePath = $request->hasFile('dr_picture')
                ? $request->file('dr_picture')->store('dr_pictures', 'public')
                : null;

            DB::beginTransaction();

            // enforce timezone-aware no-future date check (Asia/Manila)
            $manilaNow = Carbon::now('Asia/Manila')->toDateString();
            if (Carbon::parse($request->date_received, 'Asia/Manila')->gt(Carbon::parse($manilaNow))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['date_received' => ['Date received cannot be in the future.']]
                ], 422);
            }

            $requestedByUser = User::find((int) $request->requested_by_user_id);
            if (!$requestedByUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['requested_by_user_id' => ['Requested by user not found.']]
                ], 422);
            }

            $supplier = Supplier::find((int) $request->supplier_id);
            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['supplier_id' => ['Supplier not found.']]
                ], 422);
            }

            $itemsStillPending = filter_var($request->input('items_still_pending_delivery', false), FILTER_VALIDATE_BOOLEAN);

            $drNumber = trim((string) $request->input('dr_number', ''));
            if ($drNumber === '') {
                $drNumber = $this->generateTempDrNumber();
            }

            $deliveryPayload = [
                'DRNumber' => $drNumber,
                'SupplierID' => (int) $supplier->SupplierID,
                'RequestedBy' => (int) $requestedByUser->UserID,
                'PRNumber' => $request->pr_number,
                'PONumber' => $request->po_number,
                'InvoiceNumber' => $request->invoice_number,
                'ReceivedBy' => Auth::user()->UserID,
                'DateReceived' => $request->date_received,
                'InspectedBy' => Auth::user()->UserID,
                'DateInspected' => $request->date_received,
                'Remarks' => $request->remarks,
                'dr_picture' => $drPicturePath,
                'InspectionStatus' => 'inspected',
                'InspectionCompletedAt' => now(),
                'IsPartial' => $itemsStillPending,
            ];

            $deliveryReceipt = null;
            for ($attempt = 0; $attempt < 5; $attempt++) {
                $iarNumber = $this->generateDrIarNumber();

                try {
                    $deliveryReceipt = DeliveryReceipt::create(array_merge($deliveryPayload, [
                        'IARNumber' => $iarNumber,
                    ]));
                    break;
                } catch (QueryException $queryException) {
                    if ($this->isDuplicateIarNumberException($queryException)) {
                        continue;
                    }

                    throw $queryException;
                }
            }

            if (! $deliveryReceipt) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'iar_number' => ['Could not reserve a unique IAR number. Please try again.'],
                    ],
                ], 422);
            }

            // Create details
            $details = $request->details ?? [];
            $articleIds = collect($details)->pluck('article_id')->filter()->unique()->values();
            $articleMap = Article::whereIn('ArticleID', $articleIds)
                ->get(['ArticleID', 'PropertyTypeID', 'DefaultUnitOfMeasureID'])
                ->keyBy('ArticleID');
            $detailIds = [];
            $createdDetails = [];
            foreach ($details as $index => $detail) {
                $article = $articleMap->get((int) $detail['article_id']);
                if (!$article) {
                    throw new \RuntimeException('Selected article was not found.');
                }

                $unitOfMeasureId = $detail['unit_of_measure_id'] ?? null;
                if (!$unitOfMeasureId) {
                    $unitOfMeasureId = $article->DefaultUnitOfMeasureID;
                }

                $serialRaw = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
                $serialParts = $serialRaw === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $serialRaw)), fn($v) => $v !== ''));
                $quantityDelivered = (int) ($detail['quantity_delivered'] ?? 0);
                $quantityRequested = (int) ($detail['quantity_requested'] ?? 0);

                // Create a single DR detail per item submission (batch), even if multiple serials provided
                $createdDetail = DeliveryReceiptDetail::create([
                    'DRID' => $deliveryReceipt->DRID,
                    'ArticleID' => $detail['article_id'],
                    'ItemDescription' => $detail['item_description'],
                    'Brand' => $detail['brand'] ?? null,
                    'ModelNumber' => $detail['model_number'] ?? null,
                    'SerialNumber' => $serialRaw ?: null,
                    'PropertyTypeID' => $article->PropertyTypeID,
                    'QuantityDelivered' => $quantityDelivered,
                    'QuantityRequested' => $quantityRequested,
                    'ChangeReason' => $detail['change_reason'] ?? null,
                    'UnitOfMeasureID' => $unitOfMeasureId,
                    'UnitCost' => $detail['unit_cost'],
                    'TotalCost' => $quantityDelivered * $detail['unit_cost'],
                    'InspectionStatus' => 'inspected',
                ]);

                $createdDetails[$index] = $createdDetail;
                $detailIds[] = (int) $createdDetail->DRDetailID;
            }

            $this->ingestInventoryFromDeliveryReceipt($deliveryReceipt);

            $rawAssignments = $request->input('assignments');
            if (is_string($rawAssignments)) {
                $rawAssignments = html_entity_decode($rawAssignments, ENT_QUOTES, 'UTF-8');
                $decoded = json_decode($rawAssignments, true);
                $assignments = is_array($decoded) ? $decoded : [];
            } elseif (is_array($rawAssignments)) {
                $assignments = $rawAssignments;
            } else {
                $assignments = [];
            }

            $this->createIssuancesFromDeliveryReceipt($deliveryReceipt, $createdDetails, $assignments);

            DB::commit();

            $actorId = (int) (Auth::id() ?? 0);
            if ($actorId > 0) {
                AssetAuditLog::createLog(
                    'delivery_receipt',
                    (int) $deliveryReceipt->DRID,
                    'created',
                    $actorId,
                    [
                        'dr_number' => $deliveryReceipt->DRNumber,
                        'supplier' => $supplier->SupplierName,
                        'details' => $detailIds,
                    ]
                );

                foreach ($detailIds as $detailId) {
                    AssetAuditLog::createLog(
                        'delivery_receipt_detail',
                        (int) $detailId,
                        'created',
                        $actorId,
                        [
                            'dr_id' => (int) $deliveryReceipt->DRID,
                            'dr_number' => $deliveryReceipt->DRNumber,
                        ]
                    );
                }
            }

            // Load relationships
            $deliveryReceipt->load([
                'supplier:SupplierID,SupplierName',
                'details.propertyType',
                'details.unitOfMeasure',
                'details.article',
                'details.inventoryDetails:InventoryDetailID,SourceDRDetailID,PropertyNumber',
                'requestedByUser:UserID,FullName,DepartmentID',
                'requestedByUser.department:DepartmentID,DepartmentName',
            ]);
            // Set receivedByUser manually
            $user = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);
            $deliveryReceipt->receivedByUser = $user ? [
                'UserID' => $user->UserID,
                'FullName' => $user->FullName
            ] : null;

            // Convenience fields for legacy frontend usage
            $deliveryReceipt->RequestedByName = $deliveryReceipt->requestedByUser?->FullName;
            $deliveryReceipt->DepartmentName = $deliveryReceipt->requestedByUser?->department?->DepartmentName;
            $deliveryReceipt->DepartmentID = $deliveryReceipt->requestedByUser?->DepartmentID;
            $deliveryReceipt->SupplierName = $deliveryReceipt->supplier?->SupplierName;
            $deliveryReceipt->supplier_name = $deliveryReceipt->supplier?->SupplierName;
            $this->applyWorkflowStatus($deliveryReceipt);

            $recipientUserIds = $this->getProcurementNotificationUserIds(
                (int) ($deliveryReceipt->requestedByUser?->DepartmentID ?? 0),
                (int) (Auth::id() ?? 0)
            );

            app(RoleNotificationService::class)->notifyUsers(
                $recipientUserIds,
                'delivery_receipt.created',
                'Delivery Receipt Created',
                sprintf('Delivery receipt %s has been created and inspected.', (string) $deliveryReceipt->DRNumber),
                [
                    'dr_id' => (string) $deliveryReceipt->DRID,
                    'dr_number' => (string) $deliveryReceipt->DRNumber,
                    'status' => 'inspected',
                    'link' => '/procurement/delivery-receipts/' . $deliveryReceipt->DRID,
                ]
            );

            if ((int) $deliveryReceipt->RequestedBy > 0 && !in_array((int) $deliveryReceipt->RequestedBy, $recipientUserIds, true)) {
                app(RoleNotificationService::class)->notifyUser(
                    (int) $deliveryReceipt->RequestedBy,
                    'delivery_receipt.created',
                    'Delivery Receipt Processed',
                    sprintf('Delivery receipt %s for your request has been created and inspected.', (string) $deliveryReceipt->DRNumber),
                    [
                        'dr_id' => (string) $deliveryReceipt->DRID,
                        'dr_number' => (string) $deliveryReceipt->DRNumber,
                        'status' => 'inspected',
                        'link' => '/procurement/delivery-receipts/' . $deliveryReceipt->DRID,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt created successfully',
                'data' => $deliveryReceipt,
                'dr_picture_url' => Storage::disk('public')->url($deliveryReceipt->dr_picture)
            ], 201);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('DeliveryReceiptController@store failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'payload' => $request->except(['dr_picture']),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create delivery receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $deliveryReceipt = DeliveryReceipt::with([
                    'supplier:SupplierID,SupplierName',
                    'details.article',
                    'details.propertyType',
                    'details.unitOfMeasure',
                    'details.inventoryDetails:InventoryDetailID,SourceDRDetailID,PropertyNumber',
                    'requestedByUser:UserID,FullName,DepartmentID',
                    'requestedByUser.department:DepartmentID,DepartmentName',
                ])
                ->findOrFail($id);

            $deliveryReceipt->inspection_status = $deliveryReceipt->InspectionStatus ?? 'pending';
            $this->applyWorkflowStatus($deliveryReceipt);

            // Set receivedByUser explicitly
            $user = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);
            $deliveryReceipt->receivedByUser = $user ? [
                'UserID' => $user->UserID,
                'FullName' => $user->FullName
            ] : null;

            // Convenience fields for legacy frontend usage
            $deliveryReceipt->RequestedByName = $deliveryReceipt->requestedByUser?->FullName;
            $deliveryReceipt->DepartmentName = $deliveryReceipt->requestedByUser?->department?->DepartmentName;
            $deliveryReceipt->DepartmentID = $deliveryReceipt->requestedByUser?->DepartmentID;
            $deliveryReceipt->SupplierName = $deliveryReceipt->supplier?->SupplierName;
            $deliveryReceipt->supplier_name = $deliveryReceipt->supplier?->SupplierName;

            // Normalize relationship keys for frontend by converting the model to an array
$data = $deliveryReceipt->toArray();
            $data['is_partial'] = (bool) ($deliveryReceipt->IsPartial ?? false);
            $data['workflow_status'] = $deliveryReceipt->workflow_status;
            $data['workflow_status_label'] = $deliveryReceipt->workflow_status_label;

            // Resolve the RIS created at acknowledgment time (synthetic request pattern).
            $syntheticRequestNumber = 'REQ-' . date('Y') . '-DR-' . str_pad((string) $deliveryReceipt->DRID, 4, '0', STR_PAD_LEFT);
            $ris = \App\Models\RequisitionIssueSlip::query()
                ->whereHas('assetRequest', function ($q) use ($syntheticRequestNumber) {
                    $q->where('RequestNumber', $syntheticRequestNumber);
                })
                ->first(['id', 'ris_number', 'status']);

            $data['ris_id'] = $ris?->id;
            $data['ris_number'] = $ris?->ris_number;
            $data['ris_status'] = $ris?->status;
            // Build a map: inventory_detail_id => assignment rows
            $invDetailIds = collect($deliveryReceipt->details ?? [])
                ->flatMap(fn($det) => $det->inventoryDetails->pluck('InventoryDetailID'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $assignmentsByInvDetailId = [];
            if (!empty($invDetailIds)) {
                $rawAssignments = DB::table('property_issuance_assignments as pia')
                    ->join('propertyissuancedetail as pid', 'pid.IssuanceDetailID', '=', 'pia.issuance_detail_id')
                    ->whereIn('pid.InventoryDetailID', $invDetailIds)
                    ->select(
                        'pid.InventoryDetailID',
                        'pia.receiver_name',
                        'pia.position_office'
                    )
                    ->get();

                foreach ($rawAssignments as $row) {
                    $assignmentsByInvDetailId[(int) $row->InventoryDetailID] = [
                        'receiver_name'   => $row->receiver_name,
                        'position_office' => $row->position_office,
                    ];
                }
            }

            // Fallback name/position = requesting officer
            $fallbackName     = $deliveryReceipt->requestedByUser?->FullName ?? null;
            $fallbackPosition = $deliveryReceipt->requestedByUser?->department?->DepartmentName ?? null;

            if (!empty($data['details']) && is_array($data['details'])) {
                foreach ($data['details'] as &$d) {
                    $d['propertyType'] = $d['property_type'] ?? null;
                    $d['unitOfMeasure'] = $d['unit_of_measure'] ?? null;
                    $d['inventoryDetails'] = $d['inventory_details'] ?? [];
                    $d['propertyNumbers'] = collect($d['inventory_details'] ?? [])
                        ->pluck('PropertyNumber')
                        ->filter()
                        ->values()
                        ->all();

                    // Attach end user assignments per inventory detail; fallback to requesting officer
                    $endUserAssignments = [];
                    foreach (($d['inventory_details'] ?? []) as $invDet) {
                        $invDetId = (int) ($invDet['InventoryDetailID'] ?? 0);
                        $assignment = $invDetId > 0 ? ($assignmentsByInvDetailId[$invDetId] ?? null) : null;

                        $receiverName   = trim((string) ($assignment['receiver_name']   ?? ''));
                        $positionOffice = trim((string) ($assignment['position_office'] ?? ''));

                        // Fallback to requesting officer when blank
                        if ($receiverName === '') {
                            $receiverName   = $fallbackName ?? '';
                            $positionOffice = $fallbackPosition ?? '';
                        }

                        if ($invDetId > 0) {
                            $endUserAssignments[] = [
                                'inventory_detail_id' => $invDetId,
                                'receiver_name'       => $receiverName ?: null,
                                'position_office'     => $positionOffice ?: null,
                            ];
                        }
                    }
                    $d['end_user_assignments'] = $endUserAssignments;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'dr_picture_url' => $deliveryReceipt->dr_picture ? Storage::disk('public')->url($deliveryReceipt->dr_picture) : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery receipt not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
{
    try {
        $deliveryReceipt = DeliveryReceipt::findOrFail($id);

        // ── LOCK: block edits once the DR has been acknowledged ────────────────
        // Once acknowledged, inventory/RIS/issuance records already exist and
        // editing the DR would desync them. Use the /add-items flow instead.
        $isAcknowledged = \App\Models\PropertyIssuance::where('DRID', $deliveryReceipt->DRID)
            ->whereIn('Status', ['acknowledged', 'partially_acknowledged'])
            ->exists();

        if ($isAcknowledged) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit delivery receipt after acknowledgment. Inventory and issuance have already been created. Use the Add Items flow if you need to append items.',
            ], 403);
        }
        // ───────────────────────────────────────────────────────────────────────

        // For update: apply same backdate rules as store (use config)
        $backdateYears = (int) config('gso.dr_backdate_years', 5);
        $manilaNow = Carbon::now('Asia/Manila')->toDateString();
        $minDate = Carbon::now('Asia/Manila')->subYears($backdateYears)->toDateString();
        $dateRules = 'required|date|before_or_equal:' . $manilaNow . '|after_or_equal:' . $minDate;

        $validator = Validator::make($request->all(), [
            'dr_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('deliveryreceipt', 'DRNumber')->ignore($deliveryReceipt->DRID, 'DRID'),
            ],
            'supplier_id' => 'required|integer|exists:supplier,SupplierID',
            'requested_by_user_id' => 'required|integer|exists:user,UserID',
            'pr_number' => 'required|string|max:50',
            'po_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('deliveryreceipt', 'PONumber')->where(function($q) use ($request) {
                    return $q->where('SupplierID', (int) $request->supplier_id);
                })->ignore($deliveryReceipt->DRID, 'DRID'),
            ],
            'invoice_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('deliveryreceipt', 'InvoiceNumber')->where(function($q) use ($request) {
                    return $q->where('SupplierID', (int) $request->supplier_id);
                })->ignore($deliveryReceipt->DRID, 'DRID'),
            ],
            'date_received' => $dateRules,
            'remarks' => 'nullable|string',
            'dr_picture' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'details' => 'required|array|min:1',
            'details.*.dr_detail_id' => 'sometimes|integer|exists:deliveryreceiptdetail,DRDetailID',
            'details.*.article_id' => 'required|integer|exists:articles,ArticleID',
            'details.*.item_description' => 'required|string|max:255',
            'details.*.brand' => 'nullable|string|max:100',
            'details.*.model_number' => 'nullable|string|max:100',
            'details.*.serial_number' => 'nullable|string|max:100',
            'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
            'details.*.quantity_delivered' => 'required|integer|min:1',
            'details.*.quantity_requested' => 'required|integer|min:1',
            'details.*.change_reason' => 'nullable|string|max:255',
            'details.*.unit_of_measure_id' => 'nullable|integer|exists:unitofmeasure,UnitOfMeasureID',
            'details.*.unit_cost' => 'required|numeric|min:0',
            'assignments' => 'nullable',
            'items_still_pending_delivery' => 'nullable|boolean',
        ], [
            'dr_number.unique' => 'DR number already exists',
            'po_number.unique' => 'PO number already exists for this supplier',
            'invoice_number.unique' => 'Invoice number already exists for this supplier',
            'dr_picture.file' => 'DR picture must be a valid file',
            'dr_picture.mimes' => 'DR picture must be a JPEG, PNG, JPG, or GIF file',
            'dr_picture.max' => 'DR picture must not exceed 2MB',
        ]);

        $validator->after(function ($validator) use ($request, $id) {
            $details = $request->input('details', []);
            $articleIds = collect($details)->pluck('article_id')->filter()->unique()->values();
            $articleMap = Article::whereIn('ArticleID', $articleIds)
                ->get(['ArticleID', 'PropertyTypeID'])
                ->keyBy('ArticleID');

            $rawDrSerials = DB::table('deliveryreceiptdetail')
                ->whereNotNull('SerialNumber')
                ->where('SerialNumber', '!=', '')
                ->where('DRID', '!=', (int) $id)
                ->pluck('SerialNumber');

            $rawInvSerials = DB::table('propertyinventorydetail as pid')
                ->join('propertyinventory as pi', 'pi.InventoryID', '=', 'pid.InventoryID')
                ->whereNotNull('pid.SerialNumber')
                ->where('pid.SerialNumber', '!=', '')
                ->where('pi.DRID', '!=', (int) $id)
                ->pluck('pid.SerialNumber');

            $existingUsedSerials = [];
            foreach ($rawDrSerials->concat($rawInvSerials) as $raw) {
                foreach (explode(',', (string) $raw) as $s) {
                    $trimmed = strtolower(trim($s));
                    if ($trimmed !== '') {
                        $existingUsedSerials[$trimmed] = true;
                    }
                }
            }

            $seenInPayload = [];

            foreach ($details as $index => $detail) {
                $serial = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
                $serialParts = [];
                if ($serial !== '') {
                    $serialParts = array_values(array_filter(array_map('trim', explode(',', $serial)), fn($v) => $v !== ''));
                }
                $serialCount = count($serialParts);

                foreach ($serialParts as $sPart) {
                    $lowerPart = strtolower($sPart);
                    if (isset($seenInPayload[$lowerPart])) {
                        $validator->errors()->add("details.$index.serial_number", "Serial number '{$sPart}' is duplicated within this delivery receipt.");
                    } else {
                        $seenInPayload[$lowerPart] = true;
                    }

                    if (isset($existingUsedSerials[$lowerPart])) {
                        $validator->errors()->add("details.$index.serial_number", "Serial number '{$sPart}' is already used in another Delivery Receipt.");
                    }
                }

                $quantity = (int) ($detail['quantity_delivered'] ?? 0);
                if ($serialCount > 0 && $quantity !== $serialCount) {
                    $validator->errors()->add("details.$index.quantity_delivered", 'Quantity must equal the number of serial numbers provided');
                }

                $requested = (int) ($detail['quantity_requested'] ?? 0);
                if ($serialCount > 0 && $requested !== $serialCount) {
                    $validator->errors()->add("details.$index.quantity_requested", 'Quantity must equal the number of serial numbers provided');
                }

                $changeReason = isset($detail['change_reason']) ? trim((string) $detail['change_reason']) : '';
                if ($quantity !== $requested && $changeReason === '') {
                    $validator->errors()->add("details.$index.change_reason", 'Change reason is required when delivered differs from requested quantity');
                }

                $articleId = (int) ($detail['article_id'] ?? 0);
                $article = $articleMap->get($articleId);
                if ($article && (int) ($detail['property_type_id'] ?? 0) !== (int) $article->PropertyTypeID) {
                    $validator->errors()->add("details.$index.property_type_id", 'Property type must match the selected article.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $drPicturePath = null;
        if ($request->hasFile('dr_picture')) {
            $drPicturePath = $request->file('dr_picture')->store('dr_pictures', 'public');
        }

        DB::beginTransaction();

        $manilaNow = Carbon::now('Asia/Manila')->toDateString();
        if (Carbon::parse($request->date_received, 'Asia/Manila')->gt(Carbon::parse($manilaNow))) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => ['date_received' => ['Date received cannot be in the future.']]
            ], 422);
        }
        $requestedByUser = User::find((int) $request->requested_by_user_id);
        if (!$requestedByUser) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => ['requested_by_user_id' => ['Requested by user not found.']]
            ], 422);
        }

        $supplier = Supplier::find((int) $request->supplier_id);
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => ['supplier_id' => ['Supplier not found.']]
            ], 422);
        }

        $iarNumber = $deliveryReceipt->IARNumber ?: $this->generateDrIarNumber();

        $itemsStillPending = $request->has('items_still_pending_delivery')
            ? filter_var($request->input('items_still_pending_delivery', false), FILTER_VALIDATE_BOOLEAN)
            : (bool) ($deliveryReceipt->IsPartial ?? false);

        $deliveryReceipt->update([
            'DRNumber' => $request->dr_number,
            'IARNumber' => $iarNumber,
            'SupplierID' => (int) $supplier->SupplierID,
            'RequestedBy' => (int) $requestedByUser->UserID,
            'PRNumber' => $request->pr_number,
            'PONumber' => $request->po_number,
            'InvoiceNumber' => $request->invoice_number,
            'DateReceived' => $request->date_received,
            'InspectedBy' => $deliveryReceipt->InspectedBy ?? (Auth::id() ?: $deliveryReceipt->ReceivedBy),
            'DateInspected' => $deliveryReceipt->DateInspected ?? $request->date_received,
            'Remarks' => $request->remarks,
            ...$drPicturePath ? ['dr_picture' => $drPicturePath] : [],
            'InspectionStatus' => 'inspected',
            'InspectionCompletedAt' => $deliveryReceipt->InspectionCompletedAt ?? now(),
            'IsPartial' => $itemsStillPending,
        ]);

        // Handle details update
        $existingDetailIds = $deliveryReceipt->details()->pluck('DRDetailID')->toArray();
        $incomingDetailIds = collect($request->details)->pluck('dr_detail_id')->filter()->toArray();

        $detailsToDelete = array_diff($existingDetailIds, $incomingDetailIds);
        if (!empty($detailsToDelete)) {
            DeliveryReceiptDetail::whereIn('DRDetailID', $detailsToDelete)->delete();
        }

        $details = $request->details ?? [];
        $articleIds = collect($details)->pluck('article_id')->filter()->unique()->values();
        $articleMap = Article::whereIn('ArticleID', $articleIds)
            ->get(['ArticleID', 'PropertyTypeID', 'DefaultUnitOfMeasureID'])
            ->keyBy('ArticleID');

        foreach ($details as $detail) {
            $article = $articleMap->get((int) $detail['article_id']);
            if (!$article) {
                throw new \RuntimeException('Selected article was not found.');
            }

            $unitOfMeasureId = $detail['unit_of_measure_id'] ?? null;
            if (!$unitOfMeasureId) {
                $unitOfMeasureId = $article->DefaultUnitOfMeasureID;
            }

            $detailData = [
                'DRID' => $deliveryReceipt->DRID,
                'ArticleID' => $detail['article_id'],
                'ItemDescription' => $detail['item_description'],
                'Brand' => $detail['brand'] ?? null,
                'ModelNumber' => $detail['model_number'] ?? null,
                'PropertyTypeID' => $article->PropertyTypeID,
                'ChangeReason' => $detail['change_reason'] ?? null,
                'UnitOfMeasureID' => $unitOfMeasureId,
                'UnitCost' => $detail['unit_cost'],
                'InspectionStatus' => 'inspected',
            ];

            $serialRaw = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
            $quantityDelivered = (int) ($detail['quantity_delivered'] ?? 0);
            $quantityRequested = (int) ($detail['quantity_requested'] ?? 0);

            if (isset($detail['dr_detail_id']) && $detail['dr_detail_id']) {
                $existingId = (int) $detail['dr_detail_id'];
                $detailToUpdate = $detailData;
                $detailToUpdate['SerialNumber'] = $serialRaw ?: null;
                $detailToUpdate['QuantityDelivered'] = $quantityDelivered;
                $detailToUpdate['QuantityRequested'] = $quantityRequested;
                $detailToUpdate['TotalCost'] = $quantityDelivered * ($detail['unit_cost'] ?? 0);

                DeliveryReceiptDetail::where('DRDetailID', $existingId)
                    ->where('DRID', $deliveryReceipt->DRID)
                    ->update($detailToUpdate);
            } else {
                DeliveryReceiptDetail::create(array_merge($detailData, [
                    'SerialNumber' => $serialRaw ?: null,
                    'QuantityDelivered' => $quantityDelivered,
                    'QuantityRequested' => $quantityRequested,
                    'TotalCost' => $quantityDelivered * ($detail['unit_cost'] ?? 0),
                ]));
            }
        }

        $this->ingestInventoryFromDeliveryReceipt($deliveryReceipt);

        // ─────────────────────────────────────────────────────────────
        // Apply assignment changes if provided
        // ─────────────────────────────────────────────────────────────
        $rawAssignments = $request->input('assignments');
        if (is_string($rawAssignments)) {
            $rawAssignments = html_entity_decode($rawAssignments, ENT_QUOTES, 'UTF-8');
            $decoded = json_decode($rawAssignments, true);
            $assignments = is_array($decoded) ? $decoded : [];
        } elseif (is_array($rawAssignments)) {
            $assignments = $rawAssignments;
        } else {
            $assignments = [];
        }

        // Always re-run issuance assignment to ensure receiver names are correct.
        // Delete existing assignment rows and recreate from the submitted payload
        // (or fallback to requesting officer if assignments array is empty/blank).
        $issuanceDetailIds = DB::table('propertyissuancedetail as pid')
            ->join('propertyissuance as pi', 'pi.IssuanceID', '=', 'pid.IssuanceID')
            ->where('pi.DRID', (int) $deliveryReceipt->DRID)
            ->pluck('pid.IssuanceDetailID')
            ->all();

        if (!empty($issuanceDetailIds)) {
            DB::table('property_issuance_assignments')
                ->whereIn('issuance_detail_id', $issuanceDetailIds)
                ->delete();
        }

        $createdDetails = [];
        $currentDrDetails = $deliveryReceipt->details()->get()->keyBy('DRDetailID');
        foreach ($details as $idx => $detInput) {
            if (!empty($detInput['dr_detail_id']) && $currentDrDetails->has((int) $detInput['dr_detail_id'])) {
                $createdDetails[$idx] = $currentDrDetails->get((int) $detInput['dr_detail_id']);
            }
        }
        $allDetails = $deliveryReceipt->details()->orderBy('DRDetailID')->get();
        foreach ($allDetails as $idx => $det) {
            if (!isset($createdDetails[$idx])) {
                $createdDetails[$idx] = $det;
            }
        }

        $this->createIssuancesFromDeliveryReceipt($deliveryReceipt, $createdDetails, $assignments);
        // ─────────────────────────────────────────────────────────────

        DB::commit();

        $deliveryReceipt->load([
            'supplier:SupplierID,SupplierName',
            'details.propertyType',
            'details.unitOfMeasure',
            'details.article',
            'details.inventoryDetails:InventoryDetailID,SourceDRDetailID,PropertyNumber',
            'requestedByUser:UserID,FullName,DepartmentID',
            'requestedByUser.department:DepartmentID,DepartmentName',
        ]);
        $user = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);
        $deliveryReceipt->receivedByUser = $user ? [
            'UserID' => $user->UserID,
            'FullName' => $user->FullName
        ] : null;

        $deliveryReceipt->RequestedByName = $deliveryReceipt->requestedByUser?->FullName;
        $deliveryReceipt->DepartmentName = $deliveryReceipt->requestedByUser?->department?->DepartmentName;
        $deliveryReceipt->DepartmentID = $deliveryReceipt->requestedByUser?->DepartmentID;
        $deliveryReceipt->SupplierName = $deliveryReceipt->supplier?->SupplierName;
        $deliveryReceipt->supplier_name = $deliveryReceipt->supplier?->SupplierName;

        $recipientUserIds = $this->getProcurementNotificationUserIds(
            (int) ($deliveryReceipt->requestedByUser?->DepartmentID ?? 0),
            (int) (Auth::id() ?? 0)
        );

        app(RoleNotificationService::class)->notifyUsers(
            $recipientUserIds,
            'delivery_receipt.updated',
            'Delivery Receipt Updated',
            sprintf('Delivery receipt %s has been updated.', (string) $deliveryReceipt->DRNumber),
            [
                'dr_id' => (string) $deliveryReceipt->DRID,
                'dr_number' => (string) $deliveryReceipt->DRNumber,
                'status' => 'inspected',
                'link' => '/procurement/delivery-receipts/' . $deliveryReceipt->DRID,
            ]
        );

        if ((int) $deliveryReceipt->RequestedBy > 0 && !in_array((int) $deliveryReceipt->RequestedBy, $recipientUserIds, true)) {
            app(RoleNotificationService::class)->notifyUser(
                (int) $deliveryReceipt->RequestedBy,
                'delivery_receipt.updated',
                'Delivery Receipt Updated',
                sprintf('Delivery receipt %s linked to your request has been updated.', (string) $deliveryReceipt->DRNumber),
                [
                    'dr_id' => (string) $deliveryReceipt->DRID,
                    'dr_number' => (string) $deliveryReceipt->DRNumber,
                    'status' => 'inspected',
                    'link' => '/procurement/delivery-receipts/' . $deliveryReceipt->DRID,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Delivery receipt updated successfully',
            'data' => $deliveryReceipt,
            'dr_picture_url' => Storage::disk('public')->url($deliveryReceipt->dr_picture)
        ]);
    } catch (\Exception $e) {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        Log::error('DeliveryReceiptController@update failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'dr_id' => $id,
            'user_id' => Auth::id(),
            'payload' => $request->except(['dr_picture']),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to update delivery receipt',
            'error' => $e->getMessage()
        ], 500);
    }
}

    private function ingestInventoryFromDeliveryReceipt(DeliveryReceipt $deliveryReceipt, ?array $onlyDetailIds = null): void
    {
        $deliveryReceipt->loadMissing(['details', 'requestedByUser']);
        $details = $deliveryReceipt->details;

        if ($onlyDetailIds !== null) {
            $onlyDetailIds = array_map('intval', $onlyDetailIds);
            $details = $details->whereIn('DRDetailID', $onlyDetailIds);
        }

        if ($details->isEmpty()) {
            return;
        }

        $alreadyIngestedDetailIds = PropertyInventoryDetail::query()
            ->whereIn('SourceDRDetailID', $details->pluck('DRDetailID')->all())
            ->pluck('SourceDRDetailID')
            ->map(fn ($id) => (int) $id)
            ->all();

        $details = $details->filter(function ($detail) use ($alreadyIngestedDetailIds) {
            return ! in_array((int) $detail->DRDetailID, $alreadyIngestedDetailIds, true);
        });

        if ($details->isEmpty()) {
            return;
        }

        $endUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);

        $inventory = PropertyInventory::where('DRID', $deliveryReceipt->DRID)->first();
        if (! $inventory) {
            $inventory = PropertyInventory::create([
                'InventoryNumber' => 'INV-' . date('Ymd-His') . '-' . $deliveryReceipt->DRID,
                'DRID' => $deliveryReceipt->DRID,
                'IARID' => null,
                'DateReceived' => $deliveryReceipt->DateInspected ?? $deliveryReceipt->DateReceived ?? now(),
                'Remarks' => trim((string) ($deliveryReceipt->Remarks ?? '')) !== ''
                    ? trim((string) $deliveryReceipt->Remarks)
                    : 'Auto-ingested from DR #' . $deliveryReceipt->DRNumber,
                'CreatedDate' => now(),
            ]);
        }

        $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'To be issued')
            ->orWhere('StatusName', 'For issuance')
            ->orWhere('StatusName', 'For Issuance')
            ->value('PropertyStatusID');

        if (! $toBeIssuedStatusId) {
            $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'Available')
                ->value('PropertyStatusID') ?? 1;
        }

        foreach ($details as $detail) {
            $deliveredQty = (int) ($detail->QuantityDelivered ?? 0);
            if ($deliveredQty <= 0) {
                continue;
            }

            $brand = $detail->Brand;
            $model = $detail->ModelNumber;
            $serialRaw = $detail->SerialNumber ?? '';
            $serialParts = $serialRaw === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $serialRaw)), fn($v) => $v !== ''));
            $conditionId = 1;

            $propertyNumbers = $this->reservePropertyNumbers($deliveredQty);

            foreach ($propertyNumbers as $idx => $propertyNumber) {
                $serialForThis = $serialParts[$idx] ?? null;

                $inventoryDetail = PropertyInventoryDetail::create([
                    'InventoryID' => $inventory->InventoryID,
                    'PropertyTypeID' => $detail->PropertyTypeID,
                    'PropertyNumber' => $propertyNumber,
                    'ConditionID' => $conditionId,
                    'Brand' => $brand,
                    'ModelNumber' => $model,
                    'SerialNumber' => $serialForThis,
                    'Description' => $detail->ItemDescription,
                    'Quantity' => 1,
                    'QuantityAvailable' => 1,
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'UnitOfMeasureID' => $detail->UnitOfMeasureID,
                    'UnitCost' => $detail->UnitCost,
                    'TotalCost' => $detail->UnitCost,
                    'SourceIARDetailID' => null,
                    'SourceDRDetailID' => $detail->DRDetailID,
                    'Remarks' => $detail->ChangeReason,
                    'CreatedDate' => now(),
                ]);

                $actorId = (int) (Auth::id() ?? 0);
                if ($actorId > 0) {
                    AssetAuditLog::createLog(
                        'inventory_detail',
                        (int) $inventoryDetail->InventoryDetailID,
                        'created_from_dr',
                        $actorId,
                        [
                            'inventory_id' => (int) $inventory->InventoryID,
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
                    'Description' => $detail->ItemDescription,
                    'AssignedDate' => now()->toDateString(),
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'ConditionID' => $conditionId,
                    'Remarks' => 'Auto-created on DR intake',
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
                    ['PropertyID' => (int) $assignedProperty->PropertyID],
                    [
                        'InventoryDetailID' => $inventoryDetail->InventoryDetailID,
                        'DepartmentID' => (int) $assignedProperty->DepartmentID,
                        'QRCodeData' => $qrPayload,
                        'DateGenerated' => now(),
                    ]
                );
            }
        }
    }

    /**
     * Reserve a contiguous set of property numbers for the current year.
     *
     * @return array<int, string>
     */
    private function reservePropertyNumbers(int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        $year = (int) date('Y');
        $prefix = $year . '-';

        $latestItem = PropertyInventoryDetail::withTrashed()
            ->where('PropertyNumber', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByRaw("CAST(SUBSTRING_INDEX(PropertyNumber, '-', -1) AS UNSIGNED) DESC")
            ->orderBy('PropertyNumber', 'desc')
            ->first();

        $currentSequence = 0;
        if ($latestItem && preg_match('/-(\d+)$/', (string) $latestItem->PropertyNumber, $matches)) {
            $currentSequence = (int) $matches[1];
        }

        $numbers = [];
        for ($i = 0; $i < $count; $i++) {
            $currentSequence++;
            $numbers[] = $prefix . str_pad((string) $currentSequence, 6, '0', STR_PAD_LEFT);
        }

        return $numbers;
    }

    private function createIssuancesFromDeliveryReceipt(DeliveryReceipt $deliveryReceipt, array $createdDetails = [], array $assignments = []): void
    {
        $inventoryDetails = PropertyInventoryDetail::with([
                'drDetail',
                'propertyType.propertyClassification',
                'unitOfMeasure',
            ])
            ->whereHas('drDetail', function ($q) use ($deliveryReceipt) {
                $q->where('DRID', (int) $deliveryReceipt->DRID);
            })
            ->get();

        if ($inventoryDetails->isEmpty()) {
            return;
        }

        $deliveryReceipt->loadMissing(['requestedByUser']);
        $resolvedEndUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);
        $targetDeptId = (int) ($deliveryReceipt->requestedByUser?->DepartmentID ?: $resolvedEndUser->DepartmentID);

        // Pre-process assignments mapping by DRDetailID
        $assignmentsByDrDetailId = [];
        foreach ($assignments as $a) {
            $drDetailId = null;
            if (!empty($a['dr_detail_id'])) {
                $drDetailId = (int) $a['dr_detail_id'];
            } elseif (isset($a['item_index'])) {
                $idx = (int) $a['item_index'];
                if (isset($createdDetails[$idx])) {
                    $drDetailId = (int) $createdDetails[$idx]->DRDetailID;
                }
            }
            if ($drDetailId === null && count($createdDetails) === 1) {
                $first = reset($createdDetails);
                $drDetailId = (int) $first->DRDetailID;
            }

            if ($drDetailId !== null) {
                $recName = trim((string) ($a['receiver_name'] ?? ''));
                $pos = trim((string) ($a['position_office'] ?? ''));
                $assignmentsByDrDetailId[$drDetailId][] = [
                    'receiver_name' => $recName !== '' ? $recName : null,
                    'position_office' => $pos !== '' ? $pos : null,
                    'remaining_qty' => max(1, (int) ($a['quantity'] ?? 1)),
                ];
            }
        }

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
        $issuedAt = $deliveryReceipt->DateInspected ?? $deliveryReceipt->DateReceived ?? now();

        foreach ($groupedByType as $documentType => $details) {
            $documentType = (string) $documentType;
            $issuance = $existingIssuances->get($documentType);

            if (! $issuance) {
                $documentNumber = $this->generateDocumentNumber($documentType);
                $issuance = PropertyIssuance::create([
                    'RequestID' => null,
                    'DRID' => (int) $deliveryReceipt->DRID,
                    'IssuedBy' => Auth::id() ?: (int) ($deliveryReceipt->InspectedBy ?? 0),
                    'IssuerSignature' => null,
                    'IssuerSignatureSource' => null,
                    'IssuedTo' => (int) $resolvedEndUser->EndUserID,
                    'IssuedToName' => $issuedToName,
                    'DocumentType' => $documentType,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $issuedAt,
                    'Remarks' => 'Auto-created from DR inspection',
                    'Status' => 'pending_acknowledgment',
                    'AcknowledgedBy' => null,
                    'AcknowledgedAt' => null,
                    'CreatedDate' => now(),
                ]);

                $existingIssuances->put($documentType, $issuance);
            }

            foreach ($details as $detail) {
                $issuanceDetail = PropertyIssuanceDetail::where('IssuanceID', $issuance->IssuanceID)
                    ->where('InventoryDetailID', $detail->InventoryDetailID)
                    ->first();

                $itemDescription = $detail->drDetail?->ItemDescription ?? $detail->Description ?? 'Item';
                $unitName = $detail->unitOfMeasure?->UnitName ?? null;

                if (! $issuanceDetail) {
                    $issuanceDetail = PropertyIssuanceDetail::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'RequestDetailID' => null,
                        'InventoryDetailID' => $detail->InventoryDetailID,
                        'ItemDescription' => $itemDescription,
                        'Unit' => $unitName,
                        'QuantityIssued' => 1,
                        'Remarks' => $issuance->Remarks,
                    ]);
                }

                // Determine end user receiver for this specific asset item
                $sourceDrDetailId = (int) ($detail->SourceDRDetailID ?? 0);
                $assignedReceiverName = null;
                $assignedPosition = null;

                if ($sourceDrDetailId > 0 && !empty($assignmentsByDrDetailId[$sourceDrDetailId])) {
                    foreach ($assignmentsByDrDetailId[$sourceDrDetailId] as $k => &$aItem) {
                        if ($aItem['remaining_qty'] > 0) {
                            $assignedReceiverName = $aItem['receiver_name'];
                            $assignedPosition = $aItem['position_office'];
                            $aItem['remaining_qty']--;
                            if ($aItem['remaining_qty'] <= 0) {
                                unset($assignmentsByDrDetailId[$sourceDrDetailId][$k]);
                                $assignmentsByDrDetailId[$sourceDrDetailId] = array_values($assignmentsByDrDetailId[$sourceDrDetailId]);
                            }
                            break;
                        }
                    }
                    unset($aItem);
                }

                // Fallback requesting officer info
                $fallbackOfficerName = $deliveryReceipt->requestedByUser?->FullName ?? $resolvedEndUser->FullName ?? 'Unassigned';
                $fallbackOfficerDept = $deliveryReceipt->requestedByUser?->department?->DepartmentName ?? null;

                // If receiver_name is blank or unassigned, fallback to requesting officer
                if (empty($assignedReceiverName)) {
                    $assignedReceiverName = $fallbackOfficerName;
                    if (empty($assignedPosition)) {
                        $assignedPosition = $fallbackOfficerDept;
                    }
                } else {
                    // Receiver name provided! If position/office was left blank, fallback to requesting officer's department/position
                    if (empty($assignedPosition)) {
                        $assignedPosition = $fallbackOfficerDept;
                    }
                }

                // Normalize receiver name to proper case before any DB operation
                $assignedReceiverName = ucwords(strtolower(trim($assignedReceiverName)));

                // Save or update property_issuance_assignments table
               $existingAssignment = DB::table('property_issuance_assignments')
                    ->where('issuance_id', (int) $issuance->IssuanceID)
                    ->where('receiver_name', $assignedReceiverName)
                    ->first();

                if ($existingAssignment) {
                    DB::table('property_issuance_assignments')
                        ->where('id', $existingAssignment->id)
                        ->update([
                            'issuance_detail_id' => (int) $issuanceDetail->IssuanceDetailID, // repoint to latest
                            'position_office' => $assignedPosition,   // overwrite position
                            'quantity' => $existingAssignment->quantity + 1,  // stack quantity
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('property_issuance_assignments')->insert([
                        'issuance_id' => (int) $issuance->IssuanceID,
                        'issuance_detail_id' => (int) $issuanceDetail->IssuanceDetailID,
                        'receiver_name' => $assignedReceiverName,
                        'position_office' => $assignedPosition,
                        'quantity' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                // Resolve EndUser record for AssignedProperty — normalize name, never create duplicates
                $positionId = 2;
                if (!empty($assignedPosition)) {
                    $pos = Position::firstOrCreate(['PositionName' => trim($assignedPosition)]);
                    $positionId = (int) $pos->PositionID;
                }

                $receiverEndUser = EndUser::where('FullName', $assignedReceiverName)
                    ->where('DepartmentID', $targetDeptId)
                    ->first();

                if ($receiverEndUser) {
                    $receiverEndUser->update([
                        'IsActive' => 1,
                        'PositionID' => $positionId,
                    ]);
                } else {
                    $receiverEndUser = EndUser::create([
                        'FullName' => $assignedReceiverName,
                        'DepartmentID' => $targetDeptId,
                        'PositionID' => $positionId,
                        'IsActive' => 1,
                    ]);
                }

                // Update inventory availability and create/update AssignedProperty record
                $assignedProperty = AssignedProperty::where('InventoryDetailID', $detail->InventoryDetailID)->first();
                if (! $assignedProperty) {
                    $availableQty = (int) ($detail->QuantityAvailable ?? 0);
                    if ($availableQty > 0) {
                        $detail->decrement('QuantityAvailable', 1);
                        if ($detail->fresh()->QuantityAvailable <= 0) {
                            $detail->update(['PropertyStatusID' => 2]);
                        }
                    } else {
                        $detail->update(['PropertyStatusID' => 2]);
                    }
                }

                $assignedPayload = [
                    'InventoryDetailID' => $detail->InventoryDetailID,
                    'EndUserID' => (int) $receiverEndUser->EndUserID,
                    'DepartmentID' => $targetDeptId,
                    'PropertyCardNumber' => $issuance->DocumentNumber,
                    'Description' => $detail->Description ?? $itemDescription,
                    'AssignedDate' => $issuedAt,
                    'PropertyStatusID' => 2,
                    'ConditionID' => (int) ($detail->ConditionID ?? 1),
                    'Remarks' => $issuance->Remarks,
                ];

                if ($assignedProperty) {
                    $assignedProperty->fill($assignedPayload);
                    $assignedProperty->save();
                } else {
                    $assignedProperty = AssignedProperty::create($assignedPayload);
                }

                // ensure a QR code exists for this assignment (mirrors PropertyIssuanceController behavior)
                if (method_exists($this, 'ensureQrCodeForDetail')) {
                    $this->ensureQrCodeForDetail($detail, $assignedProperty);
                }
            }
        }
    }

    private function resolveEndUserForDeliveryReceipt(DeliveryReceipt $deliveryReceipt): EndUser
    {
        $requester = $deliveryReceipt->requestedByUser;
        $departmentId = (int) ($requester?->DepartmentID ?? 0);
        $fullName = $requester?->FullName ?? null;

        if (! $departmentId || ! $fullName) {
            throw new \Exception('Unable to resolve EndUser for delivery receipt inspection.');
        }

        // Normalize to proper case before any lookup or insert
        $fullName = ucwords(strtolower(trim($fullName)));

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

    /**
     * @return array<int>
     */
    private function getProcurementNotificationUserIds(int $departmentId, ?int $excludeUserId = null): array
    {
        $query = User::query()
            ->select('UserID')
            ->where('IsActive', 1)
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

    private function generateDrIarNumber(): string
    {
        $year = date('Y');
        $prefix = 'IAR-' . $year . '-';

        $latestDr = DB::table('deliveryreceipt')
            ->where('IARNumber', 'like', $prefix . '%')
            ->orderByDesc('IARNumber')
            ->value('IARNumber');

        $latestIar = DB::table('inspectionacceptancereport')
            ->where('IARNumber', 'like', $prefix . '%')
            ->orderByDesc('IARNumber')
            ->value('IARNumber');

        $maxSequence = 0;
        foreach ([$latestDr, $latestIar] as $value) {
            if ($value && preg_match('/^IAR-\d{4}-(\d{4,})$/', (string) $value, $matches)) {
                $maxSequence = max($maxSequence, (int) $matches[1]);
            }
        }

        do {
            $maxSequence++;
            $candidate = $prefix . str_pad((string) $maxSequence, 4, '0', STR_PAD_LEFT);

            $exists = DB::table('deliveryreceipt')->where('IARNumber', $candidate)->exists()
                || DB::table('inspectionacceptancereport')->where('IARNumber', $candidate)->exists();
        } while ($exists);

        return $candidate;
    }

    private function generateTempDrNumber(): string
    {
        $year = date('Y');
        $prefix = 'TEMP-DR-' . $year . '-';

        $latestDr = DB::table('deliveryreceipt')
            ->where('DRNumber', 'like', $prefix . '%')
            ->orderByDesc('DRNumber')
            ->value('DRNumber');

        $maxSequence = 0;
        if ($latestDr && preg_match('/^TEMP-DR-\d{4}-(\d{4,})$/', (string) $latestDr, $matches)) {
            $maxSequence = (int) $matches[1];
        }

        do {
            $maxSequence++;
            $candidate = $prefix . str_pad((string) $maxSequence, 4, '0', STR_PAD_LEFT);
            $exists = DB::table('deliveryreceipt')->where('DRNumber', $candidate)->exists();
        } while ($exists);

        return $candidate;
    }

    private function isDuplicateIarNumberException(QueryException $exception): bool
    {
        $message = (string) $exception->getMessage();

        return str_contains($message, 'ux_deliveryreceipt_iarnumber')
            || str_contains($message, "Duplicate entry") && str_contains($message, 'IAR-');
    }

    private function generateDocumentNumber(string $type): string
    {
        $year = date('Y');
        $prefix = $type === 'PAR' ? "PAR-{$year}-" : "ICS-{$year}-";

        $lastIssuance = \App\Models\PropertyIssuance::where('DocumentType', $type)
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

    public function addItems(Request $request, $id): JsonResponse
    {
        try {
            $deliveryReceipt = DeliveryReceipt::findOrFail($id);

            if (! (bool) ($deliveryReceipt->IsPartial ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Items can only be added to partial delivery receipts.',
                ], 403);
            }

            if ($deliveryReceipt->FinalizedAt !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'This delivery receipt has already been finalized.',
                ], 403);
            }

            if (($deliveryReceipt->InspectionStatus ?? 'pending') !== 'inspected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery receipt must be inspected before adding items.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'details' => 'required|array|min:1',
                'details.*.dr_detail_id' => 'nullable|integer|exists:deliveryreceiptdetail,DRDetailID',
                'details.*.article_id' => 'required|integer|exists:articles,ArticleID',
                'details.*.item_description' => 'required|string|max:255',
                'details.*.brand' => 'nullable|string|max:100',
                'details.*.model_number' => 'nullable|string|max:100',
                'details.*.serial_number' => 'nullable|string|max:100',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
                'details.*.quantity_requested' => 'required|integer|min:1',
                'details.*.change_reason' => 'nullable|string|max:255',
                'details.*.unit_of_measure_id' => 'nullable|integer|exists:unitofmeasure,UnitOfMeasureID',
                'details.*.unit_cost' => 'required|numeric|min:0',
            ]);

            $validator->after(function ($validator) use ($request, $deliveryReceipt) {
                $this->validateDeliveryReceiptDetails($validator, $request->input('details', []), (int) $deliveryReceipt->DRID);
            });

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $details = $request->details ?? [];
            $articleIds = collect($details)->pluck('article_id')->filter()->unique()->values();
            $articleMap = Article::whereIn('ArticleID', $articleIds)
                ->get(['ArticleID', 'PropertyTypeID', 'DefaultUnitOfMeasureID'])
                ->keyBy('ArticleID');

            $newDetailIds = [];
            $updatedDetailIds = [];

            foreach ($details as $detail) {
                $article = $articleMap->get((int) $detail['article_id']);
                if (! $article) {
                    throw new \RuntimeException('Selected article was not found.');
                }

                $unitOfMeasureId = $detail['unit_of_measure_id'] ?? null;
                if (! $unitOfMeasureId) {
                    $unitOfMeasureId = $article->DefaultUnitOfMeasureID;
                }

                $serialRaw = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
                $quantityDelivered = (int) ($detail['quantity_delivered'] ?? 0);
                $quantityRequested = (int) ($detail['quantity_requested'] ?? 0);

                if (!empty($detail['dr_detail_id'])) {
                    // Update existing item
                    $drDetailId = (int) $detail['dr_detail_id'];
                    $existingDetail = DeliveryReceiptDetail::where('DRDetailID', $drDetailId)
                        ->where('DRID', $deliveryReceipt->DRID)
                        ->first();

                    if ($existingDetail) {
                        $oldQuantityDelivered = (int) $existingDetail->QuantityDelivered;
                        $existingDetail->update([
                            'ArticleID' => $detail['article_id'],
                            'ItemDescription' => $detail['item_description'],
                            'Brand' => $detail['brand'] ?? null,
                            'ModelNumber' => $detail['model_number'] ?? null,
                            'SerialNumber' => $serialRaw ?: null,
                            'PropertyTypeID' => $article->PropertyTypeID,
                            'QuantityDelivered' => $quantityDelivered,
                            'QuantityRequested' => $quantityRequested,
                            'ChangeReason' => $detail['change_reason'] ?? null,
                            'UnitOfMeasureID' => $unitOfMeasureId,
                            'UnitCost' => $detail['unit_cost'],
                            'TotalCost' => $quantityDelivered * $detail['unit_cost'],
                        ]);

                        $this->syncInventoryForUpdatedDrDetail($deliveryReceipt, $existingDetail, $oldQuantityDelivered);
                        $updatedDetailIds[] = $drDetailId;
                    }
                } else {
                    // Create new item
                    $createdDetail = DeliveryReceiptDetail::create([
                        'DRID' => $deliveryReceipt->DRID,
                        'ArticleID' => $detail['article_id'],
                        'ItemDescription' => $detail['item_description'],
                        'Brand' => $detail['brand'] ?? null,
                        'ModelNumber' => $detail['model_number'] ?? null,
                        'SerialNumber' => $serialRaw ?: null,
                        'PropertyTypeID' => $article->PropertyTypeID,
                        'QuantityDelivered' => $quantityDelivered,
                        'QuantityRequested' => $quantityRequested,
                        'ChangeReason' => $detail['change_reason'] ?? null,
                        'UnitOfMeasureID' => $unitOfMeasureId,
                        'UnitCost' => $detail['unit_cost'],
                        'TotalCost' => $quantityDelivered * $detail['unit_cost'],
                        'InspectionStatus' => 'inspected',
                    ]);

                    $newDetailIds[] = (int) $createdDetail->DRDetailID;
                }
            }

            if (!empty($newDetailIds)) {
                $this->ingestInventoryFromDeliveryReceipt($deliveryReceipt, $newDetailIds);
            }

            if ($request->boolean('finalize_dr')) {
                $deliveryReceipt->update([
                    'IsPartial' => false,
                    'FinalizedAt' => now(),
                ]);

                PropertyIssuance::where('DRID', $deliveryReceipt->DRID)
                    ->where('Status', 'partially_acknowledged')
                    ->update([
                        'Status' => 'acknowledged',
                        'AcknowledgedAt' => now(),
                    ]);
            }

            DB::commit();

            $actorId = (int) (Auth::id() ?? 0);
            if ($actorId > 0) {
                foreach ($newDetailIds as $detailId) {
                    AssetAuditLog::createLog(
                        'delivery_receipt_detail',
                        (int) $detailId,
                        'created',
                        $actorId,
                        [
                            'dr_id' => (int) $deliveryReceipt->DRID,
                            'dr_number' => $deliveryReceipt->DRNumber,
                            'partial_add' => true,
                        ]
                    );
                }
                foreach ($updatedDetailIds as $detailId) {
                    AssetAuditLog::createLog(
                        'delivery_receipt_detail',
                        (int) $detailId,
                        'updated',
                        $actorId,
                        [
                            'dr_id' => (int) $deliveryReceipt->DRID,
                            'dr_number' => $deliveryReceipt->DRNumber,
                            'partial_update' => true,
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => $request->boolean('finalize_dr')
                    ? 'Delivery receipt items saved and finalized successfully'
                    : 'Delivery receipt items saved successfully',
                'data' => $this->loadDeliveryReceiptResponse($deliveryReceipt->fresh()),
            ]);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('DeliveryReceiptController@addItems failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'dr_id' => $id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add items to delivery receipt',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function finalize($id): JsonResponse
    {
        try {
            $deliveryReceipt = DeliveryReceipt::findOrFail($id);

            // If already finalized, return success idempotently
            if (! (bool) ($deliveryReceipt->IsPartial ?? false) && $deliveryReceipt->FinalizedAt !== null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Delivery receipt has already been finalized.',
                    'data' => $this->loadDeliveryReceiptResponse($deliveryReceipt->fresh()),
                ]);
            }

            $deliveryReceipt->update([
                'IsPartial' => false,
                'FinalizedAt' => now(),
            ]);

            // Update any existing partially_acknowledged issuances for this DR to acknowledged
            PropertyIssuance::where('DRID', $deliveryReceipt->DRID)
                ->where('Status', 'partially_acknowledged')
                ->update([
                    'Status' => 'acknowledged',
                    'AcknowledgedAt' => now(),
                ]);

            $actorId = (int) (Auth::id() ?? 0);
            if ($actorId > 0) {
                AssetAuditLog::createLog(
                    'delivery_receipt',
                    (int) $deliveryReceipt->DRID,
                    'finalized',
                    $actorId,
                    [
                        'dr_number' => $deliveryReceipt->DRNumber,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt finalized successfully',
                'data' => $this->loadDeliveryReceiptResponse($deliveryReceipt->fresh()),
            ]);
        } catch (\Exception $e) {
            Log::error('DeliveryReceiptController@finalize failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'dr_id' => $id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to finalize delivery receipt',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function syncInventoryForUpdatedDrDetail(DeliveryReceipt $deliveryReceipt, DeliveryReceiptDetail $detail, int $oldQuantity): void
    {
        $existingInvDetails = PropertyInventoryDetail::where('SourceDRDetailID', $detail->DRDetailID)
            ->orderBy('InventoryDetailID', 'asc')
            ->get();

        $serialRaw = $detail->SerialNumber ?? '';
        $serialParts = $serialRaw === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $serialRaw)), fn ($v) => $v !== ''));

        // 1. Update existing inventory records with updated properties, costs, and serial numbers
        foreach ($existingInvDetails as $idx => $invDetail) {
            $assignedSerial = $serialParts[$idx] ?? null;
            $invDetail->update([
                'PropertyTypeID' => $detail->PropertyTypeID,
                'Brand' => $detail->Brand,
                'ModelNumber' => $detail->ModelNumber,
                'SerialNumber' => $assignedSerial,
                'Description' => $detail->ItemDescription,
                'UnitOfMeasureID' => $detail->UnitOfMeasureID,
                'UnitCost' => $detail->UnitCost,
                'TotalCost' => $detail->UnitCost,
                'Remarks' => $detail->ChangeReason,
            ]);
        }

        $newDeliveredQty = (int) $detail->QuantityDelivered;
        $currentCount = $existingInvDetails->count();

        // 2. If quantity delivered increased, create additional inventory detail records
        if ($newDeliveredQty > $currentCount) {
            $inventory = PropertyInventory::where('DRID', $deliveryReceipt->DRID)->first();
            if (! $inventory) {
                $inventory = PropertyInventory::create([
                    'InventoryNumber' => 'INV-' . date('Ymd-His') . '-' . $deliveryReceipt->DRID,
                    'DRID' => $deliveryReceipt->DRID,
                    'IARID' => null,
                    'DateReceived' => $deliveryReceipt->DateInspected ?? $deliveryReceipt->DateReceived ?? now(),
                    'Remarks' => trim((string) ($deliveryReceipt->Remarks ?? '')) !== ''
                        ? trim((string) $deliveryReceipt->Remarks)
                        : 'Auto-ingested from DR #' . $deliveryReceipt->DRNumber,
                    'CreatedDate' => now(),
                ]);
            }

            $toBeIssuedStatusId = PropertyStatus::where('StatusName', 'To be issued')
                ->orWhere('StatusName', 'For issuance')
                ->orWhere('StatusName', 'For Issuance')
                ->value('PropertyStatusID') ?? 1;

            $endUser = $this->resolveEndUserForDeliveryReceipt($deliveryReceipt);
            $qtyToAdd = $newDeliveredQty - $currentCount;
            $propertyNumbers = $this->reservePropertyNumbers($qtyToAdd);

            foreach ($propertyNumbers as $addIdx => $propertyNumber) {
                $overallIdx = $currentCount + $addIdx;
                $serialForThis = $serialParts[$overallIdx] ?? null;

                $newInvDetail = PropertyInventoryDetail::create([
                    'InventoryID' => $inventory->InventoryID,
                    'PropertyTypeID' => $detail->PropertyTypeID,
                    'PropertyNumber' => $propertyNumber,
                    'ConditionID' => 1,
                    'Brand' => $detail->Brand,
                    'ModelNumber' => $detail->ModelNumber,
                    'SerialNumber' => $serialForThis,
                    'Description' => $detail->ItemDescription,
                    'Quantity' => 1,
                    'QuantityAvailable' => 1,
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'UnitOfMeasureID' => $detail->UnitOfMeasureID,
                    'UnitCost' => $detail->UnitCost,
                    'TotalCost' => $detail->UnitCost,
                    'SourceIARDetailID' => null,
                    'SourceDRDetailID' => $detail->DRDetailID,
                    'Remarks' => $detail->ChangeReason,
                    'CreatedDate' => now(),
                ]);

                $assignedPayload = [
                    'InventoryDetailID' => $newInvDetail->InventoryDetailID,
                    'EndUserID' => $endUser->EndUserID,
                    'DepartmentID' => $endUser->DepartmentID,
                    'PropertyCardNumber' => null,
                    'Description' => $detail->ItemDescription,
                    'AssignedDate' => now()->toDateString(),
                    'PropertyStatusID' => $toBeIssuedStatusId,
                    'ConditionID' => 1,
                    'Remarks' => 'Auto-created on DR intake',
                ];

                $assignedProperty = AssignedProperty::create($assignedPayload);

                $qrPayload = $this->buildQrPayloadForProperty((int) $assignedProperty->PropertyID);
                QrCode::updateOrCreate(
                    ['PropertyID' => (int) $assignedProperty->PropertyID],
                    [
                        'InventoryDetailID' => $newInvDetail->InventoryDetailID,
                        'DepartmentID' => (int) $assignedProperty->DepartmentID,
                        'QRCodeData' => $qrPayload,
                        'DateGenerated' => now(),
                    ]
                );
            }
        }
    }

    /**
     * @param  \Illuminate\Validation\Validator  $validator
     * @param  array<int, array<string, mixed>>  $details
     */
    private function validateDeliveryReceiptDetails($validator, array $details, ?int $excludeDrId = null): void
    {
        $articleIds = collect($details)->pluck('article_id')->filter()->unique()->values();
        $articleMap = Article::whereIn('ArticleID', $articleIds)
            ->get(['ArticleID', 'PropertyTypeID'])
            ->keyBy('ArticleID');

        $rawDrSerialsQuery = DB::table('deliveryreceiptdetail')
            ->whereNotNull('SerialNumber')
            ->where('SerialNumber', '!=', '');

        if ($excludeDrId) {
            $rawDrSerialsQuery->where('DRID', '!=', $excludeDrId);
        }

        $rawDrSerials = $rawDrSerialsQuery->pluck('SerialNumber');

        $rawInvSerialsQuery = DB::table('propertyinventorydetail as pid')
            ->join('propertyinventory as pi', 'pi.InventoryID', '=', 'pid.InventoryID')
            ->whereNotNull('pid.SerialNumber')
            ->where('pid.SerialNumber', '!=', '');

        if ($excludeDrId) {
            $rawInvSerialsQuery->where('pi.DRID', '!=', $excludeDrId);
        }

        $rawInvSerials = $rawInvSerialsQuery->pluck('pid.SerialNumber');

        $existingUsedSerials = [];
        foreach ($rawDrSerials->concat($rawInvSerials) as $raw) {
            foreach (explode(',', (string) $raw) as $s) {
                $trimmed = strtolower(trim($s));
                if ($trimmed !== '') {
                    $existingUsedSerials[$trimmed] = true;
                }
            }
        }

        $seenInPayload = [];

        foreach ($details as $index => $detail) {
            $serial = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
            $serialParts = [];
            if ($serial !== '') {
                $serialParts = array_values(array_filter(array_map('trim', explode(',', $serial)), fn ($v) => $v !== ''));
            }
            $serialCount = count($serialParts);

            foreach ($serialParts as $sPart) {
                $lowerPart = strtolower($sPart);
                if (isset($seenInPayload[$lowerPart])) {
                    $validator->errors()->add("details.$index.serial_number", "Serial number '{$sPart}' is duplicated within this delivery receipt.");
                } else {
                    $seenInPayload[$lowerPart] = true;
                }

                if (isset($existingUsedSerials[$lowerPart])) {
                    $validator->errors()->add("details.$index.serial_number", "Serial number '{$sPart}' is already used in another Delivery Receipt.");
                }
            }

            $quantity = (int) ($detail['quantity_delivered'] ?? 0);
            if ($serialCount > 0 && $quantity !== $serialCount) {
                $validator->errors()->add("details.$index.quantity_delivered", 'Quantity must equal the number of serial numbers provided');
            }

            $requested = (int) ($detail['quantity_requested'] ?? 0);
            if ($serialCount > 0 && $requested !== $serialCount) {
                $validator->errors()->add("details.$index.quantity_requested", 'Quantity must equal the number of serial numbers provided');
            }

            $changeReason = isset($detail['change_reason']) ? trim((string) $detail['change_reason']) : '';
            if ($quantity !== $requested && $changeReason === '') {
                $validator->errors()->add("details.$index.change_reason", 'Change reason is required when delivered differs from requested quantity');
            }

            $articleId = (int) ($detail['article_id'] ?? 0);
            $article = $articleMap->get($articleId);
            if ($article && (int) ($detail['property_type_id'] ?? 0) !== (int) $article->PropertyTypeID) {
                $validator->errors()->add("details.$index.property_type_id", 'Property type must match the selected article.');
            }
        }
    }

    private function applyWorkflowStatus(DeliveryReceipt $dr): void
    {
        $dr->inspection_status = $dr->InspectionStatus ?? 'pending';
        $isFinalized = !empty($dr->FinalizedAt) || !((bool) ($dr->IsPartial ?? false));
        $dr->is_partial = ! $isFinalized;

        $workflowStatus = ($dr->InspectionStatus ?? 'pending') === 'inspected'
            ? 'to_be_issued'
            : 'pending_inspection';

        $issuance = DB::table('propertyissuance')
            ->where('DRID', $dr->DRID)
            ->whereNull('deleted_at')
            ->orderByDesc('IssuanceID')
            ->first(['Status']);

        if ($issuance) {
            if ($issuance->Status === 'acknowledged' || $issuance->Status === 'partially_acknowledged') {
                $workflowStatus = $dr->is_partial ? 'partially_acknowledged' : 'acknowledged';
            } elseif ($issuance->Status === 'pending_acknowledgment') {
                $workflowStatus = 'to_be_issued';
            }
        }

        $issuanceLabel = match ($workflowStatus) {
            'partially_acknowledged' => 'Partial / Issued',
            'acknowledged' => 'Issued',
            'to_be_issued' => 'For Issuance',
            default => 'Pending inspection',
        };

        if ($dr->is_partial) {
            $dr->workflow_status = $workflowStatus;
            $dr->workflow_status_label = $workflowStatus === 'partially_acknowledged'
                ? 'Partial / Issued'
                : 'Partial / ' . $issuanceLabel;
        } else {
            $dr->workflow_status = $workflowStatus;
            $dr->workflow_status_label = $issuanceLabel;
        }

        if (isset($dr->received_by_name)) {
            $dr->receivedByUser = $dr->received_by_name ? [
                'UserID' => $dr->ReceivedBy,
                'FullName' => $dr->received_by_name,
            ] : null;
            unset($dr->received_by_name);
        }

        $dr->RequestedByName = $dr->requestedByUser?->FullName;
        $dr->DepartmentName = $dr->requestedByUser?->department?->DepartmentName;
        $dr->DepartmentID = $dr->requestedByUser?->DepartmentID;
        $dr->SupplierName = $dr->supplier?->SupplierName ?? $dr->SupplierName ?? null;
        $dr->supplier_name = $dr->SupplierName;
    }

    private function loadDeliveryReceiptResponse(DeliveryReceipt $deliveryReceipt): DeliveryReceipt
    {
        $deliveryReceipt->load([
            'supplier:SupplierID,SupplierName',
            'details.propertyType',
            'details.unitOfMeasure',
            'details.article',
            'details.inventoryDetails:InventoryDetailID,SourceDRDetailID,PropertyNumber',
            'requestedByUser:UserID,FullName,DepartmentID',
            'requestedByUser.department:DepartmentID,DepartmentName',
        ]);

        $user = User::find($deliveryReceipt->ReceivedBy);
        $deliveryReceipt->receivedByUser = $user ? [
            'UserID' => $user->UserID,
            'FullName' => $user->FullName,
        ] : null;

        $this->applyWorkflowStatus($deliveryReceipt);

        $deliveryReceipt->RequestedByName = $deliveryReceipt->requestedByUser?->FullName;
        $deliveryReceipt->DepartmentName = $deliveryReceipt->requestedByUser?->department?->DepartmentName;
        $deliveryReceipt->DepartmentID = $deliveryReceipt->requestedByUser?->DepartmentID;
        $deliveryReceipt->SupplierName = $deliveryReceipt->supplier?->SupplierName;
        $deliveryReceipt->supplier_name = $deliveryReceipt->supplier?->SupplierName;

        return $deliveryReceipt;
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deliveryReceipt = DeliveryReceipt::findOrFail($id);
            $deliveryReceipt->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete delivery receipt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSuppliers(): JsonResponse
    {
        try {
            $suppliers = Supplier::select('SupplierID', 'SupplierName')
                ->orderBy('SupplierName')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch suppliers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPropertyTypes(): JsonResponse
    {
        try {
            $propertyTypes = PropertyType::select('PropertyTypeID', 'PropertyName')
                ->orderBy('PropertyName')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $propertyTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch property types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRequesters(Request $request): JsonResponse
    {
        try {
            $query = User::query()
                ->leftJoin('department', 'user.DepartmentID', '=', 'department.DepartmentID')
                ->select('user.UserID', 'user.FullName', 'user.DepartmentID', 'department.DepartmentName')
                ->where('user.IsActive', 1)
                ->whereHas('userRole', function ($q) {
                    $q->where('RoleName', 'Department Head');
                });

            if ($request->filled('department_id')) {
                $query->where('user.DepartmentID', (int) $request->department_id);
            }

            $users = $query->orderBy('user.FullName')->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch requesters',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsedSerialNumbers(Request $request): JsonResponse
    {
        try {
            $excludeDrId = $request->query('exclude_dr_id');

            $drSerialsQuery = DB::table('deliveryreceiptdetail')
                ->whereNotNull('SerialNumber')
                ->where('SerialNumber', '!=', '');

            if ($excludeDrId) {
                $drSerialsQuery->where('DRID', '!=', (int) $excludeDrId);
            }

            $rawDr = $drSerialsQuery->pluck('SerialNumber');

            $invSerialsQuery = DB::table('propertyinventorydetail as pid')
                ->join('propertyinventory as pi', 'pi.InventoryID', '=', 'pid.InventoryID')
                ->whereNotNull('pid.SerialNumber')
                ->where('pid.SerialNumber', '!=', '');

            if ($excludeDrId) {
                $invSerialsQuery->where('pi.DRID', '!=', (int) $excludeDrId);
            }

            $rawInv = $invSerialsQuery->pluck('pid.SerialNumber');

            $serials = [];
            foreach ($rawDr->concat($rawInv) as $raw) {
                foreach (explode(',', (string) $raw) as $s) {
                    $trimmed = trim($s);
                    if ($trimmed !== '') {
                        $serials[] = $trimmed;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => array_values(array_unique($serials)),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch used serial numbers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

