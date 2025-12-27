<?php
// Temporary maintenance script to move signatures DB records from 'local' to 'public'
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Procurement\IarSignature;

$sigs = IarSignature::where('storage_disk', 'local')->get();
$count = 0;
foreach ($sigs as $s) {
    $s->storage_disk = 'public';
    $s->url = '/storage/' . $s->storage_path;
    $s->save();
    $count++;
}

echo "Updated signatures: $count\n";