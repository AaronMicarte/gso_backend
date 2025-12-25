<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;
use App\Models\MasterData\PropertyType;
use App\Models\MasterData\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DeliveryReceiptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DeliveryReceipt::with(['receivedByUser', 'details.propertyType', 'details.unitOfMeasure'])
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
                'po_number' => 'nullable|string|max:50', // OPTIONAL
                'invoice_number' => 'nullable|string|max:50',
                'date_received' => $dateRules,
                'override_past_limit' => 'sometimes|boolean',
                'remarks' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.item_description' => 'required|string|max:255',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
                'details.*.unit_of_measure_id' => 'required|integer|exists:unitofmeasure,UnitOfMeasureID',
                'details.*.unit_cost' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
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

            $deliveryReceipt = DeliveryReceipt::create([
                'DRNumber' => $request->dr_number,
                'SupplierName' => $request->supplier_name,
                'PONumber' => $request->po_number,
                'InvoiceNumber' => $request->invoice_number,
                'ReceivedBy' => Auth::user()->UserID,
                'DateReceived' => $request->date_received,
                'Remarks' => $request->remarks,
            ]);

            // Create details
            foreach ($request->details as $detail) {
                DeliveryReceiptDetail::create([
                    'DRID' => $deliveryReceipt->DRID,
                    'ItemDescription' => $detail['item_description'],
                    'PropertyTypeID' => $detail['property_type_id'],
                    'QuantityDelivered' => $detail['quantity_delivered'],
                    'UnitOfMeasureID' => $detail['unit_of_measure_id'],
                    'UnitCost' => $detail['unit_cost'],
                    'TotalCost' => $detail['quantity_delivered'] * $detail['unit_cost'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt created successfully',
                'data' => $deliveryReceipt->load(['receivedByUser', 'details.propertyType', 'details.unitOfMeasure'])
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
            $deliveryReceipt = DeliveryReceipt::with(['details'])
                ->findOrFail($id);

            // Load receivedByUser
            $deliveryReceipt->receivedByUser = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);

            // Load and attach relationships for details
            $deliveryReceipt->details->each(function ($detail) {
                $detail->propertyType = PropertyType::find($detail->PropertyTypeID);
                $detail->unitOfMeasure = \App\Models\MasterData\UnitOfMeasure::find($detail->UnitOfMeasureID);
            });

            return response()->json([
                'success' => true,
                'data' => $deliveryReceipt->toArray()
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
                'po_number' => 'nullable|string|max:50', // OPTIONAL
                'date_received' => $dateRules,
                'override_past_limit' => 'sometimes|boolean',
                'remarks' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.dr_detail_id' => 'sometimes|integer|exists:deliveryreceiptdetail,DRDetailID', // Optional for updates
                'details.*.item_description' => 'required|string|max:255',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
                'details.*.unit_of_measure_id' => 'required|integer|exists:unitofmeasure,UnitOfMeasureID',
                'details.*.unit_cost' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
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
            $deliveryReceipt->update([
                'DRNumber' => $request->dr_number,
                'SupplierName' => $request->supplier_name,
                'PONumber' => $request->po_number,
                'DateReceived' => $request->date_received,
                'Remarks' => $request->remarks,
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

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt updated successfully',
                'data' => $deliveryReceipt->load(['receivedByUser', 'details.propertyType', 'details.unitOfMeasure'])
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
}
