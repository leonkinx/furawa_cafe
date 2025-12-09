<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.welcome');
    }

   public function showMenu(Request $request)
{
    // Ambil semua menu yang available
    $menus = Menu::where('is_available', true)->get();
    
    // Debug: cek data best seller
    \Log::info('Best Seller Menus:', [
        'total' => $menus->where('is_best_seller', true)->count(),
        'names' => $menus->where('is_best_seller', true)->pluck('name')
    ]);
    
    // Group menus by category
    $categories = [
        'makanan' => $menus->where('category', 'makanan'),
        'minuman' => $menus->where('category', 'minuman'),
        'snack' => $menus->where('category', 'snack')
    ];

    // Default table atau dari parameter
    $table_number = $request->get('table', '1');
    
    return view('customer.menu', compact('categories', 'table_number'));
}
    public function processOrder(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        // Cari atau buat table berdasarkan nomor meja
        $table = Table::where('table_number', $request->table_number)->first();
        
        if (!$table) {
            // Jika meja tidak ada, buat meja baru
            $table = Table::create([
                'table_number' => $request->table_number,
                'capacity' => 4, // Default capacity
                'status' => 'occupied'
            ]);
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            $totalAmount += $menu->price * $item['quantity'];
        }

        // Create order
        $order = Order::create([
            'table_id' => $table->id,
            'customer_name' => $request->customer_name,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_code' => 'ORD-' . Str::random(8)
        ]);

        // Create order items
        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $menu->price
            ]);

            // Update stock
            $menu->decrement('stock', $item['quantity']);
            if ($menu->stock <= 0) {
                $menu->update(['is_available' => false]);
            }
        }

        return response()->json([
            'success' => true,
            'order_code' => $order->order_code,
            'total_amount' => $totalAmount,
            'message' => 'Pesanan berhasil dibuat!'
        ]);
    }
}