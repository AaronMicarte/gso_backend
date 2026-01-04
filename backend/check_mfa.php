<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Auth\User;

$user = User::find(3);
echo 'MFAEnabled: ' . $user->MFAEnabled . PHP_EOL;

if ($user->MFASecret) {
    try {
        $decrypted = decrypt($user->MFASecret);
        echo 'MFASecret decrypted: ' . $decrypted . PHP_EOL;
    } catch (Exception $e) {
        echo 'Error decrypting: ' . $e->getMessage() . PHP_EOL;
    }
} else {
    echo 'No MFASecret set' . PHP_EOL;
}