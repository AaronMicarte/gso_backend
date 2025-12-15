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
        // Purchase Order Status
        if (!Schema::hasTable('purchaseorderstatus')) {
            Schema::create('purchaseorderstatus', function (Blueprint $table) {
                $table->id('POStatusID');
                $table->string('StatusName', 50);
            });
        }

        // Purchase Order
        if (!Schema::hasTable('purchaseorder')) {
            Schema::create('purchaseorder', function (Blueprint $table) {
                $table->id('PurchaseOrderID');
                $table->string('PONumber', 50)->unique();
                $table->unsignedBigInteger('PurchaseRequestID');
                $table->unsignedBigInteger('SupplierID');
                $table->unsignedBigInteger('POStatusID')->default(1); // 1: Pending
                $table->date('PODate');
                $table->string('ModeOfProcurement', 100)->nullable();
                $table->string('PlaceOfDelivery', 255)->nullable();
                $table->string('DateOfDelivery', 100)->nullable(); // Can be text like "15 days upon receipt"
                $table->string('DeliveryTerm', 100)->nullable(); // e.g., FOB Destination
                $table->string('PaymentTerm', 100)->nullable(); // e.g., N/30
                $table->decimal('TotalAmount', 18, 2)->default(0);
                $table->text('Remarks')->nullable();
                $table->unsignedBigInteger('CreatedBy');
                $table->dateTime('CreatedDate')->useCurrent();
                $table->dateTime('UpdatedDate')->nullable()->useCurrentOnUpdate();

                // Foreign Keys
                // Assuming purchaserequest and supplier tables exist. 
                // If they are using MyISAM or existing tables without constraints, we might skip strict foreign keys or use them if InnoDB.
                // I'll add them for data integrity if possible, but make them nullable if tables might be missing in migration order.
                // For now, I'll just index them.
                $table->index('PurchaseRequestID');
                $table->index('SupplierID');
                $table->index('POStatusID');
                $table->index('CreatedBy');
            });
        }

        // Purchase Order Details
        if (!Schema::hasTable('purchaseorderdetail')) {
            Schema::create('purchaseorderdetail', function (Blueprint $table) {
                $table->id('PurchaseOrderDetailID');
                $table->unsignedBigInteger('PurchaseOrderID');
                $table->string('ItemDescription', 255);
                $table->integer('Quantity');
                $table->decimal('UnitCost', 18, 2);
                $table->decimal('TotalCost', 18, 2);
                $table->text('Remarks')->nullable();

                $table->foreign('PurchaseOrderID')->references('PurchaseOrderID')->on('purchaseorder')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchaseorderdetail');
        Schema::dropIfExists('purchaseorder');
        Schema::dropIfExists('purchaseorderstatus');
    }
};
