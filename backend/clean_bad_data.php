<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PropertyIssuanceDetail;
use App\Models\PropertyIssuance;
use Illuminate\Support\Facades\DB;

echo "Cleaning up invalid test issuances...\n";

// Find details with missing inventory link
$badDetails = PropertyIssuanceDetail::whereNull('InventoryDetailID')->get();

$count = $badDetails->count();
if ($count > 0) {
    echo "Found {$count} invalid issuance details. Deleting...\n";
    
    // Get unique IssuanceIDs before deleting
    $issuanceIds = $badDetails->pluck('IssuanceID')->unique();
    
    // Delete details
    PropertyIssuanceDetail::whereNull('InventoryDetailID')->forceDelete();
    
    // Check if parent issuances are now empty and delete them too
    foreach ($issuanceIds as $id) {
        if (PropertyIssuanceDetail::where('IssuanceID', $id)->count() == 0) {
            PropertyIssuance::where('IssuanceID', $id)->forceDelete();
            echo "Deleted empty parent Issuance ID: {$id}\n";
        }
    }
    
    echo "Cleanup complete.\n";
} else {
    echo "No invalid data found.\n";
}
