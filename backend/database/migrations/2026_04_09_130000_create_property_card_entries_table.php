<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_card_entries', function (Blueprint $table) {
            $table->bigIncrements('EntryID');
            // propertyinventorydetail.InventoryDetailID is int(11) signed in this codebase
            $table->integer('InventoryDetailID');
            $table->dateTime('EntryDate');
            $table->string('EntryType', 40);
            $table->unsignedInteger('ReceiptQty')->default(0);
            $table->unsignedInteger('IssueQty')->default(0);
            $table->string('ReferenceNo', 100)->nullable();
            $table->string('OfficeOfficer', 255)->nullable();
            $table->decimal('UnitCost', 15, 2)->nullable();
            $table->decimal('Amount', 15, 2)->nullable();
            $table->text('Remarks')->nullable();
            $table->string('SourceType', 40);
            $table->unsignedBigInteger('SourceID')->nullable();
            $table->string('SourceKey', 100)->unique();
            $table->unsignedBigInteger('CreatedBy')->nullable();
            $table->dateTime('CreatedAt');

            $table->index(['InventoryDetailID', 'EntryDate'], 'idx_pc_entries_detail_date');
            $table->index(['SourceType', 'SourceID'], 'idx_pc_entries_source');
            $table->foreign('InventoryDetailID', 'fk_pc_entries_inventory_detail')
                ->references('InventoryDetailID')
                ->on('propertyinventorydetail')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_card_entries');
    }
};
