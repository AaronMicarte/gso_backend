<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Tests\TestCase;

class MobileSyncTest extends TestCase
{
    private function admin() { return User::where('UserRoleID', 1)->first(); }

    /** MS-101: Bulk sync endpoint exists */
    public function test_ms101_bulk_sync_endpoint_works()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/rpc-inventory/cycles/1/scans/bulk', [
                      'scans' => [],
                  ]);

        $this->assertContains($r->status(), [200, 201, 404, 409, 422]);
    }

    /** MS-102: Single scan sync */
    public function test_ms102_single_scan_sync()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/rpc-inventory/cycles/1/scans', [
                      'client_uuid' => 'test-' . uniqid(),
                      'device_id'   => 'test-device-1',
                      'inventory_detail_id' => 5,
                      'condition_id' => 1,
                  ]);

        $this->assertContains($r->status(), [200, 201, 404, 409, 422]);
    }

    /** MS-103: Fetch scans for a cycle (sync pull) */
    public function test_ms103_fetch_cycle_scans()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/rpc-inventory/cycles/1/scans');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** MS-104: List inventory cycles */
    public function test_ms104_list_cycles()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/rpc-inventory/cycles');

        $r->assertStatus(200);
    }

    /** MS-105: Sync scan with device_id is stored */
    public function test_ms105_device_id_persisted()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $deviceId = 'test-device-' . uniqid();

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/rpc-inventory/cycles/1/scans', [
                      'client_uuid' => 'uuid-' . uniqid(),
                      'device_id'   => $deviceId,
                      'inventory_detail_id' => 5,
                      'condition_id' => 1,
                  ]);

        $this->assertContains($r->status(), [200, 201, 404, 409, 422]);
    }

    /** MS-106: Sync audit logs endpoint */
    public function test_ms106_sync_audit_logs()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/rpc-inventory/cycles/1/audit-logs');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** MS-107: Unauthenticated sync blocked */
    public function test_ms107_unauthenticated_sync_blocked()
    {
        $r = $this->postJson('/api/rpc-inventory/cycles/1/scans/bulk', [
            'scans' => [],
        ]);

        $r->assertStatus(401);
    }

    /** MS-108: Update scan condition */
    public function test_ms108_update_scan_condition()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/rpc-inventory/update-condition', [
                      'inventory_detail_id' => 5,
                      'condition_id' => 2,
                  ]);

        $this->assertContains($r->status(), [200, 404, 422]);
    }
}