<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iardetail', function (Blueprint $table) {
            if (!Schema::hasColumn('iardetail', 'VerifiedBrand')) {
                $table->string('VerifiedBrand', 100)->nullable()->after('QuantityRejected');
            }
            if (!Schema::hasColumn('iardetail', 'VerifiedModel')) {
                $table->string('VerifiedModel', 100)->nullable()->after('VerifiedBrand');
            }
            if (!Schema::hasColumn('iardetail', 'VerifiedSerial')) {
                $table->string('VerifiedSerial', 100)->nullable()->after('VerifiedModel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('iardetail', function (Blueprint $table) {
            if (Schema::hasColumn('iardetail', 'VerifiedBrand')) {
                $table->dropColumn('VerifiedBrand');
            }
            if (Schema::hasColumn('iardetail', 'VerifiedModel')) {
                $table->dropColumn('VerifiedModel');
            }
            if (Schema::hasColumn('iardetail', 'VerifiedSerial')) {
                $table->dropColumn('VerifiedSerial');
            }
        });
    }
};
