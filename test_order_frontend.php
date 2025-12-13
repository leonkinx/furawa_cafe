<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Support\Str;

echo "=== TEST ORDER DENGAN TEMPERATURE ===\n\n";

// Cari menu coklat
$menu = Menu::where('name', 'coklat')->first();

if (!$menu) {
    echo "Menu coklat tidak ditemukan\n";
    exit;
}

// Simulasi data yang dikirim dari frontend
$orderData = [
    'customer_name' => 'Test Temperature',
    'table_id' => 1,
    'payment_method' => 'cash',
    'subtotal' => 50000,
    'ppn_amount' => 5500,
    'service_charge' => 1500,
    'total_amount' => 57000,
    'items' => [
        [
            'menu_id' => $menu->id,
            'quantity' => 1,
            'temperature' => 'ice'
        ],
        [
            'menu_id' => $menu->id,
            'quantity' => 1,
            'temperature' => 'hot'
        ]
    ]
];

echo "Data yang akan dikirim:\n";
print_r($orderData);

// Buat order
$orderCode = 'ORD-' . strtoupper(Str::random(8));

$order = Order::create([
    'order_code' => $orderCode,
    'customer_name' => $orderData['customer_name'],
    'table_id' => $orderData['table_id'],
    'payment_method' => $orderData['payment_method'],
    'subtotal' => $orderData['subtotal'],
    'ppn_amount' => $orderData['ppn_amount'],
    'ppn_percentage' => 11,
    'service_charge' => $orderData['service_charge'],
    'service_charge_percentage' => 3,
    'total_amount' => $orderData['total_amount'],
    'status' => 'pending',
    'payment_status' => 'pending'
]);

echo "\nOrder created: " . $orderCode . "\n\n";

// Buat order items
foreach ($orderData['items'] as $itemData) {
    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'menu_id' => $itemData['menu_id'],
        'quantity' => $itemData['quantity'],
        'price' => $menu->price,
        'temperature' => $itemData['temperature']
    ]);
    
    echo "OrderItem created:\n";
    echo "- Menu: " . $menu->name . "\n";
    echo "- Quantity: " . $itemData['quantity'] . "\n";
    echo "- Temperature: " . $itemData['temperature'] . "\n";
    echo "- Saved temperature: " . ($orderItem->temperature ?: 'NULL') . "\n\n";
}

echo "Test selesai! Cek di admin panel: http://127.0.0.1:8000/admin/orders\n";