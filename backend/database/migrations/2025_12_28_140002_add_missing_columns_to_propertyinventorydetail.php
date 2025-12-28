<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            if (!Schema::hasColumn('propertyinventorydetail', 'PropertyTypeID')) {
                $table->integer('PropertyTypeID')->nullable()->after('InventoryID');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'Brand')) {
                $table->string('Brand', 100)->nullable()->after('ConditionID');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'ModelNumber')) {
                $table->string('ModelNumber', 100)->nullable()->after('Brand');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'UnitOfMeasureID')) {
                $table->integer('UnitOfMeasureID')->nullable()->after('PropertyStatusID');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'UnitCost')) {
                $table->decimal('UnitCost', 18, 2)->nullable()->after('UnitOfMeasureID');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'TotalCost')) {
                $table->decimal('TotalCost', 18, 2)->nullable()->after('UnitCost');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'SourceIARDetailID')) {
                $table->integer('SourceIARDetailID')->nullable()->after('TotalCost');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                $table->integer('SourceDRDetailID')->nullable()->after('SourceIARDetailID');
            }
            if (!Schema::hasColumn('propertyinventorydetail', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable();
            }
        });
        
        // Add foreign keys separately after columns exist
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            if (Schema::hasColumn('propertyinventorydetail', 'PropertyTypeID')) {
                try {
                    $table->foreign('PropertyTypeID')->references('PropertyTypeID')->on('propertytype')->nullOnDelete();
                } catch (\Exception $e) {
                    // FK might already exist
                }
            }
            if (Schema::hasColumn('propertyinventorydetail', 'UnitOfMeasureID')) {
                try {
                    $table->foreign('UnitOfMeasureID')->references('UnitOfMeasureID')->on('unitofmeasure')->nullOnDelete();
                } catch (\Exception $e) {
                    // FK might already exist
                }
            }
            if (Schema::hasColumn('propertyinventorydetail', 'SourceIARDetailID')) {
                try {
                    $table->foreign('SourceIARDetailID')->references('IARDetailID')->on('iardetail')->nullOnDelete();
                } catch (\Exception $e) {
                    // FK might already exist
                }
            }
            if (Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                try {
                    $table->foreign('SourceDRDetailID')->references('DRDetailID')->on('deliveryreceiptdetail')->nullOnDelete();
                } catch (\Exception $e) {
                    // FK might already exist
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            if (Schema::hasColumn('propertyinventorydetail', 'SourceIARDetailID')) {
                $table->dropForeign(['SourceIARDetailID']);
                $table->dropColumn('SourceIARDetailID');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                $table->dropForeign(['SourceDRDetailID']);
                $table->dropColumn('SourceDRDetailID');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'PropertyTypeID')) {
                $table->dropForeign(['PropertyTypeID']);
                $table->dropColumn('PropertyTypeID');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'UnitOfMeasureID')) {
                $table->dropForeign(['UnitOfMeasureID']);
                $table->dropColumn('UnitOfMeasureID');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'Brand')) {
                $table->dropColumn('Brand');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'ModelNumber')) {
                $table->dropColumn('ModelNumber');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'UnitCost')) {
                $table->dropColumn('UnitCost');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'TotalCost')) {
                $table->dropColumn('TotalCost');
            }
            if (Schema::hasColumn('propertyinventorydetail', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
