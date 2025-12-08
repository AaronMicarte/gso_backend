<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            if (Schema::hasColumn('trusted_devices', 'TrustedDeviceID')) {
                $table->renameColumn('TrustedDeviceID', 'DeviceID');
            }
            if (Schema::hasColumn('trusted_devices', 'CreatedAt')) {
                $table->renameColumn('CreatedAt', 'CreatedDate');
            }
            // If UpdatedAt exists but is not needed (timestamps=false in model), we can drop it or leave it.
            // Leaving it is safer, but renaming the required ones is critical.
        });
    }

    public function down(): void
    {
        Schema::table('trusted_devices', function (Blueprint $table) {
            if (Schema::hasColumn('trusted_devices', 'DeviceID')) {
                $table->renameColumn('DeviceID', 'TrustedDeviceID');
            }
            if (Schema::hasColumn('trusted_devices', 'CreatedDate')) {
                $table->renameColumn('CreatedDate', 'CreatedAt');
            }
        });
    }
};
