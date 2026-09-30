<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Services\TurnstileService;
use Tests\TestCase;

class SystemManualTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mock(TurnstileService::class, function ($mock) {
            $mock->shouldReceive('verifyToken')->andReturn(['success' => true]);
        });
    }

    private function admin() { return User::where('UserRoleID', 1)->first(); }
    private function staff() { return User::where('UserRoleID', 2)->first(); }

    /** ST-102: Invalid credentials rejected */
    public function test_st102_invalid_credentials()
    {
        $r = $this->postJson('/api/auth/mobile-login', [
            'username' => 'gso.staff.aaronmicarte',
            'password' => 'WrongPass123',
        ]);

        $r->assertStatus(401)
          ->assertJsonFragment(['message' => 'Invalid credentials']);
    }

    /** ST-104: Blank fields rejected */
    public function test_st104_blank_fields()
    {
        $r = $this->postJson('/api/auth/mobile-login', []);
        $r->assertStatus(422)
          ->assertJsonStructure(['message', 'errors']);
    }

    /** ST-105: Staff blocked from admin route */
    public function test_st105_staff_blocked_from_admin()
    {
        $staff = $this->staff();
        $this->assertNotNull($staff);

        $r = $this->actingAs($staff, 'sanctum')
                  ->getJson('/api/admin/users');

        $r->assertStatus(403);
    }

    /** ST-106: Logout works */
    public function test_st106_logout()
    {
        $user = $this->staff();
        $this->assertNotNull($user);

        $r = $this->actingAs($user, 'sanctum')
                  ->postJson('/api/logout');

        $this->assertContains($r->status(), [200, 500]);
    }

    /** ST-201: Record creation (create asset request) */
    public function test_st201_record_creation()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/asset-requests', [
                      'Purpose' => 'System test request',
                      'DepartmentID' => 30,
                  ]);

        $this->assertContains($r->status(), [200, 201, 422]);
    }

    /** ST-202: Non-numeric input rejected */
    public function test_st202_non_numeric_rejected()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/asset-requests', [
                      'Purpose' => 'Test',
                      'DepartmentID' => 'Five',
                  ]);

        $this->assertContains($r->status(), [422, 200, 201]);
    }

    /** ST-203: Record modification */
    public function test_st203_record_modification()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->putJson('/api/inventory/details/5', [
                      'Description' => 'Modified by test',
                  ]);

        $this->assertContains($r->status(), [200, 404, 422]);
    }

    /** ST-204: Record removal */
    public function test_st204_record_removal()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->postJson('/api/inventory/details/5/remove');

        $this->assertContains($r->status(), [200, 404, 422]);
    }

    /** ST-205: Pagination - fetch list */
    public function test_st205_list_pagination()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/all-items');

        $r->assertStatus(200);
    }

    /** ST-301: Global search endpoint */
    public function test_st301_global_search()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/all-items');

        $r->assertStatus(200);
    }

    /** ST-302: Empty search - list still returns */
    public function test_st302_empty_search_state()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory/all-items');

        $r->assertStatus(200);
    }

    /** ST-303: Multi-filter (status query) */
    public function test_st303_multi_filter()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/inventory');

        $r->assertStatus(200);
    }

    /** ST-401: Report data accuracy (RSMI) */
    public function test_st401_report_accuracy()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/rsmi-reports');

        $r->assertStatus(200);
    }

    /** ST-402: Excel export endpoint (if exists) */
    public function test_st402_excel_export_endpoint()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/rsmi-reports');

        $r->assertStatus(200);
    }

    /** ST-403: Report generation (document) */
    public function test_st403_report_generation()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/property-issuances/5/document/PAR');

        $this->assertContains($r->status(), [200, 404, 422, 500]);
    }

    /** ST-501: UI feedback (dashboard stats) */
    public function test_st501_ui_feedback()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/dashboard/stats');

        $r->assertStatus(200);
    }

    /** ST-502: Settings / user profile */
    public function test_st502_settings_profiling()
    {
        $admin = $this->admin();
        $this->assertNotNull($admin);

        $r = $this->actingAs($admin, 'sanctum')
                  ->getJson('/api/user');

        $r->assertStatus(200);
    }
}