<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PropertyIssuance;
use App\Models\PropertyIssuanceDetail;
use App\Models\Inventory\PropertyInventoryDetail;

echo "Issuances Count: " . PropertyIssuance::count() . "\n";
echo "Details Count: " . PropertyIssuanceDetail::count() . "\n";

echo "Inventory Status Summary:\n";
$stats = PropertyInventoryDetail::select('PropertyStatusID', \DB::raw('count(*) as total'))
    ->groupBy('PropertyStatusID')
    ->get();

foreach($stats as $stat) {
    echo "Status {$stat->PropertyStatusID}: {$stat->total}\n";
}
