<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Disable FK checks to allow structural changes
        Schema::disableForeignKeyConstraints();

        // 1. Fix PropertyInventory (Header)
        Schema::table('propertyinventory', function (Blueprint $table) {
            // Add InventoryNumber if missing
            if (!Schema::hasColumn('propertyinventory', 'InventoryNumber')) {
                $table->string('InventoryNumber', 50)->after('InventoryID')->nullable();
            }
            
            // Make columns nullable that we are effectively "deprecating" from the header
            // We keep them nullable to avoid losing data if any exists, but we won't use them for new records
            $table->integer('PropertyTypeID')->nullable()->change();
            $table->integer('UnitOfMeasureID')->nullable()->change();
            $table->decimal('UnitCost', 18, 2)->nullable()->change();
            $table->string('ModelNumber', 100)->nullable()->change();
        });

        // 2. Fix PropertyInventoryDetail (Items)
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            // Add missing columns for item definition
            if (!Schema::hasColumn('propertyinventorydetail', 'PropertyTypeID')) {
                $table->integer('PropertyTypeID')->after('InventoryID')->nullable(); // Should be required eventually
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'UnitOfMeasureID')) {
                $table->integer('UnitOfMeasureID')->after('Quantity')->nullable();
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'UnitCost')) {
                $table->decimal('UnitCost', 18, 2)->after('UnitOfMeasureID')->nullable();
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'TotalCost')) {
                $table->decimal('TotalCost', 18, 2)->after('UnitCost')->nullable();
            }
            
            // Add Traceability columns
            if (!Schema::hasColumn('propertyinventorydetail', 'SourceIARDetailID')) {
                $table->integer('SourceIARDetailID')->nullable()->after('TotalCost');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                $table->integer('SourceDRDetailID')->nullable()->after('SourceIARDetailID');
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        // We generally don't reverse this as it's a structural fix for a broken schema
    }
};
