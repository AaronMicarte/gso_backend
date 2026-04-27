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
        $now = now();

        $semiExpendableClassificationId = DB::table('propertyclassification')
            ->where('DocumentType', 'ICS')
            ->value('PropertyClassificationID');

        $ppeClassificationId = DB::table('propertyclassification')
            ->where('DocumentType', 'PAR')
            ->value('PropertyClassificationID');

        if (!$semiExpendableClassificationId || !$ppeClassificationId) {
            return;
        }

        $ppeTypes = [
            ['PropertyName' => 'Furniture and Fixtures', 'Description' => 'Desks, cabinets, chairs, tables'],
            ['PropertyName' => 'Office Equipment', 'Description' => 'Photocopiers, shredders, safes'],
            ['PropertyName' => 'IT Equipment', 'Description' => 'Desktops, laptops, servers'],
            ['PropertyName' => 'Network & Communications', 'Description' => 'Routers, switches, PBX, handsets'],
            ['PropertyName' => 'Machinery & Heavy Equipment', 'Description' => 'Generators, compressors, heavy tools'],
            ['PropertyName' => 'Medical Equipment', 'Description' => 'Medical devices and clinical equipment'],
            ['PropertyName' => 'Library & Reference Materials', 'Description' => 'Books, serials, CD/DVDs'],
            ['PropertyName' => 'Security Equipment', 'Description' => 'CCTV, access control, alarms'],
        ];

        $semiExpendableTypes = [
            ['PropertyName' => 'Computer Peripherals', 'Description' => 'Mice, keyboards, webcams, headsets and similar accessories'],
            ['PropertyName' => 'Data Storage Devices', 'Description' => 'USB drives, external drives, memory cards'],
            ['PropertyName' => 'Small Office Appliances', 'Description' => 'Electric fans, kettles, coffee makers and similar items'],
            ['PropertyName' => 'Presentation Devices', 'Description' => 'Portable projectors, presenter tools and accessories'],
            ['PropertyName' => 'Audio Equipment (Portable)', 'Description' => 'Portable speakers, microphones and small audio devices'],
            ['PropertyName' => 'Video Equipment (Portable)', 'Description' => 'Action cameras and small portable video devices'],
            ['PropertyName' => 'Power Protection Devices', 'Description' => 'UPS, AVR and surge protectors'],
            ['PropertyName' => 'Hand Tools and Instruments', 'Description' => 'Small repair and maintenance tools'],
            ['PropertyName' => 'Portable Measuring Devices', 'Description' => 'Tape measures, multimeters and handheld meters'],
            ['PropertyName' => 'Fire Safety Devices (Portable)', 'Description' => 'Portable extinguishers and safety response kits'],
            ['PropertyName' => 'Portable Lighting Equipment', 'Description' => 'Emergency lamps, desk lamps and rechargeable lights'],
            ['PropertyName' => 'Record Management Accessories', 'Description' => 'Binding machines, labelers and filing accessories'],
            ['PropertyName' => 'IT Cables and Adapters', 'Description' => 'HDMI, LAN and power adapters for operational use'],
            ['PropertyName' => 'Maintenance and Janitorial Tools', 'Description' => 'Reusable janitorial tools and maintenance kits'],
        ];

        foreach ($ppeTypes as $type) {
            $existing = DB::table('propertytype')->where('PropertyName', $type['PropertyName'])->first();

            if ($existing) {
                DB::table('propertytype')
                    ->where('PropertyTypeID', $existing->PropertyTypeID)
                    ->update([
                        'PropertyClassificationID' => $ppeClassificationId,
                        'Description' => $type['Description'],
                        'UpdatedDate' => $now,
                    ]);
                continue;
            }

            DB::table('propertytype')->insert([
                'PropertyClassificationID' => $ppeClassificationId,
                'PropertyName' => $type['PropertyName'],
                'Description' => $type['Description'],
                'CreatedDate' => $now,
                'UpdatedDate' => $now,
            ]);
        }

        foreach ($semiExpendableTypes as $type) {
            $existing = DB::table('propertytype')->where('PropertyName', $type['PropertyName'])->first();

            if ($existing) {
                DB::table('propertytype')
                    ->where('PropertyTypeID', $existing->PropertyTypeID)
                    ->update([
                        'PropertyClassificationID' => $semiExpendableClassificationId,
                        'Description' => $type['Description'],
                        'UpdatedDate' => $now,
                    ]);
                continue;
            }

            DB::table('propertytype')->insert([
                'PropertyClassificationID' => $semiExpendableClassificationId,
                'PropertyName' => $type['PropertyName'],
                'Description' => $type['Description'],
                'CreatedDate' => $now,
                'UpdatedDate' => $now,
            ]);
        }
    }
}
