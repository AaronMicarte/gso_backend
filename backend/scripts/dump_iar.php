<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = $argv[1] ?? 5;
$iar = \App\Models\Procurement\InspectionAcceptanceReport::with([
    'details.propertyType',
    'details.condition',
    'details.drDetail.unitOfMeasure',
    'deliveryReceipt' => function($q) { $q->withTrashed(); }
])->find($id);

if (! $iar) {
    echo "IAR not found\n";
    exit(1);
}

echo json_encode($iar->toArray(), JSON_PRETTY_PRINT);
