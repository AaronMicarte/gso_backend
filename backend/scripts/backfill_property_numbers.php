<?php

use App\Models\Inventory\PropertyInventoryDetail;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Starting Property Number backfill...\n";

// Disable timestamps to preserve CreatedDate/UpdatedDate if possible (PropertyInventoryDetail uses timestamps=false in Model but let's be safe)
// Model uses `public $timestamps = false;` so manual updates only

// Group items by Year of creation
// We only want items with NULL PropertyNumber
$items = PropertyInventoryDetail::whereNull('PropertyNumber')->get();

if ($items->isEmpty()) {
    echo "No items with missing PropertyNumber found.\n";
    exit(0);
}

// Group by year
$grouped = $items->groupBy(function ($item) {
    if (!$item->CreatedDate) return date('Y');
    try {
        return \Carbon\Carbon::parse($item->CreatedDate)->format('Y');
    } catch (\Exception $e) {
        return date('Y');
    }
});

foreach ($grouped as $year => $yearItems) {
    echo "Processing year $year: " . $yearItems->count() . " items\n";
    
    $prefix = $year . '-';
    
    // Get max for this year to start sequence
    // We search across ALL items (including those we might have just updated if run partially, but here we run in memory)
    // Actually, we should query DB for the max to be safe.
    $latest = PropertyInventoryDetail::where('PropertyNumber', 'like', $prefix . '%')
            ->orderBy('PropertyNumber', 'desc')
            ->withTrashed() // Include soft deleted to avoid collision
            ->first();

    $currentSequence = 0;
    if ($latest && preg_match('/-(\d+)$/', $latest->PropertyNumber, $matches)) {
        $currentSequence = intval($matches[1]);
    }
    
    echo "  Starting sequence from " . ($currentSequence + 1) . "\n";
    
    foreach ($yearItems as $item) {
        $currentSequence++;
        $propertyNumber = $prefix . str_pad($currentSequence, 6, '0', STR_PAD_LEFT);
        
        // Direct update to avoid model events if any? No, let's use model save.
        $item->PropertyNumber = $propertyNumber;
        $item->save();
        
        echo "  Assigned $propertyNumber to ID {$item->InventoryDetailID}\n";
    }
}

echo "Backfill completed.\n";
