<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_service_records')) {
            return;
        }

        // Update enums for columns to include fixed before the data transition.
        DB::statement("ALTER TABLE asset_service_records MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unserviceable') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE asset_service_records_details MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unserviceable') NOT NULL DEFAULT 'pending'");

        // Convert existing legacy statuses to the new fixed status.
        DB::table('asset_service_records')->where('Status', 'verification')->update(['Status' => 'fixed']);
        DB::table('asset_service_records_details')->where('Status', 'verification')->update(['Status' => 'fixed']);

        // Remove verification value from enum now that all rows are migrated.
        DB::statement("ALTER TABLE asset_service_records MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','closed','unserviceable') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE asset_service_records_details MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','closed','unserviceable') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('asset_service_records')) {
            return;
        }

        DB::statement("ALTER TABLE asset_service_records MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','verification','closed','unserviceable') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE asset_service_records_details MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','verification','closed','unserviceable') NOT NULL DEFAULT 'pending'");

        DB::table('asset_service_records')->where('Status', 'fixed')->update(['Status' => 'verification']);
        DB::table('asset_service_records_details')->where('Status', 'fixed')->update(['Status' => 'verification']);
    }
};