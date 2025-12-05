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
            $stats = [
                'totalAssets' => DB::table('assignedproperty')->count(),
                'totalInventory' => DB::table('propertyinventorydetail')->count(),
                'pendingRequests' => DB::table('purchaserequest')
                    ->whereIn('PRStatusID', [1]) // Pending status
                    ->count(),
                'totalUsers' => DB::table('user')->where('IsActive', 1)->count(),
                'totalDepartments' => DB::table('department')->where('IsActive', 1)->count(),
                'pendingJobOrders' => DB::table('joborder')
                    ->whereIn('JobOrderStatusID', [1, 2]) // Pending or In Progress
                    ->count(),
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
                    'loginaudit.EventTime as timestamp'
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
                    ];
                });

            // Recent purchase requests (last 5)
            $purchaseRequests = DB::table('purchaserequest')
                ->join('user', 'purchaserequest.RequestedBy', '=', 'user.UserID')
                ->join('department', 'purchaserequest.DepartmentID', '=', 'department.DepartmentID')
                ->join('purchaserequeststatus', 'purchaserequest.PRStatusID', '=', 'purchaserequeststatus.PRStatusID')
                ->select(
                    'purchaserequest.PurchaseRequestID as id',
                    'purchaserequest.PRNumber as prNumber',
                    'purchaserequeststatus.StatusName as status',
                    'user.Username as user',
                    'department.DepartmentName as department',
                    'purchaserequest.CreatedDate as timestamp'
                )
                ->orderBy('purchaserequest.CreatedDate', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'pr_' . $item->id,
                        'type' => 'purchase_request',
                        'action' => 'Purchase Request ' . $item->status,
                        'description' => $item->prNumber ?? 'PR-' . $item->id,
                        'user' => $item->department,
                        'timestamp' => $item->timestamp,
                    ];
                });

            // Merge and sort by timestamp
            $activities = collect($loginAudits)
                ->merge($purchaseRequests)
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
