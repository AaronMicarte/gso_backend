<?php
/**
 * Script to populate existing Delivery Receipts with IAR numbers
 * Run this script to add IAR numbers to all existing DRs
 */

require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;
use App\Models\Procurement\DeliveryReceipt;
use App\Http\Controllers\Admin\Procurement\DeliveryReceiptController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting IAR number population for existing DRs...\n";

// Get all DRs that don't have IAR numbers
$drsWithoutIar = DeliveryReceipt::whereNull('IARNumber')->orWhere('IARNumber', '')->get();

echo "Found " . $drsWithoutIar->count() . " DRs without IAR numbers.\n";

$controller = new DeliveryReceiptController();
$counter = 0;

foreach ($drsWithoutIar as $dr) {
    try {
        // Use the existing method to ensure IAR
        $controller->ensureIarForDeliveryReceipt($dr);
        
        // Refresh the model to get the updated data
        $dr->refresh();
        
        echo "Updated DR {$dr->DRNumber} with IAR number: {$dr->IARNumber}\n";
        $counter++;
    } catch (Exception $e) {
        echo "Error updating DR {$dr->DRNumber}: " . $e->getMessage() . "\n";
    }
}

echo "Completed! Updated {$counter} DRs with IAR numbers.\n";