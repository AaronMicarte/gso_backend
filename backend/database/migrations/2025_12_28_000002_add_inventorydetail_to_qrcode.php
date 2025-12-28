<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('qrcode', function (Blueprint $table) {
            if (!Schema::hasColumn('qrcode', 'InventoryDetailID')) {
                $table->unsignedInteger('InventoryDetailID')->nullable()->after('PropertyID');
                $table->index('InventoryDetailID');
                // Optional FK commented - enable when data integrity is ensured
                // $table->foreign('InventoryDetailID')->references('InventoryDetailID')->on('propertyinventorydetail')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('qrcode', function (Blueprint $table) {
            if (Schema::hasColumn('qrcode', 'InventoryDetailID')) {
                // if (Schema::hasColumn('qrcode', 'InventoryDetailID')) {
                $table->dropIndex(['InventoryDetailID']);
                $table->dropColumn('InventoryDetailID');
            }
        });
    }
};
