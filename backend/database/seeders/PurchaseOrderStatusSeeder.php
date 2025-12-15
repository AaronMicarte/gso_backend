<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['POStatusID' => 1, 'StatusName' => 'Pending'],
            ['POStatusID' => 2, 'StatusName' => 'Approved'],
            ['POStatusID' => 3, 'StatusName' => 'Rejected'],
            ['POStatusID' => 4, 'StatusName' => 'Cancelled'],
            ['POStatusID' => 5, 'StatusName' => 'Completed'], // For when DR is done
        ];

        foreach ($statuses as $status) {
            DB::table('purchaseorderstatus')->updateOrInsert(
                ['POStatusID' => $status['POStatusID']],
                ['StatusName' => $status['StatusName']]
            );
        }
    }
}
