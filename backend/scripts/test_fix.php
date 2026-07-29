<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$departmentId = 23;

echo "Testing dedup-fixed accountableOfficer for Dept: $departmentId" . PHP_EOL;

$officers = [];
$seen = [];  // keyed by strtolower(name) — dept head wins

// 1. Department Head first
$deptHead = DB::table('user as u')
    ->join('userrole as ur', 'ur.UserRoleID', '=', 'u.UserRoleID')
    ->where('u.DepartmentID', $departmentId)
    ->where('u.IsActive', 1)
    ->whereRaw("LOWER(COALESCE(ur.RoleName, '')) = ?", ['department head'])
    ->orderByDesc('u.UserID')
    ->value('u.FullName');

if (!empty($deptHead)) {
    $headClean = trim((string) $deptHead);
    $seen[strtolower($headClean)] = true;  // plain name key
    $officers[] = [
        'name'   => $headClean,
        'label'  => $headClean . ' (Department Head)',
        'source' => 'Department Head',
        'type'   => 'department_head',
    ];
}

// 2. Assignment officers — skip if already dept head
$assignmentOfficers = DB::table('property_issuance_assignments as pia')
    ->join('propertyissuance as pi', 'pi.IssuanceID', '=', 'pia.issuance_id')
    ->leftJoin('user as u', 'u.UserID', '=', 'pi.IssuedTo')
    ->leftJoin('propertyissuancedetail as pid', 'pid.IssuanceDetailID', '=', 'pia.issuance_detail_id')
    ->leftJoin('assignedproperty as ap', 'ap.InventoryDetailID', '=', 'pid.InventoryDetailID')
    ->where(function ($q) use ($departmentId) {
        $q->where('u.DepartmentID', $departmentId)
          ->orWhere('ap.DepartmentID', $departmentId);
    })
    ->whereNotNull('pia.receiver_name')
    ->where('pia.receiver_name', '!=', '')
    ->distinct()
    ->pluck('pia.receiver_name');

foreach ($assignmentOfficers as $name) {
    $nameClean = trim((string) $name);
    if (!empty($nameClean) && !isset($seen[strtolower($nameClean)])) {
        $seen[strtolower($nameClean)] = true;
        $officers[] = [
            'name'   => $nameClean,
            'label'  => $nameClean,
            'source' => 'Property Issuance Assignment',
            'type'   => 'assignment',
        ];
    }
}

// 3. DR Requester — skip if already present
$drRequester = DB::table('deliveryreceipt as dr')
    ->leftJoin('user as u', 'u.UserID', '=', 'dr.RequestedBy')
    ->where('u.DepartmentID', $departmentId)
    ->orderByDesc('dr.DRID')
    ->value('u.FullName');

if (!empty($drRequester)) {
    $drClean = trim((string) $drRequester);
    if (!isset($seen[strtolower($drClean)])) {
        $seen[strtolower($drClean)] = true;
        $officers[] = [
            'name'   => $drClean,
            'label'  => $drClean,
            'source' => 'Delivery Receipt (DR) Requester',
            'type'   => 'dr_requester',
        ];
    }
}

echo "Officers count: " . count($officers) . PHP_EOL;
foreach ($officers as $o) {
    echo "  - [{$o['type']}] {$o['label']}" . PHP_EOL;
}
