<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\EndUser;

class TestEndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some positions first
        $positions = [
            ['PositionName' => 'Manager', 'PositionDescription' => 'Department Manager'],
            ['PositionName' => 'Staff', 'PositionDescription' => 'Regular Staff Member'],
            ['PositionName' => 'Supervisor', 'PositionDescription' => 'Supervisor'],
            ['PositionName' => 'Officer', 'PositionDescription' => 'Department Officer'],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(
                ['PositionName' => $position['PositionName']],
                $position
            );
        }

        // Create some test end users
        $endUsers = [
            [
                'PositionID' => 1, // Manager
                'DepartmentID' => 2, // Administrative Office
                'FullName' => 'John Doe',
                'ContactNumber' => '09123456789',
                'Email' => 'john.doe@example.com',
                'IsActive' => true,
            ],
            [
                'PositionID' => 2, // Staff
                'DepartmentID' => 2, // Administrative Office
                'FullName' => 'Jane Smith',
                'ContactNumber' => '09987654321',
                'Email' => 'jane.smith@example.com',
                'IsActive' => true,
            ],
            [
                'PositionID' => 3, // Supervisor
                'DepartmentID' => 5, // IT Department
                'FullName' => 'Mike Johnson',
                'ContactNumber' => '09111222333',
                'Email' => 'mike.johnson@example.com',
                'IsActive' => true,
            ],
            [
                'PositionID' => 2, // Staff
                'DepartmentID' => 5, // IT Department
                'FullName' => 'Sarah Wilson',
                'ContactNumber' => '09444555666',
                'Email' => 'sarah.wilson@example.com',
                'IsActive' => true,
            ],
            [
                'PositionID' => 4, // Officer
                'DepartmentID' => 4, // Human Resources
                'FullName' => 'Robert Brown',
                'ContactNumber' => '09777888999',
                'Email' => 'robert.brown@example.com',
                'IsActive' => true,
            ],
        ];

        foreach ($endUsers as $userData) {
            EndUser::firstOrCreate(
                ['Email' => $userData['Email']],
                $userData
            );
        }

        $this->command->info('Created test positions and end users.');
    }
}