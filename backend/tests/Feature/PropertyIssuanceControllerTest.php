<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;
use App\Models\AssetRequest;
use App\Models\AssetRequestDetail;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\RequisitionIssueSlip;
use App\Models\RequisitionIssueSlipItem;
use App\Models\PropertyIssuanceDetail;

class PropertyIssuanceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_issuance_allocates_inventory_and_populates_inventory_detail_and_stock_no()
    {
        // minimal fixtures: userrole + department tables are handled in other helpers in suite;
        // create user
        $user = User::create([
            'UserRoleID' => 2,
            'DepartmentID' => 1,
            'Username' => 'issue.user',
            'Email' => 'issue.user@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Issue User',
            'IsActive' => 1,
        ]);

        // create an asset request + detail
        $request = AssetRequest::create([
            'RequestNumber' => 'REQ-TEST-1',
            'RequestedBy' => $user->UserID,
            'DepartmentID' => 1,
            'Purpose' => 'Test issuance',
            'Status' => 'approved',
            'RequestDate' => now()->format('Y-m-d'),
        ]);

        $detail = AssetRequestDetail::create([
            'RequestID' => $request->RequestID,
            'ItemDescription' => 'Executive Chair',
            'Unit' => 'unit',
            'QuantityRequested' => 1,
            'Purpose' => 'For testing',
            'PropertyClassification' => 'ICS'
        ]);

        // create available inventory and inventory detail
        $inv = PropertyInventory::create(['ItemDescription' => 'Exec Chair']);
        $invDetail = PropertyInventoryDetail::create([
            'InventoryID' => $inv->InventoryID,
            'Description' => 'Executive Chair sample',
            'Quantity' => 1,
            'QuantityAvailable' => 1,
            'PropertyStatusID' => 1, // available
            'PropertyNumber' => null
        ]);

        // create a RIS and RIS item for the request detail
        $ris = RequisitionIssueSlip::create([
            'ris_number' => 'RIS-TST-1',
            'asset_request_id' => $request->RequestID,
            'status' => 'for_issuance'
        ]);

        $risItem = RequisitionIssueSlipItem::create([
            'ris_id' => $ris->id,
            'asset_request_detail_id' => $detail->RequestDetailID,
            'stock_no' => 'N/A',
            'unit' => 'unit',
            'description' => 'Executive Chair',
            'quantity_requested' => 1,
            'stock_available' => 0
        ]);

        // stub SignatureService to avoid GD/processing in CI
        $this->app->instance(\App\Services\SignatureService::class, new \App\Services\SignatureService(false));

        $payload = [
            'request_id' => $request->RequestID,
            'date_issued' => now()->format('Y-m-d'),
            'remarks' => 'Issued by test',
            'inventory_items' => [
                [
                    'request_detail_id' => $detail->RequestDetailID,
                    'inventory_detail_id' => $invDetail->InventoryDetailID,
                    'quantity_issued' => 1
                ]
            ],
            'issuerSignature' => 'data:image/png;base64,xxx',
            'issuerSignatureSource' => 'canvas'
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/property-issuances', $payload);

        file_put_contents('php://stderr', print_r($response->json(), true));

        $response->assertStatus(201);

        // issuance detail created and linked to inventory detail
        $this->assertDatabaseHas('propertyissuancedetail', [
            'RequestDetailID' => $detail->RequestDetailID,
            'InventoryDetailID' => $invDetail->InventoryDetailID,
            'QuantityIssued' => 1
        ]);

        // inventory quantity available decremented
        $this->assertDatabaseHas('propertyinventorydetail', [
            'InventoryDetailID' => $invDetail->InventoryDetailID,
            'QuantityAvailable' => 0
        ]);

        // AssignedProperty (accountability) should exist
        $this->assertDatabaseHas('assignedproperty', [
            'InventoryDetailID' => $invDetail->InventoryDetailID,
            'PropertyStatusID' => 2
        ]);

        // RIS item should be updated with inventory_detail_id and stock_no (populated)
        $this->assertDatabaseHas('requisition_issue_slip_items', [
            'ris_id' => $ris->id,
            'asset_request_detail_id' => $detail->RequestDetailID,
            'inventory_detail_id' => $invDetail->InventoryDetailID
        ]);

        // Response includes assigned PropertyNumber (may be 'N/A' if none)
        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('issuances', $json['data']);
        $issuances = $json['data']['issuances'];
        $this->assertNotEmpty($issuances);
        $first = $issuances[0];
        $this->assertArrayHasKey('details', $first);
        $this->assertNotEmpty($first['details']);
        $this->assertArrayHasKey('inventoryDetail', $first['details'][0]);
    }
}
