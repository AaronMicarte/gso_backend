<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Procurement\IARDetail;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\PropertyReceived;
use App\Models\Procurement\IarSignature;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\MasterData\Condition;
use App\Models\Auth\User;
use App\Models\AssetAuditLog;
use App\Services\Notifications\RoleNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InspectionAcceptanceReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = InspectionAcceptanceReport::with([
                'details.propertyType',
                'details.condition',
                'details.drDetail.unitOfMeasure',
                'deliveryReceipt' => function ($query) {
                    $query->withTrashed()->with([
                        'requestedByUser:UserID,FullName,DepartmentID',
                        'requestedByUser.department:DepartmentID,DepartmentName',
                    ]);
                },
                'inspectedByUser',
                'acceptedByUser'
            ]);

            // Role-based filtering
            $user = AuthFacade::user();
            if ($user->UserRoleID == 2) { // GSO Staff
                $query->where(function ($q) use ($user) {
                    $q->where('InspectedBy', $user->UserID)
                      ->orWhere('AcceptedBy', $user->UserID);
                });
            } elseif ($user->UserRoleID == 4) { // Department Head
                $query->where('AcceptedBy', $user->UserID);
            }
            // Admin (UserRoleID == 1) sees all, no filter needed

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('DateInspected', [$request->start_date, $request->end_date]);
            }

            // Filter by DR number
            if ($request->has('dr_number')) {
                $query->whereHas('deliveryReceipt', function ($q) use ($request) {
                    $q->where('DRNumber', 'like', '%' . $request->dr_number . '%');
                });
            }

            // Filter by IAR number
            if ($request->has('iar_number')) {
                $query->where('IARNumber', 'like', '%' . $request->iar_number . '%');
            }

            // new generic query parameter searches both IAR and DR numbers
            if ($request->has('query')) {
                $qval = $request->query;
                $query->where(function ($q) use ($qval) {
                    $q->where('IARNumber', 'like', '%' . $qval . '%')
                      ->orWhereHas('deliveryReceipt', function ($q2) use ($qval) {
                          $q2->where('DRNumber', 'like', '%' . $qval . '%');
                      });
                });
            }

            // Filter by status (pending / inspected)
            if ($request->has('status')) {
                $query->where('Status', $request->status);
            }

            $iars = $query->orderBy('CreatedDate', 'desc')->paginate(15);

            // Ensure compatibility with older UIs that expect camelCase relation attributes
            $iars->getCollection()->transform(function ($iar) {
                // Eager loading returns snake_case keys in JSON; add camelCase duplicates for frontend compatibility
                $iar->inspectedByUser = $iar->InspectedBy ? \App\Models\Auth\User::find($iar->InspectedBy) : null;
                $iar->acceptedByUser = $iar->AcceptedBy ? \App\Models\Auth\User::find($iar->AcceptedBy) : null;
                $iar->deliveryReceipt = $iar->deliveryReceipt ?? ($iar->DRID
                    ? \App\Models\Procurement\DeliveryReceipt::withTrashed()
                        ->with([
                            'requestedByUser:UserID,FullName,DepartmentID',
                            'requestedByUser.department:DepartmentID,DepartmentName',
                        ])
                        ->find($iar->DRID)
                    : null
                );
                return $iar;
            });

            return response()->json([
                'success' => true,
                'data' => $iars
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch IARs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Preprocess request data: convert empty strings to null for nullable fields
            $requestData = $request->all();
            if (isset($requestData['date_accepted']) && $requestData['date_accepted'] === '') {
                $requestData['date_accepted'] = null;
            }
            $request->merge($requestData);

            $validator = Validator::make($request->all(), [
                'dr_id' => 'required|integer|exists:deliveryreceipt,DRID',
                'inspected_by' => 'required|integer|exists:user,UserID',
                'accepted_by' => 'nullable|integer|exists:user,UserID',
                'date_inspected' => 'required|date',
                'date_accepted' => 'nullable|date',
                'remarks' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.dr_detail_id' => 'required|integer|exists:deliveryreceiptdetail,DRDetailID',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.condition_id' => 'required|integer|exists:condition,ConditionID',
                'details.*.quantity_accepted' => 'required|integer|min:0',
                'details.*.quantity_rejected' => 'required|integer|min:0',
                'details.*.verified_brand' => 'nullable|string|max:255',
                'details.*.verified_model' => 'nullable|string|max:255',
                'details.*.verified_serial' => 'nullable|string|max:255',
                'details.*.remarks' => 'nullable|string',
            ]);

            $validator->after(function ($validator) use ($request) {
                $details = $request->input('details', []);
                $totalsByDrDetail = [];
                $conditionKeys = [];
                $drDetailIds = [];

                foreach ($details as $index => $detail) {
                    $drDetailId = $detail['dr_detail_id'] ?? null;
                    if (!$drDetailId) {
                        continue;
                    }

                    $drDetailIds[] = $drDetailId;

                    $conditionId = $detail['condition_id'] ?? null;
                    $conditionKey = $drDetailId . ':' . ($conditionId ?? '');
                    if (isset($conditionKeys[$conditionKey])) {
                        $validator->errors()->add("details.$index.condition_id", 'Duplicate condition for the same item is not allowed');
                    }
                    $conditionKeys[$conditionKey] = true;

                    $accepted = (int) ($detail['quantity_accepted'] ?? 0);
                    $rejected = (int) ($detail['quantity_rejected'] ?? 0);

                    if (!isset($totalsByDrDetail[$drDetailId])) {
                        $totalsByDrDetail[$drDetailId] = ['accepted' => 0, 'rejected' => 0];
                    }
                    $totalsByDrDetail[$drDetailId]['accepted'] += $accepted;
                    $totalsByDrDetail[$drDetailId]['rejected'] += $rejected;
                }

                $drDetails = DeliveryReceiptDetail::whereIn('DRDetailID', array_values(array_unique($drDetailIds)))
                    ->get()
                    ->keyBy('DRDetailID');

                foreach ($totalsByDrDetail as $drDetailId => $totals) {
                    $drDetail = $drDetails->get($drDetailId);
                    if (!$drDetail) {
                        continue;
                    }
                    $delivered = (int) $drDetail->QuantityDelivered;
                    if ($totals['accepted'] + $totals['rejected'] !== $delivered) {
                        $validator->errors()->add('details', 'Accepted + Rejected must equal Delivered for all items');
                        break;
                    }
                }

                foreach ($details as $index => $detail) {
                    $drDetailId = $detail['dr_detail_id'] ?? null;
                    if (!$drDetailId) {
                        continue;
                    }
                    $drDetail = $drDetails->get($drDetailId);
                    if (!$drDetail) {
                        continue;
                    }

                    $drBrand = trim((string) ($drDetail->Brand ?? ''));
                    $drModel = trim((string) ($drDetail->ModelNumber ?? ''));
                    $drSerial = trim((string) ($drDetail->SerialNumber ?? ''));

                    if ($drBrand === '' && $drModel === '' && $drSerial === '') {
                        continue;
                    }

                    $verifiedBrand = trim((string) ($detail['verified_brand'] ?? ''));
                    $verifiedModel = trim((string) ($detail['verified_model'] ?? ''));
                    $verifiedSerial = trim((string) ($detail['verified_serial'] ?? ''));

                    $hasAllVerified =
                        ($drBrand === '' || $verifiedBrand !== '') &&
                        ($drModel === '' || $verifiedModel !== '') &&
                        ($drSerial === '' || $verifiedSerial !== '');

                    $matches =
                        ($drBrand === '' || strcasecmp($drBrand, $verifiedBrand) === 0) &&
                        ($drModel === '' || strcasecmp($drModel, $verifiedModel) === 0) &&
                        ($drSerial === '' || strcasecmp($drSerial, $verifiedSerial) === 0);

                    if (!$hasAllVerified || !$matches) {
                        $validator->errors()->add("details.$index.verified_brand", 'Verified brand/model/serial must match the delivery receipt. Edit the DR if actual items differ.');
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

            // Validate dates are today (Asia/Manila timezone)
            $todayManila = new \DateTime('now', new \DateTimeZone('Asia/Manila'));
            $todayManilaStr = $todayManila->format('Y-m-d');

            if ($request->date_inspected !== $todayManilaStr) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date Inspected must be today (Philippine time)',
                    'errors' => ['date_inspected' => ['Date Inspected must be today']]
                ], 422);
            }

            if ($request->date_accepted && $request->date_accepted !== $todayManilaStr) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date Accepted must be today (Philippine time)',
                    'errors' => ['date_accepted' => ['Date Accepted must be today']]
                ], 422);
            }

            // Check if DR already has an IAR
            $existingIAR = InspectionAcceptanceReport::where('DRID', $request->dr_id)->first();
            if ($existingIAR) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Delivery Receipt already has an Inspection & Acceptance Report'
                ], 400);
            }

            DB::beginTransaction();

            // Generate IAR number (collision-safe across IAR and DR tables)
            $iarNumber = $this->generateIarNumber();

            $iar = InspectionAcceptanceReport::create([
                'IARNumber' => $iarNumber,
                'DRID' => $request->dr_id,
                'InspectedBy' => $request->inspected_by,
                'AcceptedBy' => $request->accepted_by ?: null,
                'DateInspected' => $request->date_inspected,
                'DateAccepted' => $request->date_accepted,
                'Remarks' => $request->remarks,
            ]);

            // Create details
            $iarDetailIds = [];
            foreach ($request->details as $detail) {
                $createdDetail = IARDetail::create([
                    'IARID' => $iar->IARID,
                    'DRDetailID' => $detail['dr_detail_id'],
                    'PropertyTypeID' => $detail['property_type_id'],
                    'ConditionID' => $detail['condition_id'],
                    'QuantityAccepted' => $detail['quantity_accepted'],
                    'QuantityRejected' => $detail['quantity_rejected'],
                    'VerifiedBrand' => $detail['verified_brand'] ?? null,
                    'VerifiedModel' => $detail['verified_model'] ?? null,
                    'VerifiedSerial' => $detail['verified_serial'] ?? null,
                    'Remarks' => $detail['remarks'] ?? null,
                ]);

                $iarDetailIds[] = (int) $createdDetail->IARDetailID;
            }

            // Require inspected signature when InspectedBy is provided
            $hasInspectedSignature = $request->hasFile('inspected_signature') || $request->input('inspected_signature');
            if ($iar->InspectedBy && !$hasInspectedSignature) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Inspected signature is required when Inspected By is set',
                    'errors' => ['inspected_signature' => ['Inspected signature is required']] 
                ], 422);
            }

            // If inspected signature provided inline during create, save it
            if ($hasInspectedSignature) {
                try {
                    $signatureService = new \App\Services\SignatureService();
                    $input = $request->file('inspected_signature') ?? $request->input('inspected_signature');
                    $signatureService->processAndStore($iar->IARID, 'inspected', $input, $request->input('inspected_signature_source') ?? 'upload', $iar->InspectedBy);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save inspected signature',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            // If accepted signature provided inline during create, save it too (supports CreateIARPage inline behavior)
            $hasAcceptedSignature = $request->hasFile('accepted_signature') || $request->input('accepted_signature');
            if ($hasAcceptedSignature) {
                try {
                    $signatureService = $signatureService ?? new \App\Services\SignatureService();
                    $input = $request->file('accepted_signature') ?? $request->input('accepted_signature');
                    // Prefer IAR AcceptedBy if set, otherwise use authenticated user as signer
                    $signedBy = $iar->AcceptedBy ?: (auth()->user()->UserID ?? null);
                    $signatureService->processAndStore($iar->IARID, 'accepted', $input, $request->input('accepted_signature_source') ?? 'upload', $signedBy);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save accepted signature',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            // If AcceptedBy is set, ensure an accepted signature exists (either inline saved above or previously saved)
            if ($iar->AcceptedBy) {
                $acceptedExists = IarSignature::where('iar_id', $iar->IARID)->where('role', 'accepted')->exists();
                if (! $acceptedExists) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Accepted signature is required when Accepted By is set',
                        'errors' => ['accepted_signature' => ['Accepted signature is required']]
                    ], 422);
                }

                // Create propertyreceived record if it does not exist
                $existingReceived = PropertyReceived::where('IARID', $iar->IARID)->first();
                if (! $existingReceived) {
                    $iar->load('details');
                    $totalCost = 0;
                    foreach ($iar->details as $d) {
                        $drDetail = DeliveryReceiptDetail::find($d->DRDetailID);
                        $unitCost = $drDetail ? (float) ($drDetail->UnitCost ?? 0) : 0;
                        $totalCost += $unitCost * (int) $d->QuantityAccepted;
                    }

                    PropertyReceived::create([
                        'IARID' => $iar->IARID,
                        'ReceivedBy' => $iar->AcceptedBy,
                        'ReleasedBy' => $iar->InspectedBy,
                        'DateReceived' => Carbon::now('Asia/Manila')->format('Y-m-d'),
                        'TotalCost' => $totalCost,
                        'Remarks' => 'Auto-created on acceptance'
                    ]);
                }
            }

            DB::commit();

            $actorId = (int) (AuthFacade::id() ?? 0);
            if ($actorId > 0) {
                AssetAuditLog::createLog(
                    'inspection_acceptance_report',
                    (int) $iar->IARID,
                    'created',
                    $actorId,
                    [
                        'iar_number' => $iar->IARNumber,
                        'dr_id' => (int) $iar->DRID,
                        'inspected_by' => (int) ($iar->InspectedBy ?? 0),
                        'accepted_by' => (int) ($iar->AcceptedBy ?? 0),
                        'details' => $iarDetailIds,
                    ]
                );

                foreach ($iarDetailIds as $iarDetailId) {
                    AssetAuditLog::createLog(
                        'iar_detail',
                        (int) $iarDetailId,
                        'created',
                        $actorId,
                        [
                            'iar_id' => (int) $iar->IARID,
                            'iar_number' => $iar->IARNumber,
                        ]
                    );
                }
            }

            $iar->loadMissing('deliveryReceipt.requestedByUser');
            $recipientUserIds = $this->getIarNotificationUserIds(
                (int) ($iar->deliveryReceipt->requestedByUser->DepartmentID ?? 0),
                (int) (AuthFacade::id() ?? 0)
            );

            app(RoleNotificationService::class)->notifyUsers(
                $recipientUserIds,
                'iar.created',
                'Inspection & Acceptance Report Created',
                sprintf('IAR %s has been created.', (string) $iar->IARNumber),
                [
                    'iar_id' => (string) $iar->IARID,
                    'iar_number' => (string) $iar->IARNumber,
                    'dr_id' => (string) $iar->DRID,
                    'link' => '/procurement/inspection-acceptance-reports/' . $iar->IARID,
                ]
            );

            if ((int) ($iar->AcceptedBy ?? 0) > 0) {
                app(RoleNotificationService::class)->notifyUser(
                    (int) $iar->AcceptedBy,
                    'iar.created',
                    'IAR Assigned To You',
                    sprintf('IAR %s was created and assigned for acceptance.', (string) $iar->IARNumber),
                    [
                        'iar_id' => (string) $iar->IARID,
                        'iar_number' => (string) $iar->IARNumber,
                        'dr_id' => (string) $iar->DRID,
                        'link' => '/procurement/inspection-acceptance-reports/' . $iar->IARID,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Inspection & Acceptance Report created successfully',
                'data' => $iar->load([
                    'deliveryReceipt',
                    'inspectedByUser',
                    'acceptedByUser',
                    'details.propertyType',
                    'details.condition',
                    'details.drDetail'
                ])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create IAR',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $iar = InspectionAcceptanceReport::with([
                'details.propertyType',
                'details.condition',
                'details.drDetail.unitOfMeasure',
                'deliveryReceipt' => function ($q) {
                    $q->withTrashed()->with([
                        'requestedByUser:UserID,FullName,DepartmentID',
                        'requestedByUser.department:DepartmentID,DepartmentName',
                    ]);
                }
            ])->findOrFail($id);

            // Load additional relationships
            $iar->load([
                'deliveryReceipt',
                'inspectedByUser',
                'acceptedByUser'
            ]);

            // Ensure unitOfMeasure is loaded for all details
            foreach ($iar->details as $detail) {
                if ($detail->drDetail) {
                    $detail->drDetail->load('unitOfMeasure');
                }
            }

            // Role-based access control
            $user = AuthFacade::user();
            if ($user->UserRoleID == 2) { // GSO Staff
                if ($iar->InspectedBy != $user->UserID && $iar->AcceptedBy != $user->UserID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
            } elseif ($user->UserRoleID == 3) { // Property Custodian
                if ($iar->AcceptedBy != $user->UserID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
            }
            // Admin can access all

            // Normalize response for frontend (ensure camelCase relationship keys expected by frontend)
            $data = $iar->toArray();
            // Users are manually loaded
            $data['inspectedByUser'] = $iar->inspectedByUser ? $iar->inspectedByUser->toArray() : null;
            $data['acceptedByUser'] = $iar->acceptedByUser ? $iar->acceptedByUser->toArray() : null;

            // Attach signatures if present
            $inspectedSig = $iar->inspectedSignature()->first();
            $acceptedSig = $iar->acceptedSignature()->first();
            $data['inspectedSignature'] = $inspectedSig ? [
                'url' => $inspectedSig->url,
                'source' => $inspectedSig->source,
                'storage_disk' => $inspectedSig->storage_disk,
                'storage_path' => $inspectedSig->storage_path,
                'signed_by_user_id' => $inspectedSig->signed_by_user_id,
                'signed_at' => $inspectedSig->created_at,
            ] : null;
            $data['acceptedSignature'] = $acceptedSig ? [
                'url' => $acceptedSig->url,
                'source' => $acceptedSig->source,
                'storage_disk' => $acceptedSig->storage_disk,
                'storage_path' => $acceptedSig->storage_path,
                'signed_by_user_id' => $acceptedSig->signed_by_user_id,
                'signed_at' => $acceptedSig->created_at,
            ] : null;

            // Ensure deliveryReceipt is exposed in camelCase for frontend
            $data['deliveryReceipt'] = $iar->deliveryReceipt ? $iar->deliveryReceipt->toArray() : null;
            if (!empty($data['deliveryReceipt']) && $iar->deliveryReceipt) {
                $data['deliveryReceipt']['requestedByUser'] = $iar->deliveryReceipt->requestedByUser
                    ? $iar->deliveryReceipt->requestedByUser->toArray()
                    : null;
            }

            if (!empty($data['details']) && is_array($data['details'])) {
                foreach ($data['details'] as &$d) {
                    // Map snake_case nested keys to camelCase for frontend
                    $d['drDetail'] = $d['dr_detail'] ?? null;
                    $d['condition'] = $d['condition'] ?? null;
                    $d['propertyType'] = $d['property_type'] ?? null;
                    if (!empty($d['drDetail']) && is_array($d['drDetail'])) {
                        $d['drDetail']['unitOfMeasure'] = $d['drDetail']['unit_of_measure'] ?? null;
                        // Attach property numbers for this DR detail (if any)
                        $drDetailId = $d['drDetail']['DRDetailID'] ?? null;
                        if ($drDetailId) {
                            $propertyNumbers = \App\Models\Inventory\PropertyInventoryDetail::where('SourceDRDetailID', $drDetailId)
                                ->pluck('PropertyNumber')
                                ->filter()
                                ->values()
                                ->all();
                            $d['drDetail']['propertyNumbers'] = $propertyNumbers;
                        }
                    }
                }
            }

            $iar->loadMissing('deliveryReceipt.requestedByUser');
            $recipientUserIds = $this->getIarNotificationUserIds(
                (int) ($iar->deliveryReceipt->requestedByUser->DepartmentID ?? 0),
                (int) (AuthFacade::id() ?? 0)
            );

            app(RoleNotificationService::class)->notifyUsers(
                $recipientUserIds,
                'iar.updated',
                'Inspection & Acceptance Report Updated',
                sprintf('IAR %s has been updated.', (string) $iar->IARNumber),
                [
                    'iar_id' => (string) $iar->IARID,
                    'iar_number' => (string) $iar->IARNumber,
                    'dr_id' => (string) $iar->DRID,
                    'link' => '/procurement/inspection-acceptance-reports/' . $iar->IARID,
                ]
            );

            if ((int) ($iar->AcceptedBy ?? 0) > 0) {
                app(RoleNotificationService::class)->notifyUser(
                    (int) $iar->AcceptedBy,
                    'iar.updated',
                    'IAR Updated',
                    sprintf('IAR %s assigned to you has been updated.', (string) $iar->IARNumber),
                    [
                        'iar_id' => (string) $iar->IARID,
                        'iar_number' => (string) $iar->IARNumber,
                        'dr_id' => (string) $iar->DRID,
                        'link' => '/procurement/inspection-acceptance-reports/' . $iar->IARID,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'IAR not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $iar = InspectionAcceptanceReport::findOrFail($id);

            // Role-based access control
            $user = AuthFacade::user();
            if ($user->UserRoleID == 2) { // GSO Staff
                if ($iar->InspectedBy != $user->UserID && $iar->AcceptedBy != $user->UserID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
            } elseif ($user->UserRoleID == 3) { // Property Custodian
                if ($iar->AcceptedBy != $user->UserID) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
            }
            // Admin can update all

            $validator = Validator::make($request->all(), [
                'inspected_by' => 'required|integer|exists:user,UserID',
                'accepted_by' => 'nullable|integer|exists:user,UserID',
                'date_inspected' => 'required|date',
                'date_accepted' => 'nullable|date',
                'remarks' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.dr_detail_id' => 'required|integer|exists:deliveryreceiptdetail,DRDetailID',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.condition_id' => 'required|integer|exists:condition,ConditionID',
                'details.*.quantity_accepted' => 'required|integer|min:0',
                'details.*.quantity_rejected' => 'required|integer|min:0',
                'details.*.verified_brand' => 'nullable|string|max:255',
                'details.*.verified_model' => 'nullable|string|max:255',
                'details.*.verified_serial' => 'nullable|string|max:255',
                'details.*.remarks' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate dates are today (Asia/Manila timezone) - TEMPORARILY DISABLED FOR DEBUGGING
            // $todayManila = Carbon::now('Asia/Manila')->format('Y-m-d');

            // if ($request->date_inspected !== $todayManila) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Date Inspected must be today (Philippine time)',
            //         'errors' => ['date_inspected' => ['Date Inspected must be today']]
            //     ], 422);
            // }

            // if ($request->date_accepted && $request->date_accepted !== $todayManila) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Date Accepted must be today (Philippine time)',
            //         'errors' => ['date_accepted' => ['Date Accepted must be today']]
            //     ], 422);
            // }

            DB::beginTransaction();

            $iar->update([
                'InspectedBy' => $request->inspected_by,
                'AcceptedBy' => $request->accepted_by,
                'DateInspected' => $request->date_inspected,
                'DateAccepted' => $request->date_accepted,
                'Remarks' => $request->remarks,
            ]);

            // Delete existing details
            $iar->details()->delete();

            // Create new details
            foreach ($request->details as $detail) {
                IARDetail::create([
                    'IARID' => $iar->IARID,
                    'DRDetailID' => $detail['dr_detail_id'],
                    'PropertyTypeID' => $detail['property_type_id'],
                    'ConditionID' => $detail['condition_id'],
                    'QuantityAccepted' => $detail['quantity_accepted'],
                    'QuantityRejected' => $detail['quantity_rejected'],
                    'VerifiedBrand' => $detail['verified_brand'] ?? null,
                    'VerifiedModel' => $detail['verified_model'] ?? null,
                    'VerifiedSerial' => $detail['verified_serial'] ?? null,
                    'Remarks' => $detail['remarks'] ?? null,
                ]);
            }

            // If AcceptedBy is set, ensure an accepted signature exists
            if ($iar->AcceptedBy) {
                $acceptedExists = IarSignature::where('iar_id', $iar->IARID)->where('role', 'accepted')->exists();
                if (! $acceptedExists) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Accepted signature is required when Accepted By is set',
                        'errors' => ['accepted_signature' => ['Accepted signature is required']]
                    ], 422);
                }

                // Create propertyreceived record if it does not exist
                $existingReceived = PropertyReceived::where('IARID', $iar->IARID)->first();
                if (! $existingReceived) {
                    $iar->load('details');
                    $totalCost = 0;
                    foreach ($iar->details as $d) {
                        $drDetail = DeliveryReceiptDetail::find($d->DRDetailID);
                        $unitCost = $drDetail ? (float) ($drDetail->UnitCost ?? 0) : 0;
                        $totalCost += $unitCost * (int) $d->QuantityAccepted;
                    }

                    PropertyReceived::create([
                        'IARID' => $iar->IARID,
                        'ReceivedBy' => $iar->AcceptedBy,
                        'ReleasedBy' => $iar->InspectedBy,
                        'DateReceived' => Carbon::now('Asia/Manila')->format('Y-m-d'),
                        'TotalCost' => $totalCost,
                        'Remarks' => 'Auto-created on acceptance'
                    ]);
                }
            }

            DB::commit();

            // Load relationships like in show method
            $iar->load([
                'deliveryReceipt',
                'details.propertyType',
                'details.condition',
                'details.drDetail.unitOfMeasure',
                'inspectedByUser',
                'acceptedByUser'
            ]);

            // Backwards compatibility: add camelCase properties expected by older UIs
            $iar->inspectedByUser = $iar->InspectedBy ? User::find($iar->InspectedBy) : null;
            $iar->acceptedByUser = $iar->AcceptedBy ? User::find($iar->AcceptedBy) : null;

            // Normalize response similar to show() so frontend receives signatures and deliveryReceipt
            $data = $iar->toArray();
            $data['inspectedByUser'] = $iar->inspectedByUser ? $iar->inspectedByUser->toArray() : null;
            $data['acceptedByUser'] = $iar->acceptedByUser ? $iar->acceptedByUser->toArray() : null;

            // Attach signatures if present
            $inspectedSig = $iar->inspectedSignature()->first();
            $acceptedSig = $iar->acceptedSignature()->first();
            $data['inspectedSignature'] = $inspectedSig ? [
                'url' => $inspectedSig->url,
                'source' => $inspectedSig->source,
                'storage_disk' => $inspectedSig->storage_disk,
                'storage_path' => $inspectedSig->storage_path,
                'signed_by_user_id' => $inspectedSig->signed_by_user_id,
                'signed_at' => $inspectedSig->created_at,
            ] : null;
            $data['acceptedSignature'] = $acceptedSig ? [
                'url' => $acceptedSig->url,
                'source' => $acceptedSig->source,
                'storage_disk' => $acceptedSig->storage_disk,
                'storage_path' => $acceptedSig->storage_path,
                'signed_by_user_id' => $acceptedSig->signed_by_user_id,
                'signed_at' => $acceptedSig->created_at,
            ] : null;

            // Ensure deliveryReceipt is exposed in camelCase for frontend
            $data['deliveryReceipt'] = $iar->deliveryReceipt ? $iar->deliveryReceipt->toArray() : null;

            if (!empty($data['details']) && is_array($data['details'])) {
                foreach ($data['details'] as &$d) {
                    // Map snake_case nested keys to camelCase for frontend
                    $d['drDetail'] = $d['dr_detail'] ?? null;
                    $d['condition'] = $d['condition'] ?? null;
                    $d['propertyType'] = $d['property_type'] ?? null;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'IAR updated successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update IAR',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $iar = InspectionAcceptanceReport::findOrFail($id);

            // Role-based access control - only admin can delete
            $user = AuthFacade::user();
            if ($user->UserRoleID != 1) { // Not Admin
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Delete related propertyreceived records first (hard delete as they are tightly coupled)
            PropertyReceived::where('IARID', $id)->delete();

            // Soft delete the IAR (preserves record for auditing)
            $iar->delete();

            return response()->json([
                'success' => true,
                'message' => 'IAR deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete IAR',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save signature helper used when creating/updating IAR
     */
    private function saveSignature($iarId, $role, $signatureInput, $source, $signedByUserId)
    {
        // $signatureInput may be an UploadedFile or a base64 string
        $pngContents = null;
        $mime = 'image/png';

        if ($signatureInput instanceof \Illuminate\Http\UploadedFile) {
            $file = $signatureInput;
            $original = file_get_contents($file->getRealPath());
            $detected = $file->getClientMimeType();
            if (!in_array($detected, ['image/png', 'image/jpeg', 'image/jpg'])) {
                throw new \Exception('Invalid image type.');
            }
            if (in_array($detected, ['image/jpeg', 'image/jpg'])) {
                $img = imagecreatefromstring($original);
                ob_start();
                imagepng($img);
                $pngContents = ob_get_clean();
                imagedestroy($img);
            } else {
                $pngContents = $original;
            }
        } else {
            // base64
            $sig = $signatureInput;
            if (is_string($sig) && preg_match('/^data:image\/(png|jpeg);base64,/', $sig)) {
                $base64 = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $sig);
                $decoded = base64_decode($base64);
                if ($decoded === false) {
                    throw new \Exception('Invalid base64');
                }
                $finfo = finfo_open();
                $detected = finfo_buffer($finfo, $decoded, FILEINFO_MIME_TYPE);
                finfo_close($finfo);
                if (!in_array($detected, ['image/png', 'image/jpeg'])) {
                    throw new \Exception('Invalid image type');
                }
                if ($detected === 'image/jpeg') {
                    $img = imagecreatefromstring($decoded);
                    ob_start();
                    imagepng($img);
                    $pngContents = ob_get_clean();
                    imagedestroy($img);
                } else {
                    $pngContents = $decoded;
                }
            } else {
                throw new \Exception('No valid signature data');
            }
        }

        // Basic image checks
        $image = imagecreatefromstring($pngContents);
        if (!$image) {
            throw new \Exception('Invalid image data');
        }
        $width = imagesx($image);
        $height = imagesy($image);
        imagedestroy($image);

        $disk = config('filesystems.default', 'public');
        $filename = 'signatures/iar_' . $iarId . '_' . $role . '_' . time() . '.png';
        Storage::disk($disk)->put($filename, $pngContents);
        $url = Storage::disk($disk)->url($filename);
        $tmpPath = Storage::disk($disk)->path($filename);
        $hash = hash_file('sha256', $tmpPath);
        $size = Storage::disk($disk)->size($filename);

        return IarSignature::updateOrCreate(
            ['iar_id' => $iarId, 'role' => $role],
            [
                'signed_by_user_id' => $signedByUserId,
                'source' => $source,
                'storage_disk' => $disk,
                'storage_path' => $filename,
                'url' => $url,
                'mime' => 'image/png',
                'width' => $width,
                'height' => $height,
                'size_bytes' => $size,
                'sha256_hash' => $hash,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );
    }

    /**
     * @return array<int>
     */
    private function getIarNotificationUserIds(int $departmentId, ?int $excludeUserId = null): array
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

    public function getConditions(): JsonResponse
    {
        try {
            $conditions = Condition::select('ConditionID', 'ConditionName')
                ->orderBy('ConditionName')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $conditions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conditions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(): JsonResponse
    {
        try {
            $currentUser = AuthFacade::user();
            $query = User::select('UserID', 'FullName', 'UserRoleID')
                ->where('IsActive', 1);

            // Role-based filtering: Admin and GSO Staff only see themselves
            if ($currentUser->UserRoleID == 1 || $currentUser->UserRoleID == 2) {
                // Admin (1) and GSO Staff (2) can only select themselves
                $query->where('UserID', $currentUser->UserID);
            }
            // Department Head (3) and other roles see all active users

            $users = $query->orderBy('FullName')->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateIarNumber(): string
    {
        $year = date('Y');
        $prefix = 'IAR-' . $year . '-';

        $latestIar = DB::table('inspectionacceptancereport')
            ->where('IARNumber', 'like', $prefix . '%')
            ->orderByDesc('IARNumber')
            ->value('IARNumber');

        $latestDr = DB::table('deliveryreceipt')
            ->where('IARNumber', 'like', $prefix . '%')
            ->orderByDesc('IARNumber')
            ->value('IARNumber');

        $maxSequence = 0;
        foreach ([$latestIar, $latestDr] as $value) {
            if ($value && preg_match('/^IAR-\d{4}-(\d{4,})$/', (string) $value, $matches)) {
                $maxSequence = max($maxSequence, (int) $matches[1]);
            }
        }

        do {
            $maxSequence++;
            $candidate = $prefix . str_pad((string) $maxSequence, 4, '0', STR_PAD_LEFT);

            $exists = DB::table('inspectionacceptancereport')->where('IARNumber', $candidate)->exists()
                || DB::table('deliveryreceipt')->where('IARNumber', $candidate)->exists();
        } while ($exists);

        return $candidate;
    }
}
