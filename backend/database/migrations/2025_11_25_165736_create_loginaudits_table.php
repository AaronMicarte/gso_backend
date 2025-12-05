<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * NOTE: This migration is for reference only.
 * The actual table is created from gso_db.sql with the correct schema.
 * Table name in DB: loginaudit (singular, lowercase)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loginaudit', function (Blueprint $table) {
            $table->id('LoginAuditID');
            $table->unsignedBigInteger('UserID');
            $table->timestamp('EventTime')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('Status', 50); // 'Login', 'Logout', 'FailedLogin'
            $table->string('IPAddress', 45)->nullable();
            $table->foreign('UserID')->references('UserID')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loginaudit');
    }
};
