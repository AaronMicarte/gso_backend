<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use Illuminate\Console\Command;

class NormalizeQrCodesCommand extends Command
{
    protected $signature = 'gso:normalize-qr-codes {--dry-run : Show what would change without writing}';

    protected $description = 'Normalize qrcode.QRCodeData to use the configured frontend URL and canonical /qr/scan/{PropertyID} route.';

    public function handle(): int
    {
        $frontend = rtrim((string) config('gso.frontend_url', ''), '/');
        if ($frontend === '') {
            $this->error('Missing config(gso.frontend_url). Set GSO_FRONTEND_URL in .env');
            return 1;
        }

        $dryRun = (bool) $this->option('dry-run');

        $total = 0;
        $changed = 0;

        QrCode::query()
            ->orderBy('QRCodeID')
            ->chunkById(500, function ($rows) use ($frontend, $dryRun, &$total, &$changed) {
                foreach ($rows as $qr) {
                    $total++;

                    $existing = (string) ($qr->QRCodeData ?? '');
                    if ($existing === '') {
                        continue;
                    }

                    $targetPropertyId = (int) $qr->PropertyID;

                    $match = preg_match('#/qr/scan/(\d+)#', $existing, $m) ? (int) $m[1] : null;
                    $scanId = $match && $match > 0 ? $match : null;

                    // Preserve query string if present (e.g., ?h=...)
                    $queryString = null;
                    $parsedQuery = parse_url($existing, PHP_URL_QUERY);
                    if (is_string($parsedQuery) && $parsedQuery !== '') {
                        $queryString = $parsedQuery;
                    }

                    $normalized = $frontend . '/qr/scan/' . ($scanId ?? $targetPropertyId);
                    if ($queryString) {
                        $normalized .= '?' . $queryString;
                    }

                    if ($normalized === $existing) {
                        continue;
                    }

                    $changed++;

                    if ($dryRun) {
                        $this->line("[DRY] QRCodeID {$qr->QRCodeID}: {$existing}  ->  {$normalized}");
                        continue;
                    }

                    $qr->QRCodeData = $normalized;
                    $qr->save();
                }
            }, 'QRCodeID');

        $mode = $dryRun ? 'DRY RUN' : 'APPLIED';
        $this->info("{$mode}: scanned {$total} QR rows; updated {$changed}.");

        return 0;
    }
}
