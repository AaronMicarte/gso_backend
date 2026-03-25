<?php

declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$paths = [
    '/api/asset-services/statuses',
    '/api/admin/master-data/vendors',
];

foreach ($paths as $path) {
    $request = Request::create($path, 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);
    $response = $kernel->handle($request);

    echo $path . ' => ' . $response->getStatusCode() . PHP_EOL;

    $kernel->terminate($request, $response);
}
