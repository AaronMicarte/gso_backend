<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('trusted_devices')) {
            return;
        }

        $devices = DB::table('trusted_devices')
            ->select('DeviceID', 'UserID', 'UserAgent', 'IPAddress', 'LastUsedAt', 'CreatedDate')
            ->orderBy('UserID')
            ->orderByRaw('COALESCE(LastUsedAt, CreatedDate) DESC')
            ->orderByDesc('DeviceID')
            ->get();

        $seen = [];
        $duplicateIds = [];

        foreach ($devices as $device) {
            $key = implode('|', [
                (string) $device->UserID,
                strtolower((string) ($device->UserAgent ?? '')),
                (string) ($device->IPAddress ?? ''),
            ]);

            if (isset($seen[$key])) {
                $duplicateIds[] = (int) $device->DeviceID;
                continue;
            }

            $seen[$key] = true;
        }

        if (!empty($duplicateIds)) {
            DB::table('trusted_devices')
                ->whereIn('DeviceID', $duplicateIds)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible cleanup migration.
    }
};
