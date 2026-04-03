<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\InspectionAcceptanceReport;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting IAR repair (duplicates + missing IAR records)...\n";

$sequenceByYear = [];
$used = [];

$extract = static function (?string $iarNumber): ?array {
    if (! $iarNumber) {
        return null;
    }
    if (preg_match('/^IAR-(\d{4})-(\d{4,})$/', $iarNumber, $m)) {
        return ['year' => $m[1], 'sequence' => (int) $m[2]];
    }
    return null;
};

$register = static function (?string $iarNumber) use (&$used, &$sequenceByYear, $extract): void {
    $parts = $extract($iarNumber);
    if (! $parts) {
        return;
    }
    $year = $parts['year'];
    $sequence = $parts['sequence'];
    $used[$iarNumber] = true;
    $sequenceByYear[$year] = max($sequenceByYear[$year] ?? 0, $sequence);
};

$nextIarNumber = static function (string $year) use (&$used, &$sequenceByYear): string {
    $sequenceByYear[$year] = $sequenceByYear[$year] ?? 0;
    do {
        $sequenceByYear[$year]++;
        $candidate = 'IAR-' . $year . '-' . str_pad((string) $sequenceByYear[$year], 4, '0', STR_PAD_LEFT);
    } while (isset($used[$candidate]));

    $used[$candidate] = true;
    return $candidate;
};

foreach (DB::table('inspectionacceptancereport')->select('IARNumber')->get() as $row) {
    $register($row->IARNumber);
}
foreach (DB::table('deliveryreceipt')->select('IARNumber')->get() as $row) {
    $register($row->IARNumber);
}

echo "Repairing duplicate DR IAR numbers...\n";

$seenDrIar = [];
$duplicatesFixed = 0;
$deliveryReceipts = DeliveryReceipt::query()->orderBy('CreatedDate')->orderBy('DRID')->get();
foreach ($deliveryReceipts as $dr) {
    $current = $dr->IARNumber;
    if (! $current) {
        continue;
    }

    if (! isset($seenDrIar[$current])) {
        $seenDrIar[$current] = true;
        continue;
    }

    $year = date('Y', strtotime((string) ($dr->DateReceived ?: $dr->CreatedDate ?: now())));
    $newIarNumber = $nextIarNumber((string) $year);

    DB::transaction(function () use ($dr, $newIarNumber) {
        $dr->IARNumber = $newIarNumber;
        $dr->save();

        InspectionAcceptanceReport::where('DRID', $dr->DRID)->update(['IARNumber' => $newIarNumber]);
    });

    $duplicatesFixed++;
    echo "Updated duplicate IAR for DR {$dr->DRNumber}: {$current} -> {$newIarNumber}\n";
}

echo "Checking DRs without IAR records...\n";

$drsWithoutIar = DeliveryReceipt::whereDoesntHave('iar')->get();
echo "Found " . $drsWithoutIar->count() . " DRs without IAR records.\n";

$created = 0;
foreach ($drsWithoutIar as $dr) {
    try {
        $year = date('Y', strtotime((string) ($dr->DateReceived ?: $dr->CreatedDate ?: now())));
        $iarNumber = $dr->IARNumber;

        if (! $iarNumber || ! $extract($iarNumber)) {
            $iarNumber = $nextIarNumber((string) $year);
            $dr->IARNumber = $iarNumber;
            $dr->save();
        }

        $seenDrIar[$iarNumber] = true;

        InspectionAcceptanceReport::create([
            'IARNumber' => $iarNumber,
            'DRID' => $dr->DRID,
            'InspectedBy' => 1,
            'AcceptedBy' => 1,
            'DateInspected' => now()->format('Y-m-d'),
            'DateAccepted' => now()->format('Y-m-d'),
            'Remarks' => 'Auto-generated for existing DR',
        ]);

        $created++;
        echo "Created IAR {$iarNumber} for DR {$dr->DRNumber}\n";
    } catch (\Exception $e) {
        echo "Error creating IAR for DR {$dr->DRNumber}: " . $e->getMessage() . "\n";
    }
}

echo "Completed. Duplicates fixed: {$duplicatesFixed}. Missing IAR records created: {$created}.\n";