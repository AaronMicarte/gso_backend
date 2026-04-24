<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propertytransferdetails', function (Blueprint $table) {
            if (!Schema::hasColumn('propertytransferdetails', 'ApprovedQuantity')) {
                $table->unsignedInteger('ApprovedQuantity')->nullable()->after('Quantity');
            }
        });

        Schema::table('propertyborrowitems', function (Blueprint $table) {
            if (!Schema::hasColumn('propertyborrowitems', 'ApprovedQuantity')) {
                $table->unsignedInteger('ApprovedQuantity')->nullable()->after('Quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('propertytransferdetails', function (Blueprint $table) {
            if (Schema::hasColumn('propertytransferdetails', 'ApprovedQuantity')) {
                $table->dropColumn('ApprovedQuantity');
            }
        });

        Schema::table('propertyborrowitems', function (Blueprint $table) {
            if (Schema::hasColumn('propertyborrowitems', 'ApprovedQuantity')) {
                $table->dropColumn('ApprovedQuantity');
            }
        });
    }
};