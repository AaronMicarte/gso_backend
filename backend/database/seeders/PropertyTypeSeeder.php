<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PropertyClassificationID: 1 = Semi-Expendable (ICS), 2 = Non-Expendable / PPE (PAR)
        DB::table('propertytype')->insertOrIgnore([
            // Non-Expendable / PAR
            ['PropertyClassificationID' => 2, 'PropertyName' => 'Furniture and Fixtures', 'Description' => 'Desks, cabinets, chairs, tables', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 2, 'PropertyName' => 'Office Equipment', 'Description' => 'Photocopiers, shredders, safes', 'RequiresVerification' => 1],
            ['PropertyClassificationID' => 2, 'PropertyName' => 'IT Equipment', 'Description' => 'Desktops, laptops, servers', 'RequiresVerification' => 1],
            ['PropertyClassificationID' => 2, 'PropertyName' => 'Network & Communications', 'Description' => 'Routers, switches, PBX, handsets', 'RequiresVerification' => 1],
            ['PropertyClassificationID' => 2, 'PropertyName' => 'Machinery & Heavy Equipment', 'Description' => 'Generators, compressors, heavy tools', 'RequiresVerification' => 1],
            ['PropertyClassificationID' => 2, 'PropertyName' => 'Medical Equipment', 'Description' => 'Medical devices and clinical equipment', 'RequiresVerification' => 1],
            ['PropertyClassificationID' => 2, 'PropertyName' => 'Library & Reference Materials', 'Description' => 'Books, serials, CD/DVDs', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 2, 'PropertyName' => 'Security Equipment', 'Description' => 'CCTV, access control, alarms', 'RequiresVerification' => 1],

            // Semi-Expendable / ICS
            ['PropertyClassificationID' => 1, 'PropertyName' => 'Office Supplies / Stationery', 'Description' => 'Pens, paper, notepads, consumables', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'Small Tools & Instruments', 'Description' => 'Hand tools and small instruments', 'RequiresVerification' => 1],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'IT Consumables', 'Description' => 'Ink cartridges, cables, batteries', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'Electrical Supplies', 'Description' => 'Fuses, wiring, small electrical parts', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'Cleaning & Janitorial Supplies', 'Description' => 'Cleaning chemicals and materials', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'PPE & Uniforms', 'Description' => 'Gloves, masks, uniforms', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'Consumable Medical Supplies', 'Description' => 'Syringes, bandages, disposables', 'RequiresVerification' => 0],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'Audio Visual (small)', 'Description' => 'Projectors, microphones (low value)', 'RequiresVerification' => 1],
            ['PropertyClassificationID' => 1, 'PropertyName' => 'Peripherals & Accessories', 'Description' => 'Keyboards, mice, monitors (low value)', 'RequiresVerification' => 1],
        ]);
    }
}
