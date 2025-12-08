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
        Schema::table('user', function (Blueprint $table) {
            // TOTP (Time-based One-Time Password) secret, encrypted
            $table->text('MFASecret')->nullable()->after('Password');
            // Whether MFA is enabled for this user
            $table->boolean('MFAEnabled')->default(false)->after('MFASecret');
            // Backup codes (comma-separated, encrypted)
            $table->text('BackupCodes')->nullable()->after('MFAEnabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('MFASecret');
            $table->dropColumn('MFAEnabled');
            $table->dropColumn('BackupCodes');
        });
    }
};
