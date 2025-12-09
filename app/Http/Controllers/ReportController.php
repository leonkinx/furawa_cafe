<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        
        // Hitung total
        $totalAmount = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        
        // Generate Excel HTML with styling
        $filename = 'laporan-transaksi-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $html = '
        <html xmlns:x="urn:schemas-microsoft-com:office:excel">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <style>
                table { border-collapse: collapse; width: 100%; }
                th { 
                    background-color: #4472C4; 
                    color: white; 
                    font-weight: bold; 
                    padding: 10px; 
                    border: 1px solid #2E5C9A;
                    text-align: center;
                }
                td { 
                    padding: 8px; 
                    border: 1px solid #D0D0D0;
                }
                .header-row { background-color: #E7E6E6; font-weight: bold; }
                .total-row { background-color: #FFF2CC; font-weight: bold; }
                .subtotal-row { background-color: #F2F2F2; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .status-completed { background-color: #C6EFCE; color: #006100; }
                .status-processing { background-color: #FFEB9C; color: #9C6500; }
                .status-pending { background-color: #FFC7CE; color: #9C0006; }
                .status-cancelled { background-color: #E0E0E0; color: #666666; }
            </style>
        </head>
        <body>
            <h2>LAPORAN TRANSAKSI DETAIL</h2>
            <p><strong>Periode:</strong> ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y') . '</p>
            <p><strong>Total Transaksi:</strong> ' . $totalTransactions . ' | <strong>Total Pendapatan:</strong> Rp ' . number_format($totalAmount, 0, ',', '.') . '</p>
            <br>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Meja</th>
                        <th>Nama Menu</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                        <th>Total Pesanan</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>';
        
        $no = 1;
        foreach ($transactions as $transaction) {
            $itemCount = $transaction->orderItems->count();
            $firstItem = true;
            
            $statusClass = '';
            switch($transaction->status) {
                case 'completed': $statusClass = 'status-completed'; break;
                case 'processing': $statusClass = 'status-processing'; break;
                case 'pending': $statusClass = 'status-pending'; break;
                case 'cancelled': $statusClass = 'status-cancelled'; break;
            }
            
            foreach ($transaction->orderItems as $index => $item) {
                $html .= '<tr>';
                
                // No, Order Code, Date, Customer, Table - hanya di baris pertama
                if ($firstItem) {
                    $html .= '<td class="text-center" rowspan="' . ($itemCount + 1) . '">' . $no++ . '</td>';
                    $html .= '<td rowspan="' . ($itemCount + 1) . '">' . $transaction->order_code . '</td>';
                    $html .= '<td rowspan="' . ($itemCount + 1) . '">' . $transaction->created_at->format('d M Y H:i') . '</td>';
                    $html .= '<td rowspan="' . ($itemCount + 1) . '">' . $transaction->customer_name . '</td>';
                    $html .= '<td class="text-center" rowspan="' . ($itemCount + 1) . '">' . $transaction->table_id . '</td>';
                    $firstItem = false;
                }
                
                // Item details
                $html .= '<td>' . $item->menu->name . '</td>';
                $html .= '<td class="text-center">' . $item->quantity . '</td>';
                $html .= '<td class="text-right">Rp ' . number_format($item->menu->price, 0, ',', '.') . '</td>';
                $html .= '<td class="text-right">Rp ' . number_format($item->quantity * $item->menu->price, 0, ',', '.') . '</td>';
                
                // Total, Status, Payment - hanya di baris pertama
                if ($index == 0) {
                    $html .= '<td class="text-right" rowspan="' . ($itemCount + 1) . '"><strong>Rp ' . number_format($transaction->total_amount, 0, ',', '.') . '</strong></td>';
                    $html .= '<td class="text-center ' . $statusClass . '" rowspan="' . ($itemCount + 1) . '">' . $transaction->getStatusText() . '</td>';
                    $html .= '<td class="text-center" rowspan="' . ($itemCount + 1) . '">' . $transaction->getPaymentStatusText() . '</td>';
                }
                
                $html .= '</tr>';
            }
            
            // Subtotal row untuk setiap transaksi
            $html .= '<tr class="subtotal-row">
                <td colspan="3" class="text-right">Subtotal Pesanan:</td>
                <td class="text-right"><strong>Rp ' . number_format($transaction->total_amount, 0, ',', '.') . '</strong></td>
            </tr>';
        }
        
        $html .= '
                    <tr class="total-row">
                        <td colspan="9" class="text-right"><strong>GRAND TOTAL</strong></td>
                        <td class="text-right"><strong>Rp ' . number_format($totalAmount, 0, ',', '.') . '</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <p><em>Dicetak pada: ' . now()->format('d M Y H:i') . '</em></p>
        </body>
        </html>';
        
        return response($html, 200, $headers);
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