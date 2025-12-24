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

class DeliveryReceiptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DeliveryReceipt::with(['receivedByUser', 'details.propertyType']);

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
            $validator = Validator::make($request->all(), [
                'supplier_name' => 'required|string|max:255',
                'po_number' => 'required|string|max:50',
                'invoice_number' => 'nullable|string|max:50',
                'date_received' => 'required|date',
                'remarks' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.item_description' => 'required|string|max:255',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
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

            // Generate DR number
            $drNumber = 'DR-' . date('Y') . '-' . str_pad(DeliveryReceipt::count() + 1, 4, '0', STR_PAD_LEFT);

            $deliveryReceipt = DeliveryReceipt::create([
                'DRNumber' => $drNumber,
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
                    'UnitCost' => $detail['unit_cost'],
                    'TotalCost' => $detail['quantity_delivered'] * $detail['unit_cost'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt created successfully',
                'data' => $deliveryReceipt->load(['receivedByUser', 'details.propertyType'])
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
            $deliveryReceipt = DeliveryReceipt::with(['receivedByUser', 'details.propertyType'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $deliveryReceipt
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

            $validator = Validator::make($request->all(), [
                'supplier_name' => 'required|string|max:255',
                'po_number' => 'required|string|max:50',
                'date_received' => 'required|date',
                'remarks' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.item_description' => 'required|string|max:255',
                'details.*.property_type_id' => 'required|integer|exists:propertytype,PropertyTypeID',
                'details.*.quantity_delivered' => 'required|integer|min:1',
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

            $deliveryReceipt->update([
                'SupplierName' => $request->supplier_name,
                'PONumber' => $request->po_number,
                'DateReceived' => $request->date_received,
                'Remarks' => $request->remarks,
            ]);

            // Delete existing details
            $deliveryReceipt->details()->delete();

            // Create new details
            foreach ($request->details as $detail) {
                DeliveryReceiptDetail::create([
                    'DRID' => $deliveryReceipt->DRID,
                    'ItemDescription' => $detail['item_description'],
                    'PropertyTypeID' => $detail['property_type_id'],
                    'QuantityDelivered' => $detail['quantity_delivered'],
                    'UnitCost' => $detail['unit_cost'],
                    'TotalCost' => $detail['quantity_delivered'] * $detail['unit_cost'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery receipt updated successfully',
                'data' => $deliveryReceipt->load(['receivedByUser', 'details.propertyType'])
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
