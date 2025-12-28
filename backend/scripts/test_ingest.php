<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';

// Make a request object with iar_id=1
$request = \Illuminate\Http\Request::create('/', 'POST', ['iar_id' => 1]);

$controller = new \App\Http\Controllers\Admin\Inventory\InventoryController();
$response = $controller->ingestFromIar($request);

echo "Status: " . ($response->getStatusCode() ?? 'NONE') . PHP_EOL;
echo $response->getContent() . PHP_EOL;