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
                // Total assets under this department head's department (from assignedproperty)
                'totalAssets' => DB::table('assignedproperty')
                    ->where('DepartmentID', $user->DepartmentID)
                    ->count(),

                // Currently assigned assets in this department (not returned - but assignedproperty doesn't track returns)
                'assignedAssets' => DB::table('assignedproperty')
                    ->where('DepartmentID', $user->DepartmentID)
                    ->where('PropertyStatusID', 2) // Assigned status
                    ->count(),

                // Pending issuance requests for this department head's department (if Status column exists)
                'pendingRequests' => DB::table('propertyissuance')
                    ->join('user', 'propertyissuance.IssuedTo', '=', 'user.UserID')
                    ->where('user.DepartmentID', $user->DepartmentID)
                    ->whereDate('propertyissuance.DateIssued', '>=', $now->toDateString())
                    ->count(),

                // Overdue items (this is tricky without return dates - maybe items assigned for more than 30 days)
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

            // Recent assignments in this department head's department
            $assignments = DB::table('assignedproperty')
                ->join('user', 'assignedproperty.EndUserID', '=', 'user.UserID')
                ->join('propertyinventorydetail', 'assignedproperty.InventoryDetailID', '=', 'propertyinventorydetail.InventoryDetailID')
                ->where('assignedproperty.DepartmentID', $user->DepartmentID)
                ->select(
                    'assignedproperty.PropertyID as id',
                    'assignedproperty.AssignedDate as timestamp',
                    'user.FullName as assignedTo',
                    'propertyinventorydetail.Description as itemDescription',
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
                        'message' => "Property assigned to {$item->assignedTo}",                        'time' => Carbon::parse($item->timestamp)->diffForHumans(),                        'time' => $this->getRelativeTime($item->timestamp),
                        'status' => $item->status,
                    ];
                });

            // Recent issuances in this department head's department
            $issuances = DB::table('propertyissuance')
                ->join('user', 'propertyissuance.IssuedTo', '=', 'user.UserID')
                ->join('propertyissuancedetail', 'propertyissuance.IssuanceID', '=', 'propertyissuancedetail.IssuanceID')
                ->join('propertyinventorydetail', 'propertyissuancedetail.InventoryDetailID', '=', 'propertyinventorydetail.InventoryDetailID')
                ->where('user.DepartmentID', $user->DepartmentID)
                ->select(
                    'propertyissuance.IssuanceID as id',
                    'propertyissuance.DateIssued as timestamp',
                    'user.FullName as issuedTo',
                    'propertyinventorydetail.Description as itemDescription',
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
                        'message' => "Property issued to {$item->issuedTo}",
                        'time' => $this->getRelativeTime($item->timestamp),
                        'status' => $item->status,
                    ];
                });

            // Recent property receipts in this department
            $receipts = DB::table('propertyreceived')
                ->join('inspectionacceptancereport', 'propertyreceived.IARID', '=', 'inspectionacceptancereport.IARID')
                ->where('propertyreceived.ReceivedBy', $user->UserID)
                ->select(
                    'propertyreceived.ReceivedID as id',
                    'propertyreceived.DateReceived as timestamp',
                    'inspectionacceptancereport.IARNumber as iarNumber',
                    DB::raw("'receipt' as type"),
                    DB::raw("'completed' as status")
                )
                ->orderBy('propertyreceived.DateReceived', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => 'receipt_' . $item->id,
                        'type' => $item->type,
                        'message' => "Property received from {$item->iarNumber}",
                        'time' => $this->getRelativeTime($item->timestamp),
                        'status' => $item->status,
                    ];
                });

            // Merge and sort by timestamp (most recent first)
            $activities = collect($assignments)
                ->merge($issuances)
                ->merge($receipts)
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