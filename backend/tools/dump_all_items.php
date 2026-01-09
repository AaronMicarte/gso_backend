<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Inventory\PropertyInventoryDetail;

$items = PropertyInventoryDetail::query()
    ->select('propertyinventorydetail.InventoryDetailID', 'propertyinventorydetail.PropertyStatusID', 'propertyinventorydetail.PropertyNumber')
    ->addSelect([
        'custodianName' => DB::raw("(
            SELECT COALESCE(eu.FullName, u.FullName, pi.IssuedToName)
            FROM propertyissuancedetail pidet
            JOIN propertyissuance pi ON pi.IssuanceID = pidet.IssuanceID
            LEFT JOIN enduser eu ON eu.EndUserID = pi.IssuedTo
            LEFT JOIN user u ON u.UserID = pi.IssuedTo
            WHERE pidet.InventoryDetailID = propertyinventorydetail.InventoryDetailID
              AND pi.deleted_at IS NULL
              AND pidet.deleted_at IS NULL
              AND pi.Status IN ('pending_acknowledgment','acknowledged')
            ORDER BY pi.DateIssued DESC, pi.IssuanceID DESC
            LIMIT 1
        ) as custodianName")
    ])
    ->orderBy('propertyinventorydetail.InventoryDetailID')
    ->limit(20)
    ->get();

header('Content-Type: application/json');
echo $items->toJson(JSON_PRETTY_PRINT);
