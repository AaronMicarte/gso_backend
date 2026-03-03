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
            $table->string('IARNumber')->nullable()->after('DRNumber')->comment('Auto-generated IAR number for tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            $table->dropColumn('IARNumber');
        });
    }
};
