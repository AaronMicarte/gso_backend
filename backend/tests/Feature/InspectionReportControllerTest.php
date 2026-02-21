<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\DeliveryReceiptDetail;

class InspectionReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private function ensureBaseTables(): void
    {
        if (!Schema::hasTable('userrole')) {
            Schema::create('userrole', function (Blueprint $table) {
                $table->increments('UserRoleID');
                $table->string('RoleName')->nullable();
                $table->string('Description')->nullable();
                $table->dateTime('CreatedDate')->nullable();
                $table->dateTime('UpdatedDate')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('department')) {
            Schema::create('department', function (Blueprint $table) {
                $table->increments('DepartmentID');
                $table->string('DepartmentName')->nullable();
            });
        }

        if (!Schema::hasTable('deliveryreceipt')) {
            Schema::create('deliveryreceipt', function (Blueprint $table) {
                $table->increments('DRID');
                $table->string('DRNumber')->nullable();
                $table->string('SupplierName')->nullable();
                $table->integer('RequestedBy')->nullable();
                $table->string('PONumber')->nullable();
                $table->date('DateReceived')->nullable();
                $table->integer('ReceivedBy')->nullable();
                $table->string('InspectionStatus')->default('pending');
                $table->timestamp('InspectionCompletedAt')->nullable();
                $table->dateTime('CreatedDate')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('deliveryreceiptdetail')) {
            Schema::create('deliveryreceiptdetail', function (Blueprint $table) {
                $table->increments('DRDetailID');
                $table->integer('DRID');
                $table->string('ItemDescription')->nullable();
                $table->integer('QuantityDelivered')->default(1);
                $table->decimal('UnitCost', 10, 2)->default(0);
                $table->decimal('TotalCost', 10, 2)->default(0);
                $table->string('InspectionStatus')->default('pending');
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('inspection_reports')) {
            Schema::create('inspection_reports', function (Blueprint $table) {
                $table->bigIncrements('InspectionReportID');
                $table->unsignedInteger('DRID');
                $table->unsignedInteger('InspectedBy');
                $table->string('InspectorName');
                $table->date('DateInspected');
                $table->string('Status')->default('pending');
                $table->text('Remarks')->nullable();
                $table->timestamp('CompletedAt')->nullable();
                $table->string('IPAddress')->nullable();
                $table->string('UserAgent')->nullable();
                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('inspection_report_items')) {
            Schema::create('inspection_report_items', function (Blueprint $table) {
                $table->bigIncrements('InspectionReportDetailID');
                $table->unsignedBigInteger('InspectionReportID');
                $table->unsignedInteger('DRDetailID');
                $table->string('Status')->default('pending');
                $table->text('Remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!DB::table('userrole')->where('UserRoleID', 11)->exists()) {
            DB::table('userrole')->insert([
                'UserRoleID' => 11,
                'RoleName' => 'Inspection Officer',
                'Description' => 'Performs inspection and verification of delivered items',
                'CreatedDate' => now(),
            ]);
        }

        if (!DB::table('department')->where('DepartmentID', 1)->exists()) {
            DB::table('department')->insert([
                'DepartmentID' => 1,
                'DepartmentName' => 'General Services Office',
            ]);
        }
    }

    public function test_pending_items_returns_delivery_receipts(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 11,
            'DepartmentID' => 1,
            'Username' => 'inspector.user',
            'Email' => 'inspector@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Inspector User',
            'IsActive' => 1,
        ]);

        $dr = DeliveryReceipt::create([
            'DRNumber' => 'DR-TEST-01',
            'SupplierName' => 'Test Supplier',
            'RequestedBy' => $user->UserID,
            'PONumber' => 'PO-TEST',
            'DateReceived' => now()->format('Y-m-d'),
            'ReceivedBy' => $user->UserID,
            'InspectionStatus' => 'pending',
        ]);

        DeliveryReceiptDetail::create([
            'DRID' => $dr->DRID,
            'ItemDescription' => 'Test Item',
            'QuantityDelivered' => 2,
            'UnitCost' => 100,
            'TotalCost' => 200,
            'InspectionStatus' => 'pending',
        ]);

        $response = $this->actingAs($user)->getJson('/api/inspection/pending-items');
        $response->assertStatus(200);
        $response->assertJsonFragment(['DRNumber' => 'DR-TEST-01']);
    }

    public function test_complete_inspection_updates_delivery_receipt_status(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 11,
            'DepartmentID' => 1,
            'Username' => 'inspector.complete',
            'Email' => 'inspector.complete@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Inspector Complete',
            'IsActive' => 1,
        ]);

        $dr = DeliveryReceipt::create([
            'DRNumber' => 'DR-TEST-02',
            'SupplierName' => 'Test Supplier',
            'RequestedBy' => $user->UserID,
            'PONumber' => 'PO-TEST-02',
            'DateReceived' => now()->format('Y-m-d'),
            'ReceivedBy' => $user->UserID,
            'InspectionStatus' => 'pending',
        ]);

        $detail = DeliveryReceiptDetail::create([
            'DRID' => $dr->DRID,
            'ItemDescription' => 'Test Item 2',
            'QuantityDelivered' => 1,
            'UnitCost' => 100,
            'TotalCost' => 100,
            'InspectionStatus' => 'pending',
        ]);

        $createResponse = $this->actingAs($user)->postJson('/api/inspection/reports', [
            'dr_id' => $dr->DRID,
            'inspector_name' => 'Inspector Complete',
            'date_inspected' => now()->format('Y-m-d'),
            'remarks' => 'Checked',
            'items' => [
                [
                    'dr_detail_id' => $detail->DRDetailID,
                    'status' => 'inspected',
                    'remarks' => 'OK',
                ],
            ],
        ]);

        $createResponse->assertStatus(201);
        $reportId = $createResponse->json('data.InspectionReportID');

        $completeResponse = $this->actingAs($user)->postJson("/api/inspection/reports/{$reportId}/complete");
        $completeResponse->assertStatus(200);

        $this->assertDatabaseHas('deliveryreceipt', [
            'DRID' => $dr->DRID,
            'InspectionStatus' => 'inspected',
        ]);

        $this->assertDatabaseHas('deliveryreceiptdetail', [
            'DRDetailID' => $detail->DRDetailID,
            'InspectionStatus' => 'inspected',
        ]);
    }

    public function test_delivery_receipt_list_status_changes_with_issuance(): void
    {
        $this->ensureBaseTables();

        // create an admin user (role 1) so we can call the DR list
        $user = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'admin.user',
            'Email' => 'admin@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Admin User',
            'IsActive' => 1,
        ]);

        // create a delivery receipt that is already inspected and accepted
        $dr = DeliveryReceipt::create([
            'DRNumber' => 'DR-TEST-03',
            'SupplierName' => 'Supplier X',
            'RequestedBy' => $user->UserID,
            'PONumber' => 'PO-123',
            'DateReceived' => now()->format('Y-m-d'),
            'ReceivedBy' => $user->UserID,
            'InspectionStatus' => 'inspected',
        ]);

        // insert an IAR with DateAccepted so the backend considers it accepted
        DB::table('inspectionacceptancereport')->insert([
            'DRID' => $dr->DRID,
            'DateInspected' => now()->format('Y-m-d'),
            'DateAccepted' => now()->format('Y-m-d'),
            'InspectedBy' => $user->UserID,
            'CreatedDate' => now(),
            'UpdatedDate' => now(),
        ]);

        // no issuance yet = state should be to_be_issued
        $response = $this->actingAs($user)->getJson('/api/delivery-receipts');
        $response->assertStatus(200);
        $response->assertJsonPath('data.data.0.workflow_status', 'to_be_issued');
        $response->assertJsonPath('data.data.0.workflow_status_label', 'To be issued');

        // create a property issuance record with pending_acknowledgment
        DB::table('propertyissuance')->insert([
            'DRID' => $dr->DRID,
            'Status' => 'pending_acknowledgment',
            'DateIssued' => now()->format('Y-m-d'),
            'CreatedDate' => now(),
            'UpdatedDate' => now(),
        ]);

        // list again and ensure status remains to_be_issued, not issued
        $response = $this->actingAs($user)->getJson('/api/delivery-receipts');
        $response->assertStatus(200);
        $response->assertJsonPath('data.data.0.workflow_status', 'to_be_issued');
        $response->assertJsonPath('data.data.0.workflow_status_label', 'To be issued');

        // update the issuance to acknowledged, now DR should show acknowledged
        DB::table('propertyissuance')
            ->where('DRID', $dr->DRID)
            ->update(['Status' => 'acknowledged']);

        $response = $this->actingAs($user)->getJson('/api/delivery-receipts');
        $response->assertStatus(200);
        $response->assertJsonPath('data.data.0.workflow_status', 'acknowledged');
        $response->assertJsonPath('data.data.0.workflow_status_label', 'Acknowledged');
    }
}
