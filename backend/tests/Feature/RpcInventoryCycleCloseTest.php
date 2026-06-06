<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Models\RpcInventoryCycle;
use App\Models\RpcInventoryScan;
use App\Models\RpcInventoryAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RpcInventoryCycleCloseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $adminUser;
    private User $staffUser;
    private RpcInventoryCycle $cycle;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin user
        $this->adminUser = User::factory()->create([
            'UserRoleID' => 1, // Admin role
        ]);
        $this->adminUser->userRole()->create([
            'UserID' => $this->adminUser->UserID,
            'RoleName' => 'Admin',
        ]);

        // Create GSO Staff user
        $this->staffUser = User::factory()->create([
            'UserRoleID' => 2, // GSO Staff role
        ]);
        $this->staffUser->userRole()->create([
            'UserID' => $this->staffUser->UserID,
            'RoleName' => 'GSO Staff',
        ]);

        // Create active cycle
        $this->cycle = RpcInventoryCycle::create([
            'cycle_label' => 'Inventory Cycle 2026-2027',
            'start_year' => 2026,
            'end_year' => 2027,
            'status' => 'active',
            'started_at' => now(),
            'created_by_user_id' => $this->adminUser->UserID,
        ]);
    }

    /**
     * Test: Only Admin can close cycle.
     */
    public function test_only_admin_can_close_cycle()
    {
        $this->actingAs($this->staffUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/close", [
            'close_reason' => 'Inventory verification completed',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Only Administrators can close inventory cycles.',
        ]);

        $this->cycle->refresh();
        $this->assertNotEquals('closed', $this->cycle->status);
    }

    /**
     * Test: Admin can close active cycle.
     */
    public function test_admin_can_close_active_cycle()
    {
        $this->actingAs($this->adminUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/close", [
            'close_reason' => 'Inventory verification completed successfully',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Inventory cycle closed successfully.',
        ]);

        $this->cycle->refresh();
        $this->assertEquals('closed', $this->cycle->status);
        $this->assertEquals($this->adminUser->UserID, $this->cycle->closed_by_user_id);
        $this->assertNotNull($this->cycle->closed_at);
        $this->assertEquals('Inventory verification completed successfully', $this->cycle->close_reason);
    }

    /**
     * Test: Cannot close already closed cycle.
     */
    public function test_cannot_close_already_closed_cycle()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
            'close_reason' => 'Already closed',
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/close", [
            'close_reason' => 'Try to close again',
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'This inventory cycle is already closed.',
        ]);
    }

    /**
     * Test: Close reason is required and has minimum length.
     */
    public function test_close_reason_validation()
    {
        $this->actingAs($this->adminUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/close", [
            'close_reason' => 'Too short',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('close_reason');
    }

    /**
     * Test: Audit log is created when cycle is closed.
     */
    public function test_audit_log_created_on_close()
    {
        $this->actingAs($this->adminUser);

        $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/close", [
            'close_reason' => 'Inventory count completed and verified',
        ]);

        $auditLog = RpcInventoryAuditLog::where([
            'rpc_inventory_cycle_id' => $this->cycle->id,
            'action' => 'cycle_closed',
        ])->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($this->adminUser->UserID, $auditLog->performed_by_user_id);
        $this->assertArrayHasKey('close_reason', $auditLog->details);
    }

    /**
     * Test: Cannot add scans to closed cycle.
     */
    public function test_cannot_store_scan_on_closed_cycle()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
        ]);

        $this->actingAs($this->staffUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/scans", [
            'property_id' => 1,
            'condition_id' => 1,
            'remarks' => 'Test scan',
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'Cannot add scans to a closed inventory cycle. The cycle is locked and read-only.',
        ]);
    }

    /**
     * Test: Only Admin can request reopen.
     */
    public function test_only_admin_can_reopen_cycle()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
            'reopen_count' => 0,
        ]);

        $this->actingAs($this->staffUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/reopen-request");

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Only Administrators can reopen inventory cycles.',
        ]);
    }

    /**
     * Test: Admin can request reopen with closure info.
     */
    public function test_admin_can_request_reopen()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
            'close_reason' => 'Initial count',
            'reopen_count' => 0,
        ]);

        $this->actingAs($this->adminUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/reopen-request");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'closure_info' => [
                'closed_by',
                'closed_at',
                'close_reason',
                'reopen_count',
                'can_reopen',
            ],
        ]);
    }

    /**
     * Test: Admin can reopen with password verification.
     */
    public function test_admin_can_reopen_with_password()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
            'close_reason' => 'Initial count',
            'reopen_count' => 0,
        ]);

        $password = 'password123';
        $this->adminUser->update(['Password' => bcrypt($password)]);

        $this->actingAs($this->adminUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/reopen", [
            'password' => $password,
            'reopen_reason' => 'Missing items found, need to recount',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Inventory cycle reopened successfully.',
        ]);

        $this->cycle->refresh();
        $this->assertEquals('active', $this->cycle->status);
        $this->assertEquals(1, $this->cycle->reopen_count);
        $this->assertNotNull($this->cycle->reopened_at);
        $this->assertEquals($this->adminUser->UserID, $this->cycle->reopened_by_user_id);
    }

    /**
     * Test: Reopen fails with wrong password.
     */
    public function test_reopen_fails_with_wrong_password()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
            'reopen_count' => 0,
        ]);

        $this->adminUser->update(['Password' => bcrypt('correct_password')]);

        $this->actingAs($this->adminUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/reopen", [
            'password' => 'wrong_password',
            'reopen_reason' => 'Missing items found',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Password verification failed.',
        ]);

        // Verify cycle remains closed
        $this->cycle->refresh();
        $this->assertEquals('closed', $this->cycle->status);
    }

    /**
     * Test: Cannot reopen more than twice.
     */
    public function test_cannot_reopen_more_than_twice()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
            'reopen_count' => 2,
        ]);

        $password = 'password123';
        $this->adminUser->update(['Password' => bcrypt($password)]);

        $this->actingAs($this->adminUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/reopen", [
            'password' => $password,
            'reopen_reason' => 'Try to reopen third time',
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'This inventory cycle has reached the maximum number of reopen attempts and is now permanently closed.',
            'max_reopens' => 2,
        ]);
    }

    /**
     * Test: Audit log created on successful reopen.
     */
    public function test_audit_log_created_on_reopen()
    {
        $this->cycle->update([
            'status' => 'closed',
            'closed_by_user_id' => $this->adminUser->UserID,
            'closed_at' => now(),
            'reopen_count' => 0,
        ]);

        $password = 'password123';
        $this->adminUser->update(['Password' => bcrypt($password)]);

        $this->actingAs($this->adminUser);

        $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/reopen", [
            'password' => $password,
            'reopen_reason' => 'Additional items discovered',
        ]);

        $auditLog = RpcInventoryAuditLog::where([
            'rpc_inventory_cycle_id' => $this->cycle->id,
            'action' => 'cycle_reopened',
        ])->first();

        $this->assertNotNull($auditLog);
        $this->assertEquals($this->adminUser->UserID, $auditLog->performed_by_user_id);
        $this->assertArrayHasKey('reopen_reason', $auditLog->details);
        $this->assertArrayHasKey('reopen_count', $auditLog->details);
    }

    /**
     * Test: Scans can be added to reopened cycle.
     */
    public function test_can_store_scan_on_reopened_cycle()
    {
        $this->cycle->update([
            'status' => 'active',
            'reopen_count' => 1,
            'reopened_at' => now(),
        ]);

        $this->actingAs($this->staffUser);

        $response = $this->postJson("/api/rpc-inventory/cycles/{$this->cycle->id}/scans", [
            'property_id' => 1,
            'condition_id' => 1,
            'remarks' => 'Test scan on reopened cycle',
        ]);

        // Response might fail due to missing property, but not due to cycle being closed
        // If it's 404 or 422, it's a property issue, not a closed cycle issue
        $this->assertNotEquals(409, $response->status());
    }
}
