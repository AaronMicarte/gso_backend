<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @deprecated Use DepartmentSeeder, UserRoleSeeder, and UserSeeder instead.
 * This seeder is kept for reference but should NOT be used.
 */
class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DEPRECATED: This seeder uses old column names.
        // Use the individual seeders instead:
        // - DepartmentSeeder
        // - UserRoleSeeder  
        // - UserSeeder
        // - LoginAuditSeeder

        throw new \Exception('AuthSeeder is deprecated. Use DepartmentSeeder, UserRoleSeeder, UserSeeder instead.');
    }
}
