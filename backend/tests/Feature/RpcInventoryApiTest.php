<?php

namespace Tests\Feature;

use App\Models\RpcInventoryCycle;
use App\Models\RpcInventoryScan;
use App\Models\Auth\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RpcInventoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $gsoStaff;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a GSO staff user for testing
        $this->gsoStaff = User::factory()->create([
            'Email' => 'gso.staff@test.local',
            'FullName' => 'GSO Staff Test',
            'IsActive' => 1,
        ]);
        
        // Assign GSO Staff role (assuming RoleID 3 is GSO Staff)
        $this->gsoStaff->UserRoleID = 3;
        $this->gsoStaff->save();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Test that inventory cycles can be listed.
     */
    public function test_can_list_rpc_inventory_cycles(): void
    {
        $cycle = RpcInventoryCycle::create([
            'cycle_label' => 'Inventory Cycle 2026-2027',
            'start_year' => 2026,
            'end_year' => 2027,
            'status' => 'active',
            'started_at' => now(),
            'created_by_user_id' => $this->gsoStaff->UserID,
        ]);

        $response = $this->actingAs($this->gsoStaff)
            ->getJson('/api/rpc-inventory/cycles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'cycle_label',
                        'start_year',
                        'end_year',
                        'status',
                    ],
                ],
                'current_cycle' => [
                    'start_year',
                    'end_year',
                    'label',
                ],
            ]);
    }

    /**
     * Test creating a current cycle.
     */
    public function test_can_create_current_rpc_cycle(): void
    {
        Carbon::setTestNow(Carbon::create(now()->year, 9, 5, 12, 0, 0));
        $currentYear = now()->year;

        $response = $this->actingAs($this->gsoStaff)
            ->postJson('/api/rpc-inventory/cycles/current');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'cycle' => [
                    'id',
                    'cycle_label',
                    'start_year',
                    'end_year',
                ],
            ]);

        $this->assertDatabaseHas('rpc_inventory_cycles', [
            'start_year' => $currentYear,
            'end_year' => $currentYear,
            'status' => 'active',
        ]);
    }

    public function test_cannot_create_current_cycle_before_september_first(): void
    {
        Carbon::setTestNow(Carbon::create(now()->year, 8, 31, 23, 59, 59));

        $response = $this->actingAs($this->gsoStaff)
            ->postJson('/api/rpc-inventory/cycles/current');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'RPC inventory cycles may only be opened on or after September 1.',
            ]);
    }

    /**
     * Test that conditions helper returns correct format.
     */
    public function test_can_get_rpc_conditions(): void
    {
        $response = $this->actingAs($this->gsoStaff)
            ->getJson('/api/rpc-inventory/helpers/conditions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'ConditionID',
                        'ConditionName',
                    ],
                ],
            ]);
    }

    /**
     * Test preview scan returns 404 when property not found.
     */
    public function test_preview_scan_returns_404_when_property_not_found(): void
    {
        $cycle = RpcInventoryCycle::create([
            'cycle_label' => 'Inventory Cycle 2026-2027',
            'start_year' => 2026,
            'end_year' => 2027,
            'status' => 'active',
            'started_at' => now(),
            'created_by_user_id' => $this->gsoStaff->UserID,
        ]);

        $response = $this->actingAs($this->gsoStaff)
            ->postJson("/api/rpc-inventory/cycles/{$cycle->id}/scan-preview", [
                'property_id' => 99999,
            ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_aggregate_rpc_rows_prefers_custom_remarks_over_default_not_found(): void
    {
        $controller = new \App\Http\Controllers\RpcInventoryController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('aggregateRpcRows');
        $method->setAccessible(true);

        $rows = [
            [
                'property_type' => 'Office Equipment',
                'article' => 'Printer',
                'description' => 'Laser printer',
                'property_number' => 'PN-001',
                'unit_of_measure' => 'pcs',
                'unit_value' => 120.0,
                'quantity_per_card' => 1,
                'quantity_per_count' => 1,
                'shortage_overage_qty' => 0,
                'shortage_overage_value' => 0.0,
                'remarks' => 'Gwapo si Aaron',
                'accountable_officer' => 'John Doe',
                'date_purchased' => '2026-01-01',
                'condition' => 'Good',
                'is_scanned' => true,
            ],
            [
                'property_type' => 'Office Equipment',
                'article' => 'Printer',
                'description' => 'Laser printer',
                'property_number' => 'PN-002',
                'unit_of_measure' => 'pcs',
                'unit_value' => 120.0,
                'quantity_per_card' => 1,
                'quantity_per_count' => 0,
                'shortage_overage_qty' => -1,
                'shortage_overage_value' => -120.0,
                'remarks' => null,
                'accountable_officer' => 'John Doe',
                'date_purchased' => '2026-01-01',
                'condition' => null,
                'is_scanned' => false,
            ],
        ];

        $result = $method->invoke($controller, $rows);

        $this->assertCount(1, $result);
        $this->assertSame('Gwapo si Aaron', $result[0]['remarks']);
    }
}
