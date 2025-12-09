<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // ✅ TAMBAHKAN INI

class AdminController extends Controller
{
    public function dashboard()
    {
        // Cek authentication manual
        if (!Auth::check()) {
            return redirect('/admin/login');
        }
        
        try {
            // Dashboard menampilkan data HARI INI
            $today = Carbon::today();
            
            // Stats utama - HYBRID (Revenue + Status)
            $pendingCount = Order::where('status', 'pending')->count();
            $processingCount = Order::where('status', 'processing')->count();
            
            $stats = [
                // 1. Pendapatan HARI INI (completed orders only)
                'today_income' => Order::whereDate('created_at', $today)
                    ->where('status', 'completed')
                    ->sum('total_amount') ?? 0,
                
                // 2. Belum Bayar (SEMUA pesanan yang belum bayar, bukan hanya hari ini)
                'unpaid_orders' => Order::whereIn('payment_status', ['pending', 'unpaid'])
                    ->whereNotIn('status', ['cancelled', 'completed']) // Exclude cancelled & completed
                    ->count(),
                
                // 3. Sedang Diproses (status = processing)
                'processing_orders' => $processingCount,
                
                // 4. Selesai Hari Ini (completed today)
                'completed_today' => Order::whereDate('created_at', $today)
                    ->where('status', 'completed')
                    ->count(),
                
                // Data tambahan untuk sidebar & notifikasi
                'pending_orders' => $pendingCount,
                'total_notifications' => $pendingCount + $processingCount,
                
                // Data lainnya
                'today_orders' => Order::whereDate('created_at', $today)->count(),
                'total_orders' => Order::count(),
                'total_menus' => Menu::count(),
                'total_reservations' => Reservation::count(),
            ];

            // Produk terlaris (bulan ini)
            $best_sellers = OrderItem::select(
                'menu_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->whereHas('order', function($query) {
                $query->whereMonth('created_at', Carbon::now()->month);
            })
            ->with('menu')
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(function($item) {
                return (object) [
                    'name' => $item->menu->name ?? 'Menu Tidak Ditemukan',
                    'total_sold' => $item->total_sold ?? 0,
                    'total_revenue' => $item->total_revenue ?? 0
                ];
            });

            // Pesanan terbaru
            $recent_orders = Order::with('table')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Data untuk grafik
            $chart_data = $this->getChartData();

        } catch (\Exception $e) {
            // Fallback jika ada error
            $stats = [
                'today_income' => 0,
                'today_orders' => 0,
                'pending_orders' => 0,
                'total_orders' => 0,
                'total_menus' => 0,
                'total_reservations' => 0,
                'available_tables' => 0,
                'total_tables' => 0,
            ];
            $best_sellers = [];
            $recent_orders = [];
            $chart_data = $this->getEmptyChartData();
        }

        return view('admin.dashboard', compact('stats', 'best_sellers', 'recent_orders', 'chart_data'));
    }

    private function getChartData()
    {
        // Data Harian - 7 HARI TERAKHIR (menggunakan metode ReportController)
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $revenuePerDay = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $dailyLabels = [];
        $dailyData = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dailyLabels[] = $currentDate->format('d M');
            
            $revenue = $revenuePerDay->firstWhere('date', $dateString);
            $dailyData[] = $revenue ? (float) $revenue->revenue : 0;
            
            $currentDate->addDay();
        }

        // Data Bulanan (12 bulan terakhir)
        $monthlyData = [];
        $monthlyLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            
            $income = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'completed')
                ->sum('total_amount') ?? 0;
            
            $monthlyData[] = (float) $income;
        }

        // Data Tahunan (5 tahun terakhir)
        $yearlyData = [];
        $yearlyLabels = [];
        
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $yearlyLabels[] = (string) $year;
            
            $income = Order::whereYear('created_at', $year)
                ->where('status', 'completed')
                ->sum('total_amount') ?? 0;
            
            $yearlyData[] = (float) $income;
        }

        return [
            'daily' => [
                'labels' => $dailyLabels,
                'data' => $dailyData
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData
            ],
            'yearly' => [
                'labels' => $yearlyLabels,
                'data' => $yearlyData
            ]
        ];
    }

    private function getEmptyChartData()
    {
        return [
            'daily' => [
                'labels' => [],
                'data' => []
            ],
            'monthly' => [
                'labels' => [],
                'data' => []
            ],
            'yearly' => [
                'labels' => [],
                'data' => []
            ]
        ];
    }
}