<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update deliveryreceiptdetail
        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            $table->string('Brand')->nullable()->after('ItemDescription');
            $table->string('ModelNumber')->nullable()->after('Brand');
            $table->string('SerialNumber')->nullable()->after('ModelNumber');
        });

        // 2. Update iardetail
        Schema::table('iardetail', function (Blueprint $table) {
            // These fields allow the inspector to correct/verify the data from the DR
            $table->string('VerifiedBrand')->nullable()->after('QuantityRejected');
            $table->string('VerifiedModel')->nullable()->after('VerifiedBrand');
            $table->string('VerifiedSerial')->nullable()->after('VerifiedModel');
        });

        // 3. Update propertyinventorydetail
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            $table->string('Brand')->nullable()->after('PropertyTypeID');
            $table->string('ModelNumber')->nullable()->after('Brand');
            // SerialNumber already exists
        });
    }

    public function down(): void
    {
        Schema::table('propertyinventorydetail', function (Blueprint $table) {
            $table->dropColumn(['Brand', 'ModelNumber']);
        });

        Schema::table('iardetail', function (Blueprint $table) {
            $table->dropColumn(['VerifiedBrand', 'VerifiedModel', 'VerifiedSerial']);
        });

        Schema::table('deliveryreceiptdetail', function (Blueprint $table) {
            $table->dropColumn(['Brand', 'ModelNumber', 'SerialNumber']);
        });
    }
};
