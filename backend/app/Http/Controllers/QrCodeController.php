<?php

namespace App\Http\Controllers;

use App\Models\AssignedProperty;
use App\Models\PropertyIssuanceDetail;
use App\Models\QrCode;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    // GET /api/qr/properties
    public function index(Request $request)
    {
        $perPage = max(10, (int) $request->query('per_page', 20));
        $search = trim((string) $request->query('search', ''));
        $withQrOnly = $request->boolean('with_qr_only', false);
        $missingQrOnly = $request->boolean('missing_qr_only', false);

        $query = AssignedProperty::query()
            ->with([
                'inventoryDetail.drDetail',
                'inventoryDetail.unitOfMeasure',
                'endUser.department',
                'department',
                'propertyStatus',
                'condition',
                'qrCode',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('PropertyCardNumber', 'like', "%{$search}%")
                    ->orWhere('Description', 'like', "%{$search}%")
                    ->orWhereHas('inventoryDetail', function ($iq) use ($search) {
                        $iq->where('PropertyNumber', 'like', "%{$search}%")
                            ->orWhere('SerialNumber', 'like', "%{$search}%")
                            ->orWhere('Brand', 'like', "%{$search}%")
                            ->orWhere('ModelNumber', 'like', "%{$search}%");
                    })
                    ->orWhereHas('endUser', function ($eq) use ($search) {
                        $eq->where('FullName', 'like', "%{$search}%");
                    });
            });
        }

        if ($withQrOnly) {
            $query->whereHas('qrCode');
        }

        if ($missingQrOnly) {
            $query->whereDoesntHave('qrCode');
        }

        return response()->json($query->orderByDesc('PropertyID')->paginate($perPage));
    }

    // POST /api/qr/properties/{propertyId}/generate
    public function generate(Request $request, int $propertyId)
    {
        $assignedProperty = AssignedProperty::with(['inventoryDetail'])->findOrFail($propertyId);

        $baseUrl = rtrim((string) (config('app.url') ?: env('APP_URL', '')), '/');
        if ($baseUrl === '') {
            $baseUrl = 'http://localhost:3000';
        }

        $hash = substr(hash_hmac('sha256', (string) $assignedProperty->PropertyID, (string) config('app.key')), 0, 12);
        $payload = $baseUrl . '/qr/scan/' . $assignedProperty->PropertyID . '?h=' . $hash;

        $qr = QrCode::updateOrCreate(
            ['InventoryDetailID' => $assignedProperty->InventoryDetailID],
            [
                'PropertyID' => (int) $assignedProperty->PropertyID,
                'DepartmentID' => (int) $assignedProperty->DepartmentID,
                'QRCodeData' => $payload,
                'DateGenerated' => now(),
            ]
        );

        return response()->json([
            'message' => 'QR generated',
            'qr' => $qr,
        ], 201);
    }

    // GET /api/qr/scan/{propertyId}
    public function scan(Request $request, int $propertyId)
    {
        $assignedProperty = AssignedProperty::with([
            'inventoryDetail.drDetail',
            'inventoryDetail.unitOfMeasure',
            'endUser.department',
            'department',
            'propertyStatus',
            'condition',
            'qrCode',
        ])->findOrFail($propertyId);

        $latestIssuanceDetail = PropertyIssuanceDetail::with([
            'issuance.issuedBy:UserID,FullName',
            'issuance.request.department:DepartmentID,DepartmentName,DepartmentCode',
        ])
            ->where('InventoryDetailID', $assignedProperty->InventoryDetailID)
            ->orderByDesc('IssuanceDetailID')
            ->first();

        return response()->json([
            'property' => $assignedProperty,
            'latest_issuance_detail' => $latestIssuanceDetail,
        ]);
    }
}
