<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add unique index on (SupplierName, InvoiceNumber) so the same supplier cannot have duplicate invoice numbers.
     * This is a logical requirement (invoices should be unique per supplier). This migration is safe with existing NULLs.
     *
     * @return void
     */
    public function up(): void
    {
        // Use raw statement to ensure index name and columns are added precisely
        \DB::statement("ALTER TABLE `deliveryreceipt` ADD UNIQUE KEY `ux_deliveryreceipt_supplier_invoice` (`SupplierName`, `InvoiceNumber`)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE `deliveryreceipt` DROP INDEX `ux_deliveryreceipt_supplier_invoice`");
    }
};
