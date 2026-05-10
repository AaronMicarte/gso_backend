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
            $activities = [];

            // Recent login events (last 10)
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
                ->addSelect('user.FullName as fullName')
                ->orderBy('loginaudit.EventTime', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'login_' . $item->id,
                        'type' => 'login',
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
                        'action' => 'New Delivery Receipt',
                        'description' => $item->drNumber ?? 'DR-' . $item->id,
                        'performed_by' => $item->fullName ?? $item->user,
                        'timestamp' => $item->timestamp,
                    ];
                });

            // Recent asset audit logs (generic activity) - include performer and details JSON
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

                    // Humanize action text a bit
                    $actionText = str_replace('_', ' ', $item->action);
                    $actionText = ucwords($actionText);

                    // Build a short description from details when possible
                    $desc = null;
                    if (is_array($meta)) {
                        if (!empty($meta['dr_number'])) {
                            $desc = $meta['dr_number'];
                        } elseif (!empty($meta['inventory_id'])) {
                            $desc = 'Inventory #' . $meta['inventory_id'];
                        }
                    }

                    return [
                        'id' => 'audit_' . $item->id,
                        'type' => 'audit',
                        'action' => $actionText,
                        'description' => $desc ?? ($item->entity_type . ' #' . $item->entity_id),
                        'performed_by' => $item->fullName ?? $item->user,
                        'meta' => $meta,
                        'timestamp' => $item->timestamp,
                    ];
                });
            // Merge and sort by timestamp
            $activities = collect($loginAudits)
                ->merge($deliveryReceipts)
                ->merge($auditLogs)
                ->sortByDesc('timestamp')
                ->take(10)
                ->values()
                ->map(function ($item) {
                    // Add relative time
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
}
