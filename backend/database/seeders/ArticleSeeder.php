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
                'PropertyTypeID' => 7,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Laptop Computer',
                'Description' => 'Non-expendable laptop computer',
                'PropertyTypeID' => 7,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Printer/Scanner',
                'Description' => 'Non-expendable printing and scanning equipment',
                'PropertyTypeID' => 7,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Desk',
                'Description' => 'Non-expendable office furniture - desk',
                'PropertyTypeID' => 6,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Chair',
                'Description' => 'Non-expendable office furniture - chair',
                'PropertyTypeID' => 6,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Filing Cabinet',
                'Description' => 'Non-expendable office storage furniture',
                'PropertyTypeID' => 6,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Air Conditioner',
                'Description' => 'Non-expendable cooling equipment',
                'PropertyTypeID' => 8,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Projector',
                'Description' => 'Non-expendable presentation equipment',
                'PropertyTypeID' => 7,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Telephone Set',
                'Description' => 'Non-expendable communication equipment',
                'PropertyTypeID' => 2,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Server Equipment',
                'Description' => 'Non-expendable server hardware',
                'PropertyTypeID' => 7,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // SEMI-EXPENDABLE ARTICLES (ICS items)
            [
                'ArticleName' => 'Calculator',
                'Description' => 'Semi-expendable calculation device',
                'PropertyTypeID' => 11,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Stapler',
                'Description' => 'Semi-expendable office tool',
                'PropertyTypeID' => 11,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Hole Puncher',
                'Description' => 'Semi-expendable office tool',
                'PropertyTypeID' => 11,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Supplies Set',
                'Description' => 'Semi-expendable office supplies bundle',
                'PropertyTypeID' => 11,
                'DefaultUnitOfMeasureID' => 3,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Table Fan',
                'Description' => 'Semi-expendable ventilation equipment',
                'PropertyTypeID' => 12,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Extension Cord',
                'Description' => 'Semi-expendable electrical accessory',
                'PropertyTypeID' => 12,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Office Lamp',
                'Description' => 'Semi-expendable lighting equipment',
                'PropertyTypeID' => 11,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Waste Basket',
                'Description' => 'Semi-expendable office accessory',
                'PropertyTypeID' => 6,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Clock/Watch',
                'Description' => 'Semi-expendable timekeeping device',
                'PropertyTypeID' => 11,
                'DefaultUnitOfMeasureID' => 2,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ArticleName' => 'Hand Tools Set',
                'Description' => 'Semi-expendable maintenance tools',
                'PropertyTypeID' => 12,
                'DefaultUnitOfMeasureID' => 3,
                'IsActive' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}