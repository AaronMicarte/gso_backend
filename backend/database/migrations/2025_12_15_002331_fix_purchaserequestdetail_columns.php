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
        if (Schema::hasTable('purchaserequestdetail')) {
            Schema::table('purchaserequestdetail', function (Blueprint $table) {
                if (!Schema::hasColumn('purchaserequestdetail', 'ItemDescription')) {
                    $table->string('ItemDescription')->after('PurchaseRequestID');
                }
                if (Schema::hasColumn('purchaserequestdetail', 'PropertyTypeID')) {
                    $table->integer('PropertyTypeID')->nullable()->change();
                }
                if (Schema::hasColumn('purchaserequestdetail', 'UnitOfMeasureID')) {
                    $table->integer('UnitOfMeasureID')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchaserequestdetail', function (Blueprint $table) {
            $table->dropColumn('ItemDescription');
            $table->integer('PropertyTypeID')->nullable(false)->change();
            $table->integer('UnitOfMeasureID')->nullable(false)->change();
        });
    }
};
