<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_service_records_details', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_service_records_details', 'AssignedSupplierID')) {
                $table->unsignedInteger('AssignedSupplierID')->nullable()->after('AssignedVendorID');
                $table->index('AssignedSupplierID', 'asset_service_records_details_assignedsupplierid_index');
                $table->foreign('AssignedSupplierID', 'asset_service_records_details_assignedsupplierid_foreign')
                    ->references('SupplierID')
                    ->on('supplier')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('asset_service_records_details', 'WarrantyClaimNumber')) {
                $table->string('WarrantyClaimNumber', 100)->nullable()->after('WarrantyOutcome');
            }

            if (!Schema::hasColumn('asset_service_records_details', 'WarrantyServiceCenter')) {
                $table->string('WarrantyServiceCenter', 255)->nullable()->after('WarrantyClaimNumber');
            }

            if (!Schema::hasColumn('asset_service_records_details', 'RepairOrderNumber')) {
                $table->string('RepairOrderNumber', 100)->nullable()->after('WarrantyServiceCenter');
            }
        });

        DB::statement("UPDATE asset_service_records_details SET HandlingDecision='vendor_repair' WHERE HandlingDecision='replacement_evaluation'");
        DB::statement("ALTER TABLE asset_service_records_details MODIFY HandlingDecision ENUM('warranty_claim','vendor_repair') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE asset_service_records_details MODIFY HandlingDecision ENUM('warranty_claim','vendor_repair','replacement_evaluation') NULL");

        Schema::table('asset_service_records_details', function (Blueprint $table) {
            if (Schema::hasColumn('asset_service_records_details', 'AssignedSupplierID')) {
                $table->dropForeign('asset_service_records_details_assignedsupplierid_foreign');
                $table->dropIndex('asset_service_records_details_assignedsupplierid_index');
                $table->dropColumn('AssignedSupplierID');
            }

            if (Schema::hasColumn('asset_service_records_details', 'WarrantyClaimNumber')) {
                $table->dropColumn('WarrantyClaimNumber');
            }

            if (Schema::hasColumn('asset_service_records_details', 'WarrantyServiceCenter')) {
                $table->dropColumn('WarrantyServiceCenter');
            }

            if (Schema::hasColumn('asset_service_records_details', 'RepairOrderNumber')) {
                $table->dropColumn('RepairOrderNumber');
            }
        });
    }
};
