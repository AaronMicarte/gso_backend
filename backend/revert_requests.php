<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetRequest;

echo "Reverting orphaned 'issued' requests to 'approved'...\n";

$orphanedRequests = AssetRequest::where('Status', 'issued')->get();

foreach ($orphanedRequests as $request) {
    echo "Reverting Request ID: {$request->RequestID} ({$request->RequestNumber})...\n";
    
    $request->update([
        'Status' => 'approved',
        'IssuedBy' => null,
        'IssuedAt' => null
    ]);
}

echo "Done. Reverted " . $orphanedRequests->count() . " requests.\n";
