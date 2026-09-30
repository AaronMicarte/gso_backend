<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    private function admin() { return User::where('UserRoleID', 1)->first(); }

    /** IN-101: List inventory items */
    public function test_in101_list_inventory()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory');

        $r->assertStatus(200);
    }

    /** IN-102: All items endpoint */
    public function test_in102_all_items()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/all-items');

        $r->assertStatus(200);
    }

    /** IN-103: View single inventory detail */
    public function test_in103_view_single_detail()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/5');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** IN-104: Inventory detail history */
    public function test_in104_detail_history()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/details/5/history');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** IN-105: Property card */
    public function test_in105_property_card()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/details/5/property-card');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** IN-106: Update inventory detail */
    public function test_in106_update_detail()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->putJson('/api/inventory/details/5', [
                      'Description' => 'Updated by inventory test',
                  ]);

        $this->assertContains($r->status(), [200, 404, 422]);
    }

    /** IN-107: Removed items listing */
    public function test_in107_removed_items()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/removed-items');

        $r->assertStatus(200);
    }

    /** IN-108: Unauthenticated access blocked */
    public function test_in108_unauthenticated_blocked()
    {
        $r = $this->getJson('/api/inventory');
        $r->assertStatus(401);
    }

    /** IN-109: Pending ingestion list */
    public function test_in109_pending_ingestion()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/admin/inventory/pending-ingestion');

        $this->assertContains($r->status(), [200, 403, 404]);
    }

    /** IN-110: Unserviceable items listing */
    public function test_in110_unserviceable_items()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-unserviceable');

        $r->assertStatus(200);
    }
}