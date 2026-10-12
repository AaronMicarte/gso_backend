<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\MasterData\Condition;
use App\Models\PropertyUnserviceableReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UnserviceableReportTest extends TestCase
{
    private function admin()
    {
        return User::where('UserRoleID', 1)->first();
    }

    /** Test: Unauthenticated access blocked */
    public function test_unauthenticated_cannot_access_unserviceable_endpoints()
    {
        $r = $this->postJson('/api/inventory/unserviceable-reports', []);
        $r->assertStatus(401);

        $r2 = $this->getJson('/api/inventory/details/1/unserviceable-report');
        $r2->assertStatus(401);
    }

    /** Test: Submission blocked without proof attachment */
    public function test_submission_blocked_without_proof_attachment()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $detail = PropertyInventoryDetail::where('ConditionID', 1)->first();
        if (!$detail) {
            $this->markTestSkipped('No serviceable inventory detail found for testing.');
        }

        $r = $this->actingAs($admin, 'sanctum')->postJson('/api/inventory/unserviceable-reports', [
            'items' => [$detail->InventoryDetailID],
            'remarks' => 'Test without proof',
        ]);

        $r->assertStatus(422);
        $r->assertJsonValidationErrors(['attachments']);
    }

    /** Test: Submission blocked with invalid file format */
    public function test_submission_blocked_with_invalid_file_format()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $detail = PropertyInventoryDetail::where('ConditionID', 1)->first();
        if (!$detail) {
            $this->markTestSkipped('No serviceable inventory detail found for testing.');
        }

        Storage::fake('local');
        $invalidFile = UploadedFile::fake()->create('proof.txt', 100, 'text/plain');

        $r = $this->actingAs($admin, 'sanctum')->post('/api/inventory/unserviceable-reports', [
            'items' => json_encode([$detail->InventoryDetailID]),
            'remarks' => 'Invalid file test',
            'attachments' => [$invalidFile],
        ], ['Accept' => 'application/json']);

        $r->assertStatus(422);
    }

    /** Test: Successful report submission and status update */
    public function test_successful_unserviceable_report_submission_and_detail_update()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        // Find or create an item that is serviceable
        $detail = PropertyInventoryDetail::where('ConditionID', 1)->first();
        if (!$detail) {
            $this->markTestSkipped('No serviceable inventory detail found for testing.');
        }

        Storage::fake('local');
        $proofPdf = UploadedFile::fake()->create('signed_inspection_report.pdf', 500, 'application/pdf');

        $response = $this->actingAs($admin, 'sanctum')->post('/api/inventory/unserviceable-reports', [
            'items' => json_encode([$detail->InventoryDetailID]),
            'remarks' => 'Defect confirmed during technical inspection',
            'attachments' => [$proofPdf],
        ], ['Accept' => 'application/json']);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'report' => [
                'id',
                'report_number',
                'status',
                'items',
                'attachments',
            ],
        ]);

        $data = $response->json();
        $reportId = $data['report']['id'];
        $this->assertStringStartsWith('UNS-', $data['report']['report_number']);

        // Verify database records
        $this->assertDatabaseHas('property_unserviceable_reports', [
            'id' => $reportId,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('property_unserviceable_items', [
            'unserviceable_report_id' => $reportId,
            'inventory_detail_id' => $detail->InventoryDetailID,
        ]);

        $this->assertDatabaseHas('property_unserviceable_attachments', [
            'unserviceable_report_id' => $reportId,
            'file_name' => 'signed_inspection_report.pdf',
        ]);

        // Verify item condition updated to Unserviceable (ConditionID = 2)
        $detail->refresh();
        $this->assertEquals(2, $detail->ConditionID);

        // Test eye-icon endpoint returns this report
        $eyeRes = $this->actingAs($admin, 'sanctum')->getJson("/api/inventory/details/{$detail->InventoryDetailID}/unserviceable-report");
        $eyeRes->assertStatus(200);
        $eyeRes->assertJson([
            'has_report' => true,
            'report' => [
                'id' => $reportId,
                'status' => 'active',
            ],
        ]);

        // Test duplicate active report prevention
        $dupProof = UploadedFile::fake()->create('duplicate_proof.pdf', 200, 'application/pdf');
        $dupRes = $this->actingAs($admin, 'sanctum')->post('/api/inventory/unserviceable-reports', [
            'items' => json_encode([$detail->InventoryDetailID]),
            'remarks' => 'Attempting duplicate submission',
            'attachments' => [$dupProof],
        ], ['Accept' => 'application/json']);

        $dupRes->assertStatus(422);
    }

    /** Test: Eye-icon endpoint returns empty state for item without report */
    public function test_detail_without_unserviceable_report_returns_empty_state()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        // Use high non-existing or non-reported detail ID
        $r = $this->actingAs($admin, 'sanctum')->getJson('/api/inventory/details/99999999/unserviceable-report');
        $r->assertStatus(200);
        $r->assertJson([
            'has_report' => false,
            'report' => null,
        ]);
    }
}
