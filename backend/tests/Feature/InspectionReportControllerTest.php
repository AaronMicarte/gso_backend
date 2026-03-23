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
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use Illuminate\Http\UploadedFile;

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

        if (!Schema::hasTable('propertyinventory')) {
            Schema::create('propertyinventory', function (Blueprint $table) {
                $table->increments('InventoryID');
                $table->string('InventoryNumber')->nullable();
                $table->integer('DRID')->nullable();
                $table->integer('IARID')->nullable();
                $table->date('DateReceived')->nullable();
                $table->text('Remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('propertyinventorydetail')) {
            Schema::create('propertyinventorydetail', function (Blueprint $table) {
                $table->increments('InventoryDetailID');
                $table->integer('InventoryID');
                $table->integer('SourceDRDetailID')->nullable();
                $table->string('Description')->nullable();
                $table->integer('QuantityAvailable')->default(0);
                $table->integer('PropertyStatusID')->default(1);
                $table->timestamps();
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

        // ensure table for inspection acceptance report used by new feature
        if (!Schema::hasTable('inspectionacceptancereport')) {
            Schema::create('inspectionacceptancereport', function (Blueprint $table) {
                $table->increments('IARID');
                $table->string('IARNumber')->nullable();
                $table->integer('DRID')->nullable();
                $table->integer('InspectedBy')->nullable();
                $table->integer('AcceptedBy')->nullable();
                $table->date('DateInspected')->nullable();
                $table->date('DateAccepted')->nullable();
                $table->text('Remarks')->nullable();
                $table->timestamp('CreatedDate')->nullable();
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

    public function test_reports_pending_returns_delivery_receipts(): void
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

        $response = $this->actingAs($user)->getJson('/api/inspection/reports/pending');
        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));
    }

    public function test_saving_dr_auto_generates_iar(): void
    {
        $this->ensureBaseTables();

        // create simple user (role doesn't really matter for helper method)
        $user = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'admin.user',
            'Email' => 'admin2@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Admin User',
            'IsActive' => 1,
        ]);

        // make a delivery receipt record
        $dr = DeliveryReceipt::create([
            'DRNumber' => 'DR-AUTO-01',
            'SupplierName' => 'Test Supplier',
            'RequestedBy' => $user->UserID,
            'PONumber' => 'PO-AUTO',
            'DateReceived' => now()->format('Y-m-d'),
            'ReceivedBy' => $user->UserID,
            'InspectionStatus' => 'pending',
        ]);

        // call the helper directly
        $controller = app(DeliveryReceiptController::class);
        $controller->ensureIarForDeliveryReceipt($dr);

        $this->assertDatabaseHas('inspectionacceptancereport', [
            'DRID' => $dr->DRID,
        ]);
    }

    public function test_acknowledge_iar_creates_issuance_and_assigned_property(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'ack.user',
            'Email' => 'ack@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Ack User',
            'IsActive' => 1,
        ]);

        // create inspected DR with one detail and corresponding inventory
        $dr = DeliveryReceipt::create([
            'DRNumber' => 'DR-ACK-01',
            'SupplierName' => 'Supplier A',
            'RequestedBy' => $user->UserID,
            'PONumber' => 'PO-ACK',
            'DateReceived' => now()->format('Y-m-d'),
            'ReceivedBy' => $user->UserID,
            'InspectionStatus' => 'inspected',
        ]);

        $detail = DeliveryReceiptDetail::create([
            'DRID' => $dr->DRID,
            'ItemDescription' => 'Test Item',
            'QuantityDelivered' => 1,
            'UnitCost' => 100,
            'TotalCost' => 100,
            'InspectionStatus' => 'inspected',
        ]);

        $inventory = PropertyInventory::create([
            'InventoryNumber' => 'INV-ACK-1',
            'DRID' => $dr->DRID,
            'IARID' => null,
            'DateReceived' => now()->format('Y-m-d'),
            'Remarks' => 'test',
        ]);

        $invDetail = PropertyInventoryDetail::create([
            'InventoryID' => $inventory->InventoryID,
            'SourceDRDetailID' => $detail->DRDetailID,
            'Description' => 'Test Item',
            'QuantityAvailable' => 1,
            'PropertyStatusID' => 1,
        ]);

        $response = $this->actingAs($user)->postJson('/api/property-issuances/acknowledge-iar', ['dr_id' => $dr->DRID]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('propertyissuance', ['DRID' => $dr->DRID]);
        $this->assertDatabaseHas('assignedproperty', ['InventoryDetailID' => $invDetail->InventoryDetailID, 'DepartmentID' => $user->DepartmentID]);
    }

    public function test_department_head_sees_assigned_property_after_ack(): void
    {
        $this->ensureBaseTables();

        // create admin and dept head users in same department
        $admin = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'adm.user',
            'Email' => 'adm@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Admin User',
            'IsActive' => 1,
        ]);
        $deptHead = User::create([
            'UserRoleID' => 14, // Department Head role
            'DepartmentID' => 1,
            'Username' => 'dh.user',
            'Email' => 'dh@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Dept Head',
            'IsActive' => 1,
        ]);

        // create DR and inventory
        $dr = DeliveryReceipt::create([
            'DRNumber' => 'DR-DH-01',
            'SupplierName' => 'Supplier DH',
            'RequestedBy' => $admin->UserID,
            'PONumber' => 'PO-DH',
            'DateReceived' => now()->format('Y-m-d'),
            'ReceivedBy' => $admin->UserID,
            'InspectionStatus' => 'inspected',
        ]);
        $detail = DeliveryReceiptDetail::create([
            'DRID' => $dr->DRID,
            'ItemDescription' => 'Test Item',
            'QuantityDelivered' => 1,
            'UnitCost' => 100,
            'TotalCost' => 100,
            'InspectionStatus' => 'inspected',
        ]);
        $inventory = PropertyInventory::create([
            'InventoryNumber' => 'INV-DH-1',
            'DRID' => $dr->DRID,
            'IARID' => null,
            'DateReceived' => now()->format('Y-m-d'),
            'Remarks' => 'test',
        ]);
        $invDetail = PropertyInventoryDetail::create([
            'InventoryID' => $inventory->InventoryID,
            'SourceDRDetailID' => $detail->DRDetailID,
            'Description' => 'Test Item',
            'QuantityAvailable' => 1,
            'PropertyStatusID' => 1,
        ]);

        $this->actingAs($admin)->postJson('/api/property-issuances/acknowledge-iar', ['dr_id' => $dr->DRID]);

        // now login as department head and fetch qr properties
        $response = $this->actingAs($deptHead)->getJson('/api/qr/properties');
        $response->assertStatus(200);
        $this->assertStringContainsString('INV-DH-1', json_encode($response->json('data')));
    }

    public function test_api_create_delivery_receipt_generates_iar(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'api.user',
            'Email' => 'api@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'API User',
            'IsActive' => 1,
        ]);

        // prepare reference data
        DB::table('supplier')->insert(['SupplierID' => 1, 'SupplierName' => 'Test Supplier']);
        DB::table('article')->insert(['ArticleID' => 1, 'ArticleName' => 'Test Article', 'PropertyTypeID' => 1, 'DefaultUnitOfMeasureID' => 1]);
        DB::table('propertytype')->insert(['PropertyTypeID' => 1, 'PropertyName' => 'Type1', 'PropertyClassificationID' => 1]);
        DB::table('unitofmeasure')->insert(['UnitOfMeasureID' => 1, 'UnitName' => 'pcs']);

        $payload = [
            'dr_number' => 'DR-API-01',
            'supplier_id' => 1,
            'requested_by_user_id' => $user->UserID,
            'pr_number' => 'PR-API',
            'po_number' => 'PO-API',
            'invoice_number' => 'INV-API',
            'date_received' => now()->format('Y-m-d'),
            'remarks' => '',
            'dr_picture' => UploadedFile::fake()->image('dr.jpg'),
            'details' => [
                [
                    'article_id' => 1,
                    'item_description' => 'API Item',
                    'brand' => '',
                    'model_number' => '',
                    'serial_number' => '',
                    'property_type_id' => 1,
                    'quantity_delivered' => 1,
                    'quantity_requested' => 1,
                    'change_reason' => '',
                    'unit_of_measure_id' => 1,
                    'unit_cost' => 100,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/admin/procurement/delivery-receipts', $payload);
        $response->assertStatus(200);

        $latestDrId = DB::table('deliveryreceipt')->max('DRID');
        $this->assertDatabaseHas('inspectionacceptancereport', ['DRID' => $latestDrId]);
    }

    public function test_validation_disallows_future_date_received(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'future.user',
            'Email' => 'future@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Future User',
            'IsActive' => 1,
        ]);

        DB::table('supplier')->insert(['SupplierID' => 1, 'SupplierName' => 'Test Supplier']);
        DB::table('article')->insert(['ArticleID' => 1, 'ArticleName' => 'Test Article', 'PropertyTypeID' => 1, 'DefaultUnitOfMeasureID' => 1]);
        DB::table('propertytype')->insert(['PropertyTypeID' => 1, 'PropertyName' => 'Type1', 'PropertyClassificationID' => 1]);
        DB::table('unitofmeasure')->insert(['UnitOfMeasureID' => 1, 'UnitName' => 'pcs']);

        $payload = [
            'dr_number' => 'DR-FUTURE-01',
            'supplier_id' => 1,
            'requested_by_user_id' => $user->UserID,
            'pr_number' => 'PR-FUTURE',
            'po_number' => 'PO-FUTURE',
            'invoice_number' => 'INV-FUTURE',
            'date_received' => now()->addDay()->format('Y-m-d'),
            'remarks' => '',
            'dr_picture' => UploadedFile::fake()->image('dr.jpg'),
            'details' => [[
                'article_id' => 1,
                'item_description' => 'API Item',
                'brand' => '',
                'model_number' => '',
                'serial_number' => '',
                'property_type_id' => 1,
                'quantity_delivered' => 1,
                'quantity_requested' => 1,
                'change_reason' => '',
                'unit_of_measure_id' => 1,
                'unit_cost' => 100,
            ]],
        ];

        $response = $this->actingAs($user)->postJson('/api/admin/procurement/delivery-receipts', $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date_received']);
    }

    public function test_validation_disallows_date_received_more_than_one_year_old(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'past.user',
            'Email' => 'past@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Past User',
            'IsActive' => 1,
        ]);

        DB::table('supplier')->insert(['SupplierID' => 1, 'SupplierName' => 'Test Supplier']);
        DB::table('article')->insert(['ArticleID' => 1, 'ArticleName' => 'Test Article', 'PropertyTypeID' => 1, 'DefaultUnitOfMeasureID' => 1]);
        DB::table('propertytype')->insert(['PropertyTypeID' => 1, 'PropertyName' => 'Type1', 'PropertyClassificationID' => 1]);
        DB::table('unitofmeasure')->insert(['UnitOfMeasureID' => 1, 'UnitName' => 'pcs']);

        $payload = [
            'dr_number' => 'DR-PAST-01',
            'supplier_id' => 1,
            'requested_by_user_id' => $user->UserID,
            'pr_number' => 'PR-PAST',
            'po_number' => 'PO-PAST',
            'invoice_number' => 'INV-PAST',
            'date_received' => now()->subYear()->subDay()->format('Y-m-d'),
            'remarks' => '',
            'dr_picture' => UploadedFile::fake()->image('dr.jpg'),
            'details' => [[
                'article_id' => 1,
                'item_description' => 'API Item',
                'brand' => '',
                'model_number' => '',
                'serial_number' => '',
                'property_type_id' => 1,
                'quantity_delivered' => 1,
                'quantity_requested' => 1,
                'change_reason' => '',
                'unit_of_measure_id' => 1,
                'unit_cost' => 100,
            ]],
        ];

        $response = $this->actingAs($user)->postJson('/api/admin/procurement/delivery-receipts', $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date_received']);
    }

    public function test_validation_disallows_serial_with_multiple_quantity(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 1,
            'DepartmentID' => 1,
            'Username' => 'val.user',
            'Email' => 'val@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Validation User',
            'IsActive' => 1,
        ]);

        // same reference data as before
        DB::table('supplier')->insert(['SupplierID' => 1, 'SupplierName' => 'Test Supplier']);
        DB::table('article')->insert(['ArticleID' => 1, 'ArticleName' => 'Test Article', 'PropertyTypeID' => 1, 'DefaultUnitOfMeasureID' => 1]);
        DB::table('propertytype')->insert(['PropertyTypeID' => 1, 'PropertyName' => 'Type1', 'PropertyClassificationID' => 1]);
        DB::table('unitofmeasure')->insert(['UnitOfMeasureID' => 1, 'UnitName' => 'pcs']);

        $payload = [
            'dr_number' => 'DR-VAL-01',
            'supplier_id' => 1,
            'requested_by_user_id' => $user->UserID,
            'pr_number' => 'PR-VAL',
            'po_number' => 'PO-VAL',
            'invoice_number' => 'INV-VAL',
            'date_received' => now()->format('Y-m-d'),
            'remarks' => '',
            'dr_picture' => UploadedFile::fake()->image('dr.jpg'),
            'details' => [
                [
                    'article_id' => 1,
                    'item_description' => 'API Item',
                    'brand' => '',
                    'model_number' => '',
                    'serial_number' => 'SN01',
                    'property_type_id' => 1,
                    'quantity_delivered' => 2,
                    'quantity_requested' => 2,
                    'change_reason' => '',
                    'unit_of_measure_id' => 1,
                    'unit_cost' => 100,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/admin/procurement/delivery-receipts', $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['details.0.quantity_delivered', 'details.0.quantity_requested']);
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

    public function test_inspection_reports_endpoint_filters_by_status_and_query(): void
    {
        $this->ensureBaseTables();

        $user = User::create([
            'UserRoleID' => 11,
            'DepartmentID' => 1,
            'Username' => 'inspector.filter',
            'Email' => 'inspector.filter@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Inspector Filter',
            'IsActive' => 1,
        ]);

        // create two reports with different statuses
        $pending = DB::table('inspection_reports')->insertGetId([
            'DRID' => 1,
            'InspectedBy' => $user->UserID,
            'InspectorName' => $user->FullName,
            'DateInspected' => now()->format('Y-m-d'),
            'Status' => 'pending',
            'CreatedDate' => now(),
        ]);

        $inspected = DB::table('inspection_reports')->insertGetId([
            'DRID' => 2,
            'InspectedBy' => $user->UserID,
            'InspectorName' => $user->FullName,
            'DateInspected' => now()->format('Y-m-d'),
            'Status' => 'inspected',
            'CreatedDate' => now(),
        ]);

        // query pending only
        $response = $this->actingAs($user)->getJson('/api/inspection/reports?status=pending');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('pending', $response->json('data.data.0.Status'));

        // query inspected only
        $response = $this->actingAs($user)->getJson('/api/inspection/reports?status=inspected');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('inspected', $response->json('data.data.0.Status'));

        // query using generic query param that matches DRID should return whichever
        $response = $this->actingAs($user)->getJson('/api/inspection/reports?query=2');
        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data.data')));
    }
}
