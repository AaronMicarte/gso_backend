<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\Inventory\PropertyInventoryDetail::count();
echo "Total inventory items: $count\n";

$items = \App\Models\Inventory\PropertyInventoryDetail::take(5)->get();
foreach($items as $item) {
    echo $item->InventoryDetailID . ': ' . ($item->Description ?? 'No desc') . "\n";
}
