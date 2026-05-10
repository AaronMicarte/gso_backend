<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $frontend = rtrim((string) config('gso.frontend_url', ''), '/');
        if ($frontend === '') {
            $frontend = rtrim((string) env('GSO_FRONTEND_URL', ''), '/');
        }

        if ($frontend === '') {
            $frontend = 'http://localhost:3000';
        }

        DB::table('qrcode')
            ->orderBy('QRCodeID')
            ->chunkById(500, function ($rows) use ($frontend): void {
                foreach ($rows as $row) {
                    $propertyId = (int) ($row->PropertyID ?? 0);
                    if ($propertyId <= 0) {
                        continue;
                    }

                    $existing = (string) ($row->QRCodeData ?? '');
                    $query = parse_url($existing, PHP_URL_QUERY);
                    $normalized = $frontend . '/qr/scan/' . $propertyId;

                    if (is_string($query) && $query !== '') {
                        $normalized .= '?' . $query;
                    }

                    if ($normalized === $existing) {
                        continue;
                    }

                    DB::table('qrcode')
                        ->where('QRCodeID', $row->QRCodeID)
                        ->update(['QRCodeData' => $normalized]);
                }
            }, 'QRCodeID');
    }

    public function down(): void
    {
        // Intentionally left blank: the migration normalizes existing QR payloads.
    }
};