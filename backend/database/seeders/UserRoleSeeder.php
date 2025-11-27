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
        DB::table('userroles')->insert([
            ['UserRoleID' => 1, 'Role' => 'gso admin'],
            ['UserRoleID' => 2, 'Role' => 'gso staff'],
            ['UserRoleID' => 3, 'Role' => 'custodian'],
        ]);
    }
}
