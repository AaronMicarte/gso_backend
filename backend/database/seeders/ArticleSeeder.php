<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('articles')->insertOrIgnore([
            // NON-EXPENDABLE ARTICLES (PPE items)
            [
                'ArticleName' => 'Desktop Computer',
                'Description' => 'Non-expendable desktop computer hardware',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Laptop Computer',
                'Description' => 'Non-expendable laptop computer',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Printer/Scanner',
                'Description' => 'Non-expendable printing and scanning equipment',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Desk',
                'Description' => 'Non-expendable office furniture - desk',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Chair',
                'Description' => 'Non-expendable office furniture - chair',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Filing Cabinet',
                'Description' => 'Non-expendable office storage furniture',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Air Conditioner',
                'Description' => 'Non-expendable cooling equipment',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Projector',
                'Description' => 'Non-expendable presentation equipment',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Telephone Set',
                'Description' => 'Non-expendable communication equipment',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Server Equipment',
                'Description' => 'Non-expendable server hardware',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // SEMI-EXPENDABLE ARTICLES (ICS items)
            [
                'ArticleName' => 'Calculator',
                'Description' => 'Semi-expendable calculation device',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Stapler',
                'Description' => 'Semi-expendable office tool',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Hole Puncher',
                'Description' => 'Semi-expendable office tool',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Supplies Set',
                'Description' => 'Semi-expendable office supplies bundle',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Table Fan',
                'Description' => 'Semi-expendable ventilation equipment',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Extension Cord',
                'Description' => 'Semi-expendable electrical accessory',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Lamp',
                'Description' => 'Semi-expendable lighting equipment',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Waste Basket',
                'Description' => 'Semi-expendable office accessory',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Clock/Watch',
                'Description' => 'Semi-expendable timekeeping device',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Hand Tools Set',
                'Description' => 'Semi-expendable maintenance tools',
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}