<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetRequest;

echo "Checking Asset Requests...\n";

// Find requests that are 'issued' but have no existing PropertyIssuance?
// Actually, since we wiped PropertyIssuance table, any request marked 'issued' is now orphaned in that state.

$orphanedRequests = AssetRequest::where('Status', 'issued')->get();

echo "Found " . $orphanedRequests->count() . " requests stuck in 'issued' status.\n";

foreach ($orphanedRequests as $request) {
    echo "Request ID: {$request->RequestID}, Number: {$request->RequestNumber}, Status: {$request->Status}\n";
}
