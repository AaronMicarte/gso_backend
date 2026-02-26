<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $legacyInvoice = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplier_invoice'");
        if (!empty($legacyInvoice)) {
            DB::statement('ALTER TABLE `deliveryreceipt` DROP INDEX `ux_deliveryreceipt_supplier_invoice`');
        }

        $legacyPo = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplier_po'");
        if (!empty($legacyPo)) {
            DB::statement('ALTER TABLE `deliveryreceipt` DROP INDEX `ux_deliveryreceipt_supplier_po`');
        }

        if (Schema::hasColumn('deliveryreceipt', 'SupplierID')) {
            $supplierInvoice = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplierid_invoice'");
            if (empty($supplierInvoice)) {
                Schema::table('deliveryreceipt', function (Blueprint $table) {
                    $table->unique(['SupplierID', 'InvoiceNumber'], 'ux_deliveryreceipt_supplierid_invoice');
                });
            }

            $supplierPo = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplierid_po'");
            if (empty($supplierPo)) {
                Schema::table('deliveryreceipt', function (Blueprint $table) {
                    $table->unique(['SupplierID', 'PONumber'], 'ux_deliveryreceipt_supplierid_po');
                });
            }
        }
    }

    public function down(): void
    {
        $supplierInvoice = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplierid_invoice'");
        if (!empty($supplierInvoice)) {
            DB::statement('ALTER TABLE `deliveryreceipt` DROP INDEX `ux_deliveryreceipt_supplierid_invoice`');
        }

        $supplierPo = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplierid_po'");
        if (!empty($supplierPo)) {
            DB::statement('ALTER TABLE `deliveryreceipt` DROP INDEX `ux_deliveryreceipt_supplierid_po`');
        }

        $legacyInvoice = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplier_invoice'");
        if (empty($legacyInvoice)) {
            Schema::table('deliveryreceipt', function (Blueprint $table) {
                $table->unique(['SupplierName', 'InvoiceNumber'], 'ux_deliveryreceipt_supplier_invoice');
            });
        }

        $legacyPo = DB::select("SHOW INDEX FROM `deliveryreceipt` WHERE Key_name = 'ux_deliveryreceipt_supplier_po'");
        if (empty($legacyPo)) {
            Schema::table('deliveryreceipt', function (Blueprint $table) {
                $table->unique(['SupplierName', 'PONumber'], 'ux_deliveryreceipt_supplier_po');
            });
        }
    }
};
