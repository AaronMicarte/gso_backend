<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_service_records')) {
            return;
        }

        Schema::table('asset_service_records', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_service_records', 'FinalNotes')) {
                $table->text('FinalNotes')->nullable()->after('RecordNotes');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('asset_service_records')) {
            return;
        }

        Schema::table('asset_service_records', function (Blueprint $table) {
            if (Schema::hasColumn('asset_service_records', 'FinalNotes')) {
                $table->dropColumn('FinalNotes');
            }
        });
    }
};