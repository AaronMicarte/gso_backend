<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create departments
        DB::table('department')->insert([
            ['DepartmentID' => 1, 'DepartmentName' => 'Information Technology'],
            ['DepartmentID' => 2, 'DepartmentName' => 'Procurement'],
            ['DepartmentID' => 3, 'DepartmentName' => 'Finance'],
            ['DepartmentID' => 4, 'DepartmentName' => 'General Services'],
        ]);

        // Create user roles
        DB::table('userrole')->insert([
            ['UserRoleID' => 1, 'Role' => 'Administrator'],
            ['UserRoleID' => 2, 'Role' => 'Procurement Officer'],
            ['UserRoleID' => 3, 'Role' => 'GSO Staff'],
            ['UserRoleID' => 4, 'Role' => 'Department Head'],
        ]);

        // Create test users
        DB::table('user')->insert([
            [
                'UserID' => 1,
                'UserRoleID' => 1,
                'DepartmentID' => 1,
                'Username' => 'admin',
                'Email' => 'admin@gso.edu.ph',
                'Password' => Hash::make('admin123'),
            ],
            [
                'UserID' => 2,
                'UserRoleID' => 2,
                'DepartmentID' => 2,
                'Username' => 'procurement',
                'Email' => 'procurement@gso.edu.ph',
                'Password' => Hash::make('proc123'),
            ],
            [
                'UserID' => 3,
                'UserRoleID' => 3,
                'DepartmentID' => 4,
                'Username' => 'gso_staff',
                'Email' => 'staff@gso.edu.ph',
                'Password' => Hash::make('staff123'),
            ],
        ]);
    }
}
