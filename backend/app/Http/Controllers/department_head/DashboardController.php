<?php

namespace App\Http\Controllers\department_head;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get department head dashboard statistics
     * Returns counts specific to the authenticated department head
     */
    public function getStats()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Set timezone to Manila (PH)
            $now = Carbon::now('Asia/Manila');

            // Get department head-specific stats
            $stats = [
                // Total assets assigned to this department head's department
                'totalAssets' => DB::table('assignedproperty')
                    ->where('DepartmentID', $user->DepartmentID)
                    ->count(),

                // Currently assigned assets (all assigned properties in this department)
                'assignedAssets' => DB::table('assignedproperty')
                    ->where('DepartmentID', $user->DepartmentID)
                    ->count(),

                // Pending asset requests from this department head's department that need approval
                'pendingRequests' => DB::table('assetrequest')
                    ->where('DepartmentID', $user->DepartmentID)
                    ->where('Status', 'pending')
                    ->count(),

                // Items assigned more than 30 days ago (potentially overdue for return)
                'overdueReturns' => DB::table('assignedproperty')
                    ->where('DepartmentID', $user->DepartmentID)
                    ->where('AssignedDate', '<', $now->copy()->subDays(30)->toDateString())
                    ->count(),
            ];

            return response()->json([
                'stats' => $stats,
                'generatedAt' => $now->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch department head dashboard stats',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Get recent activities for the department head dashboard
     */
    public function getActivities()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $activities = [];

            // Recent asset requests from this department head's department
            $requests = DB::table('assetrequest')
                ->join('user', 'assetrequest.RequestedBy', '=', 'user.UserID')
                ->leftJoin('asset_request_signatures', function($join) {
                    $join->on('assetrequest.RequestID', '=', 'asset_request_signatures.request_id')
                         ->where('asset_request_signatures.role', '=', 'requester');
                })
                ->where('assetrequest.DepartmentID', $user->DepartmentID)
                ->select(
                    'assetrequest.RequestID as id',
                    'assetrequest.created_at as timestamp',
                    'assetrequest.Status as status',
                    'user.FullName as requesterName',
                    'assetrequest.Purpose as purpose',
                    DB::raw("'request' as type")
                )
                ->orderBy('assetrequest.created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    $status = $item->status;
                    $statusText = match($status) {
                        'pending' => 'pending',
                        'approved' => 'completed',
                        'rejected' => 'completed',
                        'issued' => 'completed',
                        'cancelled' => 'completed',
                        default => 'pending'
                    };

                    return [
                        'id' => 'request_' . $item->id,
                        'type' => $item->type,
                        'message' => "Asset request by {$item->requesterName} - " . ucfirst($status),
                        'time' => $this->getRelativeTime($item->timestamp),
                        'status' => $statusText,
                    ];
                });

            // Recent property assignments in this department (simplified)
            $assignments = DB::table('assignedproperty')
                ->join('user', 'assignedproperty.EndUserID', '=', 'user.UserID')
                ->where('assignedproperty.DepartmentID', $user->DepartmentID)
                ->select(
                    'assignedproperty.PropertyID as id',
                    'assignedproperty.AssignedDate as timestamp',
                    'user.FullName as assignedTo',
                    'assignedproperty.Description as itemDescription',
                    DB::raw("'assignment' as type"),
                    DB::raw("'completed' as status")
                )
                ->orderBy('assignedproperty.AssignedDate', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'assignment_' . $item->id,
                        'type' => $item->type,
                        'message' => "Property assigned to {$item->assignedTo}",
                        'time' => $this->getRelativeTime($item->timestamp),
                        'status' => $item->status,
                    ];
                });

            // Recent issuances in this department (simplified)
            $issuances = DB::table('propertyissuance')
                ->join('user', 'propertyissuance.IssuedBy', '=', 'user.UserID')
                ->where('user.DepartmentID', $user->DepartmentID)
                ->select(
                    'propertyissuance.IssuanceID as id',
                    'propertyissuance.DateIssued as timestamp',
                    'propertyissuance.IssuedToName as issuedTo',
                    'propertyissuance.DocumentType as documentType',
                    'propertyissuance.DocumentNumber as documentNumber',
                    DB::raw("'issuance' as type"),
                    DB::raw("'completed' as status")
                )
                ->orderBy('propertyissuance.DateIssued', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'issuance_' . $item->id,
                        'type' => $item->type,
                        'message' => ucfirst($item->documentType) . " issued to {$item->issuedTo}",
                        'time' => $this->getRelativeTime($item->timestamp),
                        'status' => $item->status,
                    ];
                });

            // Merge and sort by timestamp (most recent first)
            $activities = collect($requests)
                ->merge($assignments)
                ->merge($issuances)
                ->sortByDesc(function ($item) {
                    return strtotime($item['timestamp'] ?? '1970-01-01');
                })
                ->take(10)
                ->values();

            return response()->json([
                'activities' => $activities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch department head activities',
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
}