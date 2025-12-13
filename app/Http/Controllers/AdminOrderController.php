<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        try {
            // Redirect ke URL dengan date_filter=today jika tidak ada parameter
            if (!$request->has('date_filter')) {
                return redirect()->route('admin.orders.index', ['date_filter' => 'today']);
            }
            
            // Hitung jumlah pesanan berdasarkan status
            $pendingCount = Order::where('status', 'pending')->count();
            $processingCount = Order::where('status', 'processing')->count();
            
            // Query builder untuk orders
            $query = Order::with(['orderItems.menu']);
            
            // Filter by date range - DEFAULT: HARI INI
            $dateFilter = $request->get('date_filter', 'today'); // default: today
            
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today('Asia/Jakarta'));
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday('Asia/Jakarta'));
                    break;
                case '7days':
                    $query->whereDate('created_at', '>=', Carbon::today('Asia/Jakarta')->subDays(6));
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'custom':
                    if ($request->has('start_date') && $request->has('end_date')) {
                        $query->whereBetween('created_at', [
                            $request->start_date . ' 00:00:00',
                            $request->end_date . ' 23:59:59'
                        ]);
                    }
                    break;
                case 'all':
                    // No date filter
                    break;
            }
            
            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_code', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('table_id', 'like', "%{$search}%");
                });
            }
            
            // Filter by status
            if ($request->has('filter') && $request->filter != '') {
                $filter = $request->filter;
                if ($filter === 'unpaid') {
                    $query->whereIn('payment_status', ['pending', 'unpaid'])
                          ->whereNotIn('status', ['cancelled', 'completed']);
                } else {
                    $query->where('status', $filter);
                }
            }
            
            // Pagination (10 items per page)
            $orders = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return view('admin.orders.index', compact('orders', 'pendingCount', 'processingCount', 'dateFilter'));
            
        } catch (\Exception $e) {
            Log::error('Error in AdminOrderController@index: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Gagal memuat data pesanan');
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        try {
            // Load relasi order items dengan menu
            $order->load(['orderItems.menu']);
            return view('admin.orders.show', compact('order'));
            
        } catch (\Exception $e) {
            Log::error('Error in AdminOrderController@show: ' . $e->getMessage());
            return redirect()->route('admin.orders.index')->with('error', 'Gagal memuat detail pesanan');
        }
    }

    /**
     * Update order status - FIXED VERSION WITH BUSINESS LOGIC
     */
    public function updateStatus(Request $request, $id)
    {
        Log::info('=== UPDATE STATUS REQUEST START ===');
        Log::info('Order ID:', ['id' => $id]);
        Log::info('Full Request Data:', $request->all());
        
        try {
            // Validasi input - status hanya pending, processing, completed, cancelled
            // paid HANYA untuk payment_status!
            $validated = $request->validate([
                'status' => [
                    'sometimes',
                    'string',
                    'in:pending,processing,completed,cancelled'
                ],
                'payment_status' => [
                    'sometimes',
                    'string', 
                    'in:pending,paid,unpaid,failed'
                ]
            ]);
            
            Log::info('Validation passed:', $validated);
            
            // Cek minimal ada satu yang diupdate
            if (empty($validated)) {
                throw new \Exception('Tidak ada data yang akan diupdate');
            }
            
            // Cari order
            $order = Order::find($id);
            
            if (!$order) {
                Log::error('Order not found with ID:', ['id' => $id]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pesanan tidak ditemukan dengan ID: ' . $id
                    ], 404);
                }
                
                return redirect()->back()->with('error', 'Pesanan tidak ditemukan');
            }
            
            Log::info('Order found:', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'current_status' => $order->status,
                'current_payment_status' => $order->payment_status
            ]);
            
            // Simpan status lama untuk logika stok
            $oldStatus = $order->status;
            
            // Mulai transaction
            DB::beginTransaction();
            
            try {
                // Update status order jika ada
                if (isset($validated['status'])) {
                    $newStatus = (string) $validated['status'];
                    
                    // ===== BUSINESS LOGIC VALIDATION =====
                    // 1. Tidak bisa cancel jika sudah paid
                    if ($newStatus === 'cancelled' && $order->payment_status === 'paid') {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Pesanan yang sudah dibayar tidak dapat dibatalkan!',
                                'type' => 'warning'
                            ], 400);
                        }
                        return redirect()->back()->with('error', 'Pesanan yang sudah dibayar tidak dapat dibatalkan!');
                    }
                    
                    // 2. Tidak bisa cancel jika sudah processing atau completed
                    if ($newStatus === 'cancelled' && in_array($order->status, ['processing', 'completed'])) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Pesanan yang sudah diproses tidak dapat dibatalkan!',
                                'type' => 'warning'
                            ], 400);
                        }
                        return redirect()->back()->with('error', 'Pesanan yang sudah diproses tidak dapat dibatalkan!');
                    }
                    
                    // 3. TIDAK BISA DIPROSES (processing/completed) JIKA BELUM BAYAR
                    if (in_array($newStatus, ['processing', 'completed']) && $order->payment_status !== 'paid') {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Pesanan harus dibayar terlebih dahulu sebelum diproses!',
                                'type' => 'warning'
                            ], 400);
                        }
                        return redirect()->back()->with('error', 'Pesanan harus dibayar terlebih dahulu sebelum diproses!');
                    }
                    
                    // 4. Tidak bisa kembali ke status sebelumnya (harus maju)
                    $statusOrder = ['pending' => 1, 'processing' => 2, 'completed' => 3, 'cancelled' => 4];
                    $currentOrder = $statusOrder[$order->status] ?? 0;
                    $newOrder = $statusOrder[$newStatus] ?? 0;
                    
                    if ($newStatus !== 'cancelled' && $newOrder < $currentOrder) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Status pesanan tidak dapat dikembalikan ke status sebelumnya!',
                                'type' => 'warning'
                            ], 400);
                        }
                        return redirect()->back()->with('error', 'Status pesanan tidak dapat dikembalikan ke status sebelumnya!');
                    }
                    
                    // Validasi manual
                    $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
                    if (!in_array($newStatus, $validStatuses)) {
                        throw new \InvalidArgumentException("Status '$newStatus' tidak valid. Status yang diperbolehkan: " . implode(', ', $validStatuses));
                    }
                    
                    $order->status = $newStatus;
                    
                    // Logika untuk stok
                    if ($oldStatus !== 'cancelled' && $newStatus === 'cancelled') {
                        // Jika order dibatalkan, kembalikan stok
                        $this->restoreStock($order);
                    } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                        // Jika order aktif kembali dari cancelled, kurangi stok
                        $this->deductStock($order);
                    }
                }
                
                // Update payment status jika ada
                if (isset($validated['payment_status'])) {
                    $paymentStatus = (string) $validated['payment_status'];
                    $validPaymentStatuses = ['pending', 'paid', 'unpaid', 'failed'];
                    
                    if (!in_array($paymentStatus, $validPaymentStatuses)) {
                        throw new \InvalidArgumentException("Status pembayaran '$paymentStatus' tidak valid. Status yang diperbolehkan: " . implode(', ', $validPaymentStatuses));
                    }
                    
                    $order->payment_status = $paymentStatus;
                }
                
                // Logika otomatis: 
                // 1. Jika payment_status paid dan status masih pending, auto ubah ke processing
                //    Karena tidak masuk akal pesanan sudah bayar tapi masih pending
                if ($order->payment_status === 'paid' && $order->status === 'pending') {
                    $order->status = 'processing';
                    Log::info('Auto-updating status to processing because payment is paid');
                }
                
                // 2. Jika status completed, payment_status HARUS paid
                if ($order->status === 'completed' && $order->payment_status !== 'paid') {
                    $order->payment_status = 'paid';
                    Log::info('Auto-updating payment_status to paid for completed order');
                }
                
                // 3. Jika status cancelled, payment_status HARUS failed
                if ($order->status === 'cancelled') {
                    $order->payment_status = 'failed';
                    Log::info('Auto-updating payment_status to failed for cancelled order');
                }
                
                // Simpan perubahan
                $saved = $order->save();
                
                if (!$saved) {
                    throw new \Exception('Gagal menyimpan perubahan ke database');
                }
                
                // Commit transaction
                DB::commit();
                
                Log::info('Order updated successfully:', [
                    'order_id' => $order->id,
                    'new_status' => $order->status,
                    'new_payment_status' => $order->payment_status
                ]);
                
                // Response untuk AJAX request
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Status pesanan berhasil diperbarui!',
                        'order' => [
                            'id' => $order->id,
                            'status' => $order->status,
                            'status_text' => $order->getStatusText(),
                            'status_badge' => $order->getStatusBadgeClass(),
                            'payment_status' => $order->payment_status,
                            'payment_status_text' => $order->getPaymentStatusText(),
                            'order_code' => $order->order_code,
                            'customer_name' => $order->customer_name
                        ]
                    ]);
                }
                
                // Response untuk regular request
                return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in updateStatus:', $e->errors());
            
            $errorMessage = 'Validasi gagal: ' . implode(', ', array_map(function ($fieldErrors) {
                return implode(', ', $fieldErrors);
            }, $e->errors()));
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Error in AdminOrderController@updateStatus: ' . $e->getMessage());
            Log::error('Stack trace:', ['trace' => $e->getTraceAsString()]);
            
            $errorMessage = 'Gagal memperbarui status: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        } finally {
            Log::info('=== UPDATE STATUS REQUEST END ===');
        }
    }

    /**
     * Restore stock when order is cancelled
     */
    private function restoreStock(Order $order)
    {
        try {
            Log::info('Restoring stock for cancelled order:', ['order_id' => $order->id]);
            
            foreach ($order->orderItems as $item) {
                $menu = $item->menu;
                if ($menu && $menu->stock !== null) {
                    $oldStock = $menu->stock;
                    $menu->increment('stock', $item->quantity);
                    $newStock = $menu->fresh()->stock;
                    
                    Log::info('Stock restored:', [
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'quantity' => $item->quantity,
                        'old_stock' => $oldStock,
                        'new_stock' => $newStock
                    ]);
                }
            }
            
            Log::info('Stock restore completed for order:', ['order_id' => $order->id]);
            
        } catch (\Exception $e) {
            Log::error('Error restoring stock: ' . $e->getMessage());
            throw new \Exception('Gagal mengembalikan stok: ' . $e->getMessage());
        }
    }

    /**
     * Deduct stock when order is active again
     */
    private function deductStock(Order $order)
    {
        try {
            Log::info('Deducting stock for reactivated order:', ['order_id' => $order->id]);
            
            foreach ($order->orderItems as $item) {
                $menu = $item->menu;
                if ($menu && $menu->stock !== null) {
                    if ($menu->stock >= $item->quantity) {
                        $oldStock = $menu->stock;
                        $menu->decrement('stock', $item->quantity);
                        $newStock = $menu->fresh()->stock;
                        
                        Log::info('Stock deducted:', [
                            'menu_id' => $menu->id,
                            'menu_name' => $menu->name,
                            'quantity' => $item->quantity,
                            'old_stock' => $oldStock,
                            'new_stock' => $newStock
                        ]);
                    } else {
                        $errorMessage = "Stok {$menu->name} tidak cukup. Stok tersedia: {$menu->stock}, dibutuhkan: {$item->quantity}";
                        Log::error('Insufficient stock:', [
                            'menu' => $menu->name,
                            'available' => $menu->stock,
                            'required' => $item->quantity
                        ]);
                        throw new \Exception($errorMessage);
                    }
                }
            }
            
            Log::info('Stock deduction completed for order:', ['order_id' => $order->id]);
            
        } catch (\Exception $e) {
            Log::error('Error deducting stock: ' . $e->getMessage());
            throw new \Exception('Gagal mengurangi stok: ' . $e->getMessage());
        }
    }

    /**
     * Get notifications for new orders - HANYA HARI INI
     */
    public function getNotifications()
    {
        try {
            // Hitung pesanan HARI INI saja untuk notifikasi
            $pendingCount = Order::where('status', 'pending')
                ->whereDate('created_at', today())
                ->count();
            $processingCount = Order::where('status', 'processing')
                ->whereDate('created_at', today())
                ->count();
            
            // Ambil pesanan terbaru HARI INI
            $recentOrders = Order::whereIn('status', ['pending', 'processing'])
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'order_code', 'customer_name', 'status', 'created_at']);
            
            return response()->json([
                'success' => true,
                'pending_count' => $pendingCount,
                'processing_count' => $processingCount,
                'total_notifications' => $pendingCount + $processingCount,
                'recent_orders' => $recentOrders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_code' => $order->order_code,
                        'customer_name' => $order->customer_name,
                        'status' => $order->status,
                        'status_text' => $order->getStatusText(),
                        'time_ago' => $order->created_at->diffForHumans(),
                        'created_at' => $order->created_at->toIso8601String()
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getNotifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}