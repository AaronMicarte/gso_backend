<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('classification')->insertOrIgnore([
            ['CategoryID' => 1, 'ClassificationName' => 'Office', 'Description' => 'Office-related items and equipment'],
            ['CategoryID' => 1, 'ClassificationName' => 'Information & Communication', 'Description' => 'IT, network, and communication equipment'],
            ['CategoryID' => 1, 'ClassificationName' => 'Machinery & Equipment', 'Description' => 'Heavy and light machinery, production equipment'],
            ['CategoryID' => 1, 'ClassificationName' => 'Furniture & Fixtures', 'Description' => 'Desks, cabinets, chairs, shelves'],
            ['CategoryID' => 1, 'ClassificationName' => 'Library & Media', 'Description' => 'Books, publications, audiovisual media'],
            ['CategoryID' => 1, 'ClassificationName' => 'Medical & Laboratory', 'Description' => 'Medical devices and laboratory equipment'],
            ['CategoryID' => 1, 'ClassificationName' => 'Supplies & Consumables', 'Description' => 'Consumable supplies and small tools'],
            ['CategoryID' => 1, 'ClassificationName' => 'Security & Safety', 'Description' => 'Cameras, alarms, safety gear'],
        ]);
    }
}
