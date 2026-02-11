<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\MasterData\PropertyType;
use App\Models\Auth\User;
use App\Models\MasterData\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DeliveryReceiptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DeliveryReceipt::with(['details.propertyType', 'details.unitOfMeasure', 'iar', 'department', 'requestedByUser:UserID,FullName,DepartmentID'])
                ->leftJoin('user', 'deliveryreceipt.ReceivedBy', '=', 'user.UserID')
                ->select('deliveryreceipt.*', 'user.FullName as received_by_name')
                ->withCount('iar as hasIAR');

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('DateReceived', [$request->start_date, $request->end_date]);
            }

            // Filter by supplier
            if ($request->has('supplier_name')) {
                $query->where('SupplierName', 'like', '%' . $request->supplier_name . '%');
            }

            // Filter by PO number
            if ($request->has('po_number')) {
                $query->where('PONumber', 'like', '%' . $request->po_number . '%');
            }

            $deliveryReceipts = $query->orderBy('CreatedDate', 'desc')->paginate(15);

            // Transform to include IAR info
            $deliveryReceipts->getCollection()->transform(function ($dr) {
                $dr->hasIAR = $dr->hasIAR > 0;

                // Determine IAR status: Pending IAR, Inspected, Accepted
                if ($dr->hasIAR) {
                    $iar = $dr->iar ?? $dr->iar()->first();
                    if ($iar && !empty($iar->DateAccepted)) {
                        $dr->iar_status = 'Accepted';
                    } else {
                        $dr->iar_status = 'Inspected';
                    }
                } else {
                    $dr->iar_status = 'Pending IAR';
                }

                // Set receivedByUser from the joined data
                $dr->receivedByUser = $dr->received_by_name ? [
                    'UserID' => $dr->ReceivedBy,
                    'FullName' => $dr->received_by_name
                ] : null;
                // Remove the temporary field
                unset($dr->received_by_name);
                return $dr;
            });

            return response()->json([
                'success' => true,
                'data' => $deliveryReceipts
            ]);
        } catch (\Exception $e) {
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
            $isAdmin = Auth::user() && (Auth::user()->UserRoleID ?? null) == 1;

            $dateRules = 'required|date|before_or_equal:today';
            // allow admin override via request flag 'override_past_limit'
            $overrideRequested = $request->boolean('override_past_limit', false);
            if (!($isAdmin && $overrideRequested)) {
                $dateRules .= '|after_or_equal:' . $minDate;
            }

            $validator = Validator::make($request->all(), [
                'dr_number' => 'required|string|max:50|unique:deliveryreceipt,DRNumber',
                'supplier_name' => 'required|string|max:255',
                'requested_by_user_id' => 'required|integer|exists:user,UserID',
                'department_id' => 'required|integer|exists:department,DepartmentID',
                'pr_number' => 'required|string|max:50',
                'po_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('deliveryreceipt', 'PONumber')->where(function($q) use ($request) {
                        return $q->where('SupplierName', $request->supplier_name);
                    }),
                ],
                'invoice_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('deliveryreceipt', 'InvoiceNumber')->where(function($q) use ($request) {
                        return $q->where('SupplierName', $request->supplier_name);
                    }),
                ],
                'date_received' => $dateRules,
                'override_past_limit' => 'sometimes|boolean',
                'remarks' => 'nullable|string',
                'dr_picture' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                'details' => 'required|array|min:1',
                'details.*.item_description' => 'required|string|max:255',
                'details.*.brand' => 'nullable|string|max:100',
                'details.*.model_number' => 'nullable|string|max:100',
                'details.*.serial_number' => 'nullable|string|max:100',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
                'details.*.unit_of_measure_id' => 'required|integer|exists:unitofmeasure,UnitOfMeasureID',
                'details.*.unit_cost' => 'required|numeric|min:0',
            ], [
                'dr_number.unique' => 'DR number already exists',
                'po_number.unique' => 'PO number already exists for this supplier',
                'invoice_number.unique' => 'Invoice number already exists for this supplier',
                'dr_picture.required' => 'DR picture is required',
                'dr_picture.file' => 'DR picture must be a valid file',
                'dr_picture.mimes' => 'DR picture must be a JPEG, PNG, JPG, or GIF file',
                'dr_picture.max' => 'DR picture must not exceed 2MB',
            ]);

            $validator->after(function ($validator) use ($request) {
                $details = $request->input('details', []);
                foreach ($details as $index => $detail) {
                    $serial = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
                    $quantity = (int) ($detail['quantity_delivered'] ?? 0);
                    if ($serial !== '' && $quantity > 1) {
                        $validator->errors()->add("details.$index.quantity_delivered", 'Quantity must be 1 when a serial number is provided');
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

            // Handle DR picture upload
            $drPicturePath = $request->file('dr_picture')->store('dr_pictures', 'public');

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

            if ((int) $requestedByUser->DepartmentID !== (int) $request->department_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['requested_by_user_id' => ['Requested by user must belong to the selected department.']]
                ], 422);
            }

            $deliveryReceipt = DeliveryReceipt::create([
                'DRNumber' => $request->dr_number,
                'SupplierName' => $request->supplier_name,
                'RequestedBy' => $requestedByUser->FullName,
                'RequestedByUserID' => (int) $requestedByUser->UserID,
                'DepartmentID' => $request->department_id,
                'PRNumber' => $request->pr_number,
                'PONumber' => $request->po_number,
                'InvoiceNumber' => $request->invoice_number,
                'ReceivedBy' => Auth::user()->UserID,
                'DateReceived' => $request->date_received,
                'Remarks' => $request->remarks,
                'dr_picture' => $drPicturePath,
            ]);

            // Create details
            foreach ($request->details as $detail) {
                DeliveryReceiptDetail::create([
                    'DRID' => $deliveryReceipt->DRID,
                    'ItemDescription' => $detail['item_description'],
                    'Brand' => $detail['brand'] ?? null,
                    'ModelNumber' => $detail['model_number'] ?? null,
                    'SerialNumber' => $detail['serial_number'] ?? null,
                    'PropertyTypeID' => $detail['property_type_id'],
                    'QuantityDelivered' => $detail['quantity_delivered'],
                    'UnitOfMeasureID' => $detail['unit_of_measure_id'],
                    'UnitCost' => $detail['unit_cost'],
                    'TotalCost' => $detail['quantity_delivered'] * $detail['unit_cost'],
                ]);
            }

            DB::commit();

            // Load relationships
            $deliveryReceipt->load(['details.propertyType', 'details.unitOfMeasure', 'department', 'requestedByUser:UserID,FullName,DepartmentID']);
            // Set receivedByUser manually
            $user = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);
            $deliveryReceipt->receivedByUser = $user ? [
                'UserID' => $user->UserID,
                'FullName' => $user->FullName
            ] : null;

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt created successfully',
                'data' => $deliveryReceipt,
                'dr_picture_url' => Storage::disk('public')->url($deliveryReceipt->dr_picture)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
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
            $deliveryReceipt = DeliveryReceipt::with(['details.propertyType', 'details.unitOfMeasure', 'department', 'requestedByUser:UserID,FullName,DepartmentID'])
                ->findOrFail($id);

            // Load IAR count
            $deliveryReceipt->loadCount('iar as hasIAR');
            $deliveryReceipt->hasIAR = $deliveryReceipt->hasIAR > 0;

            // Determine IAR status
            if ($deliveryReceipt->hasIAR) {
                $iar = $deliveryReceipt->iar()->first();
                $deliveryReceipt->iar_status = ($iar && !empty($iar->DateAccepted)) ? 'Accepted' : 'Inspected';
            } else {
                $deliveryReceipt->iar_status = 'Pending IAR';
            }

            // Set receivedByUser explicitly
            $user = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);
            $deliveryReceipt->receivedByUser = $user ? [
                'UserID' => $user->UserID,
                'FullName' => $user->FullName
            ] : null;

            // Normalize relationship keys for frontend by converting the model to an array
            $data = $deliveryReceipt->toArray();
            if (!empty($data['details']) && is_array($data['details'])) {
                foreach ($data['details'] as &$d) {
                    $d['propertyType'] = $d['property_type'] ?? null;
                    $d['unitOfMeasure'] = $d['unit_of_measure'] ?? null;
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

            // Check if DR has been inspected (has IAR)
            $deliveryReceipt->loadCount('iar as hasIAR');
            if ($deliveryReceipt->hasIAR > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit delivery receipt that has already been inspected.',
                ], 403);
            }

            // For update: apply same backdate rules as store (use config)
            $backdateYears = (int) config('gso.dr_backdate_years', 5);
            $minDate = Carbon::now('Asia/Manila')->subYears($backdateYears)->toDateString();
            $isAdmin = Auth::user() && (Auth::user()->UserRoleID ?? null) == 1;
            $dateRules = 'required|date|before_or_equal:today';
            $overrideRequested = $request->boolean('override_past_limit', false);
            if (!($isAdmin && $overrideRequested)) {
                $dateRules .= '|after_or_equal:' . $minDate;
            }

            $validator = Validator::make($request->all(), [
                'dr_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('deliveryreceipt', 'DRNumber')->ignore($deliveryReceipt->DRID, 'DRID'),
                ],
                'supplier_name' => 'required|string|max:255',
                'requested_by_user_id' => 'required|integer|exists:user,UserID',
                'department_id' => 'required|integer|exists:department,DepartmentID',
                'pr_number' => 'required|string|max:50',
                'po_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('deliveryreceipt', 'PONumber')->where(function($q) use ($request) {
                        return $q->where('SupplierName', $request->supplier_name);
                    })->ignore($deliveryReceipt->DRID, 'DRID'),
                ],
                'invoice_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('deliveryreceipt', 'InvoiceNumber')->where(function($q) use ($request) {
                        return $q->where('SupplierName', $request->supplier_name);
                    })->ignore($deliveryReceipt->DRID, 'DRID'),
                ],
                'date_received' => $dateRules,
                'override_past_limit' => 'sometimes|boolean',
                'remarks' => 'nullable|string',
                'dr_picture' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'details' => 'required|array|min:1',
                'details.*.dr_detail_id' => 'sometimes|integer|exists:deliveryreceiptdetail,DRDetailID', // Optional for updates
                'details.*.item_description' => 'required|string|max:255',
                'details.*.brand' => 'nullable|string|max:100',
                'details.*.model_number' => 'nullable|string|max:100',
                'details.*.serial_number' => 'nullable|string|max:100',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
                'details.*.unit_of_measure_id' => 'required|integer|exists:unitofmeasure,UnitOfMeasureID',
                'details.*.unit_cost' => 'required|numeric|min:0',
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
                foreach ($details as $index => $detail) {
                    $serial = isset($detail['serial_number']) ? trim((string) $detail['serial_number']) : '';
                    $quantity = (int) ($detail['quantity_delivered'] ?? 0);
                    if ($serial !== '' && $quantity > 1) {
                        $validator->errors()->add("details.$index.quantity_delivered", 'Quantity must be 1 when a serial number is provided');
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

            // Handle DR picture upload (only if a new picture is provided)
            $drPicturePath = null;
            if ($request->hasFile('dr_picture')) {
                $drPicturePath = $request->file('dr_picture')->store('dr_pictures', 'public');
            }

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

            if ((int) $requestedByUser->DepartmentID !== (int) $request->department_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['requested_by_user_id' => ['Requested by user must belong to the selected department.']]
                ], 422);
            }

            $deliveryReceipt->update([
                'DRNumber' => $request->dr_number,
                'SupplierName' => $request->supplier_name,
                'RequestedBy' => $requestedByUser->FullName,
                'RequestedByUserID' => (int) $requestedByUser->UserID,
                'DepartmentID' => $request->department_id,
                'PRNumber' => $request->pr_number,
                'PONumber' => $request->po_number,
                'InvoiceNumber' => $request->invoice_number,
                'DateReceived' => $request->date_received,
                'Remarks' => $request->remarks,
                ...$drPicturePath ? ['dr_picture' => $drPicturePath] : [],
            ]);

            // Handle details update properly - update existing, delete removed, insert new
            $existingDetailIds = $deliveryReceipt->details()->pluck('DRDetailID')->toArray();
            $incomingDetailIds = collect($request->details)->pluck('dr_detail_id')->filter()->toArray();

            // Delete details that are no longer in the request
            $detailsToDelete = array_diff($existingDetailIds, $incomingDetailIds);
            if (!empty($detailsToDelete)) {
                DeliveryReceiptDetail::whereIn('DRDetailID', $detailsToDelete)->delete();
            }

            // Update existing details and insert new ones
            foreach ($request->details as $detail) {
                $detailData = [
                    'DRID' => $deliveryReceipt->DRID,
                    'ItemDescription' => $detail['item_description'],
                    'Brand' => $detail['brand'] ?? null,
                    'ModelNumber' => $detail['model_number'] ?? null,
                    'SerialNumber' => $detail['serial_number'] ?? null,
                    'PropertyTypeID' => $detail['property_type_id'],
                    'QuantityDelivered' => $detail['quantity_delivered'],
                    'UnitOfMeasureID' => $detail['unit_of_measure_id'],
                    'UnitCost' => $detail['unit_cost'],
                    'TotalCost' => $detail['quantity_delivered'] * $detail['unit_cost'],
                ];

                if (isset($detail['dr_detail_id']) && $detail['dr_detail_id']) {
                    // Update existing detail
                    DeliveryReceiptDetail::where('DRDetailID', $detail['dr_detail_id'])
                        ->where('DRID', $deliveryReceipt->DRID) // Security check
                        ->update($detailData);
                } else {
                    // Insert new detail
                    DeliveryReceiptDetail::create($detailData);
                }
            }

            DB::commit();

            // Load relationships
            $deliveryReceipt->load(['details.propertyType', 'details.unitOfMeasure', 'department', 'requestedByUser:UserID,FullName,DepartmentID']);
            // Set receivedByUser manually
            $user = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);
            $deliveryReceipt->receivedByUser = $user ? [
                'UserID' => $user->UserID,
                'FullName' => $user->FullName
            ] : null;

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt updated successfully',
                'data' => $deliveryReceipt,
                'dr_picture_url' => Storage::disk('public')->url($deliveryReceipt->dr_picture)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update delivery receipt',
                'error' => $e->getMessage()
            ], 500);
        }
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
                $query->where('DepartmentID', (int) $request->department_id);
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
}
