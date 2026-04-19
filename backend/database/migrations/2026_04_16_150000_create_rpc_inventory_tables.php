<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpc_inventory_cycles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cycle_label', 50)->unique();
            $table->unsignedSmallInteger('start_year');
            $table->unsignedSmallInteger('end_year');
            $table->string('status', 20)->default('active');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->integer('created_by_user_id');
            $table->integer('updated_by_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['start_year', 'end_year'], 'rpc_inventory_cycles_year_unique');
            $table->index('status');

            $table->foreign('created_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
            $table->foreign('updated_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
        });

        Schema::create('rpc_inventory_scans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rpc_inventory_cycle_id');
            $table->unsignedBigInteger('inventory_detail_id');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('rpc_type', 10);
            $table->unsignedInteger('property_classification_id')->nullable();
            // `condition.ConditionID` is signed INT in the legacy schema.
            $table->integer('condition_id');
            $table->dateTime('scanned_at');
            $table->integer('scanned_by_user_id');

            $table->unsignedInteger('end_user_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->string('article', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('property_number', 100)->nullable();
            $table->string('unit_of_measure', 50)->nullable();
            $table->decimal('unit_value', 15, 2)->nullable();
            $table->unsignedInteger('quantity_per_card')->default(1);
            $table->unsignedInteger('quantity_per_count')->default(1);
            $table->string('remarks', 255)->nullable();
            $table->string('accountable_officer', 255)->nullable();

            $table->timestamps();

            $table->unique(['rpc_inventory_cycle_id', 'inventory_detail_id'], 'rpc_inventory_cycle_detail_unique');
            $table->index(['rpc_inventory_cycle_id', 'rpc_type'], 'rpc_inventory_cycle_type_idx');
            $table->index('property_number');

            $table->foreign('rpc_inventory_cycle_id')->references('id')->on('rpc_inventory_cycles')->onDelete('cascade');
            $table->foreign('condition_id')->references('ConditionID')->on('condition')->onDelete('restrict');
            $table->foreign('scanned_by_user_id')->references('UserID')->on('user')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpc_inventory_scans');
        Schema::dropIfExists('rpc_inventory_cycles');
    }
};
