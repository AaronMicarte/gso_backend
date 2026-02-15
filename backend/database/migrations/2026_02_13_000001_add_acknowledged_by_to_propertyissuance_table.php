<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propertyissuance', function (Blueprint $table) {
            if (! Schema::hasColumn('propertyissuance', 'AcknowledgedBy')) {
                $table->unsignedInteger('AcknowledgedBy')->nullable()->after('AcknowledgedAt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('propertyissuance', function (Blueprint $table) {
            if (Schema::hasColumn('propertyissuance', 'AcknowledgedBy')) {
                $table->dropColumn('AcknowledgedBy');
            }
        });
    }
};
