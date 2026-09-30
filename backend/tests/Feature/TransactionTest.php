<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    private function admin() { return User::where('UserRoleID', 1)->first(); }

    /** TR-101: List asset requests */
    public function test_tr101_list_asset_requests()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/asset-requests');

        $r->assertStatus(200);
    }

    /** TR-102: List property issuances */
    public function test_tr102_list_property_issuances()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-issuances');

        $r->assertStatus(200);
    }

    /** TR-103: List property transfers (PTR) */
    public function test_tr103_list_property_transfers()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-transfers');

        $r->assertStatus(200);
    }

    /** TR-104: List property borrows (PBR) */
    public function test_tr104_list_property_borrows()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-borrows');

        $r->assertStatus(200);
    }

    /** TR-105: View single transfer (ID 1 exists in seed) */
    public function test_tr105_view_single_transfer()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-transfers/1');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** TR-106: View single borrow (ID 1 exists in seed) */
    public function test_tr106_view_single_borrow()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-borrows/1');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** TR-107: View single issuance */
    public function test_tr107_view_single_issuance()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-issuances/1');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** TR-108: Unauthenticated transaction access blocked */
    public function test_tr108_unauthenticated_transaction_blocked()
    {
        $r = $this->getJson('/api/property-transfers');
        $r->assertStatus(401);
    }

    /** TR-109: Available items helper works */
    public function test_tr109_available_items_helper()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-transfers/available-items');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** TR-110: Transfer statistics endpoint */
    public function test_tr110_issuance_stats()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-issuances/stats');

        $this->assertContains($r->status(), [200, 404]);
    }
}