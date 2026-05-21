<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditLogsController extends Controller
{
    /**
     * Cached department names keyed by DepartmentID.
     *
     * @var array<int, string>
     */
    private array $departmentNameCache = [];

    /**
     * Get paginated activity/audit logs
     */
    public function getActivityLogs(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $search = $request->get('search', '');
            $entityType = $request->get('entity_type', '');
            $startDate = $request->get('start_date', '');
            $endDate = $request->get('end_date', '');

            // Build query
            $query = DB::table('asset_audit_logs')
                ->leftJoin('user', 'asset_audit_logs.PerformedBy', '=', 'user.UserID')
                ->select(
                    'asset_audit_logs.AuditID as id',
                    'asset_audit_logs.EntityType as entity_type',
                    'asset_audit_logs.EntityID as entity_id',
                    'asset_audit_logs.Action as action',
                    'asset_audit_logs.Details as details',
                    'asset_audit_logs.IPAddress as ip_address',
                    'user.Username as user',
                    'user.FullName as performed_by',
                    'asset_audit_logs.CreatedAt as timestamp'
                )
                // Keep audit list at header level to avoid duplicate detail/item rows.
                ->whereNotIn('asset_audit_logs.EntityType', [
                    'inventory_detail',
                    'delivery_receipt_detail',
                    'property_transfer_item',
                    'asset_service_record_detail',
                ]);

            // Apply filters
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('asset_audit_logs.Action', 'like', "%{$search}%")
                        ->orWhere('user.FullName', 'like', "%{$search}%")
                        ->orWhere('user.Username', 'like', "%{$search}%")
                        ->orWhere('asset_audit_logs.Details', 'like', "%{$search}%");
                });
            }

            if (!empty($entityType)) {
                $query->where('asset_audit_logs.EntityType', $entityType);
            }

            if (!empty($startDate)) {
                $query->whereDate('asset_audit_logs.CreatedAt', '>=', $startDate);
            }

            if (!empty($endDate)) {
                $query->whereDate('asset_audit_logs.CreatedAt', '<=', $endDate);
            }

            // Get total count before pagination
            $total = $query->count();

            // Apply ordering and pagination
            $logs = $query->orderBy('asset_audit_logs.CreatedAt', 'desc')
                ->skip(($page - 1) * $perPage)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    $meta = $this->decodeDetails($item->details);
                    $entityLabel = $this->formatEntityLabel($item->entity_type);
                    $actionLabel = $this->formatActionLabel($item->action);
                    $summary = $this->buildSummary($meta, $item->entity_type, (int) $item->entity_id);
                    $highlights = $this->buildDetailHighlights($meta, $item->entity_type);

                    return [
                        'id' => $item->id,
                        'type' => 'audit',
                        'action' => $actionLabel,
                        'action_key' => $item->action,
                        'entity_label' => $entityLabel,
                        'description' => $summary,
                        'summary' => $summary,
                        'detail_highlights' => $highlights,
                        'performed_by' => $item->performed_by ?? $item->user ?? 'System',
                        'entity_type' => $item->entity_type,
                        'meta' => $meta,
                        'ip_address' => $item->ip_address,
                        'timestamp' => $item->timestamp,
                        'time_ago' => $this->getRelativeTime($item->timestamp),
                    ];
                });

            $lastPage = ceil($total / $perPage);

            return response()->json([
                'data' => $logs,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch activity logs',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Decode the details payload into an array.
     */
    private function decodeDetails($details): ?array
    {
        if (is_array($details)) {
            return $details;
        }

        if (!is_string($details) || trim($details) === '') {
            return null;
        }

        $decoded = json_decode($details, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Convert a machine entity type into a readable label.
     */
    private function formatEntityLabel(string $entityType): string
    {
        return match ($entityType) {
            'delivery_receipt' => 'Delivery Receipt',
            'delivery_receipt_detail' => 'Delivery Receipt Detail',
            'inventory_detail' => 'Inventory Detail',
            'property_transfer' => 'Property Transfer',
            'property_transfer_item' => 'Property Transfer Item',
            'property_borrow' => 'Property Borrow',
            'property_return' => 'Property Return',
            'asset_request' => 'Asset Request',
            'asset_issuance' => 'Asset Issuance',
            default => ucwords(str_replace('_', ' ', $entityType)),
        };
    }

    /**
     * Build a contextual action label for the table.
     */
    private function formatActionLabel(string $action): string
    {
        return match ($action) {
            'created' => 'Created record',
            'created_from_dr' => 'Created from delivery receipt',
            'transfer_requested' => 'Transfer request submitted',
            'transfer_releasing_approved', 'releasing_approved' => 'Transfer release approved',
            'transfer_approved', 'approved' => 'Transfer approved',
            'transfer_releasing_signed', 'releasing_signed' => 'Transfer releasing office signed',
            'transfer_receiving_signed', 'receiving_signed' => 'Transfer receiving office signed',
            'transfer_completed' => 'Transfer completed',
            'borrow_requested' => 'Borrow request submitted',
            'borrow_releasing_approved' => 'Borrow release approved',
            'borrow_approved' => 'Borrow approved',
            'borrow_borrowed' => 'Borrow item released',
            'borrow_returned' => 'Borrow item returned',
            'borrow_partially_returned' => 'Borrow item partially returned',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            default => ucwords(str_replace('_', ' ', $action)),
        };
    }

    /**
     * Build the summary line displayed in the details column.
     */
    private function buildSummary(?array $meta, string $entityType, int $entityId): string
    {
        if (!is_array($meta)) {
            return $this->formatEntityLabel($entityType) . ' activity recorded';
        }

        $candidates = [
            'dr_number',
            'transfer_number',
            'borrow_number',
            'request_number',
            'property_number',
            'property_tag',
            'reference',
        ];

        foreach ($candidates as $key) {
            if (!empty($meta[$key])) {
                return (string) $meta[$key];
            }
        }

        if (!empty($meta['supplier'])) {
            return (string) $meta['supplier'];
        }

        if (!empty($meta['inventory_id'])) {
            return 'Inventory record ' . $meta['inventory_id'];
        }

        return $this->formatEntityLabel($entityType) . ' activity recorded';
    }

    /**
     * Extract a few useful chips from the raw details payload.
     */
    private function buildDetailHighlights(?array $meta, string $entityType): array
    {
        if (!is_array($meta)) {
            return [];
        }

        $keysByEntity = match ($entityType) {
            'delivery_receipt', 'delivery_receipt_detail' => ['dr_number', 'supplier', 'details_count'],
            'property_transfer', 'property_transfer_item' => ['transfer_number', 'from_department_id', 'to_department_id'],
            'property_borrow' => ['borrow_number', 'from_department_id', 'to_department_id', 'due_date'],
            'inventory_detail' => ['property_number', 'dr_number', 'inventory_id'],
            default => ['reference', 'supplier', 'property_number', 'inventory_id'],
        };

        $highlights = [];

        foreach ($keysByEntity as $key) {
            if (empty($meta[$key])) {
                continue;
            }

            $value = $meta[$key];

            if ($key === 'details_count' && is_array($meta['details'] ?? null)) {
                $value = count($meta['details']);
            }

            if ($key === 'from_department_id') {
                $value = 'From ' . $this->resolveDepartmentName((int) $value);
            } elseif ($key === 'to_department_id') {
                $value = 'To ' . $this->resolveDepartmentName((int) $value);
            } elseif ($key === 'inventory_id') {
                $value = 'Inventory ' . $value;
            }

            $highlights[] = (string) $value;

            if (count($highlights) >= 3) {
                break;
            }
        }

        return $highlights;
    }

    /**
     * Resolve a department name from its ID, with a small in-memory cache.
     */
    private function resolveDepartmentName(int $departmentId): string
    {
        if ($departmentId <= 0) {
            return 'Unknown Department';
        }

        if (isset($this->departmentNameCache[$departmentId])) {
            return $this->departmentNameCache[$departmentId];
        }

        $departmentName = DB::table('department')
            ->where('DepartmentID', $departmentId)
            ->value('DepartmentName');

        $resolvedName = $departmentName ?: 'Department ' . $departmentId;
        $this->departmentNameCache[$departmentId] = $resolvedName;

        return $resolvedName;
    }

    /**
     * Get unique entity types for filtering
     */
    public function getEntityTypes()
    {
        try {
            $types = DB::table('asset_audit_logs')
                ->distinct()
                ->pluck('EntityType')
                ->sort()
                ->values();

            return response()->json([
                'entity_types' => $types,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch entity types',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Get relative time string (e.g., "2 mins ago")
     */
    private function getRelativeTime(?string $timestamp): string
    {
        if (!$timestamp) {
            return 'Unknown';
        }

        try {
            $time = \Carbon\Carbon::parse($timestamp, 'Asia/Manila');
            return $time->diffForHumans();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
