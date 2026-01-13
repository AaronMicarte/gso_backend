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
            if (!Schema::hasColumn('user', 'EmailVerifiedAt')) {
                $table->dateTime('EmailVerifiedAt')->nullable()->after('Email');
            }
            if (!Schema::hasColumn('user', 'ActivationTokenHash')) {
                $table->string('ActivationTokenHash', 64)->nullable()->after('EmailVerifiedAt');
            }
            if (!Schema::hasColumn('user', 'ActivationSentAt')) {
                $table->dateTime('ActivationSentAt')->nullable()->after('ActivationTokenHash');
            }
            if (!Schema::hasColumn('user', 'ActivationExpiresAt')) {
                $table->dateTime('ActivationExpiresAt')->nullable()->after('ActivationSentAt');
            }
            if (!Schema::hasColumn('user', 'MustChangePassword')) {
                $table->boolean('MustChangePassword')->default(true)->after('Password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $columns = [
                'EmailVerifiedAt',
                'ActivationTokenHash',
                'ActivationSentAt',
                'ActivationExpiresAt',
                'MustChangePassword',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('user', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
