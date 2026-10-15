<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Country;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard/stats
     * Return overview stats and chart data for the admin dashboard.
     */
    public function stats()
    {
        // ── Counts ────────────────────────────────────────────────────────────
        $totalUsers     = User::count();
        $activeBookings = Booking::whereIn('status', ['pending', 'confirmed'])->count();
        $totalRevenue   = Booking::where('payment_status', 'paid')->sum('total_amount');

        // Count all active packages from the tour_packages table
        $activePackages = \App\Models\TourPackage::where('is_active', true)->count()
                        + \App\Models\DayTour::where('is_active', true)->count();

        // ── Revenue Chart (last 7 days) ────────────────────────────────────
        $labels    = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date        = now()->subDays($i);
            $labels[]    = $date->format('D');           // Mon, Tue…
            $chartData[] = (float) Booking::where('payment_status', 'paid')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_amount');
        }

        // ── Recent Activity ────────────────────────────────────────────────
        $recentBookings = Booking::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'type'    => 'booking',
                'message' => "New booking #{$b->booking_reference} by {$b->first_name} {$b->last_name} for {$b->destination}",
                'amount'  => $b->total_amount,
                'time'    => $b->created_at->diffForHumans(),
            ]);

        $recentEnquiries = Enquiry::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'type'    => 'enquiry',
                'message' => "New enquiry from {$e->full_name} for {$e->destination}",
                'time'    => $e->created_at->diffForHumans(),
            ]);

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn($u) => [
                'type'    => 'signup',
                'message' => "{$u->name} joined PlantripOnline",
                'time'    => $u->created_at->diffForHumans(),
            ]);

        // Merge and sort all activity by recency
        $recentActivity = collect()
            ->merge($recentBookings)
            ->merge($recentEnquiries)
            ->merge($recentUsers)
            ->sortByDesc('time')
            ->values()
            ->take(10);

        // ── Pending Enquiries ──────────────────────────────────────────────
        $pendingEnquiries = Enquiry::where('status', 'pending')->count();

        return response()->json([
            'total_users'      => $totalUsers,
            'active_bookings'  => $activeBookings,
            'total_revenue'    => round($totalRevenue, 2),
            'active_packages'  => $activePackages,
            'pending_enquiries'=> $pendingEnquiries,
            'revenue_chart'    => [
                'labels' => $labels,
                'data'   => $chartData,
            ],
            'recent_activity'  => $recentActivity,
            'countries_count'  => Country::where('is_active', true)->count(),
        ]);
    }
}
