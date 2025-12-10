<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// ==================== ROUTE LOGIN GLOBAL ====================
// FIX: Tambahkan route 'login' global untuk menghindari error
Route::get('/login', function() {
    return redirect('/admin/login');
})->name('login');

Route::post('/logout', function() {
    Auth::logout();
    return redirect('/');
})->name('logout');

// ==================== CUSTOMER ROUTES (PUBLIC - NO AUTH) ====================
Route::get('/', [CustomerController::class, 'index'])->name('customer.welcome');
Route::get('/menu', [CustomerController::class, 'showMenu'])->name('customer.menu');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// ✅ TAMBAHKAN ROUTE INI: Customer bisa lihat status pesanan
Route::get('/orders/status', [OrderController::class, 'customerOrderStatus'])->name('orders.status');
Route::post('/orders/check-status', [OrderController::class, 'checkOrderStatus'])->name('orders.check-status');
Route::get('/orders/track/{order_code}', [OrderController::class, 'trackOrder'])->name('orders.track');

// Route untuk get menu prices
Route::get('/menu/prices', function (Request $request) {
    $menuIds = explode(',', $request->ids);
    $menus = App\Models\Menu::whereIn('id', $menuIds)
        ->get(['id', 'name', 'price'])
        ->keyBy('id');
    
    return response()->json($menus);
});

// ==================== IMAGE SERVING ROUTE ====================
Route::get('/storage/menu-images/{filename}', function($filename) {
    $path = storage_path('app/public/menu-images/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    
    return response($file, 200)->header('Content-Type', $type);
})->where('filename', '.*');

// ==================== API ROUTES FOR CUSTOMER ====================
Route::prefix('api')->group(function () {
    // Get all orders (for customer tracking)
    Route::get('/orders/my-orders', [OrderController::class, 'getOrderStatus']);
    
    // Get service charge percentage
    Route::get('/settings/service-charge', [\App\Http\Controllers\SettingController::class, 'getServiceCharge']);
    
    // Cancel order
    Route::post('/orders/{order_code}/cancel', function($order_code) {
        $order = App\Models\Order::where('order_code', $order_code)->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }
        
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan'
            ], 400);
        }
        
        $order->update(['status' => 'cancelled']);
        
        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
    });
});

// ==================== TEST ROUTE FOR DEBUGGING ====================
Route::get('/test-dashboard-data', function() {
    $today = \Carbon\Carbon::today();
    $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
    $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
    
    $data = [
        'today' => $today->toDateString(),
        'start_of_month' => $startOfMonth->toDateString(),
        'end_of_month' => $endOfMonth->toDateString(),
        'today_income' => \App\Models\Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount'),
        'today_orders' => \App\Models\Order::whereDate('created_at', $today)->count(),
        'month_income' => \App\Models\Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->sum('total_amount'),
        'month_orders' => \App\Models\Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
        'all_completed_orders' => \App\Models\Order::where('status', 'completed')
            ->select('id', 'order_code', 'status', 'total_amount', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get(),
    ];
    
    return response()->json($data, JSON_PRETTY_PRINT);
});

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->name('admin.')->group(function () {
    // Public routes - no auth needed (LOGIN)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    
    // Protected routes - require authentication
    Route::middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Menu Management Routes
        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        
        // Orders Routes
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            // Route spesifik harus di atas route dengan parameter!
            Route::get('/notifications', [AdminOrderController::class, 'getNotifications'])->name('notifications');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::post('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
        });
                
        // Stock management routes
        Route::patch('/menus/{menu}/update-stock', [MenuController::class, 'updateStock'])->name('menus.update-stock');
        Route::patch('/menus/{menu}/toggle-availability', [MenuController::class, 'toggleAvailability'])->name('menus.toggle-availability');

        // Route untuk order customer (Customer view)
        Route::prefix('customer-orders')->name('customer-orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            Route::get('/status', [OrderController::class, 'getOrderStatus'])->name('status');
        });
        
        // Reports Routes
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/filter', [ReportController::class, 'filter'])->name('reports.filter');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        
        // User Management Routes
        Route::resource('users', \App\Http\Controllers\UserController::class);
        
        // Settings Routes
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        
        // ✅ TAMBAHKAN ROUTE INI: Fix database (opsional, hati-hati!)
        // Tambahkan ini di web.php di dalam group admin (setelah reports routes):

Route::post('/fix-database', function() {
    try {
        // Fix kolom status
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        
        // Fix kolom payment_status  
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'unpaid', 'failed') NOT NULL DEFAULT 'pending'");
        
        return response()->json([
            'success' => true,
            'message' => 'Database berhasil diperbaiki! Kolom status dan payment_status sudah sesuai.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbaiki database: ' . $e->getMessage()
        ], 500);
    }
})->name('admin.fix-database')->middleware('auth');
    });
});

// ==================== AUTH ROUTES DEFAULT ====================
// Comment jika tidak butuh register dan reset password untuk customer
// Auth::routes();