<?php

use Illuminate\Support\Facades\DB;

$canonical = [
    'Accounting' => 'ACCOUNTING',
    'Admin' => 'ADMIN',
    'Agriculture' => 'AGRICULTURE',
    'Assessor' => 'ASSESSOR',
    'BAC' => 'BAC',
    'BFP' => 'BFP',
    'BIR' => 'BIR',
    'BJMP' => 'BJMP',
    'BPLO' => 'BPLO',
    'Budget' => 'BUDGET',
    'CCE' => 'CCE',
    'CCR' => 'CCR',
    'CDRRMO' => 'CDRRMO',
    'CEEDO' => 'CEEDO',
    'CESHUDO' => 'CESHUDO',
    'CLENRO' => 'CLENRO',
    'COA' => 'COA',
    'COMELEC' => 'COMELEC',
    'CPDC' => 'CPDC',
    'CSWDO' => 'CSWDO',
    'OSCA' => 'OSCA',
    'PDAW' => 'PDAW',
    'DEPED' => 'DEPED',
    'DILG' => 'DILG',
    'Engineering' => 'ENGINEERING',
    'FM Radio' => 'FM_RADIO',
    'GSO' => 'GSO',
    'Park and Plaza' => 'PARK_AND_PLAZA',
    'Sound System' => 'SOUND_SYSTEM',
    'GYM' => 'GYM',
    'Health Office' => 'HEALTH_OFFICE',
    'HRMO' => 'HRMO',
    'IAS' => 'IAS',
    'Legal Office' => 'LEGAL_OFFICE',
    'Lying-In' => 'LYING_IN',
    'Market' => 'MARKET',
    "Mayor's Office" => 'MAYORS_OFFICE',
    'MCTC' => 'MCTC',
    'Motorpool Nutrition' => 'MOTORPOOL_NUTRITION',
    'OCBO' => 'OCBO',
    'PESO' => 'PESO',
    'PNP' => 'PNP',
    'POPCOM' => 'POPCOM',
    'Postal' => 'POSTAL',
    'Prosecutor' => 'PROSECUTOR',
    'Sanitary' => 'SANITARY',
    'SP' => 'SP',
    'TIMU' => 'TIMU',
    'Tourism' => 'TOURISM',
    'Treasury' => 'TREASURY',
    'Veterinary' => 'VETERINARY',
    "Vice Mayor's Office" => 'VICE_MAYORS_OFFICE',
    'Waterworks' => 'WATERWORKS',
    'C1' => 'C1',
    'C2' => 'C2',
    'C3' => 'C3',
    'C4' => 'C4',
    'C5' => 'C5',
    'C6' => 'C6',
    'C7' => 'C7',
    'C8' => 'C8',
    'C9' => 'C9',
    'C10' => 'C10',
    'K' => 'K',
    'ABC' => 'ABC',
    'BLDG MNT' => 'BLDG_MNT',
    'PAO' => 'PAO',
    'NBI' => 'NBI',
    'ECCO' => 'ECCO',
    'Poblacion' => 'POBLACION',
    'Molugan' => 'MOLUGAN',
    'Amoros' => 'AMOROS',
    'Sambulawan' => 'SAMBULAWAN',
    'Taytay' => 'TAYTAY',
    'Himaya' => 'HIMAYA',
    'Ulaliman' => 'ULALIMAN',
    'Bolobolo (Pedro S Baculio)' => 'BOLOBOLO_PEDRO_S',
    'Sinaloc' => 'SINALOC',
    'Calongonan (San Francisco de Asis)' => 'CALONGONAN_SF',
    'Hinigdaan' => 'HINIGDAAN',
    'Kalabaylabay' => 'KALABAYLABAY',
    'Bolsong' => 'BOLSONG',
    'Quibonbon' => 'QUIBONBON',
    'Cogon' => 'COGON',
];

$remapByName = [
    'IT Department' => 'GSO',
    'General Services Office' => 'GSO',
    'Administrative Office' => 'Admin',
    'Finance Department' => 'Accounting',
    'Human Resources' => 'HRMO',
    'Department of Health' => 'Health Office',
    '818' => 'BIR',
    'PAD' => 'PAO',
    'Lying-In Market' => 'Lying-In',
];

DB::beginTransaction();

try {
    foreach ($canonical as $name => $code) {
        DB::table('department')->updateOrInsert(
            ['DepartmentName' => $name],
            [
                'DepartmentCode' => $code,
                'IsActive' => 1,
                'deleted_at' => null,
            ]
        );
    }

    $idByName = DB::table('department')->pluck('DepartmentID', 'DepartmentName')->toArray();

    $deptColumns = DB::select("SELECT TABLE_NAME, COLUMN_NAME
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME <> 'department'
          AND (COLUMN_NAME LIKE '%DepartmentID%' OR COLUMN_NAME LIKE '%department_id%')");

    foreach ($remapByName as $fromName => $toName) {
        if (!isset($idByName[$fromName], $idByName[$toName])) {
            continue;
        }

        $fromId = (int) $idByName[$fromName];
        $toId = (int) $idByName[$toName];

        if ($fromId === $toId) {
            continue;
        }

        foreach ($deptColumns as $col) {
            $table = $col->TABLE_NAME;
            $column = $col->COLUMN_NAME;
            DB::statement("UPDATE `{$table}` SET `{$column}` = ? WHERE `{$column}` = ?", [$toId, $fromId]);
        }
    }

    DB::table('department')->whereNotIn('DepartmentName', array_keys($canonical))->delete();

    $remaining = DB::table('department')->orderBy('DepartmentName')->get(['DepartmentID', 'DepartmentName', 'DepartmentCode']);

    DB::commit();

    dump(['remaining_count' => $remaining->count()]);
    dump($remaining->toArray());
} catch (Throwable $e) {
    DB::rollBack();
    throw $e;
}
