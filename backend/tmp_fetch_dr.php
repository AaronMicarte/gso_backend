<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$deliveryReceipt = App\Models\Procurement\DeliveryReceipt::with(['details.propertyType','details.unitOfMeasure'])->where('DRNumber','DR_TEST_03')->first();
if (!$deliveryReceipt) { echo json_encode(null, JSON_PRETTY_PRINT); exit; }
$user = \App\Models\Auth\User::find($deliveryReceipt->ReceivedBy);
$deliveryReceipt->receivedByUser = $user ? ['UserID' => $user->UserID, 'FullName' => $user->FullName] : null;

// Build array and normalize keys like the controller
$data = $deliveryReceipt->toArray();
if (!empty($data['details']) && is_array($data['details'])) {
    foreach ($data['details'] as &$d) {
        $d['propertyType'] = $d['property_type'] ?? null;
        $d['unitOfMeasure'] = $d['unit_of_measure'] ?? null;
    }
}

echo json_encode($data, JSON_PRETTY_PRINT);
