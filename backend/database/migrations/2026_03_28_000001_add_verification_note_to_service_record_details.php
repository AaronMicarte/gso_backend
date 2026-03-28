<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_service_records_details', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_service_records_details', 'VerificationNote')) {
                $table->string('VerificationNote', 4000)->nullable()->after('Status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_service_records_details', function (Blueprint $table) {
            if (Schema::hasColumn('asset_service_records_details', 'VerificationNote')) {
                $table->dropColumn('VerificationNote');
            }
        });
    }
};
