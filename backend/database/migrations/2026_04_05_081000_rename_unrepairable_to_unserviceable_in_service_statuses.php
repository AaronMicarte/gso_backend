<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE asset_service_records SET Status='unserviceable' WHERE Status='unrepairable'");
        DB::statement("UPDATE asset_service_records_details SET Status='unserviceable' WHERE Status='unrepairable'");

        DB::statement("ALTER TABLE asset_service_records MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unserviceable') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE asset_service_records_details MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unserviceable') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("UPDATE asset_service_records SET Status='unrepairable' WHERE Status='unserviceable'");
        DB::statement("UPDATE asset_service_records_details SET Status='unrepairable' WHERE Status='unserviceable'");

        DB::statement("ALTER TABLE asset_service_records MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unrepairable') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE asset_service_records_details MODIFY COLUMN Status ENUM('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unrepairable') NOT NULL DEFAULT 'pending'");
    }
};
