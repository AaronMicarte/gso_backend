<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('assignedproperty')) {
            Schema::table('assignedproperty', function (Blueprint $table) {
                if (
                    Schema::hasColumn('assignedproperty', 'InventoryDetailID')
                    && Schema::hasColumn('assignedproperty', 'UpdatedDate')
                    && Schema::hasColumn('assignedproperty', 'CreatedDate')
                    && Schema::hasColumn('assignedproperty', 'PropertyID')
                ) {
                    $table->index(
                        ['InventoryDetailID', 'UpdatedDate', 'CreatedDate', 'PropertyID'],
                        'assignedproperty_detail_date_property_idx'
                    );
                }
            });
        }

        if (Schema::hasTable('propertyissuancedetail')) {
            Schema::table('propertyissuancedetail', function (Blueprint $table) {
                if (
                    Schema::hasColumn('propertyissuancedetail', 'InventoryDetailID')
                    && Schema::hasColumn('propertyissuancedetail', 'deleted_at')
                    && Schema::hasColumn('propertyissuancedetail', 'IssuanceID')
                ) {
                    $table->index(
                        ['InventoryDetailID', 'deleted_at', 'IssuanceID'],
                        'pidet_inventory_deleted_issuance_idx'
                    );
                }
            });
        }

        if (Schema::hasTable('propertyinventorydetail')) {
            Schema::table('propertyinventorydetail', function (Blueprint $table) {
                if (
                    Schema::hasColumn('propertyinventorydetail', 'PropertyStatusID')
                    && Schema::hasColumn('propertyinventorydetail', 'CreatedDate')
                ) {
                    $table->index(
                        ['PropertyStatusID', 'CreatedDate'],
                        'pid_status_created_date_idx'
                    );
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('assignedproperty')) {
            Schema::table('assignedproperty', function (Blueprint $table) {
                $table->dropIndex('assignedproperty_detail_date_property_idx');
            });
        }

        if (Schema::hasTable('propertyissuancedetail')) {
            Schema::table('propertyissuancedetail', function (Blueprint $table) {
                $table->dropIndex('pidet_inventory_deleted_issuance_idx');
            });
        }

        if (Schema::hasTable('propertyinventorydetail')) {
            Schema::table('propertyinventorydetail', function (Blueprint $table) {
                $table->dropIndex('pid_status_created_date_idx');
            });
        }
    }
};
