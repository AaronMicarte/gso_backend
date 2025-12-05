<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user')->insert([
            [
                'UserRoleID' => 1,
                'DepartmentID' => 1,
                'Username' => 'gso.admin',
                'Email' => 'admin@gso.gov.ph',
                'Password' => Hash::make('123'),
                'FullName' => 'GSO Administrator',
                'IsActive' => 1,
            ],
            [
                'UserRoleID' => 2,
                'DepartmentID' => 1,
                'Username' => 'gso.staff.aaronmicarte',
                'Email' => 'aaron.micarte@gso.gov.ph',
                'Password' => Hash::make('123'),
                'FullName' => 'Aaron Micarte',
                'IsActive' => 1,
            ],
            [
                'UserRoleID' => 3,
                'DepartmentID' => 1,
                'Username' => 'custodian',
                'Email' => 'custodian@gso.gov.ph',
                'Password' => Hash::make('123'),
                'FullName' => 'Property Custodian',
                'IsActive' => 1,
            ],
        ]);
    }
}
