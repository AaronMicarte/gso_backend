<?php

namespace App\Http\Controllers;

use App\Models\AssetAuditLog;
use App\Models\Auth\User;
use App\Models\PropertyWasteReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyWasteController extends Controller
{
    private const ROLE_ADMIN = 'Admin';
    private const ROLE_GSO_STAFF = 'GSO Staff';
    private const ROLE_DEPARTMENT_HEAD = 'Department Head';

    private const ALLOWED_STATUSES = [
        'draft',
        'completed',
        'cancelled',
    ];

    private const ALLOWED_ITEM_STATUSES = [
        'pending_method',
        'method_selected',
        'for_disposal',
        'disposed',
    ];

    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canView($roleName)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = PropertyWasteReport::query()
            ->with([
                'items.disposalMethod:DisposalMethodID,MethodName,Description,IconKey',
                'items.inventoryDetail:InventoryDetailID,PropertyNumber,Description,Brand,ModelNumber,SerialNumber,ConditionID,SourceDRDetailID',
                'items.inventoryDetail.drDetail:DRDetailID,ArticleID,ItemDescription,Brand,ModelNumber,SerialNumber',
                'items.inventoryDetail.drDetail.article:ArticleID,ArticleName',
                'items.condition:ConditionID,ConditionName',
                'department:DepartmentID,DepartmentName,DepartmentCode',
                'sourceServiceRecord:ServiceRecordID,ServiceNumber,Status',
                'createdBy:UserID,FullName',
                'updatedBy:UserID,FullName',
            ]);

        if ($roleName === self::ROLE_DEPARTMENT_HEAD) {
            $query->where('DepartmentID', (int) ($user?->DepartmentID ?? 0));
        }

        if ($request->filled('status')) {
            $query->where('Status', (string) $request->query('status'));
        }

        if ($request->filled('department_id')) {
            $query->where('DepartmentID', (int) $request->query('department_id'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('WasteNumber', 'like', "%{$search}%")
                    ->orWhereHas('sourceServiceRecord', function ($sq) use ($search) {
                        $sq->where('ServiceNumber', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('PropertyNumber', 'like', "%{$search}%")
                            ->orWhere('ItemDescription', 'like', "%{$search}%")
                            ->orWhereHas('disposalMethod', function ($dq) use ($search) {
                                $dq->where('MethodName', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $perPage = max(10, min(100, (int) $request->query('per_page', 15)));
        $rows = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($rows);
    }

    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canView($roleName)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $report = PropertyWasteReport::with([
            'items.disposalMethod:DisposalMethodID,MethodName,Description,IconKey',
            'items.inventoryDetail:InventoryDetailID,PropertyNumber,Description,Brand,ModelNumber,SerialNumber,ConditionID,SourceDRDetailID',
            'items.inventoryDetail.drDetail:DRDetailID,ArticleID,ItemDescription,Brand,ModelNumber,SerialNumber',
            'items.inventoryDetail.drDetail.article:ArticleID,ArticleName',
            'items.condition:ConditionID,ConditionName',
            'department:DepartmentID,DepartmentName,DepartmentCode',
            'sourceServiceRecord:ServiceRecordID,ServiceNumber,Status',
            'createdBy:UserID,FullName',
            'updatedBy:UserID,FullName',
        ])->findOrFail($id);

        if ($roleName === self::ROLE_DEPARTMENT_HEAD && (int) $report->DepartmentID !== (int) ($user?->DepartmentID ?? 0)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($report);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = $user?->userRole?->RoleName ?? '';

        if (!$this->canManage($roleName)) {
            return response()->json(['message' => 'Unauthorized to update waste records'], 403);
        }

        $report = PropertyWasteReport::with('items')->findOrFail($id);
        $oldStatus = (string) $report->Status;

        $validated = $request->validate([
            'status' => 'nullable|in:' . implode(',', self::ALLOWED_STATUSES),
            'notes' => 'nullable|string|max:4000',
            'items' => 'nullable|array|min:1',
            'items.*.property_waste_item_id' => 'required_with:items|integer|exists:property_waste_items,PropertyWasteItemID',
            'items.*.disposal_method_id' => 'nullable|integer|exists:disposalmethod,DisposalMethodID',
            'items.*.disposal_method_note' => 'nullable|string|max:1000',
            'items.*.status' => 'nullable|in:' . implode(',', self::ALLOWED_ITEM_STATUSES),
        ]);

        return DB::transaction(function () use ($validated, $report, $user, $oldStatus, $roleName) {
            if (array_key_exists('notes', $validated)) {
                $report->Notes = $validated['notes'];
            }

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemInput) {
                    $item = $report->items->firstWhere('PropertyWasteItemID', (int) $itemInput['property_waste_item_id']);
                    if (!$item) {
                        continue;
                    }

                    $item->DisposalMethodID = $itemInput['disposal_method_id'] ?? $item->DisposalMethodID;
                    $item->DisposalMethodNote = $itemInput['disposal_method_note'] ?? $item->DisposalMethodNote;

                    $nextStatus = $itemInput['status'] ?? $item->Status;
                    if (empty($itemInput['status']) && !empty($itemInput['disposal_method_id'])) {
                        $nextStatus = 'method_selected';
                    }

                    if (empty($item->DisposalMethodID) && in_array($nextStatus, ['method_selected', 'for_disposal', 'disposed'], true)) {
                        $nextStatus = 'pending_method';
                    }

                    if (!empty($item->DisposalMethodID) && $nextStatus === 'pending_method') {
                        $nextStatus = 'method_selected';
                    }

                    $item->Status = $nextStatus;
                    $item->ActionedByUserID = (int) $user->UserID;
                    $item->save();
                }
            }

            if (array_key_exists('status', $validated)) {
                $report->Status = in_array($validated['status'], ['completed', 'cancelled'], true)
                    ? $validated['status']
                    : 'draft';
            } else {
                $latestStatuses = $report->items()->pluck('Status')->map(fn ($value) => (string) $value)->all();
                if (!empty($latestStatuses) && count(array_filter($latestStatuses, fn ($status) => $status === 'disposed')) === count($latestStatuses)) {
                    $report->Status = 'completed';
                } else {
                    $report->Status = 'draft';
                }
            }

            $report->UpdatedByUserID = (int) $user->UserID;
            $report->save();

            AssetAuditLog::createLog(
                'property_waste_report',
                (int) $report->PropertyWasteReportID,
                'updated',
                (int) $user->UserID,
                [
                    'waste_number' => (string) $report->WasteNumber,
                    'old_status' => $oldStatus,
                    'new_status' => (string) $report->Status,
                ]
            );

            return response()->json([
                'message' => 'Property waste report updated successfully',
                'report' => $report->fresh([
                    'items.disposalMethod:DisposalMethodID,MethodName,Description,IconKey',
                    'items.inventoryDetail:InventoryDetailID,PropertyNumber,Description,Brand,ModelNumber,SerialNumber,ConditionID,SourceDRDetailID',
                    'items.inventoryDetail.drDetail:DRDetailID,ArticleID,ItemDescription,Brand,ModelNumber,SerialNumber',
                    'items.inventoryDetail.drDetail.article:ArticleID,ArticleName',
                    'items.condition:ConditionID,ConditionName',
                    'department:DepartmentID,DepartmentName,DepartmentCode',
                    'sourceServiceRecord:ServiceRecordID,ServiceNumber,Status',
                    'createdBy:UserID,FullName',
                    'updatedBy:UserID,FullName',
                ]),
            ]);
        });
    }

    public function statuses(): JsonResponse
    {
        return response()->json([
            'report_statuses' => self::ALLOWED_STATUSES,
            'item_statuses' => self::ALLOWED_ITEM_STATUSES,
        ]);
    }

    public function helperDisposalMethods(): JsonResponse
    {
        $rows = \App\Models\MasterData\DisposalMethod::query()
            ->orderBy('MethodName')
            ->get(['DisposalMethodID', 'MethodName', 'Description', 'IconKey'])
            ->map(static function ($item) {
                return [
                    'DisposalMethodID' => (int) $item->DisposalMethodID,
                    'MethodName' => (string) $item->MethodName,
                    'Description' => $item->Description,
                    'IconKey' => $item->IconKey,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    private function canView(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF, self::ROLE_DEPARTMENT_HEAD], true);
    }

    private function canManage(string $roleName): bool
    {
        return in_array($roleName, [self::ROLE_ADMIN, self::ROLE_GSO_STAFF], true);
    }
}
