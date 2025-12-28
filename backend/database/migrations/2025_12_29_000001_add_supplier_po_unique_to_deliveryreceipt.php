<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add unique index on (SupplierName, PONumber) so the same supplier cannot have duplicate POs.
     *
     * @return void
     */
    public function up(): void
    {
        \DB::statement("ALTER TABLE `deliveryreceipt` ADD UNIQUE KEY `ux_deliveryreceipt_supplier_po` (`SupplierName`, `PONumber`)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE `deliveryreceipt` DROP INDEX `ux_deliveryreceipt_supplier_po`");
    }
};
