<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Exports\SalesReportExport;
use App\Exports\SimpleReportExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default: bulan ini
        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        // Hitung statistik
        $totalRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');
            
        $totalTransactions = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        $highestTransaction = Order::whereBetween('created_at', [$startDate, $endDate])
            ->max('total_amount');
        
        // Data untuk chart pendapatan per hari
        $revenueData = $this->getRevenueChartData($startDate, $endDate);
        
        // Data untuk chart status
        $statusData = $this->getStatusChartData($startDate, $endDate);
        
        // Menu terlaris
        $topProducts = $this->getTopProducts($startDate, $endDate);
        
        // Detail transaksi dengan search dan filter
        $query = Order::with('orderItems.menu');
        
        // Filter tanggal
        $query->whereBetween('created_at', [$startDate, $endDate]);
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('table_id', 'like', "%{$search}%");
            });
        }
        
        // Filter status
        if ($request->has('status_filter') && $request->status_filter != '') {
            $query->where('status', $request->status_filter);
        }
        
        // Filter payment status
        if ($request->has('payment_filter') && $request->payment_filter != '') {
            $query->where('payment_status', $request->payment_filter);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalTransactions',
            'averageTransaction',
            'highestTransaction',
            'revenueData',
            'statusData',
            'topProducts',
            'transactions',
            'startDate',
            'endDate'
        ));
    }
    
    public function filter(Request $request)
    {
        $period = $request->period;
        
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }
        
        // Hitung statistik
        $totalRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');
            
        $totalTransactions = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        $highestTransaction = Order::whereBetween('created_at', [$startDate, $endDate])
            ->max('total_amount');
        
        // Data untuk chart
        $revenueData = $this->getRevenueChartData($startDate, $endDate);
        $statusData = $this->getStatusChartData($startDate, $endDate);
        
        // Menu terlaris
        $topProducts = $this->getTopProducts($startDate, $endDate);
        
        // Detail transaksi
        $transactions = Order::withCount('orderItems as items_count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json([
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageTransaction' => $averageTransaction,
            'highestTransaction' => $highestTransaction,
            'revenueData' => $revenueData,
            'statusData' => $statusData,
            'topProducts' => $topProducts,
            'transactions' => $transactions
        ]);
    }
    
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        $filename = 'laporan-transaksi-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new SimpleReportExport($startDate, $endDate), $filename);
    }
    
    public function export(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        
        // Query transaksi
        $query = Order::with('orderItems.menu');
        $query->whereBetween('created_at', [$startDate, $endDate]);
        
        // Apply filters
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('table_id', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status_filter') && $request->status_filter != '') {
            $query->where('status', $request->status_filter);
        }
        
        if ($request->has('payment_filter') && $request->payment_filter != '') {
            $query->where('payment_status', $request->payment_filter);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        // Hitung statistik berdasarkan status (exclude cancelled)
        $totalAmount = $transactions->whereNotIn('status', ['cancelled'])->sum('total_amount');
        $totalTransactions = $transactions->count();
        $totalTransactionsExcludeCancelled = $transactions->whereNotIn('status', ['cancelled'])->count();
        
        // Statistik per status
        $pendingCount = $transactions->where('status', 'pending')->count();
        $paidCount = $transactions->where('status', 'paid')->count();
        $processingCount = $transactions->where('status', 'processing')->count();
        $completedCount = $transactions->where('status', 'completed')->count();
        $cancelledCount = $transactions->where('status', 'cancelled')->count();
        
        $pendingAmount = $transactions->where('status', 'pending')->sum('total_amount');
        $paidAmount = $transactions->where('status', 'paid')->sum('total_amount');
        $processingAmount = $transactions->where('status', 'processing')->sum('total_amount');
        $completedAmount = $transactions->where('status', 'completed')->sum('total_amount');
        $cancelledAmount = $transactions->where('status', 'cancelled')->sum('total_amount');
        
        // Generate clean CSV content
        $filename = 'laporan-transaksi-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        // Build clean tabular CSV content
        $csvContent = [];
        
        // === HEADER SECTION ===
        $csvContent[] = ['LAPORAN TRANSAKSI FURAWA CAFE'];
        $csvContent[] = ['Periode: ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')];
        $csvContent[] = ['Dicetak pada: ' . now()->format('d M Y H:i:s')];
        $csvContent[] = [];
        
        // === TABEL 1: RINGKASAN STATUS ===
        $csvContent[] = ['TABEL 1: RINGKASAN BERDASARKAN STATUS'];
        $csvContent[] = [];
        $csvContent[] = ['Status Pesanan', 'Jumlah Transaksi', 'Total Nilai (Rp)', 'Persentase (%)'];
        $csvContent[] = ['==================', '==================', '==================', '=================='];
        $csvContent[] = ['BELUM BAYAR', $pendingCount, number_format($pendingAmount, 0, ',', '.'), ($totalTransactionsExcludeCancelled > 0 ? round(($pendingCount / $totalTransactionsExcludeCancelled) * 100, 1) : '0')];
        $csvContent[] = ['SUDAH BAYAR', $paidCount, number_format($paidAmount, 0, ',', '.'), ($totalTransactionsExcludeCancelled > 0 ? round(($paidCount / $totalTransactionsExcludeCancelled) * 100, 1) : '0')];
        $csvContent[] = ['SEDANG DIPROSES', $processingCount, number_format($processingAmount, 0, ',', '.'), ($totalTransactionsExcludeCancelled > 0 ? round(($processingCount / $totalTransactionsExcludeCancelled) * 100, 1) : '0')];
        $csvContent[] = ['SELESAI', $completedCount, number_format($completedAmount, 0, ',', '.'), ($totalTransactionsExcludeCancelled > 0 ? round(($completedCount / $totalTransactionsExcludeCancelled) * 100, 1) : '0')];
        $csvContent[] = ['DIBATALKAN', $cancelledCount, number_format($cancelledAmount, 0, ',', '.'), ($totalTransactions > 0 ? round(($cancelledCount / $totalTransactions) * 100, 1) : '0')];
        $csvContent[] = ['==================', '==================', '==================', '=================='];
        $csvContent[] = ['TOTAL KESELURUHAN PENDAPATAN', $totalTransactionsExcludeCancelled, number_format($totalAmount, 0, ',', '.'), '100.0'];
        $csvContent[] = ['(Tidak termasuk dibatalkan)', '', '', ''];
        $csvContent[] = [];
        $csvContent[] = [];
        
        // === TABEL 2: DETAIL TRANSAKSI ===
        $csvContent[] = ['TABEL 2: DETAIL TRANSAKSI'];
        $csvContent[] = [];
        $csvContent[] = ['No', 'Kode Pesanan', 'Tanggal & Waktu', 'Nama Customer', 'Meja', 'Menu', 'Qty', 'Harga Satuan', 'Subtotal Item', 'PPN', 'Service Charge', 'Total Pesanan', 'Status', 'Payment'];
        $csvContent[] = ['===', '================', '==================', '================', '======', '========================', '===', '================', '================', '======', '================', '================', '==========', '======='];
        
        $no = 1;
        foreach ($transactions as $transaction) {
            // Status text
            $statusText = '';
            switch($transaction->status) {
                case 'pending': $statusText = 'BELUM BAYAR'; break;
                case 'paid': $statusText = 'SUDAH BAYAR'; break;
                case 'processing': $statusText = 'DIPROSES'; break;
                case 'completed': $statusText = 'SELESAI'; break;
                case 'cancelled': $statusText = 'DIBATALKAN'; break;
            }
            
            foreach ($transaction->orderItems as $index => $item) {
                $itemSubtotal = $item->quantity * $item->price;
                
                $csvContent[] = [
                    ($index == 0) ? $no : '', // No
                    ($index == 0) ? $transaction->order_code : '', // Order code
                    ($index == 0) ? $transaction->created_at->format('d/m/Y H:i') : '', // Date
                    ($index == 0) ? $transaction->customer_name : '', // Customer
                    ($index == 0) ? $transaction->table_id : '', // Table
                    $item->menu->name, // Menu
                    $item->quantity, // Qty
                    number_format($item->price, 0, ',', '.'), // Unit price
                    number_format($itemSubtotal, 0, ',', '.'), // Subtotal
                    ($index == 0) ? number_format($transaction->ppn_amount ?? 0, 0, ',', '.') : '', // PPN
                    ($index == 0) ? number_format($transaction->service_charge ?? 0, 0, ',', '.') : '', // Service charge
                    ($index == 0) ? number_format($transaction->total_amount, 0, ',', '.') : '', // Total
                    ($index == 0) ? $statusText : '', // Status
                    ($index == 0) ? 'TUNAI' : '' // Payment
                ];
            }
            $no++;
        }
        
        $csvContent[] = ['===', '================', '==================', '================', '======', '========================', '===', '================', '================', '======', '================', '================', '==========', '======='];
        $csvContent[] = ['GRAND TOTAL PENDAPATAN', '', '', '', '', '', '', '', '', '', '', number_format($totalAmount, 0, ',', '.'), $totalTransactionsExcludeCancelled . ' Transaksi', ''];
        $csvContent[] = ['(Tidak termasuk pesanan dibatalkan)', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $csvContent[] = [];
        $csvContent[] = [];
        
        // === TABEL 3: KETERANGAN STATUS ===
        $csvContent[] = ['TABEL 3: KETERANGAN STATUS'];
        $csvContent[] = [];
        $csvContent[] = ['Status', 'Keterangan'];
        $csvContent[] = ['======', '=========='];
        $csvContent[] = ['BELUM BAYAR', 'Pesanan dibuat tapi belum dibayar'];
        $csvContent[] = ['SUDAH BAYAR', 'Pesanan sudah dibayar, menunggu diproses'];
        $csvContent[] = ['DIPROSES', 'Pesanan sedang disiapkan'];
        $csvContent[] = ['SELESAI', 'Pesanan sudah selesai dan diserahkan'];
        $csvContent[] = ['DIBATALKAN', 'Pesanan dibatalkan'];
        
        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        
        // Add BOM for UTF-8
        fwrite($output, "\xEF\xBB\xBF");
        
        foreach ($csvContent as $row) {
            fputcsv($output, $row, ',', '"');
        }
        
        rewind($output);
        $csvString = stream_get_contents($output);
        fclose($output);
        
        return response($csvString, 200, $headers);
    }
    
    private function getRevenueChartData($startDate, $endDate)
    {
        $revenuePerDay = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $labels = [];
        $data = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            
            $revenue = $revenuePerDay->firstWhere('date', $dateString);
            $data[] = $revenue ? $revenue->revenue : 0;
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    private function getStatusChartData($startDate, $endDate)
    {
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();
        
        $labels = ['Menunggu', 'Dibayar', 'Diproses', 'Selesai'];
        $data = [0, 0, 0, 0];
        
        foreach ($statusCounts as $status) {
            switch ($status->status) {
                case 'pending':
                    $data[0] = $status->count;
                    break;
                case 'paid':
                    $data[1] = $status->count;
                    break;
                case 'processing':
                    $data[2] = $status->count;
                    break;
                case 'completed':
                    $data[3] = $status->count;
                    break;
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    private function getTopProducts($startDate, $endDate, $limit = 5)
    {
        return OrderItem::select(
                'menus.id',
                'menus.name',
                'menus.price',
                'menus.category',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * menus.price) as revenue')
            )
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->groupBy('menus.id', 'menus.name', 'menus.price', 'menus.category')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }
}