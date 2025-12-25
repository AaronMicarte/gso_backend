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
        // Fetch classification IDs we just seeded (or existing ones)
        $officeId = DB::table('classification')->where('ClassificationName', 'Office')->value('ClassificationID');
        $infoId = DB::table('classification')->where('ClassificationName', 'Information & Communication')->value('ClassificationID');
        $machineryId = DB::table('classification')->where('ClassificationName', 'Machinery & Equipment')->value('ClassificationID');
        $furnitureId = DB::table('classification')->where('ClassificationName', 'Furniture & Fixtures')->value('ClassificationID');
        $libraryId = DB::table('classification')->where('ClassificationName', 'Library & Media')->value('ClassificationID');
        $medicalId = DB::table('classification')->where('ClassificationName', 'Medical & Laboratory')->value('ClassificationID');
        $suppliesId = DB::table('classification')->where('ClassificationName', 'Supplies & Consumables')->value('ClassificationID');
        $securityId = DB::table('classification')->where('ClassificationName', 'Security & Safety')->value('ClassificationID');

        // PropertyClassificationID: 1 = Semi-Expendable (ICS), 2 = Non-Expendable / PPE (PAR)
        DB::table('propertytype')->insertOrIgnore([
            // Non-Expendable / PAR
            ['ClassificationID' => $furnitureId, 'PropertyClassificationID' => 2, 'PropertyName' => 'Furniture and Fixtures', 'Description' => 'Desks, cabinets, chairs, tables'],
            ['ClassificationID' => $officeId, 'PropertyClassificationID' => 2, 'PropertyName' => 'Office Equipment', 'Description' => 'Photocopiers, shredders, safes'],
            ['ClassificationID' => $infoId, 'PropertyClassificationID' => 2, 'PropertyName' => 'IT Equipment', 'Description' => 'Desktops, laptops, servers'],
            ['ClassificationID' => $infoId, 'PropertyClassificationID' => 2, 'PropertyName' => 'Network & Communications', 'Description' => 'Routers, switches, PBX, handsets'],
            ['ClassificationID' => $machineryId, 'PropertyClassificationID' => 2, 'PropertyName' => 'Machinery & Heavy Equipment', 'Description' => 'Generators, compressors, heavy tools'],
            ['ClassificationID' => $medicalId, 'PropertyClassificationID' => 2, 'PropertyName' => 'Medical Equipment', 'Description' => 'Medical devices and clinical equipment'],
            ['ClassificationID' => $libraryId, 'PropertyClassificationID' => 2, 'PropertyName' => 'Library & Reference Materials', 'Description' => 'Books, serials, CD/DVDs'],
            ['ClassificationID' => $securityId, 'PropertyClassificationID' => 2, 'PropertyName' => 'Security Equipment', 'Description' => 'CCTV, access control, alarms'],

            // Semi-Expendable / ICS
            ['ClassificationID' => $suppliesId, 'PropertyClassificationID' => 1, 'PropertyName' => 'Office Supplies / Stationery', 'Description' => 'Pens, paper, notepads, consumables'],
            ['ClassificationID' => $suppliesId, 'PropertyClassificationID' => 1, 'PropertyName' => 'Small Tools & Instruments', 'Description' => 'Hand tools and small instruments'],
            ['ClassificationID' => $suppliesId, 'PropertyClassificationID' => 1, 'PropertyName' => 'IT Consumables', 'Description' => 'Ink cartridges, cables, batteries'],
            ['ClassificationID' => $suppliesId, 'PropertyClassificationID' => 1, 'PropertyName' => 'Electrical Supplies', 'Description' => 'Fuses, wiring, small electrical parts'],
            ['ClassificationID' => $suppliesId, 'PropertyClassificationID' => 1, 'PropertyName' => 'Cleaning & Janitorial Supplies', 'Description' => 'Cleaning chemicals and materials'],
            ['ClassificationID' => $suppliesId, 'PropertyClassificationID' => 1, 'PropertyName' => 'PPE & Uniforms', 'Description' => 'Gloves, masks, uniforms'],
            ['ClassificationID' => $suppliesId, 'PropertyClassificationID' => 1, 'PropertyName' => 'Consumable Medical Supplies', 'Description' => 'Syringes, bandages, disposables'],
            ['ClassificationID' => $officeId, 'PropertyClassificationID' => 1, 'PropertyName' => 'Audio Visual (small)', 'Description' => 'Projectors, microphones (low value)'],
            ['ClassificationID' => $infoId, 'PropertyClassificationID' => 1, 'PropertyName' => 'Peripherals & Accessories', 'Description' => 'Keyboards, mice, monitors (low value)'],
        ]);
    }
}
