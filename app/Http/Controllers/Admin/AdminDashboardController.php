<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class AdminDashboardController extends Controller
{
    public function index()
    {
        // Marketplace statistics
        $totalUsers = User::count();

        $totalCustomers = User::whereHas('roles', function ($query) {
            $query->where('slug', 'customer');
        })->count();

        $totalVendors = Vendor::count();

        $pendingVendors = Vendor::where('status', 'pending')->count();

        $approvedVendors = Vendor::where('status', 'approved')->count();

        $rejectedVendors = Vendor::where('status', 'rejected')->count();

        // Last 12 months analytics
        $months = collect(range(11, 0))->map(function ($i) {
            return Carbon::now()->startOfMonth()->subMonths($i);
        });

        $labels = $months->map(function ($month) {
            return $month->format('M Y');
        });

        $startDate = $months->first()->copy()->startOfMonth();

        // Monthly customer registrations
        $customerRegistrations = User::whereHas('roles', function ($query) {
            $query->where('slug', 'customer');
        })
        ->where('created_at', '>=', $startDate)
        ->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('year', 'month')
        ->get()
        ->keyBy(function ($item) {
            return $item->year . '-' . $item->month;
        });

        // Monthly vendor registrations
        $vendorRegistrations = Vendor::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function ($item) {
                return $item->year . '-' . $item->month;
            });

        $customerChartData = $months->map(function ($month) use ($customerRegistrations) {
            $key = $month->year . '-' . $month->month;

            return (int) optional($customerRegistrations->get($key))->total;
        });

        $vendorChartData = $months->map(function ($month) use ($vendorRegistrations) {
            $key = $month->year . '-' . $month->month;

            return (int) optional($vendorRegistrations->get($key))->total;
        });

        // Recent vendor activity
    $recentVendors = Vendor::with('user')
                ->latest()
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
            'totalUsers',
            'totalCustomers',
            'totalVendors',
            'pendingVendors',
            'approvedVendors',
            'rejectedVendors',
            'labels',
            'customerChartData',
            'vendorChartData',
            'recentVendors'
        ));
    }
}
