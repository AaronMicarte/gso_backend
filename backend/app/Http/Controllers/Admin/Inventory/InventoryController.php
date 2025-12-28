<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Procurement\PropertyReceived;
use App\Models\Procurement\DeliveryReceiptDetail;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    // POST /api/admin/inventory/ingest-from-iar
    public function ingestFromIar(Request $request)
    {
        $request->validate([
            'iar_id' => 'required|integer|exists:inspectionacceptancereport,IARID',
        ]);

        $iarId = $request->input('iar_id');

        // Fetch IAR and its details
        $iar = InspectionAcceptanceReport::with(['details'])->find($iarId);
        if (!$iar) {
            return response()->json(['message' => 'IAR not found.'], 404);
        }
        if ($iar->details->isEmpty()) {
            return response()->json(['message' => 'No IAR details to ingest.'], 422);
        }

        // Start transaction for safety
        DB::beginTransaction();
        try {
            // Create PropertyInventory record
            $inventory = PropertyInventory::create([
                'InventoryNumber' => 'INV-' . date('Ymd-His') . '-' . $iar->IARID,
                'IARID' => $iar->IARID,
                'DateReceived' => $iar->DateAccepted ?? now(),
                'Remarks' => 'Auto-ingested from IAR #' . $iar->IARNumber,
                'CreatedDate' => now(),
            ]);

            // For each IAR detail, create PropertyInventoryDetail
            foreach ($iar->details as $detail) {
                // Get DR detail for cost/unit info
                $drDetail = DeliveryReceiptDetail::find($detail->DRDetailID);
                if (!$drDetail) continue;

                // Determine Brand, Model, Serial (Prefer Verified from IAR, fallback to DR)
                $brand = $detail->VerifiedBrand ?? $drDetail->Brand;
                $model = $detail->VerifiedModel ?? $drDetail->ModelNumber;
                $serial = $detail->VerifiedSerial ?? $drDetail->SerialNumber;

                PropertyInventoryDetail::create([
                    'InventoryID' => $inventory->InventoryID,
                    'PropertyTypeID' => $detail->PropertyTypeID,
                    'ConditionID' => $detail->ConditionID,
                    'Brand' => $brand,
                    'ModelNumber' => $model,
                    'SerialNumber' => $serial,
                    'Quantity' => $detail->QuantityAccepted,
                    'QuantityAvailable' => $detail->QuantityAccepted,
                    'PropertyStatusID' => 1, // Available
                    'UnitOfMeasureID' => $drDetail->UnitOfMeasureID,
                    'UnitCost' => $drDetail->UnitCost,
                    'TotalCost' => $drDetail->UnitCost * $detail->QuantityAccepted,
                    'SourceIARDetailID' => $detail->IARDetailID,
                    'SourceDRDetailID' => $detail->DRDetailID,
                    'Remarks' => $detail->Remarks,
                    'CreatedDate' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Inventory ingestion successful.','inventory_id' => $inventory->InventoryID], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ingestion failed: ' . $e->getMessage()], 500);
        }
    }

    // GET /api/admin/inventory/pending-ingestion
    public function pendingIngestion()
    {
        $pendingIars = InspectionAcceptanceReport::whereNotNull('DateAccepted')
            ->whereNotIn('IARID', function($query) {
                $query->select('IARID')->from('propertyinventory')->whereNotNull('IARID');
            })
            ->with(['details'])
            ->get();

        return response()->json($pendingIars);
    }

    // GET /api/admin/inventory
    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(10, (int) $request->query('per_page', 10));

        $query = PropertyInventory::with(['details']);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($paginator);
    }

    // GET /api/admin/inventory/{id}
    public function show($id)
    {
        $inventory = PropertyInventory::with(['details.propertyType', 'details.qrCode'])->find($id);
        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        return response()->json($inventory);
    }

    // POST /api/admin/inventory/details/{id}/generate-qr
    public function generateQr(Request $request, $id)
    {
        $detail = PropertyInventoryDetail::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Inventory detail not found.'], 404);
        }

        $force = $request->boolean('force', false);

        // Only allow manual generation if the inventory detail has been issued (or force=true)
        $issued = \App\Models\Inventory\PropertyIssuanceDetail::where('InventoryDetailID', $detail->InventoryDetailID)->exists();
        if (!$issued && !$force) {
            return response()->json(['message' => 'Cannot generate QR: inventory item is not issued. Generate QR during issuance or set force=true if you really want to.'], 403);
        }

        // If already has QR and not forcing, return existing
        if ($detail->qrCode && !$force) {
            return response()->json(['message' => 'QR already exists', 'qr' => $detail->qrCode], 200);
        }

        // Generate deterministic QR payload (URL with hash)
        $baseUrl = config('app.url') ?? env('APP_URL');
        $hash = substr(sha1($detail->InventoryDetailID . '|' . now()->timestamp), 0, 12);
        $payload = rtrim($baseUrl, '/') . '/inventory/scan/' . $detail->InventoryDetailID . '?h=' . $hash;

        // Upsert into qrcode table
        $qr = \App\Models\QrCode::updateOrCreate(
            ['InventoryDetailID' => $detail->InventoryDetailID],
            [
                'PropertyID' => null,
                'DepartmentID' => null,
                'QRCodeData' => $payload,
                'DateGenerated' => now(),
            ]
        );

        return response()->json(['message' => 'QR generated', 'qr' => $qr], 201);
    }
}
