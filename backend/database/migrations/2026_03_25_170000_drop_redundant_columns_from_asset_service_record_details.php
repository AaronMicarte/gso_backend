<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_service_records_details', function (Blueprint $table) {
            if (Schema::hasColumn('asset_service_records_details', 'WarrantyCovered')) {
                $table->dropColumn('WarrantyCovered');
            }

            if (Schema::hasColumn('asset_service_records_details', 'WarrantyOutcome')) {
                $table->dropColumn('WarrantyOutcome');
            }

            if (Schema::hasColumn('asset_service_records_details', 'WarrantyServiceCenter')) {
                $table->dropColumn('WarrantyServiceCenter');
            }

            if (Schema::hasColumn('asset_service_records_details', 'CompletionNotes')) {
                $table->dropColumn('CompletionNotes');
            }

            if (Schema::hasColumn('asset_service_records_details', 'VerificationNotes')) {
                $table->dropColumn('VerificationNotes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_service_records_details', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_service_records_details', 'WarrantyCovered')) {
                $table->boolean('WarrantyCovered')->default(false)->after('ActualCost');
            }

            if (!Schema::hasColumn('asset_service_records_details', 'WarrantyOutcome')) {
                $table->string('WarrantyOutcome', 255)->nullable()->after('WarrantyCovered');
            }

            if (!Schema::hasColumn('asset_service_records_details', 'WarrantyServiceCenter')) {
                $table->string('WarrantyServiceCenter', 255)->nullable()->after('WarrantyClaimNumber');
            }

            if (!Schema::hasColumn('asset_service_records_details', 'CompletionNotes')) {
                $table->text('CompletionNotes')->nullable()->after('RepairOrderNumber');
            }

            if (!Schema::hasColumn('asset_service_records_details', 'VerificationNotes')) {
                $table->text('VerificationNotes')->nullable()->after('CompletionNotes');
            }
        });
    }
};
