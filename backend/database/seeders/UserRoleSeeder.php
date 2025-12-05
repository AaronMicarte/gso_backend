<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('userrole')->insert([
            ['RoleName' => 'Admin', 'Description' => 'System Administrator - Full Access'],
            ['RoleName' => 'GSO Staff', 'Description' => 'General Services Office Staff'],
            ['RoleName' => 'Property Custodian', 'Description' => 'Manages property inventory'],
            ['RoleName' => 'Department Head', 'Description' => 'Can approve requests'],
            ['RoleName' => 'End User', 'Description' => 'Regular property accountable user'],
        ]);
    }
}
