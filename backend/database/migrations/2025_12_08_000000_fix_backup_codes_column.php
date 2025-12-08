<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'MFABackupCodes')) {
                $table->renameColumn('MFABackupCodes', 'BackupCodes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'BackupCodes')) {
                $table->renameColumn('BackupCodes', 'MFABackupCodes');
            }
        });
    }
};
