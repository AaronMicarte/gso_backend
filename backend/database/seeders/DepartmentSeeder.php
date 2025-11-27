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
        DB::table('departments')->insert([
            ['DepartmentID' => 1, 'DepartmentName' => 'GSO'],
            ['DepartmentID' => 2, 'DepartmentName' => 'Human Resources'],
            ['DepartmentID' => 3, 'DepartmentName' => 'PESO'],
            ['DepartmentID' => 4, 'DepartmentName' => 'Accounting'],
            ['DepartmentID' => 5, 'DepartmentName' => 'CSWD'],
        ]);
    }
}
