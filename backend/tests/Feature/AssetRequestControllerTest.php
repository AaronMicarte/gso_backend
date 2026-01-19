<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Auth\User;
use App\Models\AssetRequest;
use App\Models\AssetRequestDetail;

class AssetRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_review_allows_missing_property_classification_for_unapproved_items()
    {
        // create user
        $user = User::create([
            'UserRoleID' => 4, // Property Officer
            'DepartmentID' => 1,
            'Username' => 'review.user',
            'Email' => 'review.user@example.com',
            'Password' => bcrypt('password'),
            'FullName' => 'Review User',
            'IsActive' => 1,
        ]);

        $request = AssetRequest::create([
            'RequestNumber' => 'REQ-REV-1',
            'RequestedBy' => $user->UserID,
            'DepartmentID' => 1,
            'Purpose' => 'Test review behaviour',
            'Status' => 'pending',
            'RequestDate' => now()->format('Y-m-d'),
        ]);

        $detail = AssetRequestDetail::create([
            'RequestID' => $request->RequestID,
            'ItemDescription' => 'Sample item',
            'Unit' => 'pcs',
            'QuantityRequested' => 1,
            'PropertyClassification' => 'ICS',
        ]);

        $payload = [
            'ReviewNotes' => 'Saving review with unapproved item',
            'details' => [
                [
                    'RequestDetailID' => $detail->RequestDetailID,
                    // intentionally OMIT PropertyClassification for an unapproved line
                    'AssignedEndUserID' => null,
                    'AssignedDepartmentID' => null,
                    'AdminNotes' => 'No-op',
                    'IsApprovedForIssuance' => false,
                ]
            ]
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/asset-requests/{$request->RequestID}/review", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('assetrequestdetail', [
            'RequestDetailID' => $detail->RequestDetailID,
        ]);
    }
}
