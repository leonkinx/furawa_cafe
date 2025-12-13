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
        Log::info('Items Detail:', $request->input('items', []));
        
        // Log setiap item secara detail dari request
        foreach ($request->input('items', []) as $index => $item) {
            Log::info("Raw Item {$index}:", [
                'menu_id' => $item['menu_id'] ?? 'missing',
                'quantity' => $item['quantity'] ?? 'missing', 
                'temperature' => $item['temperature'] ?? 'NULL'
            ]);
        }
        
        try {
            // Validasi data
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'table_id' => 'required|integer|min:1',
                'payment_method' => 'required|in:qris,bank_transfer,cash',
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.temperature' => 'nullable|string|in:ice,hot',
                'subtotal' => 'required|numeric|min:0',
                'ppn_amount' => 'required|numeric|min:0',
                'service_charge' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:1'
            ]);
            
            Log::info('Validated Data:', $validated);
            
            // Log setiap item secara detail dari validated data
            foreach ($validated['items'] as $index => $item) {
                Log::info("Validated Item {$index}:", [
                    'menu_id' => $item['menu_id'] ?? 'missing',
                    'quantity' => $item['quantity'] ?? 'missing', 
                    'temperature' => $item['temperature'] ?? 'NULL'
                ]);
            }
            
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
            
            // Calculate percentages for display
            $ppnPercentage = 0;
            $serviceChargePercentage = 0;
            
            if ($validated['subtotal'] > 0) {
                if ($validated['ppn_amount'] > 0) {
                    $ppnPercentage = ($validated['ppn_amount'] / $validated['subtotal']) * 100;
                }
                if ($validated['service_charge'] > 0) {
                    $serviceChargePercentage = ($validated['service_charge'] / $validated['subtotal']) * 100;
                }
            }
            
            // Create order dengan table_id yang sudah divalidasi
            $order = Order::create([
                'order_code' => $orderCode,
                'customer_name' => $validated['customer_name'],
                'table_id' => $tableId,
                'payment_method' => $validated['payment_method'],
                'subtotal' => $validated['subtotal'],
                'ppn_amount' => $validated['ppn_amount'],
                'ppn_percentage' => $ppnPercentage,
                'service_charge' => $validated['service_charge'],
                'service_charge_percentage' => $serviceChargePercentage,
                'total_amount' => $validated['total_amount'],
                'status' => 'pending',
                'payment_status' => $paymentStatus
            ]);
            
            Log::info('Order created. ID: ' . $order->id);
            
            // Validate stock before creating order items
            foreach ($validated['items'] as $itemData) {
                $menu = Menu::find($itemData['menu_id']);
                
                if (!$menu) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Menu tidak ditemukan'
                    ], 400);
                }
                
                // Check stock availability
                if ($menu->stock !== null && $menu->stock < $itemData['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Maaf, stok {$menu->name} tidak mencukupi! 😔\nStok tersedia: {$menu->stock}\nJumlah yang dipesan: {$itemData['quantity']}\n\nSilakan kurangi jumlah pesanan atau pilih menu lain. 🙏",
                        'error_type' => 'stock_insufficient',
                        'menu_name' => $menu->name,
                        'available_stock' => $menu->stock,
                        'requested_quantity' => $itemData['quantity']
                    ], 400);
                }
            }
            
            // Create order items after stock validation
            foreach ($validated['items'] as $itemData) {
                $menu = Menu::find($itemData['menu_id']);
                
                $temperature = $itemData['temperature'] ?? null;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $itemData['menu_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $menu->price,
                    'temperature' => $temperature
                ]);
                
                Log::info('Order item created: ' . $menu->name . ' x' . $itemData['quantity'] . ' = Rp ' . number_format($menu->price * $itemData['quantity'], 0, ',', '.'));
                Log::info('Temperature data: ' . ($temperature ?: 'NULL') . ' for menu: ' . $menu->name);
                
                // Update stock
                if ($menu->stock !== null) {
                    $menu->decrement('stock', $itemData['quantity']);
                    Log::info('Stock updated for ' . $menu->name . ': -' . $itemData['quantity']);
                }
            }
            
            DB::commit();
            
            // Enable foreign key checks kembali jika dinonaktifkan
            if (!$tablesTableExists) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
            
            Log::info('Order transaction committed successfully');
            
            // Optimized response - hanya data yang diperlukan
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order' => [
                    'id' => $order->id,
                    'order_code' => $order->order_code, // Hapus duplikasi order_number
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
            DB::beginTransaction();
            
            try {
                // Restore stock for cancelled items
                foreach ($order->orderItems as $item) {
                    if ($item->menu && $item->menu->stock !== null) {
                        $item->menu->increment('stock', $item->quantity);
                    }
                }
                
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed'
                ]);
                
                DB::commit();
                
                return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan');
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error cancelling order: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan');
            }
        }
        
        return redirect()->back()->with('error', 'Tidak dapat membatalkan pesanan yang sudah diproses');
    }

    public function cancelByCode($order_code)
    {
        try {
            $order = Order::where('order_code', $order_code)->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dapat dibatalkan karena sudah diproses'
                ], 400);
            }
            
            DB::beginTransaction();
            
            try {
                // Restore stock for cancelled items
                foreach ($order->orderItems as $item) {
                    if ($item->menu && $item->menu->stock !== null) {
                        $item->menu->increment('stock', $item->quantity);
                    }
                }
                
                // Update order status - gunakan method update() yang aman
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed' // Gunakan 'failed' karena 'cancelled' tidak ada di enum
                ]);
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibatalkan'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            \Log::error('Error cancelling order: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
    

    

    

    
    public function trackOrder($order_code)
    {
        $order = Order::where('order_code', $order_code)
            ->with('orderItems.menu')
            ->firstOrFail();
            
        return view('customer.order-track', compact('order'));
    }
    
    public function showReceipt($order_code)
    {
        $order = Order::where('order_code', $order_code)
            ->with('orderItems.menu')
            ->firstOrFail();
        
        // Only allow receipt access for completed orders
        if ($order->status !== 'completed') {
            return redirect()->route('orders.track', $order_code)
                ->with('error', 'Struk hanya dapat diakses setelah pesanan selesai.');
        }
            
        return view('customer.receipt', compact('order'));
    }
}