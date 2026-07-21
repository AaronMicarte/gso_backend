<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // -----------------------------------------------------------------------
        // propertyinventorydetail — covering index for the filter + sort + stats
        // The filter query does:
        //   WHERE PropertyStatusID = ? AND PropertyTypeID = ?
        //   ORDER BY CreatedDate DESC
        //   COUNT(*) with SUM(CASE PropertyStatusID ...)
        // A covering index on (PropertyStatusID, PropertyTypeID, CreatedDate)
        // avoids a full table scan + filesort.
        // -----------------------------------------------------------------------
        if (Schema::hasTable('propertyinventorydetail')) {
            Schema::table('propertyinventorydetail', function (Blueprint $table) {
                $cols = DB::select("SHOW INDEX FROM `propertyinventorydetail`");
                $existingNames = array_column($cols, 'Key_name');

                if (
                    !in_array('pid_status_type_created_covering_idx', $existingNames)
                    && Schema::hasColumn('propertyinventorydetail', 'PropertyStatusID')
                    && Schema::hasColumn('propertyinventorydetail', 'PropertyTypeID')
                    && Schema::hasColumn('propertyinventorydetail', 'InventoryID')
                    && Schema::hasColumn('propertyinventorydetail', 'CreatedDate')
                ) {
                    $table->index(
                        ['PropertyStatusID', 'PropertyTypeID', 'InventoryID', 'CreatedDate'],
                        'pid_status_type_created_covering_idx'
                    );
                }
            });
        }

        // -----------------------------------------------------------------------
        // propertyissuancedetail — the enrichment correlated subqueries hit this
        // table with: WHERE InventoryDetailID = X AND deleted_at IS NULL
        // A composite index lets the DB satisfy the WHERE cheaply.
        // -----------------------------------------------------------------------
        if (Schema::hasTable('propertyissuancedetail')) {
            Schema::table('propertyissuancedetail', function (Blueprint $table) {
                $cols = DB::select("SHOW INDEX FROM `propertyissuancedetail`");
                $existingNames = array_column($cols, 'Key_name');

                if (
                    !in_array('pidet_invdetail_deleted_idx', $existingNames)
                    && Schema::hasColumn('propertyissuancedetail', 'InventoryDetailID')
                    && Schema::hasColumn('propertyissuancedetail', 'deleted_at')
                ) {
                    $table->index(
                        ['InventoryDetailID', 'deleted_at'],
                        'pidet_invdetail_deleted_idx'
                    );
                }
            });
        }

        // -----------------------------------------------------------------------
        // assignedproperty — enrichment hits: WHERE InventoryDetailID = X
        //   ORDER BY COALESCE(UpdatedDate,CreatedDate) DESC, PropertyID DESC
        // -----------------------------------------------------------------------
        if (Schema::hasTable('assignedproperty')) {
            Schema::table('assignedproperty', function (Blueprint $table) {
                $cols = DB::select("SHOW INDEX FROM `assignedproperty`");
                $existingNames = array_column($cols, 'Key_name');

                if (
                    !in_array('ap_invdetail_propid_idx', $existingNames)
                    && Schema::hasColumn('assignedproperty', 'InventoryDetailID')
                    && Schema::hasColumn('assignedproperty', 'PropertyID')
                ) {
                    $table->index(
                        ['InventoryDetailID', 'PropertyID'],
                        'ap_invdetail_propid_idx'
                    );
                }
            });
        }

        // -----------------------------------------------------------------------
        // propertyborrowitems — enrichment hits:
        //   WHERE InventoryDetailID = X AND deleted_at IS NULL
        // -----------------------------------------------------------------------
        if (Schema::hasTable('propertyborrowitems')) {
            Schema::table('propertyborrowitems', function (Blueprint $table) {
                $cols = DB::select("SHOW INDEX FROM `propertyborrowitems`");
                $existingNames = array_column($cols, 'Key_name');

                if (
                    !in_array('pbi_invdetail_deleted_idx', $existingNames)
                    && Schema::hasColumn('propertyborrowitems', 'InventoryDetailID')
                    && Schema::hasColumn('propertyborrowitems', 'deleted_at')
                ) {
                    $table->index(
                        ['InventoryDetailID', 'deleted_at'],
                        'pbi_invdetail_deleted_idx'
                    );
                }
            });
        }

        // -----------------------------------------------------------------------
        // property_issuance_assignments — enrichment hits:
        //   WHERE issuance_detail_id = X ORDER BY id DESC
        // -----------------------------------------------------------------------
        if (Schema::hasTable('property_issuance_assignments')) {
            Schema::table('property_issuance_assignments', function (Blueprint $table) {
                $cols = DB::select("SHOW INDEX FROM `property_issuance_assignments`");
                $existingNames = array_column($cols, 'Key_name');

                if (
                    !in_array('pia_issuance_detail_id_idx', $existingNames)
                    && Schema::hasColumn('property_issuance_assignments', 'issuance_detail_id')
                ) {
                    $table->index('issuance_detail_id', 'pia_issuance_detail_id_idx');
                }
            });
        }

        // -----------------------------------------------------------------------
        // qrcode — enrichment hits:
        //   WHERE InventoryDetailID = X ORDER BY DateGenerated DESC, QRCodeID DESC
        //   This supplements the existing single-col index with the sort columns.
        // -----------------------------------------------------------------------
        if (Schema::hasTable('qrcode')) {
            Schema::table('qrcode', function (Blueprint $table) {
                $cols = DB::select("SHOW INDEX FROM `qrcode`");
                $existingNames = array_column($cols, 'Key_name');

                if (
                    !in_array('qrcode_invdetail_qrcodeid_idx', $existingNames)
                    && Schema::hasColumn('qrcode', 'InventoryDetailID')
                    && Schema::hasColumn('qrcode', 'QRCodeID')
                ) {
                    $table->index(
                        ['InventoryDetailID', 'QRCodeID'],
                        'qrcode_invdetail_qrcodeid_idx'
                    );
                }
            });
        }
    }

    public function down()
    {
        $drops = [
            'propertyinventorydetail' => 'pid_status_type_created_covering_idx',
            'propertyissuancedetail'  => 'pidet_invdetail_deleted_idx',
            'assignedproperty'        => 'ap_invdetail_propid_idx',
            'propertyborrowitems'     => 'pbi_invdetail_deleted_idx',
            'property_issuance_assignments' => 'pia_issuance_detail_id_idx',
            'qrcode'                  => 'qrcode_invdetail_qrcodeid_idx',
        ];

        foreach ($drops as $table => $index) {
            if (Schema::hasTable($table)) {
                try {
                    Schema::table($table, fn ($t) => $t->dropIndex($index));
                } catch (\Exception $e) {
                    // Index may not exist — safe to ignore.
                }
            }
        }
    }
};
