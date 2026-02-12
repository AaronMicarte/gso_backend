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
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (Schema::hasColumn('deliveryreceipt', 'RequestedByLegacy')) {
                $table->dropColumn('RequestedByLegacy');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveryreceipt', 'RequestedByLegacy')) {
                $table->string('RequestedByLegacy')->nullable()->after('RequestedBy');
            }
        });
    }
};
