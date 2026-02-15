<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iardetail', function (Blueprint $table) {
            if (!Schema::hasColumn('iardetail', 'NoSerial')) {
                $table->boolean('NoSerial')->default(false)->after('VerifiedSerial');
            }
        });
    }

    public function down(): void
    {
        Schema::table('iardetail', function (Blueprint $table) {
            if (Schema::hasColumn('iardetail', 'NoSerial')) {
                $table->dropColumn('NoSerial');
            }
        });
    }
};
