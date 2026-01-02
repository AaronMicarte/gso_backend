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
            $table->string('dr_picture', 255)->nullable(false)->comment('Path to the uploaded DR picture file')->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveryreceipt', function (Blueprint $table) {
            $table->dropColumn('dr_picture');
        });
    }
};
