<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TRUSTED DEVICES CHECK ===\n\n";

$devices = DB::table('trusted_devices')
    ->where('UserID', 3)
    ->orderBy('CreatedDate', 'desc')
    ->get();

echo "Trusted devices for User ID 3:\n";
echo "Count: " . $devices->count() . "\n\n";

foreach ($devices as $device) {
    echo "DeviceID: " . $device->DeviceID . "\n";
    echo "DeviceName: " . $device->DeviceName . "\n";
    echo "DeviceToken (hashed): " . substr($device->DeviceToken, 0, 20) . "...\n";
    echo "CreatedDate: " . $device->CreatedDate . "\n";
    echo "ExpiresAt: " . $device->ExpiresAt . "\n";
    echo "LastUsedAt: " . ($device->LastUsedAt ?? 'Never') . "\n\n";
}

echo "=== RECENT LOGINS ===\n\n";
$logins = DB::table('loginaudit')
    ->where('UserID', 3)
    ->orderBy('EventTime', 'desc')
    ->limit(5)
    ->get();

foreach ($logins as $login) {
    echo "EventTime: " . $login->EventTime . " | Status: " . $login->Status . " | IP: " . $login->IPAddress . "\n";
}
