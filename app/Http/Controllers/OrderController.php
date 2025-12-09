<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        Log::info('=== ORDER STORE REQUEST ===');
        Log::info('Request Data:', $request->all());
        
        try {
            // Validasi data
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'table_id' => 'required|integer|min:1',
                'payment_method' => 'required|in:qris,bank_transfer,cash',
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1',
                'total_amount' => 'required|numeric|min:1'
            ]);
            
            Log::info('Validated Data:', $validated);
            
            // ✅ FIX: Cek dan handle table_id untuk foreign key constraint
            $tableId = $validated['table_id'];
            
            // Cek apakah tabel 'tables' ada di database
            $tablesTableExists = Schema::hasTable('tables');
            
            if ($tablesTableExists) {
                // Jika tabel 'tables' ada, cek apakah table_id valid
                $tableExists = DB::table('tables')->where('id', $tableId)->exists();
                
                if (!$tableExists) {
                    // Jika tidak ada, cari table yang ada atau buat default
                    $firstTable = DB::table('tables')->first();
                    
                    if ($firstTable) {
                        $tableId = $firstTable->id;
                        Log::warning("Table ID {$validated['table_id']} not found, using first available table ID: {$tableId}");
                    } else {
                        // Jika tabel 'tables' kosong, insert default table
                        $tableId = DB::table('tables')->insertGetId([
                            'table_number' => $validated['table_id'],
                            'capacity' => 4,
                            'status' => 'available',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        Log::warning("Tables table empty, created new table with ID: {$tableId}");
                    }
                }
            } else {
                // Jika tabel 'tables' tidak ada, kita perlu handle foreign key constraint
                Log::warning('Tables table does not exist, but foreign key constraint exists. Need to fix database.');
                
                // Untuk sementara, disable foreign key checks (hanya untuk development!)
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
            }
            
            DB::beginTransaction();
            
            // Generate order code
            $orderCode = 'ORD-' . strtoupper(Str::random(8));
            
            while (Order::where('order_code', $orderCode)->exists()) {
                $orderCode = 'ORD-' . strtoupper(Str::random(8));
            }
            
            Log::info('Creating order with code: ' . $orderCode);
            
            // Tentukan payment status
            $paymentStatus = $validated['payment_method'] === 'cash' ? 'pending' : 'unpaid';
            
            // Create order dengan table_id yang sudah divalidasi
            $order = Order::create([
                'order_code' => $orderCode,
                'customer_name' => $validated['customer_name'],
                'table_id' => $tableId,
                'payment_method' => $validated['payment_method'],
                'total_amount' => $validated['total_amount'],
                'status' => 'pending',
                'payment_status' => $paymentStatus
            ]);
            
            Log::info('Order created. ID: ' . $order->id);
            
            // Create order items
            foreach ($validated['items'] as $itemData) {
                $menu = Menu::find($itemData['menu_id']);
                
                if (!$menu) {
                    throw new \Exception('Menu not found: ' . $itemData['menu_id']);
                }
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $itemData['menu_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $menu->price
                ]);
                
                Log::info('Order item created: ' . $menu->name . ' x' . $itemData['quantity'] . ' = Rp ' . number_format($menu->price * $itemData['quantity'], 0, ',', '.'));
                
                // Update stock jika dikelola
                if ($menu->stock !== null && $menu->stock >= $itemData['quantity']) {
                    $menu->decrement('stock', $itemData['quantity']);
                    Log::info('Stock updated for ' . $menu->name . ': -' . $itemData['quantity']);
                } elseif ($menu->stock !== null && $menu->stock < $itemData['quantity']) {
                    throw new \Exception('Stok ' . $menu->name . ' tidak cukup. Stok tersedia: ' . $menu->stock);
                }
            }
            
            DB::commit();
            
            // Enable foreign key checks kembali jika dinonaktifkan
            if (!$tablesTableExists) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
            
            Log::info('Order transaction committed successfully');
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_code,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->customer_name,
                    'table_id' => $order->table_id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors()))
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Enable foreign key checks jika ada error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            Log::error('Error creating order: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'Gagal membuat pesanan: ' . $e->getMessage();
            
            // Berikan pesan error yang lebih spesifik untuk foreign key constraint
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $errorMessage = 'Gagal membuat pesanan: Table ID tidak valid. Coba gunakan table ID 1-6.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }
    
    public function index()
    {
        $orders = Order::with('orderItems.menu')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $order->load('orderItems.menu');
        return view('orders.show', compact('order'));
    }
    
    public function cancel(Order $order)
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan');
        }
        
        return redirect()->back()->with('error', 'Tidak dapat membatalkan pesanan yang sudah diproses');
    }
    
    public function getOrderStatus()
    {
        $orders = Order::with('orderItems.menu')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return response()->json($orders);
    }
    
    public function customerOrderStatus()
    {
        return view('customer.order-status');
    }
    
    public function checkOrderStatus(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string'
        ]);
        
        $order = Order::where('order_code', $request->order_code)
            ->with('orderItems.menu')
            ->first();
            
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan. Pastikan kode pesanan benar.'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'order' => $order,
            'order_items' => $order->orderItems,
            'status_text' => $order->getStatusText(),
            'payment_status_text' => $order->getPaymentStatusText(),
            'formatted_total' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
            'formatted_date' => $order->created_at->translatedFormat('d M Y H:i')
        ]);
    }
    
    public function trackOrder($order_code)
    {
        $order = Order::where('order_code', $order_code)
            ->with('orderItems.menu')
            ->firstOrFail();
            
        return view('customer.order-track', compact('order'));
    }
}