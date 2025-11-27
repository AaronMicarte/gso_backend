<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoginAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('loginaudits')->insert([
            ['LoginAuditID' => 1, 'UserID' => 1, 'EventTime' => '2025-11-26 10:00:00', 'Status' => 'Success'],
            ['LoginAuditID' => 2, 'UserID' => 2, 'EventTime' => '2025-11-26 10:05:00', 'Status' => 'Success'],
            ['LoginAuditID' => 3, 'UserID' => 3, 'EventTime' => '2025-11-26 10:10:00', 'Status' => 'Failed'],
        ]);
    }
}
