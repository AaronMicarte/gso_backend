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

            // Get total counts from database
            // Count pending requests using both assetrequest and assetrequestdetail context —
            // include requests that are still pending or approved/waiting for issuance so
            // the admin dashboard reflects items that still need action.
            $pendingRequests = DB::table('assetrequest')
                ->leftJoin('assetrequestdetail', 'assetrequest.RequestID', '=', 'assetrequestdetail.RequestID')
                ->whereNull('assetrequest.deleted_at')
                ->where(function ($q) {
                    $q->whereIn('assetrequest.Status', ['pending', 'approved', 'waiting_for_issuance'])
                      ->orWhere(function ($q2) {
                          // Include requests that have details not approved yet and are not finalized
                          $q2->whereNotIn('assetrequest.Status', ['received', 'cancelled', 'rejected'])
                             ->where(function ($q3) {
                                 $q3->whereNull('assetrequestdetail.IsApprovedForIssuance')
                                    ->orWhere('assetrequestdetail.IsApprovedForIssuance', false);
                             });
                      });
                })
                ->distinct()
                ->count('assetrequest.RequestID');

            $stats = [
                'totalAssets' => DB::table('assignedproperty')->count(),
                'totalInventory' => DB::table('propertyinventorydetail')->count(),
                'availableInventory' => DB::table('propertyinventorydetail')->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('assignedproperty')
                          ->whereRaw('propertyinventorydetail.InventoryDetailID = assignedproperty.InventoryDetailID');
                })->count(),
                'totalDeliveryReceipts' => DB::table('deliveryreceipt')->count(),
                'totalUsers' => DB::table('user')->where('IsActive', 1)->count(),
                'totalDepartments' => DB::table('department')->where('IsActive', 1)->count(),
                'pendingRequests' => $pendingRequests,
                'forDisposal' => DB::table('assignedproperty')
                    ->where('PropertyStatusID', 4) // For Disposal status
                    ->count(),
                'forRepair' => DB::table('assignedproperty')
                    ->where('PropertyStatusID', 3) // For Repair status
                    ->count(),
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
                ->orderBy('loginaudit.EventTime', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'login_' . $item->id,
                        'type' => 'login',
                        'action' => $this->formatLoginAction($item->action),
                        'description' => $item->fullName ?? $item->user,
                        'user' => $item->user,
                        'timestamp' => $item->timestamp,
                        'ip_address' => $item->ip_address,
                    ];
                });

            // Recent delivery receipts (last 5)
            $deliveryReceipts = DB::table('deliveryreceipt')
                ->join('user', 'deliveryreceipt.ReceivedBy', '=', 'user.UserID')
                ->select(
                    'deliveryreceipt.DRID as id',
                    'deliveryreceipt.DRNumber as drNumber',
                    'deliveryreceipt.SupplierName as supplier',
                    'user.Username as user',
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
                        'user' => $item->supplier ?? $item->user,
                        'timestamp' => $item->timestamp,
                    ];
                });

            // Merge and sort by timestamp
            $activities = collect($loginAudits)
                ->merge($deliveryReceipts)
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
