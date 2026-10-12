<?php
require 'c:/xampp/htdocs/gso_backend/backend/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/gso_backend/backend/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\RpcInventoryController;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

$user = User::first();
if ($user) Auth::login($user);

$rpcCtrl = app(RpcInventoryController::class);

$reqRpc = Request::create('/api/rpc-inventory/cross-check/department', 'GET', [
    'department_id' => 30,
    'classification_type' => 'ALL',
]);
$resRpc = $rpcCtrl->departmentCrossCheck($reqRpc);
$rpcData = $resRpc->getData();

$eloiseRpcItems = array_values(array_filter($rpcData->data, function($item) {
    return stripos($item->end_user ?? '', 'Eloise Bridgerton') !== false;
}));

print_r($eloiseRpcItems[0]);
