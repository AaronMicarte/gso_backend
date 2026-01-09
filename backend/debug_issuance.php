<?php

// Load Laravel's autoloader
require __DIR__ . '/vendor/autoload.php';

// Bootstrap the application
$app = require __DIR__ . '/bootstrap/app.php';

// Boot the console kernel to set up the environment (DB connection, etc.)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\Inventory\PropertyInventoryDetail;

echo "--- DEBUG START ---\n";

$docNum = 'PAR-2026-0001';
$issuance = PropertyIssuance::where('DocumentNumber', $docNum)->first();

if (!$issuance) {
    // Try finding any issuance to verify DB connection
    $any = PropertyIssuance::first();
    if ($any) {
        echo "Issuance '$docNum' not found, but DB has data (e.g., {$any->DocumentNumber}).\n";
    } else {
        echo "Issuance '$docNum' not found and DB table 'propertyissuance' seems empty.\n";
    }
    exit;
}

echo "Found Issuance: {$issuance->DocumentNumber} (ID: {$issuance->IssuanceID})\n";

// Get raw details
$details = PropertyIssuanceDetail::where('IssuanceID', $issuance->IssuanceID)->get();
echo "Found {$details->count()} detail rows.\n";

foreach ($details as $idx => $d) {
    echo "\n[Detail Row #{$idx}]\n";
    echo "  IssuanceDetailID: {$d->IssuanceDetailID}\n";
    echo "  InventoryDetailID: " . json_encode($d->InventoryDetailID) . "\n";
    echo "  ItemDescription (direct): " . json_encode($d->ItemDescription) . "\n";
    echo "  Unit (direct): " . json_encode($d->Unit) . "\n";

    if ($d->InventoryDetailID) {
        // Check availability in inventory table (including soft deletes)
        $inv = PropertyInventoryDetail::withTrashed()->find($d->InventoryDetailID);
        if ($inv) {
            echo "  -> Linked Inventory found (ID: {$inv->InventoryDetailID}):\n";
            echo "     Description: {$inv->Description}\n";
            echo "     Brand: {$inv->Brand}\n";
            echo "     PropertyNumber: {$inv->PropertyNumber}\n";
            echo "     DeletedAt: " . ($inv->deleted_at ? $inv->deleted_at : 'NULL') . "\n";
        } else {
             echo "  -> Linked Inventory ID {$d->InventoryDetailID} DOES NOT EXIST in 'propertyinventorydetail' table.\n";
        }
    } else {
        echo "  -> No InventoryDetailID linked.\n";
    }
}
echo "--- DEBUG END ---\n";
