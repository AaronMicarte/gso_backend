<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$items = \App\Models\Inventory\PropertyInventoryDetail::all();

foreach($items as $item) {
    $iarDetail = \App\Models\Procurement\IARDetail::find($item->SourceIARDetailID);
    if ($iarDetail) {
        $item->update(['Description' => $iarDetail->Remarks]);
        echo "Updated item {$item->InventoryDetailID} with description: {$iarDetail->Remarks}\n";
    }
}

echo "Updated " . $items->count() . " items\n";
