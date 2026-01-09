<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\PropertyIssuanceDetail;
use Illuminate\Support\Facades\DB;

echo "Starting Inventory Status Fix...\n";

// Eager load for performance if needed, but iteration is fine for script size
$details = PropertyInventoryDetail::all();
echo "Found " . $details->count() . " inventory details.\n";
$issuanceDetails = PropertyIssuanceDetail::all();
echo "Total Issuance Details in DB: " . $issuanceDetails->count() . "\n";
foreach($issuanceDetails as $id) {
    echo "IssuanceDetail ID: {$id->IssuanceDetailID} Created: {$id->created_at} points to InvDetailID: " . ($id->InventoryDetailID ?? 'NULL') . "\n";
}

$fixedCount = 0;

foreach ($details as $detail) {
    // Calculate total issued for this specific inventory item
    // We need to look at PropertyIssuanceDetail where InventoryDetailID matches
    // And verify the issuance wasn't cancelled/deleted (though database soft deletes usually handle deleted_at)
    
    // Check for soft deletes on issuance detail if model supports it?
    // PropertyIssuanceDetail usually doesn't have soft deletes, but let's check.
    // Assuming standard behavior.
    
    $totalIssued = PropertyIssuanceDetail::where('InventoryDetailID', $detail->InventoryDetailID)->sum('QuantityIssued');

    // Calculate what available should be
    $shouldBeAvailable = $detail->Quantity - $totalIssued;

    if ($totalIssued > 0) {
        echo "DEBUG: Item {$detail->InventoryDetailID} has issued {$totalIssued}. Qty: {$detail->Quantity}. Available DB: {$detail->QuantityAvailable}. Calculated: {$shouldBeAvailable}.\n";
    }

    // Sanity check
    if ($shouldBeAvailable < 0) {
        echo "WARNING: Item {$detail->InventoryDetailID} ({$detail->Description}) has issued {$totalIssued} but only had {$detail->Quantity}. Setting available to 0.\n";
        $shouldBeAvailable = 0;
    }

    $needsUpdate = false;
    $updates = [];

    // Check if QuantityAvailable is wrong
    // Note: Use loose comparison or cast to int because DB might return string
    if ((int)$detail->QuantityAvailable !== (int)$shouldBeAvailable) {
        echo "Item {$detail->InventoryDetailID}: Syncing QuantityAvailable ({$detail->QuantityAvailable} -> {$shouldBeAvailable}). \n";
        $updates['QuantityAvailable'] = $shouldBeAvailable;
        $needsUpdate = true;
    }

    // Check status logic
    // Status 1 = Available
    // Status 2 = Assigned
    // Status 3 = For Repair
    // Status 4 = For Disposal
    // Status 5 = Disposed
    
    $currentStatus = $detail->PropertyStatusID;

    if ($shouldBeAvailable <= 0) {
        // If no stock left, it should be 'Assigned' IF it is currently 'Available'
        // We do not overwrite 'For Repair' or 'Disposed' states indiscriminately
        if ($currentStatus == 1) { 
            echo "Item {$detail->InventoryDetailID}: Changing Status Available -> Assigned. \n";
            $updates['PropertyStatusID'] = 2; // Set to Assigned
            $needsUpdate = true;
        }
    } else {
        // If stock is available, it should NOT be 'Assigned'
        if ($currentStatus == 2) {
            echo "Item {$detail->InventoryDetailID}: Changing Status Assigned -> Available. \n";
            $updates['PropertyStatusID'] = 1; // Set back to Available
            $needsUpdate = true;
        }
    }

    if ($needsUpdate) {
        $detail->update($updates);
        $fixedCount++;
    }
}

echo "Done. Fixed {$fixedCount} items.\n";
