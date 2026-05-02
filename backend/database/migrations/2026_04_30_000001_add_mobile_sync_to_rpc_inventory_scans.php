<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rpc_inventory_scans', function (Blueprint $table) {
            // Add client_uuid for idempotent mobile sync
            $table->char('client_uuid', 36)->nullable()->after('id');
            // Add device_id for audit trail (which mobile device made the scan)
            $table->string('device_id', 100)->nullable()->after('client_uuid');
            
            // Create unique index on (cycle_id, client_uuid) for idempotency
            $table->unique(['rpc_inventory_cycle_id', 'client_uuid'], 'rpc_scans_cycle_uuid_unique');
            
            // Add index on device_id for tracking
            $table->index('device_id', 'rpc_scans_device_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpc_inventory_scans', function (Blueprint $table) {
            $table->dropUnique('rpc_scans_cycle_uuid_unique');
            $table->dropIndex('rpc_scans_device_id_index');
            $table->dropColumn(['client_uuid', 'device_id']);
        });
    }
};
