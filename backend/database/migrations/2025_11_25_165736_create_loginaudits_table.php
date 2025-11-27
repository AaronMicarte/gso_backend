<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loginaudits', function (Blueprint $table) {
            $table->id('LoginAuditID');
            $table->unsignedBigInteger('UserID');
            $table->timestamp('EventTime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('Status', 50);
            $table->foreign('UserID')->references('UserID')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loginaudits');
    }
};
