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
        $departments = [
            ['DepartmentName' => 'Accounting', 'DepartmentCode' => 'ACCOUNTING', 'IsActive' => 1],
            ['DepartmentName' => 'Admin', 'DepartmentCode' => 'ADMIN', 'IsActive' => 1],
            ['DepartmentName' => 'Agriculture', 'DepartmentCode' => 'AGRICULTURE', 'IsActive' => 1],
            ['DepartmentName' => 'Assessor', 'DepartmentCode' => 'ASSESSOR', 'IsActive' => 1],
            ['DepartmentName' => 'BAC', 'DepartmentCode' => 'BAC', 'IsActive' => 1],
            ['DepartmentName' => 'BFP', 'DepartmentCode' => 'BFP', 'IsActive' => 1],
            ['DepartmentName' => 'BIR', 'DepartmentCode' => 'BIR', 'IsActive' => 1],
            ['DepartmentName' => 'BJMP', 'DepartmentCode' => 'BJMP', 'IsActive' => 1],
            ['DepartmentName' => 'BPLO', 'DepartmentCode' => 'BPLO', 'IsActive' => 1],
            ['DepartmentName' => 'Budget', 'DepartmentCode' => 'BUDGET', 'IsActive' => 1],
            ['DepartmentName' => 'CCE', 'DepartmentCode' => 'CCE', 'IsActive' => 1],
            ['DepartmentName' => 'CCR', 'DepartmentCode' => 'CCR', 'IsActive' => 1],
            ['DepartmentName' => 'CDRRMO', 'DepartmentCode' => 'CDRRMO', 'IsActive' => 1],
            ['DepartmentName' => 'CEEDO', 'DepartmentCode' => 'CEEDO', 'IsActive' => 1],
            ['DepartmentName' => 'CESHUDO', 'DepartmentCode' => 'CESHUDO', 'IsActive' => 1],
            ['DepartmentName' => 'CLENRO', 'DepartmentCode' => 'CLENRO', 'IsActive' => 1],
            ['DepartmentName' => 'COA', 'DepartmentCode' => 'COA', 'IsActive' => 1],
            ['DepartmentName' => 'COMELEC', 'DepartmentCode' => 'COMELEC', 'IsActive' => 1],
            ['DepartmentName' => 'CPDC', 'DepartmentCode' => 'CPDC', 'IsActive' => 1],
            ['DepartmentName' => 'CSWDO', 'DepartmentCode' => 'CSWDO', 'IsActive' => 1],
            ['DepartmentName' => 'OSCA', 'DepartmentCode' => 'OSCA', 'IsActive' => 1],
            ['DepartmentName' => 'PDAW', 'DepartmentCode' => 'PDAW', 'IsActive' => 1],
            ['DepartmentName' => 'DEPED', 'DepartmentCode' => 'DEPED', 'IsActive' => 1],
            ['DepartmentName' => 'DILG', 'DepartmentCode' => 'DILG', 'IsActive' => 1],
            ['DepartmentName' => 'Engineering', 'DepartmentCode' => 'ENGINEERING', 'IsActive' => 1],
            ['DepartmentName' => 'FM Radio', 'DepartmentCode' => 'FM_RADIO', 'IsActive' => 1],
            ['DepartmentName' => 'GSO', 'DepartmentCode' => 'GSO', 'IsActive' => 1],
            ['DepartmentName' => 'Park and Plaza', 'DepartmentCode' => 'PARK_AND_PLAZA', 'IsActive' => 1],
            ['DepartmentName' => 'Sound System', 'DepartmentCode' => 'SOUND_SYSTEM', 'IsActive' => 1],
            ['DepartmentName' => 'GYM', 'DepartmentCode' => 'GYM', 'IsActive' => 1],
            ['DepartmentName' => 'Health Office', 'DepartmentCode' => 'HEALTH_OFFICE', 'IsActive' => 1],
            ['DepartmentName' => 'HRMO', 'DepartmentCode' => 'HRMO', 'IsActive' => 1],
            ['DepartmentName' => 'IAS', 'DepartmentCode' => 'IAS', 'IsActive' => 1],
            ['DepartmentName' => 'Legal Office', 'DepartmentCode' => 'LEGAL_OFFICE', 'IsActive' => 1],
            ['DepartmentName' => 'Lying-In', 'DepartmentCode' => 'LYING_IN', 'IsActive' => 1],
            ['DepartmentName' => 'Market', 'DepartmentCode' => 'MARKET', 'IsActive' => 1],
            ['DepartmentName' => "Mayor's Office", 'DepartmentCode' => 'MAYORS_OFFICE', 'IsActive' => 1],
            ['DepartmentName' => 'MCTC', 'DepartmentCode' => 'MCTC', 'IsActive' => 1],
            ['DepartmentName' => 'Motorpool Nutrition', 'DepartmentCode' => 'MOTORPOOL_NUTRITION', 'IsActive' => 1],
            ['DepartmentName' => 'OCBO', 'DepartmentCode' => 'OCBO', 'IsActive' => 1],
            ['DepartmentName' => 'PESO', 'DepartmentCode' => 'PESO', 'IsActive' => 1],
            ['DepartmentName' => 'PNP', 'DepartmentCode' => 'PNP', 'IsActive' => 1],
            ['DepartmentName' => 'POPCOM', 'DepartmentCode' => 'POPCOM', 'IsActive' => 1],
            ['DepartmentName' => 'Postal', 'DepartmentCode' => 'POSTAL', 'IsActive' => 1],
            ['DepartmentName' => 'Prosecutor', 'DepartmentCode' => 'PROSECUTOR', 'IsActive' => 1],
            ['DepartmentName' => 'Sanitary', 'DepartmentCode' => 'SANITARY', 'IsActive' => 1],
            ['DepartmentName' => 'SP', 'DepartmentCode' => 'SP', 'IsActive' => 1],
            ['DepartmentName' => 'TIMU', 'DepartmentCode' => 'TIMU', 'IsActive' => 1],
            ['DepartmentName' => 'Tourism', 'DepartmentCode' => 'TOURISM', 'IsActive' => 1],
            ['DepartmentName' => 'Treasury', 'DepartmentCode' => 'TREASURY', 'IsActive' => 1],
            ['DepartmentName' => 'Veterinary', 'DepartmentCode' => 'VETERINARY', 'IsActive' => 1],
            ['DepartmentName' => "Vice Mayor's Office", 'DepartmentCode' => 'VICE_MAYORS_OFFICE', 'IsActive' => 1],
            ['DepartmentName' => 'Waterworks', 'DepartmentCode' => 'WATERWORKS', 'IsActive' => 1],
            ['DepartmentName' => 'C1', 'DepartmentCode' => 'C1', 'IsActive' => 1],
            ['DepartmentName' => 'C2', 'DepartmentCode' => 'C2', 'IsActive' => 1],
            ['DepartmentName' => 'C3', 'DepartmentCode' => 'C3', 'IsActive' => 1],
            ['DepartmentName' => 'C4', 'DepartmentCode' => 'C4', 'IsActive' => 1],
            ['DepartmentName' => 'C5', 'DepartmentCode' => 'C5', 'IsActive' => 1],
            ['DepartmentName' => 'C6', 'DepartmentCode' => 'C6', 'IsActive' => 1],
            ['DepartmentName' => 'C7', 'DepartmentCode' => 'C7', 'IsActive' => 1],
            ['DepartmentName' => 'C8', 'DepartmentCode' => 'C8', 'IsActive' => 1],
            ['DepartmentName' => 'C9', 'DepartmentCode' => 'C9', 'IsActive' => 1],
            ['DepartmentName' => 'C10', 'DepartmentCode' => 'C10', 'IsActive' => 1],
            ['DepartmentName' => 'K', 'DepartmentCode' => 'K', 'IsActive' => 1],
            ['DepartmentName' => 'ABC', 'DepartmentCode' => 'ABC', 'IsActive' => 1],
            ['DepartmentName' => 'BLDG MNT', 'DepartmentCode' => 'BLDG_MNT', 'IsActive' => 1],
            ['DepartmentName' => 'PAO', 'DepartmentCode' => 'PAO', 'IsActive' => 1],
            ['DepartmentName' => 'NBI', 'DepartmentCode' => 'NBI', 'IsActive' => 1],
            ['DepartmentName' => 'ECCO', 'DepartmentCode' => 'ECCO', 'IsActive' => 1],
            ['DepartmentName' => 'Poblacion', 'DepartmentCode' => 'POBLACION', 'IsActive' => 1],
            ['DepartmentName' => 'Molugan', 'DepartmentCode' => 'MOLUGAN', 'IsActive' => 1],
            ['DepartmentName' => 'Amoros', 'DepartmentCode' => 'AMOROS', 'IsActive' => 1],
            ['DepartmentName' => 'Sambulawan', 'DepartmentCode' => 'SAMBULAWAN', 'IsActive' => 1],
            ['DepartmentName' => 'Taytay', 'DepartmentCode' => 'TAYTAY', 'IsActive' => 1],
            ['DepartmentName' => 'Himaya', 'DepartmentCode' => 'HIMAYA', 'IsActive' => 1],
            ['DepartmentName' => 'Ulaliman', 'DepartmentCode' => 'ULALIMAN', 'IsActive' => 1],
            ['DepartmentName' => 'Bolobolo (Pedro S Baculio)', 'DepartmentCode' => 'BOLOBOLO_PEDRO_S', 'IsActive' => 1],
            ['DepartmentName' => 'Sinaloc', 'DepartmentCode' => 'SINALOC', 'IsActive' => 1],
            ['DepartmentName' => 'Calongonan (San Francisco de Asis)', 'DepartmentCode' => 'CALONGONAN_SF', 'IsActive' => 1],
            ['DepartmentName' => 'Hinigdaan', 'DepartmentCode' => 'HINIGDAAN', 'IsActive' => 1],
            ['DepartmentName' => 'Kalabaylabay', 'DepartmentCode' => 'KALABAYLABAY', 'IsActive' => 1],
            ['DepartmentName' => 'Bolsong', 'DepartmentCode' => 'BOLSONG', 'IsActive' => 1],
            ['DepartmentName' => 'Quibonbon', 'DepartmentCode' => 'QUIBONBON', 'IsActive' => 1],
            ['DepartmentName' => 'Cogon', 'DepartmentCode' => 'COGON', 'IsActive' => 1],
        ];

        foreach ($departments as $department) {
            DB::table('department')->updateOrInsert(
                ['DepartmentName' => $department['DepartmentName']],
                $department
            );
        }

        DB::table('department')
            ->whereNotIn('DepartmentName', array_column($departments, 'DepartmentName'))
            ->delete();
    }
}
