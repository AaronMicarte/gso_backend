<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inspection_report_items')) {
            return;
        }

        Schema::table('inspection_report_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inspection_report_items', 'NoSerial')) {
                $table->boolean('NoSerial')->default(false)->after('Remarks');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('inspection_report_items')) {
            return;
        }

        Schema::table('inspection_report_items', function (Blueprint $table) {
            if (Schema::hasColumn('inspection_report_items', 'NoSerial')) {
                $table->dropColumn('NoSerial');
            }
        });
    }
};
