<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        // Drop existing composite unique index if present, then add unique on start_year only.
        // Use raw statements to be robust across MySQL versions and existing index names.
        DB::statement('ALTER TABLE `rpc_inventory_cycles` DROP INDEX IF EXISTS `rpc_inventory_cycles_year_unique`');
        DB::statement('ALTER TABLE `rpc_inventory_cycles` DROP INDEX IF EXISTS `rpc_inventory_cycles_start_year_unique`');
        DB::statement('ALTER TABLE `rpc_inventory_cycles` ADD UNIQUE KEY `rpc_inventory_cycles_start_year_unique` (`start_year`)');
        // Migrate existing cycle_label values from ranges like "Inventory Cycle 2026-2027"
        // to calendar-year-only labels like "Inventory Cycle 2026".
        DB::statement("UPDATE `rpc_inventory_cycles` SET `cycle_label` = CONCAT('Inventory Cycle ', `start_year`) WHERE `cycle_label` LIKE '%-%'");
    }

    public function down(): void
    {
        // Revert to composite unique index (start_year, end_year)
        DB::statement('ALTER TABLE `rpc_inventory_cycles` DROP INDEX IF EXISTS `rpc_inventory_cycles_start_year_unique`');
        DB::statement('ALTER TABLE `rpc_inventory_cycles` ADD UNIQUE KEY `rpc_inventory_cycles_year_unique` (`start_year`,`end_year`)');
    }
};
