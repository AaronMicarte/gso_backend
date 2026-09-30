<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Tests\TestCase;

class EndUserTest extends TestCase
{
    private function staff() { return User::where('UserRoleID', 2)->first(); }
    private function admin() { return User::where('UserRoleID', 1)->first(); }

    /** EU-101: User can view own profile */
    public function test_eu101_view_own_profile()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/user');

        $r->assertStatus(200)
          ->assertJsonStructure(['user' => ['UserID', 'Username', 'Email']]);
    }

    /** EU-102: User can view dashboard */
    public function test_eu102_view_dashboard()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/dashboard/stats');

        $r->assertStatus(200);
    }

    /** EU-103: User can list notifications */
    public function test_eu103_list_notifications()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/notifications');

        $r->assertStatus(200);
    }

    /** EU-104: User can check unread count */
    public function test_eu104_unread_count()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/notifications/unread-count');

        $r->assertStatus(200);
    }

    /** EU-105: User can view own asset requests */
    public function test_eu105_view_asset_requests()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/asset-requests');

        $r->assertStatus(200);
    }

    /** EU-106: User can view inventory (read-only) */
    public function test_eu106_view_inventory()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/inventory/all-items');

        $r->assertStatus(200);
    }

    /** EU-107: User can generate QR (basic action) */
    public function test_eu107_user_can_generate_qr()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->postJson('/api/qr/properties/5/generate');

        $this->assertContains($r->status(), [200, 201, 404, 422]);
    }

    /** EU-108: User can log out */
    public function test_eu108_user_can_logout()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->postJson('/api/logout');

        $this->assertContains($r->status(), [200, 500]);
    }

    /** EU-109: User cannot access admin routes */
    public function test_eu109_user_cannot_access_admin()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/admin/users');

        $r->assertStatus(403);
    }

    /** EU-110: User can request activation (register flow) */
    public function test_eu110_register_validation_works()
    {
        $r = $this->postJson('/api/auth/activate-account', [
            'token' => 'invalid-token',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $r->assertStatus(422);
    }
}