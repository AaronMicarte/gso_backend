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
                'UserID' => 1,
                'UserRoleID' => 1,
                'DepartmentID' => 1,
                'Username' => 'gso.admin',
                'Email' => 'gso.admin@gmail.com',
                'Password' => Hash::make('123'),
            ],
            [
                'UserID' => 2,
                'UserRoleID' => 2,
                'DepartmentID' => 2,
                'Username' => 'gso.staff',
                'Email' => 'gso.staff@gmail.com',
                'Password' => Hash::make('123'),
            ],
            [
                'UserID' => 3,
                'UserRoleID' => 3,
                'DepartmentID' => 5,
                'Username' => 'gso.custodian',
                'Email' => 'gso.custodian@gmail.com',
                'Password' => Hash::make('123'),
            ],
        ]);
    }
}
