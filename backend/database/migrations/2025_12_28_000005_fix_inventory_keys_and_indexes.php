<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // Make InventoryID and InventoryDetailID AUTO_INCREMENT
        // This assumes tables are empty or IDs won't conflict
        // Use raw statements because change() sometimes doesn't support auto increment change on older MySQL.
        try {
            DB::statement('ALTER TABLE `propertyinventory` MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT;');
        } catch (\Exception $e) {
            // ignore if not supported or already set
        }

        try {
            DB::statement('ALTER TABLE `propertyinventorydetail` MODIFY `InventoryDetailID` int(11) NOT NULL AUTO_INCREMENT;');
        } catch (\Exception $e) {
            // ignore if not supported or already set
        }

        // Ensure InventoryNumber column exists
        if (!Schema::hasColumn('propertyinventory', 'InventoryNumber')) {
            Schema::table('propertyinventory', function (Blueprint $table) {
                $table->string('InventoryNumber', 50)->nullable();
            });
        }

        // Add unique index on InventoryNumber (best-effort)
        try {
            DB::statement('ALTER TABLE `propertyinventory` ADD UNIQUE `inventory_inventorynumber_unique` (`InventoryNumber`);');
        } catch (\Exception $e) {
            // ignore if already exists
        }

        // Add FK: propertyinventory.IARID -> inspectionacceptancereport.IARID (best-effort)
        try {
            DB::statement('ALTER TABLE `propertyinventory` ADD CONSTRAINT `fk_propertyinventory_iar` FOREIGN KEY (`IARID`) REFERENCES `inspectionacceptancereport`(`IARID`) ON DELETE SET NULL;');
        } catch (\Exception $e) {
            // ignore if already exists or not supported
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::table('propertyinventory', function (Blueprint $table) {
            try { $table->dropForeign('fk_propertyinventory_iar'); } catch (\Exception $e) {}
            try { $table->dropUnique('inventory_inventorynumber_unique'); } catch (\Exception $e) {}
        });

        // Note: Reverting auto_increment changes is non-trivial and omitted intentionally
    }
};