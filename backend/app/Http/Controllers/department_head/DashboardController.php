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
            $departmentId = $user->DepartmentID;

            // Get department head-specific stats
            $stats = [
                // Total assets under this department's custody
                'totalAssets' => DB::table('assignedproperty')
                    ->where('DepartmentID', $departmentId)
                    ->count(),

                // Currently issued assets in this department
                'assignedAssets' => DB::table('assignedproperty')
                    ->join('propertystatus', 'assignedproperty.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
                    ->where('assignedproperty.DepartmentID', $departmentId)
                    ->whereIn('propertystatus.StatusName', ['Issued', 'Assigned'])
                    ->count(),

                // Items ready for issuance that were requested by this department
                'forIssuance' => DB::table('propertyinventorydetail')
                    ->join('propertystatus', 'propertyinventorydetail.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
                    ->join('deliveryreceiptdetail', 'propertyinventorydetail.SourceDRDetailID', '=', 'deliveryreceiptdetail.DRDetailID')
                    ->join('deliveryreceipt', 'deliveryreceiptdetail.DRID', '=', 'deliveryreceipt.DRID')
                    ->join('user', 'deliveryreceipt.RequestedBy', '=', 'user.UserID')
                    ->where('user.DepartmentID', $departmentId)
                    ->whereIn('propertystatus.StatusName', ['For issuance', 'For Issuance', 'To be issued', 'Available'])
                    ->count(),

                // Items assigned more than 30 days ago (potentially overdue for return)
                'overdueReturns' => DB::table('assignedproperty')
                    ->join('propertystatus', 'assignedproperty.PropertyStatusID', '=', 'propertystatus.PropertyStatusID')
                    ->where('assignedproperty.DepartmentID', $departmentId)
                    ->whereIn('propertystatus.StatusName', ['Issued', 'Assigned'])
                    ->where('assignedproperty.AssignedDate', '<', $now->copy()->subDays(30)->toDateString())
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

            $departmentId = $user->DepartmentID;

            // Recent property assignments in this department
            $assignments = DB::table('assignedproperty')
                ->leftJoin('enduser', 'assignedproperty.EndUserID', '=', 'enduser.EndUserID')
                ->where('assignedproperty.DepartmentID', $departmentId)
                ->select(
                    'assignedproperty.PropertyID as id',
                    'assignedproperty.AssignedDate as timestamp',
                    'assignedproperty.Description as itemDescription',
                    'assignedproperty.PropertyStatusID as statusId',
                    'enduser.FullName as assignedTo',
                    DB::raw("'assignment' as type")
                )
                ->orderBy('assignedproperty.AssignedDate', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $statusText = ((int) $item->statusId === 2) ? 'completed' : 'pending';

                    return [
                        'id' => 'assignment_' . $item->id,
                        'type' => $item->type,
                        'message' => "Property assigned to {$item->assignedTo}",
                        'time' => $this->getRelativeTime($item->timestamp),
                        'timestamp' => $item->timestamp,
                        'status' => $statusText,
                    ];
                });

            // Recent issuances tied to this department's end users
            $issuances = DB::table('propertyissuance')
                ->leftJoin('enduser', 'propertyissuance.IssuedTo', '=', 'enduser.EndUserID')
                ->where('enduser.DepartmentID', $departmentId)
                ->select(
                    'propertyissuance.IssuanceID as id',
                    'propertyissuance.DateIssued as timestamp',
                    'propertyissuance.IssuedToName as issuedToName',
                    'propertyissuance.DocumentType as documentType',
                    'propertyissuance.DocumentNumber as documentNumber',
                    'propertyissuance.Status as issuanceStatus',
                    DB::raw("'issuance' as type")
                )
                ->orderBy('propertyissuance.DateIssued', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $statusText = $item->issuanceStatus === 'acknowledged' ? 'completed' : 'pending';
                    $issuedTo = $item->issuedToName ?: 'End user';

                    return [
                        'id' => 'issuance_' . $item->id,
                        'type' => $item->type,
                        'message' => strtoupper($item->documentType) . " issued to {$issuedTo}",
                        'time' => $this->getRelativeTime($item->timestamp),
                        'timestamp' => $item->timestamp,
                        'status' => $statusText,
                    ];
                });

            // Merge and sort by timestamp (most recent first)
            $activities = collect($assignments)
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