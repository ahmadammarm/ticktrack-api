<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        try {
            $currentMonth = Carbon::now()->startOfMonth();
            $endOfMonth = $currentMonth->copy()->endOfMonth();

            $totalTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])->count();
            $activeTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                ->where('status', '!=', 'solved')
                ->count();

            $solvedTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                ->where('status', 'solved')
                ->count();

            $avgResolutionTime = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                ->where('status', 'solved')
                ->whereNotNull('solved_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, solved_at)) as avg_resolution_time'))
                ->value('avg_resolution_time') ?? 0;

            $statusDistribution = [
                'open' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                    ->where('status', 'open')
                    ->count(),
                'in_progress' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                    ->where('status', 'in_progress')
                    ->count(),
                'solved' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                    ->where('status', 'solved')
                    ->count(),
                'rejected' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                    ->where('status', 'rejected')
                    ->count(),
            ];

            $dashboardData = [
                'total_tickets' => $totalTickets,
                'active_tickets' => $activeTickets,
                'solved_tickets' => $solvedTickets,
                'avg_resolution_time' => round($avgResolutionTime, 1),
                'status_distribution' => $statusDistribution,
            ];

            return response()->json([
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => new DashboardResource($dashboardData)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve dashboard statistics: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
