<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\AssetAuditLog;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\Inventory\InventoryDetail;
use App\Models\EndUser;
use App\Services\AssetRequestSignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyIssuanceController extends Controller
{
    /**
     * Get all property issuances
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PropertyIssuance::with([
            'request.requester:UserID,FirstName,LastName',
            'request.department:DepartmentID,DepartmentName',
            'issuedBy:UserID,FirstName,LastName',
            'issuedTo:EndUserID,FirstName,LastName,DepartmentID',
            'details.inventoryDetail.inventory:InventoryID,ItemDescription,Brand,ModelNumber,PropertyTypeID,Accountable',
        ]);

        // Role-based filtering
        if ($user->RoleID === 3) { // Department Head
            $query->whereHas('request', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        } elseif ($user->RoleID === 2) { // Staff
            $query->whereHas('issuedTo', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        }

        // Status filter (based on request status)
        if ($request->filled('status')) {
            if ($request->status === 'issued') {
                $query->whereNotNull('DateIssued');
            }
        }

        $issuances = $query->orderBy('DateIssued', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($issuances);
    }

    /**
     * Get single property issuance
     */
    public function show($id)
    {
        $issuance = PropertyIssuance::with([
            'request.requester:UserID,FirstName,LastName,Email',
            'request.department:DepartmentID,DepartmentName,DepartmentCode',
            'issuedBy:UserID,FirstName,LastName,Email',
            'issuedTo:EndUserID,FirstName,LastName,Position,DepartmentID',
            'details.inventoryDetail.inventory:InventoryID,ItemDescription,Brand,ModelNumber,PropertyTypeID,Accountable,PropertyNumber,SerialNumber',
        ])->findOrFail($id);

        return response()->json($issuance);
    }

    /**
     * Create new property issuance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:assetrequest,RequestID',
            'date_issued' => 'required|date',
            'remarks' => 'nullable|string',
            'issuerSignature' => 'required|string', // base64 data URL
            'issuerSignatureSource' => 'required|in:canvas,upload',
        ]);

        try {
            DB::beginTransaction();

            // Get the asset request with its details
            $assetRequest = AssetRequest::with('details')->findOrFail($validated['request_id']);

            // Verify the request is approved
            if ($assetRequest->Status !== 'approved') {
                return response()->json([
                    'message' => 'Only approved requests can be issued',
                    'error' => 'Invalid request status'
                ], 422);
            }

            // Group items by end user (ID or Name) and classification
            $groupedItems = $assetRequest->details->groupBy(function ($detail) {
                $userKey = $detail->AssignedEndUserID ? 'ID_'.$detail->AssignedEndUserID : 'NAME_'.($detail->IntendedEndUser ?? 'Unknown');
                return $userKey . '_' . ($detail->PropertyClassification ?? 'ICS');
            });

            $issuances = [];

            foreach ($groupedItems as $key => $items) {
                $firstItem = $items->first();
                $endUserId = $firstItem->AssignedEndUserID;
                $endUserName = $firstItem->IntendedEndUser;
                $classification = $firstItem->PropertyClassification ?? 'ICS';

                // Generate document number
                $documentNumber = $this->generateDocumentNumber($classification);

                // Prepare issuance data
                $issuanceData = [
                    'RequestID' => $validated['request_id'],
                    'IssuedBy' => Auth::id(),
                    'DocumentType' => $classification,
                    'DocumentNumber' => $documentNumber,
                    'DateIssued' => $validated['date_issued'],
                    'Remarks' => $validated['remarks'] ?? null,
                    'Status' => 'pending_acknowledgment',
                    'CreatedDate' => now(),
                ];

                if ($endUserId) {
                    $issuanceData['IssuedTo'] = $endUserId;
                } else {
                    $issuanceData['IssuedTo'] = null;
                    $issuanceData['IssuedToName'] = $endUserName ?? 'Unspecified User';
                }

                // Create the property issuance
                $issuance = PropertyIssuance::create($issuanceData);

                // Create issuance details from asset request details
                foreach ($items as $detail) {
                    PropertyIssuanceDetail::create([
                        'IssuanceID' => $issuance->IssuanceID,
                        'RequestDetailID' => $detail->RequestDetailID,
                        'ItemDescription' => $detail->ItemDescription,
                        'Unit' => $detail->Unit,
                        'QuantityIssued' => $detail->QuantityRequested,
                        'Remarks' => $detail->AdminNotes,
                    ]);
                }

                $issuances[] = $issuance;
            }

            // Update the request status to issued
            $assetRequest->update([
                'Status' => 'issued',
                'IssuedBy' => Auth::id(),
                'IssuedAt' => now()
            ]);

            // Create audit log
            AssetAuditLog::create([
                'EntityType' => 'asset_request',
                'EntityID' => $assetRequest->RequestID,
                'Action' => 'issued',
                'PerformedBy' => Auth::id(),
                'Details' => json_encode([
                    'request_number' => $assetRequest->RequestNumber,
                    'issuances_count' => count($issuances),
                    'date_issued' => $validated['date_issued']
                ]),
                'IPAddress' => $request->ip(),
                'UserAgent' => $request->userAgent(),
            ]);

            // Process issuer signature
            $signatureService = new AssetRequestSignatureService();
            $signatureService->processAndStore(
                $validated['request_id'],
                'issuer',
                $validated['issuerSignature'],
                $validated['issuerSignatureSource'],
                Auth::id()
            );

            DB::commit();

            return response()->json([
                'message' => 'Assets issued successfully',
                'data' => [
                    'issuances' => $issuances->map(function ($issuance) {
                        return $issuance->load([
                            'request',
                            'issuedBy:UserID,FullName',
                            'issuedTo:EndUserID,FullName',
                            'details'
                        ]);
                    })
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create property issuance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate document number for PAR/ICS
     */
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

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get approved asset requests for issuance
     */
    public function getApprovedRequests(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Simple query to get approved requests first
            $query = AssetRequest::where('Status', 'approved');

            // Role-based filtering
            if ($user && $user->RoleID === 3) { // Department Head
                $query->where('DepartmentID', $user->DepartmentID);
            }

            // Only get requests that haven't been issued yet
            $query->whereDoesntHave('propertyIssuance');

            $requests = $query->with([
                'requester:UserID,FullName',
                'department:DepartmentID,DepartmentName',
                'details:RequestDetailID,RequestID,ItemDescription,QuantityRequested,Purpose,IntendedEndUser,PropertyClassification'
            ])->orderBy('RequestDate', 'desc')->get();

            return response()->json([
                'data' => $requests,
                'message' => $requests->isEmpty() ? 'No approved requests available for issuance' : 'Approved requests retrieved successfully',
                'total' => $requests->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'message' => 'Error retrieving approved requests: ' . $e->getMessage(),
                'total' => 0,
                'error' => true
            ], 500);
        }
    }

    /**
     * Generate PAR/ICS document
     */
    public function generateDocument($id, $type)
    {
        $issuance = PropertyIssuance::with([
            'request.requester:UserID,FirstName,LastName,Position',
            'request.department:DepartmentID,DepartmentName',
            'issuedBy:UserID,FirstName,LastName,Position',
            'issuedTo:EndUserID,FirstName,LastName,Position',
            'details.inventoryDetail.inventory:InventoryID,ItemDescription,Brand,ModelNumber,PropertyNumber,SerialNumber,Accountable,UnitCost',
        ])->findOrFail($id);

        $documentType = strtoupper($type);
        
        if (!in_array($documentType, ['PAR', 'ICS'])) {
            return response()->json(['message' => 'Invalid document type'], 400);
        }

        // Generate document data
        $documentData = [
            'type' => $documentType,
            'document_number' => $issuance->generateDocumentNumber($documentType),
            'date_issued' => $issuance->DateIssued,
            'department' => $issuance->request->department->DepartmentName,
            'recipient' => [
                'name' => $issuance->issuedTo->FirstName . ' ' . $issuance->issuedTo->LastName,
                'position' => $issuance->issuedTo->Position ?? 'N/A'
            ],
            'issuer' => [
                'name' => $issuance->issuedBy->FirstName . ' ' . $issuance->issuedBy->LastName,
                'position' => $issuance->issuedBy->Position ?? 'N/A'
            ],
            'items' => $issuance->details->map(function ($detail) {
                return [
                    'description' => $detail->inventoryDetail->inventory->ItemDescription,
                    'brand' => $detail->inventoryDetail->inventory->Brand,
                    'model' => $detail->inventoryDetail->inventory->ModelNumber,
                    'property_number' => $detail->inventoryDetail->inventory->PropertyNumber,
                    'serial_number' => $detail->inventoryDetail->inventory->SerialNumber,
                    'unit_cost' => $detail->inventoryDetail->inventory->UnitCost,
                    'quantity' => $detail->QuantityIssued,
                    'total_cost' => $detail->inventoryDetail->inventory->UnitCost * $detail->QuantityIssued
                ];
            }),
            'total_amount' => $issuance->details->sum(function ($detail) {
                return $detail->inventoryDetail->inventory->UnitCost * $detail->QuantityIssued;
            }),
            'remarks' => $issuance->Remarks
        ];

        return response()->json($documentData);
    }

    /**
     * Get issuance statistics
     */
    public function getStats()
    {
        $user = Auth::user();
        $query = PropertyIssuance::query();

        // Role-based filtering
        if ($user->RoleID === 3) { // Department Head
            $query->whereHas('request', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        } elseif ($user->RoleID === 2) { // Staff
            $query->whereHas('issuedTo', function ($q) use ($user) {
                $q->where('DepartmentID', $user->DepartmentID);
            });
        }

        $total = $query->count();
        $thisMonth = $query->whereMonth('DateIssued', now()->month)
                          ->whereYear('DateIssued', now()->year)
                          ->count();
        
        $parCount = $query->whereHas('details.inventoryDetail.inventory', function ($q) {
            $q->where('Accountable', 'Yes');
        })->count();
        
        $icsCount = $total - $parCount;

        return response()->json([
            'total_issuances' => $total,
            'this_month' => $thisMonth,
            'par_count' => $parCount,
            'ics_count' => $icsCount,
        ]);
    }
}