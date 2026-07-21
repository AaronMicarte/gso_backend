<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('qrcode')) {
            Schema::table('qrcode', function (Blueprint $table) {
                if (Schema::hasColumn('qrcode', 'InventoryDetailID') && Schema::hasColumn('qrcode', 'DateGenerated')) {
                    $table->index(['InventoryDetailID', 'DateGenerated'], 'qrcode_inventory_detail_date_generated_idx');
                }
            });
        }

        if (Schema::hasTable('propertyborrowitems')) {
            Schema::table('propertyborrowitems', function (Blueprint $table) {
                if (Schema::hasColumn('propertyborrowitems', 'InventoryDetailID') && Schema::hasColumn('propertyborrowitems', 'BorrowID')) {
                    $table->index(['InventoryDetailID', 'BorrowID'], 'propertyborrowitems_inventorydetail_borrowid_idx');
                }
            });
        }

        if (Schema::hasTable('propertyinventorydetail')) {
            Schema::table('propertyinventorydetail', function (Blueprint $table) {
                if (Schema::hasColumn('propertyinventorydetail', 'created_at')) {
                    $table->index('created_at', 'pid_created_at_idx');
                }
                if (Schema::hasColumn('propertyinventorydetail', 'CreatedDate')) {
                    $table->index('CreatedDate', 'pid_created_date_idx');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('qrcode')) {
            Schema::table('qrcode', function (Blueprint $table) {
                if (Schema::hasColumn('qrcode', 'InventoryDetailID') && Schema::hasColumn('qrcode', 'DateGenerated')) {
                    $table->dropIndex('qrcode_inventory_detail_date_generated_idx');
                }
            });
        }

        if (Schema::hasTable('propertyborrowitems')) {
            Schema::table('propertyborrowitems', function (Blueprint $table) {
                if (Schema::hasColumn('propertyborrowitems', 'InventoryDetailID') && Schema::hasColumn('propertyborrowitems', 'BorrowID')) {
                    $table->dropIndex('propertyborrowitems_inventorydetail_borrowid_idx');
                }
            });
        }

        if (Schema::hasTable('propertyinventorydetail')) {
            Schema::table('propertyinventorydetail', function (Blueprint $table) {
                if (Schema::hasColumn('propertyinventorydetail', 'created_at')) {
                    $table->dropIndex('pid_created_at_idx');
                }
                if (Schema::hasColumn('propertyinventorydetail', 'CreatedDate')) {
                    $table->dropIndex('pid_created_date_idx');
                }
            });
        }
    }
};
