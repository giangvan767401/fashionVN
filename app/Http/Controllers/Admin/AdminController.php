<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $totalRevenue = \App\Models\Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $activeUsers = \App\Models\User::count();
        $totalOrders = \App\Models\Order::count();
        $pageViews = 284302; // Placeholder for now

        // Dữ liệu biểu đồ 9 tháng
        $chartMonths = [];
        $revenueData = [];
        $ordersData = [];

        for ($i = 8; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $chartMonths[] = 'T' . $date->format('n');
            
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $revenueData[] = \App\Models\Order::whereBetween('created_at', [$start, $end])
                                            ->where('status', '!=', 'cancelled')
                                            ->sum('total_amount');
            $ordersData[] = \App\Models\Order::whereBetween('created_at', [$start, $end])->count();
        }

        // Mục tiêu tháng
        $currentMonthRevenue = end($revenueData);
        $targetRevenue = 50000000; // Mục tiêu 50tr
        $revenueGoalPercent = $targetRevenue > 0 ? min(100, round(($currentMonthRevenue / $targetRevenue) * 100)) : 0;

        $currentMonthUsers = \App\Models\User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $targetUsers = 100; // Mục tiêu 100 khách
        $usersGoalPercent = $targetUsers > 0 ? min(100, round(($currentMonthUsers / $targetUsers) * 100)) : 0;

        $conversionRate = 76; // Tạm thời hardcode cho tỷ lệ chuyển đổi

        return view('admin.dashboard', compact(
            'totalRevenue', 'activeUsers', 'totalOrders', 'pageViews',
            'chartMonths', 'revenueData', 'ordersData',
            'currentMonthRevenue', 'targetRevenue', 'revenueGoalPercent',
            'currentMonthUsers', 'targetUsers', 'usersGoalPercent',
            'conversionRate'
        ));
    }

    public function analytics(): View
    {
        return view('admin.analytics');
    }

    public function getAnalyticsData(Request $request)
    {
        $period = $request->input('period', '7d');
        $now = \Carbon\Carbon::now();
        $startDate = $now->copy();
        $previousStartDate = $now->copy();
        $previousEndDate = $now->copy();
        $currentEndDate = $now->copy();

        $format = 'Y-m-d';
        $groupBy = 'DATE(created_at)';
        
        switch ($period) {
            case '24h':
                $startDate = $now->copy()->subHours(24);
                $previousStartDate = $startDate->copy()->subHours(24);
                $previousEndDate = $startDate->copy();
                $format = 'H:00';
                $groupBy = 'HOUR(created_at)'; // Assuming MySQL
                break;
            case '7d':
                $startDate = $now->copy()->subDays(7);
                $previousStartDate = $startDate->copy()->subDays(7);
                $previousEndDate = $startDate->copy();
                $format = 'd/m';
                break;
            case '30d':
                $startDate = $now->copy()->subDays(30);
                $previousStartDate = $startDate->copy()->subDays(30);
                $previousEndDate = $startDate->copy();
                $format = 'd/m';
                break;
            case '1y':
                $startDate = $now->copy()->subMonths(12)->startOfMonth();
                $previousStartDate = $startDate->copy()->subMonths(12);
                $previousEndDate = $startDate->copy();
                $format = 'T.m.Y';
                $groupBy = 'MONTH(created_at), YEAR(created_at)'; // Group by month
                break;
            case 'custom':
                $startParam = $request->input('start_date');
                $endParam = $request->input('end_date');
                if ($startParam && $endParam) {
                    $startDate = \Carbon\Carbon::parse($startParam)->startOfDay();
                    $currentEndDate = \Carbon\Carbon::parse($endParam)->endOfDay();
                    $diffInDays = $startDate->diffInDays($currentEndDate);
                    
                    $previousEndDate = $startDate->copy()->subSecond();
                    $previousStartDate = $previousEndDate->copy()->subDays($diffInDays)->startOfDay();
                } else {
                    $period = '7d';
                    $startDate = $now->copy()->subDays(7);
                    $previousStartDate = $startDate->copy()->subDays(7);
                    $previousEndDate = $startDate->copy();
                }
                break;
        }

        // Dữ liệu current period
        $currentOrders = \App\Models\Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $currentEndDate])
            ->get();

        $totalOrders = $currentOrders->count();

        // Dữ liệu previous period
        $previousOrders = \App\Models\Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();

        $percentageChange = 0;
        $isIncrease = true;

        if ($previousOrders > 0) {
            $percentageChange = round((($totalOrders - $previousOrders) / $previousOrders) * 100, 1);
            $isIncrease = $percentageChange >= 0;
            $percentageChange = abs($percentageChange);
        } else if ($totalOrders > 0) {
             $percentageChange = 100;
             $isIncrease = true;
        }

        // Nhóm dữ liệu cho Chart
        $groupedData = [];

        // Pre-fill dữ liệu trống tùy period để tránh đứt gãy chart
        if ($period == 'custom') {
            $days = $startDate->diffInDays($currentEndDate);
            for ($i = $days; $i >= 0; $i--) {
                $dateStr = $currentEndDate->copy()->subDays($i)->format('d/m');
                $groupedData[$dateStr] = ['orders' => 0, 'revenue' => 0];
            }
        } elseif ($period == '24h') {
            for ($i = 23; $i >= 0; $i--) {
                $time = $now->copy()->subHours($i)->format('H:00');
                $groupedData[$time] = ['orders' => 0, 'revenue' => 0];
            }
        } elseif (in_array($period, ['7d', '30d'])) {
            $days = $period === '7d' ? 6 : 29;
            for ($i = $days; $i >= 0; $i--) {
                $dateStr = $now->copy()->subDays($i)->format('d/m');
                $groupedData[$dateStr] = ['orders' => 0, 'revenue' => 0];
            }
        } elseif ($period === '1y') {
            for ($i = 11; $i >= 0; $i--) {
                 $monthStr = $now->copy()->subMonths($i)->format('T.n'); // T.1, T.2...
                 $groupedData[$monthStr] = ['orders' => 0, 'revenue' => 0];
            }
        }

        foreach ($currentOrders as $order) {
            if ($period === '24h') {
                $key = $order->created_at->format('H:00');
            } elseif ($period === '1y') {
                $key = 'T.' . $order->created_at->format('n');
            } else {
                $key = $order->created_at->format('d/m');
            }

            if (isset($groupedData[$key])) {
                $groupedData[$key]['orders'] += 1;
                $groupedData[$key]['revenue'] += $order->total_amount;
            } else {
                 $groupedData[$key] = [
                     'orders' => 1,
                     'revenue' => $order->total_amount
                 ];
            }
        }

        $categories = array_keys($groupedData);
        $ordersCount = array_column($groupedData, 'orders');
        $revenues = array_column($groupedData, 'revenue');

        return response()->json([
            'categories' => $categories,
            'orders' => $ordersCount,
            'revenues' => $revenues,
            'totalOrders' => $totalOrders,
            'percentageChange' => $percentageChange,
            'isIncrease' => $isIncrease
        ]);
    }
}
