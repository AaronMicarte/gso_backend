<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpc_inventory_audit_logs', function (Blueprint $table) {
            $table->bigIncrements('AuditID');
            $table->unsignedBigInteger('rpc_inventory_cycle_id');
            $table->unsignedBigInteger('rpc_inventory_scan_id')->nullable();
            $table->string('action', 60);
            $table->string('source', 20)->default('web');
            $table->integer('performed_by_user_id');
            $table->string('performed_by_device_id', 100)->nullable();
            $table->unsignedBigInteger('inventory_detail_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('property_number', 100)->nullable();
            $table->integer('condition_id')->nullable();
            $table->integer('previous_condition_id')->nullable();
            $table->string('previous_remarks', 255)->nullable();
            $table->string('new_remarks', 255)->nullable();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['rpc_inventory_cycle_id', 'created_at'], 'rpc_inventory_audit_cycle_created_idx');
            $table->index(['rpc_inventory_cycle_id', 'action'], 'rpc_inventory_audit_cycle_action_idx');
            $table->index('rpc_inventory_scan_id', 'rpc_inventory_audit_scan_idx');
            $table->index('performed_by_user_id', 'rpc_inventory_audit_user_idx');

            $table->foreign('rpc_inventory_cycle_id')
                ->references('id')
                ->on('rpc_inventory_cycles')
                ->onDelete('cascade');

            $table->foreign('rpc_inventory_scan_id')
                ->references('id')
                ->on('rpc_inventory_scans')
                ->nullOnDelete();

            $table->foreign('performed_by_user_id')
                ->references('UserID')
                ->on('user')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpc_inventory_audit_logs');
    }
};
