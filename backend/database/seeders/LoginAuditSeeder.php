<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoginAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Status values: 'Login', 'Logout', 'FailedLogin'
     */
    public function run(): void
    {
        // LoginAudit is populated during actual login/logout events
        // Seeding sample data for testing purposes only
        DB::table('loginaudit')->insert([
            ['UserID' => 1, 'EventTime' => '2025-11-26 10:00:00', 'Status' => 'Login'],
            ['UserID' => 2, 'EventTime' => '2025-11-26 10:05:00', 'Status' => 'Login'],
            ['UserID' => 3, 'EventTime' => '2025-11-26 10:10:00', 'Status' => 'FailedLogin'],
        ]);
    }
}
