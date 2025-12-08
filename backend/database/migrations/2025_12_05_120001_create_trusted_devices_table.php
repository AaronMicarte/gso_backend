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
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id('DeviceID');
            $table->unsignedBigInteger('UserID');
            $table->string('DeviceToken', 255)->unique();
            $table->string('DeviceName', 255)->nullable(); // e.g., "Chrome on Windows"
            $table->string('UserAgent', 500)->nullable();
            $table->string('IPAddress', 50)->nullable();
            $table->timestamp('CreatedDate')->useCurrent();
            $table->timestamp('ExpiresAt')->nullable();
            $table->timestamp('LastUsedAt')->nullable();
            $table->foreign('UserID')->references('UserID')->on('user')->onDelete('cascade');
            $table->index('UserID');
            $table->index('DeviceToken');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
    }
};
