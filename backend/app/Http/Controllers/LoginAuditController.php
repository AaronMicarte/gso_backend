<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoginAuditController extends Controller
{
    /**
     * Get paginated login audit logs
     */
    public function getLoginAudits(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $search = $request->get('search', '');
            $status = $request->get('status', '');
            $startDate = $request->get('start_date', '');
            $endDate = $request->get('end_date', '');

            // Build query
            $query = DB::table('loginaudit')
                ->leftJoin('user', 'loginaudit.UserID', '=', 'user.UserID')
                ->select(
                    'loginaudit.LoginAuditID as id',
                    'loginaudit.UserID as user_id',
                    'loginaudit.Status as status',
                    'loginaudit.EventTime as timestamp',
                    'loginaudit.IPAddress as ip_address',
                    'user.Username as username',
                    'user.FullName as performed_by'
                );

            // Apply filters
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('user.FullName', 'like', "%{$search}%")
                        ->orWhere('user.Username', 'like', "%{$search}%")
                        ->orWhere('loginaudit.IPAddress', 'like', "%{$search}%");
                });
            }

            if (!empty($status)) {
                $query->where('loginaudit.Status', $status);
            }

            if (!empty($startDate)) {
                $query->whereDate('loginaudit.EventTime', '>=', $startDate);
            }

            if (!empty($endDate)) {
                $query->whereDate('loginaudit.EventTime', '<=', $endDate);
            }

            // Get total count before pagination
            $total = $query->count();

            // Apply ordering and pagination
            $logs = $query->orderBy('loginaudit.EventTime', 'desc')
                ->skip(($page - 1) * $perPage)
                ->limit($perPage)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'login',
                        'status' => $item->status,
                        'performed_by' => $item->performed_by ?? $item->username ?? 'Unknown User',
                        'username' => $item->username,
                        'ip_address' => $item->ip_address,
                        'timestamp' => $item->timestamp,
                        'time_ago' => $this->getRelativeTime($item->timestamp),
                        'status_badge' => $this->getStatusBadge($item->status),
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
                'message' => 'Failed to fetch login audits',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Get unique login statuses for filtering
     */
    public function getLoginStatuses()
    {
        try {
            $statuses = DB::table('loginaudit')
                ->distinct()
                ->pluck('Status')
                ->sort()
                ->values();

            return response()->json([
                'statuses' => $statuses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch statuses',
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
            $time = Carbon::parse($timestamp, 'Asia/Manila');
            return $time->diffForHumans();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get badge info for status
     */
    private function getStatusBadge(string $status): array
    {
        return match (strtolower($status)) {
            'login' => [
                'bgClass' => 'bg-emerald-100 text-emerald-800',
                'label' => 'Login',
                'icon' => 'LogIn'
            ],
            'logout' => [
                'bgClass' => 'bg-slate-100 text-slate-800',
                'label' => 'Logout',
                'icon' => 'LogOut'
            ],
            'failedlogin' => [
                'bgClass' => 'bg-rose-100 text-rose-800',
                'label' => 'Failed Login',
                'icon' => 'AlertCircle'
            ],
            default => [
                'bgClass' => 'bg-slate-100 text-slate-800',
                'label' => $status,
                'icon' => 'Clock'
            ]
        };
    }
}
