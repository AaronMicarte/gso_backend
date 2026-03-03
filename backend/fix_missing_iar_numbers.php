<?php
/**
 * Script to ensure all existing Delivery Receipts have IAR numbers
 * Run this script to fix DRs that were created before the IAR generation fix
 */

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';

use App\Models\Procurement\DeliveryReceipt;
use App\Models\Procurement\InspectionAcceptanceReport;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting IAR number generation for existing DRs...\n";

// Get all DRs that don't have an associated IAR
$drsWithoutIar = DeliveryReceipt::whereDoesntHave('iar')->get();

echo "Found " . $drsWithoutIar->count() . " DRs without IAR numbers.\n";

$counter = 0;
foreach ($drsWithoutIar as $dr) {
    try {
        // Generate IAR number
        $iarNumber = 'IAR-' . date('Y') . '-' . str_pad(InspectionAcceptanceReport::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Create IAR record with default values for required fields
        InspectionAcceptanceReport::create([
            'IARNumber' => $iarNumber,
            'DRID' => $dr->DRID,
            'InspectedBy' => 1, // Default to admin user
            'AcceptedBy' => 1,  // Default to admin user
            'DateInspected' => now()->format('Y-m-d'),
            'DateAccepted' => now()->format('Y-m-d'),
            'Remarks' => 'Auto-generated for existing DR',
        ]);
        
        $counter++;
        echo "Created IAR {$iarNumber} for DR {$dr->DRNumber}\n";
        
    } catch (Exception $e) {
        echo "Error creating IAR for DR {$dr->DRNumber}: " . $e->getMessage() . "\n";
    }
}

echo "Completed! Created {$counter} IAR records.\n";