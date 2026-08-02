<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $req = Illuminate\Http\Request::create('/api/inventory?page=1&per_page=10&sort_by=date&sort_dir=desc', 'GET');
    $ctrl = app(App\Http\Controllers\Admin\Inventory\InventoryController::class);
    $result = $ctrl->index($req);
    echo 'OK total=' . $result->total() . PHP_EOL;
} catch (Throwable $e) {
    echo get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    echo $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}
