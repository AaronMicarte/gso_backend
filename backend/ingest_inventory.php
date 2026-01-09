<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\Procurement\InspectionAcceptanceReport;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$iars = InspectionAcceptanceReport::whereNotNull('AcceptedBy')->get();

foreach($iars as $iar) {
    echo "Ingesting IAR {$iar->IARID}\n";
    try {
        $controller = new \App\Http\Controllers\Admin\Inventory\InventoryController();
        $request = new \Illuminate\Http\Request(['iar_id' => $iar->IARID]);
        $response = $controller->ingestFromIar($request);
        echo "Response: " . $response->getContent() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
