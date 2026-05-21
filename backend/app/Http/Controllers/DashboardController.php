<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     * Returns counts and recent activity for the admin dashboard
     */
    public function getStats()
    {
        try {
            // Set timezone to Manila (PH)
            $now = Carbon::now('Asia/Manila');

            $statusCounts = function (array $names): int {
                return DB::table('propertyinventorydetail')
                    ->join('propertystatus', 'propertyinventorydetail.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
                    ->whereIn('propertystatus.StatusName', $names)
                    ->count();
            };

            $stats = [
                'totalInventory' => DB::table('propertyinventorydetail')->count(),
                'forIssuanceInventory' => $statusCounts(['For issuance', 'For Issuance', 'To be issued', 'Available']),
                'issuedInventory' => $statusCounts(['Issued', 'Assigned']),
                'totalDeliveryReceipts' => DB::table('deliveryreceipt')->whereNull('deleted_at')->count(),
                'totalUsers' => DB::table('user')->where('IsActive', 1)->count(),
                'totalDepartments' => DB::table('department')->where('IsActive', 1)->count(),
                'forDisposal' => $statusCounts(['For Disposal', 'Disposed']),
                'forRepair' => $statusCounts(['For Repair']),
            ];

            return response()->json([
                'stats' => $stats,
                'generatedAt' => $now->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch dashboard stats',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Get recent activity for the dashboard
     * Combines login audits, purchase requests, and property transfers
     */
    public function getRecentActivity()
    {
        try {
            // Recent login events (last 5)
            $loginAudits = DB::table('loginaudit')
                ->join('user', 'loginaudit.UserID', '=', 'user.UserID')
                ->select(
                    'loginaudit.LoginAuditID as id',
                    'loginaudit.Status as action',
                    'user.Username as user',
                    'user.FullName as fullName',
                    'loginaudit.EventTime as timestamp',
                    'loginaudit.IPAddress as ip_address'
                )
                ->orderBy('loginaudit.EventTime', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $actionKey = strtolower((string) $item->action);

                    return [
                        'id' => 'login_' . $item->id,
                        'type' => 'login',
                        'entity_type' => 'login',
                        'entity_label' => 'Login',
                        'action_key' => $actionKey,
                        'action' => $this->formatLoginAction($item->action),
                        'description' => $item->fullName ?? $item->user,
                        'performed_by' => $item->fullName ?? $item->user,
                        'timestamp' => $item->timestamp,
                        'ip_address' => $item->ip_address,
                    ];
                });

            // Recent delivery receipts (last 5)
            $deliveryReceipts = DB::table('deliveryreceipt')
                ->join('user', 'deliveryreceipt.ReceivedBy', '=', 'user.UserID')
                ->leftJoin('supplier', 'deliveryreceipt.SupplierID', '=', 'supplier.SupplierID')
                ->select(
                    'deliveryreceipt.DRID as id',
                    'deliveryreceipt.DRNumber as drNumber',
                    'supplier.SupplierName as supplier',
                    'user.Username as user',
                    'user.FullName as fullName',
                    'deliveryreceipt.CreatedDate as timestamp'
                )
                ->orderBy('deliveryreceipt.CreatedDate', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'dr_' . $item->id,
                        'type' => 'delivery_receipt',
                        'entity_type' => 'delivery_receipt',
                        'entity_label' => 'Delivery Receipt',
                        'action_key' => 'created',
                        'action' => 'Delivery receipt created',
                        'description' => $item->drNumber ?? 'Delivery receipt record created',
                        'performed_by' => $item->fullName ?? $item->user,
                        'timestamp' => $item->timestamp,
                    ];
                });

            // Recent asset audit logs with action metadata.
            $auditLogs = DB::table('asset_audit_logs')
                ->leftJoin('user', 'asset_audit_logs.PerformedBy', '=', 'user.UserID')
                ->select(
                    'asset_audit_logs.AuditID as id',
                    'asset_audit_logs.EntityType as entity_type',
                    'asset_audit_logs.EntityID as entity_id',
                    'asset_audit_logs.Action as action',
                    'asset_audit_logs.Details as details',
                    'user.Username as user',
                    'user.FullName as fullName',
                    'asset_audit_logs.CreatedAt as timestamp'
                )
                // Keep activity feed at header level to avoid duplicate detail/item noise.
                ->whereNotIn('asset_audit_logs.EntityType', [
                    'inventory_detail',
                    'delivery_receipt_detail',
                    'property_transfer_item',
                    'asset_service_record_detail',
                ])
                ->orderBy('asset_audit_logs.CreatedAt', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    $meta = null;
                    try {
                        $meta = json_decode($item->details, true);
                    } catch (\Exception $e) {
                        $meta = null;
                    }

                    $actionKey = strtolower((string) $item->action);
                    $entityType = strtolower((string) $item->entity_type);
                    $entityLabel = ucwords(str_replace('_', ' ', $entityType));

                    return [
                        'id' => 'audit_' . $item->id,
                        'type' => $entityType ?: 'audit',
                        'entity_type' => $entityType,
                        'entity_label' => $entityLabel,
                        'action_key' => $actionKey,
                        'action' => $this->formatAuditAction($actionKey),
                        'description' => $this->buildAuditSummary($meta, $entityLabel),
                        'performed_by' => $item->fullName ?? $item->user,
                        'meta' => $meta,
                        'timestamp' => $item->timestamp,
                    ];
                });

            $activities = collect($loginAudits)
                ->merge($deliveryReceipts)
                ->merge($auditLogs)
                ->sortByDesc('timestamp')
                ->take(15)
                ->values()
                ->map(function ($item) {
                    $item['timeAgo'] = $this->getRelativeTime($item['timestamp']);
                    return $item;
                });

            return response()->json([
                'activities' => $activities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch recent activity',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Format login action for display
     */
    private function formatLoginAction(string $status): string
    {
        return match ($status) {
            'Login' => 'User logged in',
            'Logout' => 'User logged out',
            'FailedLogin' => 'Failed login attempt',
            default => $status,
        };
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
            $time = Carbon::parse($timestamp, 'Asia/Manila');
            return $time->diffForHumans();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Build explicit, human-readable action labels for audit keys.
     */
    private function formatAuditAction(string $action): string
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
     * Build a concise summary without hash-style fallback identifiers.
     */
    private function buildAuditSummary(?array $meta, string $entityLabel): string
    {
        if (!is_array($meta)) {
            return $entityLabel . ' activity recorded';
        }

        $candidates = [
            'dr_number',
            'transfer_number',
            'borrow_number',
            'request_number',
            'property_number',
            'property_tag',
            'reference',
            'supplier',
        ];

        foreach ($candidates as $key) {
            if (!empty($meta[$key])) {
                return (string) $meta[$key];
            }
        }

        return $entityLabel . ' activity recorded';
    }
}
