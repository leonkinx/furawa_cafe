<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;

echo "=== DEBUG TEMPERATURE DATA ===\n\n";

// Cek pesanan terbaru
$latestOrder = Order::with('orderItems.menu')->latest()->first();

if ($latestOrder) {
    echo "Pesanan Terbaru:\n";
    echo "Order Code: " . $latestOrder->order_code . "\n";
    echo "Customer: " . $latestOrder->customer_name . "\n";
    echo "Created: " . $latestOrder->created_at . "\n\n";
    
    echo "Order Items:\n";
    foreach ($latestOrder->orderItems as $index => $item) {
        echo ($index + 1) . ". Menu: " . $item->menu->name . "\n";
        echo "   Quantity: " . $item->quantity . "\n";
        echo "   Temperature: " . ($item->temperature ?: 'NULL') . "\n";
        echo "   Price: " . $item->price . "\n\n";
    }
} else {
    echo "Tidak ada pesanan ditemukan.\n";
}

// Cek semua order items yang ada temperature
echo "=== SEMUA ORDER ITEMS DENGAN TEMPERATURE ===\n";
$itemsWithTemp = OrderItem::whereNotNull('temperature')->with('menu', 'order')->get();

if ($itemsWithTemp->count() > 0) {
    foreach ($itemsWithTemp as $item) {
        echo "Order: " . $item->order->order_code . "\n";
        echo "Menu: " . $item->menu->name . "\n";
        echo "Temperature: " . $item->temperature . "\n";
        echo "Quantity: " . $item->quantity . "\n\n";
    }
} else {
    echo "Tidak ada order items dengan temperature ditemukan.\n";
}