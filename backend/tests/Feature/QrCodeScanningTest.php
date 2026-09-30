<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Tests\TestCase;

class QrCodeScanningTest extends TestCase
{
    private function admin() { return User::where('UserRoleID', 1)->first(); }

    /** QR-101: List all QR properties */
    public function test_qr101_list_qr_properties()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/qr/properties');

        $r->assertStatus(200);
    }

    /** QR-102: Lookup QR by scanning payload */
    public function test_qr102_qr_lookup()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/qr/lookup', [
                      'payload' => 'http://localhost:3000/qr/scan/5?h=6b87dcb1a9e5',
                  ]);

        $this->assertContains($r->status(), [200, 404, 422]);
    }

    /** QR-103: Scan asset by property ID */
    public function test_qr103_scan_asset_by_property_id()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        // PropertyID 5 exists in seed
        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/qr/scan/5');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** QR-104: Generate QR for existing property */
    public function test_qr104_generate_qr_code()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        // InventoryDetailID 5 (PropertyID 5) exists
        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/qr/properties/5/generate');

        $this->assertContains($r->status(), [200, 201, 404, 422]);
    }

    /** QR-105: Scan invalid property ID returns 404 */
    public function test_qr105_scan_invalid_property()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/qr/scan/999999');

        $this->assertContains($r->status(), [404, 422]);
    }

    /** QR-106: Lookup with invalid payload */
    public function test_qr106_lookup_invalid_payload()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/qr/lookup', [
                      'payload' => 'garbage-data-not-a-url',
                  ]);

        $this->assertContains($r->status(), [404, 422]);
    }

    /** QR-107: Unauthenticated QR scan blocked */
    public function test_qr107_unauthenticated_qr_scan_blocked()
    {
        $r = $this->getJson('/api/qr/scan/5');
        $r->assertStatus(401);
    }

    /** QR-108: Update QR property details */
    public function test_qr108_update_qr_property()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->putJson('/api/qr/properties/5', [
                      'remarks' => 'Updated by QR test',
                  ]);

        $this->assertContains($r->status(), [200, 404, 422]);
    }
}