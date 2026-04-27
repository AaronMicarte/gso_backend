<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('propertyissuancedetail')) {
            return;
        }

        if (!Schema::hasColumn('propertyissuancedetail', 'ris_item_id')) {
            Schema::table('propertyissuancedetail', function (Blueprint $table) {
                $table->unsignedBigInteger('ris_item_id')->nullable()->after('RequestDetailID');
                $table->index('ris_item_id', 'propertyissuancedetail_ris_item_id_index');
                $table->foreign('ris_item_id', 'propertyissuancedetail_ris_item_id_foreign')
                    ->references('id')
                    ->on('requisition_issue_slip_items')
                    ->nullOnDelete();
            });
        }

        DB::statement(
            "
            UPDATE propertyissuancedetail pid
            INNER JOIN propertyissuance pi
                ON pi.IssuanceID = pid.IssuanceID
               AND pi.deleted_at IS NULL
            SET pid.ris_item_id = (
                SELECT rii.id
                FROM requisition_issue_slips ris
                INNER JOIN requisition_issue_slip_items rii
                    ON rii.ris_id = ris.id
                   AND rii.deleted_at IS NULL
                   AND rii.asset_request_detail_id = pid.RequestDetailID
                WHERE ris.deleted_at IS NULL
                  AND ris.asset_request_id = pi.RequestID
                ORDER BY
                    (CASE WHEN rii.inventory_detail_id = pid.InventoryDetailID THEN 1 ELSE 0 END) DESC,
                    ris.id DESC,
                    rii.id ASC
                LIMIT 1
            )
            WHERE pid.deleted_at IS NULL
              AND pid.ris_item_id IS NULL
              AND pid.RequestDetailID IS NOT NULL
            "
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('propertyissuancedetail') || !Schema::hasColumn('propertyissuancedetail', 'ris_item_id')) {
            return;
        }

        Schema::table('propertyissuancedetail', function (Blueprint $table) {
            $table->dropForeign('propertyissuancedetail_ris_item_id_foreign');
            $table->dropIndex('propertyissuancedetail_ris_item_id_index');
            $table->dropColumn('ris_item_id');
        });
    }
};
