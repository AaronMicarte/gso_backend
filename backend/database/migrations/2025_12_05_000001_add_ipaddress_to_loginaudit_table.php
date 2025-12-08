<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add IPAddress column to loginaudit table for proper audit logging.
 * This is CRITICAL for security - always log client IP on login/logout events.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loginaudit', function (Blueprint $table) {
            // VARCHAR(45) to support both IPv4 and IPv6 addresses
            $table->string('IPAddress', 45)->nullable()->after('Status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loginaudit', function (Blueprint $table) {
            $table->dropColumn('IPAddress');
        });
    }
};
