<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = $argv[1] ?? 5;
$iar = \App\Models\Procurement\InspectionAcceptanceReport::with([
    'details.propertyType',
    'details.condition',
    'details.drDetail.unitOfMeasure',
    'deliveryReceipt' => function($q) { $q->withTrashed(); }
])->find($id);

if (! $iar) {
    echo "IAR not found\n";
    exit(1);
}

// Normalize like controller does
$data = $iar->toArray();
$data['inspectedByUser'] = $iar->inspectedByUser ? $iar->inspectedByUser->toArray() : null;
$data['acceptedByUser'] = $iar->acceptedByUser ? $iar->acceptedByUser->toArray() : null;

$inspectedSig = $iar->inspectedSignature()->first();
$acceptedSig = $iar->acceptedSignature()->first();
$data['inspectedSignature'] = $inspectedSig ? [
    'url' => $inspectedSig->url,
    'source' => $inspectedSig->source,
    'storage_disk' => $inspectedSig->storage_disk,
    'storage_path' => $inspectedSig->storage_path,
    'signed_by_user_id' => $inspectedSig->signed_by_user_id,
    'signed_at' => $inspectedSig->created_at,
] : null;
$data['acceptedSignature'] = $acceptedSig ? [
    'url' => $acceptedSig->url,
    'source' => $acceptedSig->source,
    'storage_disk' => $acceptedSig->storage_disk,
    'storage_path' => $acceptedSig->storage_path,
    'signed_by_user_id' => $acceptedSig->signed_by_user_id,
    'signed_at' => $acceptedSig->created_at,
] : null;

$data['deliveryReceipt'] = $iar->deliveryReceipt ? $iar->deliveryReceipt->toArray() : null;

if (!empty($data['details']) && is_array($data['details'])) {
    foreach ($data['details'] as &$d) {
        $d['drDetail'] = $d['dr_detail'] ?? null;
        $d['condition'] = $d['condition'] ?? null;
        $d['propertyType'] = $d['property_type'] ?? null;
    }
}

echo json_encode(['success' => true, 'data' => $data], JSON_PRETTY_PRINT);
