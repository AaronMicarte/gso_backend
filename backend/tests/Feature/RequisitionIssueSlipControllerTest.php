<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;
use App\Models\Auth\UserRole;
use App\Models\AssetRequest;
use App\Models\AssetRequestDetail;
use Illuminate\Support\Facades\DB;

class RequisitionIssueSlipControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_prepare_ris_includes_unapproved_items_and_uses_admin_notes()
    {
        // Create Admin role and user
        $role = UserRole::create(["RoleName" => "Admin", "Description" => "Administrator"]);

        $user = User::create([
            'UserRoleID' => $role->UserRoleID,
            'DepartmentID' => 1,
            'Username' => 'ris.admin',
            'Email' => 'ris.admin@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'RIS Admin',
            'IsActive' => 1,
        ]);

        $request = AssetRequest::create([
            'RequestNumber' => 'REQ-RIS-1',
            'RequestedBy' => $user->UserID,
            'DepartmentID' => 1,
            'Purpose' => 'Test RIS behaviour',
            'Status' => 'approved',
            'RequestDate' => now()->format('Y-m-d'),
        ]);

        // Approved detail
        $approved = AssetRequestDetail::create([
            'RequestID' => $request->RequestID,
            'ItemDescription' => 'Approved Item',
            'Unit' => 'pcs',
            'QuantityRequested' => 1,
            'IsApprovedForIssuance' => true,
            'AdminNotes' => 'Available for issuance',
        ]);

        // Unapproved detail
        $unapproved = AssetRequestDetail::create([
            'RequestID' => $request->RequestID,
            'ItemDescription' => 'Unapproved Item',
            'Unit' => 'pcs',
            'QuantityRequested' => 1,
            'IsApprovedForIssuance' => false,
            'AdminNotes' => 'Not available',
        ]);

        // Provide required signatures (requester + approver)
        DB::table('asset_request_signatures')->insert([
            [
                'request_id' => $request->RequestID,
                'role' => 'requester',
                'signed_by_user_id' => $user->UserID,
                'source' => 'canvas',
                'storage_disk' => 'public',
                'storage_path' => '',
                'url' => '',
                'mime' => 'image/png',
                'width' => 1000,
                'height' => 300,
                'size_bytes' => 0,
                'sha256_hash' => 'hash',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'phpunit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'request_id' => $request->RequestID,
                'role' => 'approver',
                'signed_by_user_id' => $user->UserID,
                'source' => 'canvas',
                'storage_disk' => 'public',
                'storage_path' => '',
                'url' => '',
                'mime' => 'image/png',
                'width' => 1000,
                'height' => 300,
                'size_bytes' => 0,
                'sha256_hash' => 'hash',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'phpunit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $payload = [
            'asset_request_id' => $request->RequestID,
            'ris_date' => now()->format('Y-m-d'),
            'entity_name' => 'Unit Test Entity',
            'fund_cluster' => 'FC-1',
            'division' => 'Unit Test Division',
            'responsibility_center_code' => 'RCC-1',
            'remarks' => 'Prepared by test',
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/requisition-issue-slips', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('requisition_issue_slip_items', [
            'asset_request_detail_id' => $approved->RequestDetailID,
            'stock_available' => 1,
            'issue_remarks' => 'Available for issuance',
        ]);

        $this->assertDatabaseHas('requisition_issue_slip_items', [
            'asset_request_detail_id' => $unapproved->RequestDetailID,
            'stock_available' => 0,
            'issue_remarks' => 'Not available',
        ]);
    }
}
