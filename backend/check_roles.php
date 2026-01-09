<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "User Roles in Database:\n";
$roles = DB::table('userrole')->get();

foreach ($roles as $role) {
    echo "ID: {$role->UserRoleID} - Name: {$role->RoleName}\n";
}
