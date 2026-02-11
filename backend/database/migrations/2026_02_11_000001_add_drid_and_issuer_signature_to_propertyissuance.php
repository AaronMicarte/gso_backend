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
        Schema::table('propertyissuance', function (Blueprint $table) {
            if (!Schema::hasColumn('propertyissuance', 'DRID')) {
                $table->unsignedInteger('DRID')->nullable()->after('RequestID');
                $table->index('DRID');
            }

            if (!Schema::hasColumn('propertyissuance', 'IssuerSignature')) {
                $table->text('IssuerSignature')->nullable()->after('IssuedBy');
            }

            if (!Schema::hasColumn('propertyissuance', 'IssuerSignatureSource')) {
                $table->string('IssuerSignatureSource', 20)->nullable()->after('IssuerSignature');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('propertyissuance', function (Blueprint $table) {
            if (Schema::hasColumn('propertyissuance', 'IssuerSignatureSource')) {
                $table->dropColumn('IssuerSignatureSource');
            }

            if (Schema::hasColumn('propertyissuance', 'IssuerSignature')) {
                $table->dropColumn('IssuerSignature');
            }

            if (Schema::hasColumn('propertyissuance', 'DRID')) {
                $table->dropIndex(['DRID']);
                $table->dropColumn('DRID');
            }
        });
    }
};
