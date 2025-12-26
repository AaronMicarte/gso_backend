<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Procurement\IARDetail;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\MasterData\Condition;
use App\Models\Auth\User;
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
                'deliveryReceipt',
                'inspectedByUser',
                'acceptedByUser',
                'details.propertyType',
                'details.condition',
                'details.drDetail'
            ]);

            // Role-based filtering
            $user = AuthFacade::user();
            if ($user->UserRoleID == 2) { // GSO Staff
                $query->where(function ($q) use ($user) {
                    $q->where('InspectedBy', $user->UserID)
                      ->orWhere('AcceptedBy', $user->UserID);
                });
            } elseif ($user->UserRoleID == 3) { // Property Custodian
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

            $iars = $query->orderBy('CreatedDate', 'desc')->paginate(15);

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
                'details.*.remarks' => 'nullable|string',
            ]);

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

            // Generate IAR number
            $iarNumber = 'IAR-' . date('Y') . '-' . str_pad(InspectionAcceptanceReport::count() + 1, 4, '0', STR_PAD_LEFT);

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
            foreach ($request->details as $detail) {
                IARDetail::create([
                    'IARID' => $iar->IARID,
                    'DRDetailID' => $detail['dr_detail_id'],
                    'PropertyTypeID' => $detail['property_type_id'],
                    'ConditionID' => $detail['condition_id'],
                    'QuantityAccepted' => $detail['quantity_accepted'],
                    'QuantityRejected' => $detail['quantity_rejected'],
                    'Remarks' => $detail['remarks'] ?? null,
                ]);
            }

            DB::commit();

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
                'deliveryReceipt.details.propertyType',
                'inspectedByUser',
                'acceptedByUser',
                'details.propertyType',
                'details.condition',
                'details.drDetail'
            ])->findOrFail($id);

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

            return response()->json([
                'success' => true,
                'data' => $iar
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
                'accepted_by' => 'required|integer|exists:user,UserID',
                'date_inspected' => 'required|date',
                'date_accepted' => 'nullable|date',
                'remarks' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.dr_detail_id' => 'required|integer|exists:deliveryreceiptdetail,DRDetailID',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.condition_id' => 'required|integer|exists:condition,ConditionID',
                'details.*.quantity_accepted' => 'required|integer|min:0',
                'details.*.quantity_rejected' => 'required|integer|min:0',
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
                    'Remarks' => $detail['remarks'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'IAR updated successfully',
                'data' => $iar->load([
                    'deliveryReceipt',
                    'inspectedByUser',
                    'acceptedByUser',
                    'details.propertyType',
                    'details.condition',
                    'details.drDetail'
                ])
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

            $iar->details()->delete();
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
            // Property Custodian (3) and other roles see all active users

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
}
