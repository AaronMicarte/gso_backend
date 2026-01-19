<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'waiting_for_issuance' to assetrequest.Status enum
        DB::statement("ALTER TABLE `assetrequest` MODIFY `Status` ENUM('pending','approved','waiting_for_issuance','rejected','issued','received','cancelled') NOT NULL DEFAULT 'pending'");

        // Some older migrations created `asset_requests` (plural) in other branches - handle gracefully
        if (Schema::hasTable('asset_requests')) {
            DB::statement("ALTER TABLE `asset_requests` MODIFY `Status` ENUM('pending','approved','waiting_for_issuance','rejected','issued','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        // Revert enum to previous values (remove waiting_for_issuance)
        DB::statement("ALTER TABLE `assetrequest` MODIFY `Status` ENUM('pending','approved','rejected','issued','received','cancelled') NOT NULL DEFAULT 'pending'");

        if (Schema::hasTable('asset_requests')) {
            DB::statement("ALTER TABLE `asset_requests` MODIFY `Status` ENUM('pending','approved','rejected','issued','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
