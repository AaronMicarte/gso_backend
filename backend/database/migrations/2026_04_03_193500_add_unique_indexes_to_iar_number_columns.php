<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateDr = DB::table('deliveryreceipt')
            ->select('IARNumber')
            ->whereNotNull('IARNumber')
            ->where('IARNumber', '!=', '')
            ->groupBy('IARNumber')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicateDr) {
            throw new RuntimeException('Cannot add unique index: duplicate IARNumber values still exist in deliveryreceipt.');
        }

        $duplicateIar = DB::table('inspectionacceptancereport')
            ->select('IARNumber')
            ->whereNotNull('IARNumber')
            ->where('IARNumber', '!=', '')
            ->groupBy('IARNumber')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicateIar) {
            throw new RuntimeException('Cannot add unique index: duplicate IARNumber values still exist in inspectionacceptancereport.');
        }

        $drIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'deliveryreceipt')
            ->where('index_name', 'ux_deliveryreceipt_iarnumber')
            ->exists();

        if (! $drIndexExists) {
            DB::statement('ALTER TABLE `deliveryreceipt` ADD UNIQUE KEY `ux_deliveryreceipt_iarnumber` (`IARNumber`)');
        }

        $iarIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'inspectionacceptancereport')
            ->where('index_name', 'ux_inspectionacceptancereport_iarnumber')
            ->exists();

        if (! $iarIndexExists) {
            DB::statement('ALTER TABLE `inspectionacceptancereport` ADD UNIQUE KEY `ux_inspectionacceptancereport_iarnumber` (`IARNumber`)');
        }
    }

    public function down(): void
    {
        $drIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'deliveryreceipt')
            ->where('index_name', 'ux_deliveryreceipt_iarnumber')
            ->exists();

        if ($drIndexExists) {
            DB::statement('ALTER TABLE `deliveryreceipt` DROP INDEX `ux_deliveryreceipt_iarnumber`');
        }

        $iarIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'inspectionacceptancereport')
            ->where('index_name', 'ux_inspectionacceptancereport_iarnumber')
            ->exists();

        if ($iarIndexExists) {
            DB::statement('ALTER TABLE `inspectionacceptancereport` DROP INDEX `ux_inspectionacceptancereport_iarnumber`');
        }
    }
};
