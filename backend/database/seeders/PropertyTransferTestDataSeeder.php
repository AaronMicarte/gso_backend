<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyTransferTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean up any existing test data (delete in proper order due to FK constraints)
        DB::table('propertytransferdetails')->delete();
        DB::table('propertytransfer')->delete();

        $now = Carbon::now();

        // Insert sample property transfers with complete, proper data
        $transferId1 = DB::table('propertytransfer')->insertGetId([
            'TransferNumber' => 'PTR-2026-0001',
            'RequestedByUserID' => 3, // Chokoy Covid (IT Head)
            'FromDepartmentID' => 5, // IT Department  
            'ToDepartmentID' => 2, // Administrative Office
            'FromEndUserID' => 2, // Chokoy Covid
            'ToEndUserID' => 2, // Chokoy Covid (same person, different role)
            'Reason' => 'Office relocation and equipment redistribution for improved workflow efficiency',
            'Status' => 'approved',
            'ApprovedByUserID' => 1, // GSO Admin
            'ApprovedAt' => $now->copy()->subDays(1),
            'CompletedByUserID' => null,
            'CompletedAt' => null,
            'BorrowerSignedAt' => null,
            'BorrowingSignedAt' => null,
            'GsoNotes' => 'Transfer approved pending completion of physical handover documentation',
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        $transferId2 = DB::table('propertytransfer')->insertGetId([
            'TransferNumber' => 'PTR-2026-0002', 
            'RequestedByUserID' => 1, // GSO Admin
            'FromDepartmentID' => 2, // Administrative Office
            'ToDepartmentID' => 17, // Department of Health
            'FromEndUserID' => 2, // Chokoy Covid
            'ToEndUserID' => 2, // Chokoy Covid
            'Reason' => 'Equipment transfer to support health department operations and compliance requirements',
            'Status' => 'completed',
            'ApprovedByUserID' => 1, // GSO Admin
            'ApprovedAt' => $now->copy()->subDays(3),
            'CompletedByUserID' => 1, // GSO Admin
            'CompletedAt' => $now->copy()->subDays(1),
            'BorrowerSignedAt' => $now->copy()->subDays(1)->addHours(1),
            'BorrowingSignedAt' => $now->copy()->subDays(1)->addHours(2),
            'GsoNotes' => 'Transfer completed successfully with all required signatures obtained and inventory updated',
            'created_at' => $now->copy()->subDays(4),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        $transferId3 = DB::table('propertytransfer')->insertGetId([
            'TransferNumber' => 'PTR-2026-0003',
            'RequestedByUserID' => 2, // Aaron Micarte (GSO Staff)
            'FromDepartmentID' => 17, // Department of Health
            'ToDepartmentID' => 1, // GSO
            'FromEndUserID' => 2, // Chokoy Covid
            'ToEndUserID' => 2, // Chokoy Covid
            'Reason' => 'IT equipment consolidation for better maintenance and technical support coverage',
            'Status' => 'pending',
            'ApprovedByUserID' => null,
            'ApprovedAt' => null,
            'CompletedByUserID' => null,
            'CompletedAt' => null,
            'BorrowerSignedAt' => null,
            'BorrowingSignedAt' => null,
            'GsoNotes' => null,
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        // Insert transfer details with proper inventory references
        $transferDetails = [
            // Transfer 1 items
            [
                'TransferID' => $transferId1,
                'InventoryDetailID' => 1, // Wireless Mouse
                'Quantity' => 1,
                'Remarks' => 'Working condition, includes original packaging and documentation',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'TransferID' => $transferId1,
                'InventoryDetailID' => 2, // Keyboard  
                'Quantity' => 1,
                'Remarks' => 'Excellent condition, all keys functional, includes USB receiver',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],

            // Transfer 2 items
            [
                'TransferID' => $transferId2,
                'InventoryDetailID' => 3, // Monitor 24"
                'Quantity' => 1,
                'Remarks' => 'Perfect working condition with stand and all cables included',
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'TransferID' => $transferId2,
                'InventoryDetailID' => 4, // HDMI Cable
                'Quantity' => 1,
                'Remarks' => 'New condition, 6ft length, supports 4K resolution',
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'TransferID' => $transferId2,
                'InventoryDetailID' => 5, // HDMI Cable
                'Quantity' => 1,
                'Remarks' => 'Good condition, tested and verified working properly',
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(1),
            ],

            // Transfer 3 items
            [
                'TransferID' => $transferId3,
                'InventoryDetailID' => 6, // HDMI Cable
                'Quantity' => 1,
                'Remarks' => 'Standard condition, functional with no visible damage',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'TransferID' => $transferId3,
                'InventoryDetailID' => 7, // HDMI Cable
                'Quantity' => 1,
                'Remarks' => 'Serviceable condition, requires careful handling during transport',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
            [
                'TransferID' => $transferId3,
                'InventoryDetailID' => 14, // Air Conditioner related item
                'Quantity' => 1,
                'Remarks' => 'Operational condition, includes warranty documentation and service manual',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('propertytransferdetails')->insert($transferDetails);

        $this->command->info('Property transfer test data seeded successfully with complete details');
    }
}