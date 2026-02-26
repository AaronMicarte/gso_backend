<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceipt', 'SupplierID')) {
                // add integer column to hold the FK
                $table->integer('SupplierID')->nullable()->after('SupplierName');
            }
        });

        // Ensure SupplierID uses signed INT to match supplier.SupplierID
        if (Schema::hasColumn('deliveryreceipt', 'SupplierID')) {
            DB::statement('ALTER TABLE `deliveryreceipt` MODIFY `SupplierID` INT NULL');
        }

        // backfill from the supplier table based on name match
        DB::table('deliveryreceipt as dr')
            ->join('supplier as s', 'dr.SupplierName', '=', 's.SupplierName')
            ->update(['dr.SupplierID' => DB::raw('s.SupplierID')]);

        if (Schema::hasColumn('deliveryreceipt', 'SupplierID')) {
            $indexExists = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'deliveryreceipt_supplierid_index'");
            if (empty($indexExists)) {
                Schema::table('deliveryreceipt', function (Blueprint $table) {
                    $table->index('SupplierID', 'deliveryreceipt_supplierid_index');
                });
            }

            $fkExists = DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'deliveryreceipt'
                   AND COLUMN_NAME = 'SupplierID'
                   AND REFERENCED_TABLE_NAME = 'supplier'"
            );

            if (empty($fkExists)) {
                Schema::table('deliveryreceipt', function (Blueprint $table) {
                    $table->foreign('SupplierID')
                        ->references('SupplierID')
                        ->on('supplier')
                        ->onDelete('set null');
                });
            }
        }

        // optional: if you wish to remove the old text column uncomment below
        // Schema::table('deliveryreceipt', function (Blueprint $table) {
        //     if (Schema::hasColumn('deliveryreceipt', 'SupplierName')) {
        //         $table->dropColumn('SupplierName');
        //     }
        // });
    }

    public function down(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceipt', 'SupplierID')) {
                // drop FK/index first
                $table->dropForeign(['SupplierID']);
                $table->dropIndex(['SupplierID']);
                $table->dropColumn('SupplierID');
            }

            // restore SupplierName column if needed?
            if (!Schema::hasColumn('deliveryreceipt', 'SupplierName')) {
                $table->string('SupplierName', 255)->nullable()->after('DRNumber');
            }
        });
    }
};
