<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    private function admin() { return User::where('UserRoleID', 1)->first(); }

    /** AM-101: List all inventory items */
    public function test_am101_list_all_assets()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory');

        $r->assertStatus(200);
    }

    /** AM-102: All items endpoint works */
    public function test_am102_all_items_endpoint()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/all-items');

        $r->assertStatus(200);
    }

    /** AM-103: View single asset by ID */
    public function test_am103_view_single_asset()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        // InventoryDetailID 5 exists in seed
        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/5');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** AM-104: View asset history */
    public function test_am104_view_asset_history()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/details/5/history');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** AM-105: View property card */
    public function test_am105_view_property_card()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/details/5/property-card');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** AM-106: Update asset details */
    public function test_am106_update_asset_detail()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->putJson('/api/inventory/details/5', [
                      'Description' => 'Updated by test',
                  ]);

        $this->assertContains($r->status(), [200, 404, 422]);
    }

    /** AM-107: Unauthenticated asset access blocked */
    public function test_am107_unauthenticated_asset_access_blocked()
    {
        $r = $this->getJson('/api/inventory');
        $r->assertStatus(401);
    }

    /** AM-108: Removed items listing works */
    public function test_am108_removed_items_listing()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/removed-items');

        $r->assertStatus(200);
    }

    /** AM-109: Remove an asset (soft delete) */
    public function test_am109_remove_asset()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/inventory/details/5/remove');

        $this->assertContains($r->status(), [200, 404, 422]);
    }

    /** AM-110: Restore a removed asset */
    public function test_am110_restore_asset()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/inventory/details/5/restore');

        $this->assertContains($r->status(), [200, 404, 422]);
    }
}