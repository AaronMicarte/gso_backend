<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['PRStatusID' => 1, 'StatusName' => 'Pending'],
            ['PRStatusID' => 2, 'StatusName' => 'Approved'],
            ['PRStatusID' => 3, 'StatusName' => 'Rejected'],
            ['PRStatusID' => 4, 'StatusName' => 'Cancelled'],
        ];

        foreach ($statuses as $status) {
            DB::table('purchaserequeststatus')->updateOrInsert(
                ['PRStatusID' => $status['PRStatusID']],
                ['StatusName' => $status['StatusName']]
            );
        }
    }
}
