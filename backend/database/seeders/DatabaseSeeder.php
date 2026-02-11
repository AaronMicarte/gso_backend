<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            UserRoleSeeder::class,
            UserSeeder::class,
            LoginAuditSeeder::class,
            PurchaseRequestStatusSeeder::class,
            PurchaseOrderStatusSeeder::class,
            ClassificationSeeder::class,
            PropertyTypeSeeder::class,
            \Database\Seeders\DeliveryReceiptSeeder::class,
        ]);
    }
}
