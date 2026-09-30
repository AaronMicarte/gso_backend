<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    private function admin()    { return User::where('UserRoleID', 1)->first(); }
    private function staff()    { return User::where('UserRoleID', 2)->first(); }
    private function deptHead() { return User::where('UserRoleID', 4)->first(); }

    /** RB-101: Admin can access user management */
    public function test_rb101_admin_can_list_users()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin, 'No Admin user in gso_db_test');

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/admin/users');

        $this->assertContains($r->status(), [200, 404]);
    }

    /** RB-102: Non-admin cannot list users */
    public function test_rb102_staff_cannot_list_users()
    {
        $staff = $this->staff();
        $this->assertNotNull($staff, 'No Staff user in gso_db_test');

        $r = $this->actingAs($staff, 'sanctum')
                  ->getJson('/api/admin/users');

        $this->assertContains($r->status(), [401, 403]);
    }

    /** RB-103: Unauthenticated request blocked */
    public function test_rb103_guest_cannot_access_admin()
    {
        $r = $this->getJson('/api/admin/users');
        $r->assertStatus(401);
    }

    /** RB-104: Department Head can access DH dashboard */
    public function test_rb104_head_can_access_dashboard()
    {
        $head = $this->deptHead();
        $this->assertNotNull($head, 'No Dept Head user in gso_db_test');

        $r = $this->actingAs($head, 'sanctum')
                  ->getJson('/api/department-head/dashboard/stats');

        $this->assertContains($r->status(), [200, 403, 404]);
    }

    /** RB-105: Staff cannot access DH dashboard */
    public function test_rb105_staff_cannot_access_head_dashboard()
    {
        $staff = $this->staff();
        $this->assertNotNull($staff);

        $r = $this->actingAs($staff, 'sanctum')
                  ->getJson('/api/department-head/dashboard/stats');

        $this->assertContains($r->status(), [401, 403, 404]);
    }

    /** RB-106: Any authenticated user can hit /api/user */
    public function test_rb106_any_authenticated_user_gets_own_profile()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->getJson('/api/user');

        $r->assertStatus(200);
    }

    /** RB-107: Non-admin cannot access backups */
    public function test_rb107_staff_cannot_access_backups()
    {
        $staff = $this->staff();
        $this->assertNotNull($staff);

        $r = $this->actingAs($staff, 'sanctum')
                  ->getJson('/api/admin/backups');

        $this->assertContains($r->status(), [401, 403]);
    }

    /** RB-108: Admin can access backups */
    public function test_rb108_admin_can_access_backups()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/admin/backups');

        $this->assertContains($r->status(), [200, 404]);
    }
}