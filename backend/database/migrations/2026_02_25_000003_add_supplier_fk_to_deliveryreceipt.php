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
                $table->integer('SupplierID')->unsigned()->nullable()->after('SupplierName');
            }
        });

        // backfill from the supplier table based on name match
        DB::table('deliveryreceipt as dr')
            ->join('supplier as s', 'dr.SupplierName', '=', 's.SupplierName')
            ->update(['dr.SupplierID' => DB::raw('s.SupplierID')]);

        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceipt', 'SupplierID')) {
                return;
            }

            // make sure there is an index and foreign key
            $table->index('SupplierID');
            $table->foreign('SupplierID')
                ->references('SupplierID')
                ->on('supplier')
                ->onDelete('set null');
        });

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
