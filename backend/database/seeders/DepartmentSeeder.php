<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('department')->insert([
            ['DepartmentName' => 'General Services Office', 'DepartmentCode' => 'GSO', 'IsActive' => 1],
            ['DepartmentName' => 'Administrative Office', 'DepartmentCode' => 'ADMIN', 'IsActive' => 1],
            ['DepartmentName' => 'Finance Department', 'DepartmentCode' => 'FIN', 'IsActive' => 1],
            ['DepartmentName' => 'Human Resources', 'DepartmentCode' => 'HR', 'IsActive' => 1],
            ['DepartmentName' => 'IT Department', 'DepartmentCode' => 'IT', 'IsActive' => 1],
        ]);
    }
}
