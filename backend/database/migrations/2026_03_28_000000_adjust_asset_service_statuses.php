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

        DB::table('asset_service_records')
            ->whereIn('Status', ['under_review'])
            ->update(['Status' => 'work_approved']);

        DB::table('asset_service_records')
            ->whereIn('Status', ['closed', 'unserviceable'])
            ->update(['Status' => 'fixed']);

        if (Schema::hasTable('asset_service_records_details')) {
            DB::table('asset_service_records_details')
                ->whereIn('Status', ['under_review'])
                ->update(['Status' => 'work_approved']);

            DB::table('asset_service_records_details')
                ->whereIn('Status', ['closed', 'unserviceable'])
                ->update(['Status' => 'fixed']);
        }
    }

    public function down(): void
    {
        // No safe reversible mapping for status meanings.
    }
};
