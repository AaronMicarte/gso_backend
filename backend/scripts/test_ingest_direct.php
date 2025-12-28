<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\Procurement\InspectionAcceptanceReport;
use App\Models\Inventory\PropertyInventory;
use App\Models\Inventory\PropertyInventoryDetail;
use App\Models\Procurement\DeliveryReceiptDetail;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$iar = InspectionAcceptanceReport::with(['details'])->find(1);
if (!$iar) {
    echo "IAR not found\n";
    exit(1);
}

if ($iar->details->isEmpty()) {
    echo "IAR has no details\n";
    exit(1);
}

DB::beginTransaction();
try {
    $inventory = PropertyInventory::create([
        'InventoryNumber' => 'INV-test-'.time(),
        'IARID' => $iar->IARID,
        'DateReceived' => $iar->DateAccepted ?? now(),
        'Remarks' => 'Test ingestion',
        'CreatedDate' => now(),
    ]);

    foreach ($iar->details as $detail) {
        $dr = DeliveryReceiptDetail::find($detail->DRDetailID);
        if (!$dr) continue;

        $payload = [
            'InventoryID' => $inventory->InventoryID,
            'PropertyTypeID' => $detail->PropertyTypeID,
            'ConditionID' => $detail->ConditionID,
            'Quantity' => $detail->QuantityAccepted,
            'QuantityAvailable' => $detail->QuantityAccepted,
            'PropertyStatusID' => 1, // Available
            'UnitOfMeasureID' => $dr->UnitOfMeasureID,
            'UnitCost' => $dr->UnitCost,
            'TotalCost' => $dr->UnitCost * $detail->QuantityAccepted,
            'SourceIARDetailID' => $detail->IARDetailID,
            'SourceDRDetailID' => $detail->DRDetailID,
            'Remarks' => $detail->Remarks,
            'CreatedDate' => now(),
        ];

        var_export($payload);
        echo PHP_EOL;

        PropertyInventoryDetail::create($payload);
    }

    DB::commit();
    echo "OK: inventory_id=".$inventory->InventoryID."\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERR: " . $e->getMessage() . "\n";
}
