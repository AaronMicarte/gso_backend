<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('propertyinventorydetail')) {
            Schema::table('propertyinventorydetail', function (Blueprint $table) {
                if (!Schema::hasColumn('propertyinventorydetail', 'PropertyTypeID')) return;
                $table->index('PropertyTypeID', 'pid_property_type_idx');
                if (!Schema::hasColumn('propertyinventorydetail', 'PropertyStatusID')) return;
                $table->index('PropertyStatusID', 'pid_property_status_idx');
                if (!Schema::hasColumn('propertyinventorydetail', 'InventoryID')) return;
                $table->index('InventoryID', 'pid_inventory_id_idx');
                if (Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                    $table->index('SourceDRDetailID', 'pid_source_dr_detail_idx');
                }
                // Composite index often helps when filtering by both
                $table->index(['PropertyTypeID', 'PropertyStatusID'], 'pid_type_status_idx');
            });
        }

        if (Schema::hasTable('propertyclassification') && Schema::hasColumn('propertyclassification', 'DocumentType')) {
            Schema::table('propertyclassification', function (Blueprint $table) {
                $table->index('DocumentType', 'pclassification_document_type_idx');
            });
        }

        if (Schema::hasTable('deliveryreceiptdetail') && Schema::hasColumn('deliveryreceiptdetail', 'DRDetailID')) {
            Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
                $table->index('DRDetailID', 'drdetail_id_idx');
            });
        }

        if (Schema::hasTable('qrcode') && Schema::hasColumn('qrcode', 'InventoryDetailID')) {
            Schema::table('qrcode', function (Blueprint $table) {
                $table->index('InventoryDetailID', 'qrcode_inventory_detail_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('propertyinventorydetail')) {
            Schema::table('propertyinventorydetail', function (Blueprint $table) {
                $table->dropIndex('pid_property_type_idx');
                $table->dropIndex('pid_property_status_idx');
                $table->dropIndex('pid_inventory_id_idx');
                if (Schema::hasColumn('propertyinventorydetail', 'SourceDRDetailID')) {
                    $table->dropIndex('pid_source_dr_detail_idx');
                }
                $table->dropIndex('pid_type_status_idx');
            });
        }

        if (Schema::hasTable('propertyclassification') && Schema::hasColumn('propertyclassification', 'DocumentType')) {
            Schema::table('propertyclassification', function (Blueprint $table) {
                $table->dropIndex('pclassification_document_type_idx');
            });
        }

        if (Schema::hasTable('deliveryreceiptdetail') && Schema::hasColumn('deliveryreceiptdetail', 'DRDetailID')) {
            Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
                $table->dropIndex('drdetail_id_idx');
            });
        }

        if (Schema::hasTable('qrcode') && Schema::hasColumn('qrcode', 'InventoryDetailID')) {
            Schema::table('qrcode', function (Blueprint $table) {
                $table->dropIndex('qrcode_inventory_detail_idx');
            });
        }
    }
};
