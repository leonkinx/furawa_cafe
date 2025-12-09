<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Table;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin Furawa',
            'email' => 'admin@furawacafe.com',
            'password' => bcrypt('password123'),
        ]);

        // Sample tables
        Table::create(['table_number' => 'A1', 'capacity' => 4, 'status' => 'available']);
        Table::create(['table_number' => 'A2', 'capacity' => 2, 'status' => 'available']);
        Table::create(['table_number' => 'A3', 'capacity' => 6, 'status' => 'available']);
        Table::create(['table_number' => 'B1', 'capacity' => 4, 'status' => 'occupied']);
        Table::create(['table_number' => 'B2', 'capacity' => 8, 'status' => 'available']);
        Table::create(['table_number' => 'B3', 'capacity' => 2, 'status' => 'reserved']);

        // Sample menus - MAKANAN
        $nasiGoreng = Menu::create([
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan ayam, udang, dan sayuran segar',
            'details' => 'Bahan: nasi, ayam fillet, udang, wortel, buncis, telur, bawang merah, bawang putih, kecap manis, saus tiram, minyak goreng.',
            'price' => 25000,
            'category' => 'makanan',
            'stock' => 50,
            'image' => null,
            'is_best_seller' => true,
            'is_available' => true
        ]);

        $mieAyam = Menu::create([
            'name' => 'Mie Ayam Bakso',
            'description' => 'Mie ayam dengan bakso sapi dan pangsit goreng',
            'details' => 'Bahan: mie telur, ayam suwir, bakso sapi, pangsit goreng, sawi, daun bawang, minyak ayam, kecap asin.',
            'price' => 20000,
            'category' => 'makanan',
            'stock' => 30,
            'image' => null,
            'is_best_seller' => true,
            'is_available' => true
        ]);

        $gadoGado = Menu::create([
            'name' => 'Gado-Gado',
            'description' => 'Salad Indonesia dengan bumbu kacang dan kerupuk',
            'details' => 'Bahan: sayuran rebus (kangkung, tauge, kacang panjang), tahu, tempe, telur, kentang, bumbu kacang, kerupuk.',
            'price' => 18000,
            'category' => 'makanan',
            'stock' => 25,
            'image' => null,
            'is_best_seller' => false,
            'is_available' => true
        ]);

        $sateAyam = Menu::create([
            'name' => 'Sate Ayam',
            'description' => 'Sate ayam dengan bumbu kacang dan lontong',
            'details' => 'Bahan: daging ayam, bumbu kacang, kecap manis, lontong, bawang goreng, sambal.',
            'price' => 22000,
            'category' => 'makanan',
            'stock' => 0, // Stok habis
            'image' => null,
            'is_best_seller' => false,
            'is_available' => false
        ]);

        // Sample menus - MINUMAN
        $esTeh = Menu::create([
            'name' => 'Es Teh Manis',
            'description' => 'Es teh segar dengan gula merah',
            'details' => 'Bahan: teh celup, gula merah, es batu, air matang.',
            'price' => 8000,
            'category' => 'minuman',
            'stock' => null, // Tidak dikelola stok
            'image' => null,
            'is_best_seller' => true,
            'is_available' => true
        ]);

        $jusAlpukat = Menu::create([
            'name' => 'Jus Alpukat',
            'description' => 'Jus alpukat segar dengan susu dan es krim',
            'details' => 'Bahan: alpukat matang, susu kental manis, es krim vanilla, es batu, gula cair.',
            'price' => 15000,
            'category' => 'minuman',
            'stock' => 20,
            'image' => null,
            'is_best_seller' => true,
            'is_available' => true
        ]);

        $kopiHitam = Menu::create([
            'name' => 'Kopi Hitam',
            'description' => 'Kopi hitam arabika fresh brew',
            'details' => 'Bahan: biji kopi arabika, air panas, gula optional.',
            'price' => 12000,
            'category' => 'minuman',
            'stock' => null, // Tidak dikelola stok
            'image' => null,
            'is_best_seller' => false,
            'is_available' => true
        ]);

        $esJeruk = Menu::create([
            'name' => 'Es Jeruk',
            'description' => 'Es jeruk segar dengan madu',
            'details' => 'Bahan: jeruk peras, madu, es batu, air matang.',
            'price' => 10000,
            'category' => 'minuman',
            'stock' => 15,
            'image' => null,
            'is_best_seller' => false,
            'is_available' => true
        ]);

        // Sample menus - SNACK
        $kentangGoreng = Menu::create([
            'name' => 'Kentang Goreng',
            'description' => 'Kentang goreng renyah dengan saus tomat dan mayo',
            'details' => 'Bahan: kentang, tepung bumbu, minyak goreng, saus tomat, mayonnaise.',
            'price' => 15000,
            'category' => 'snack',
            'stock' => 40,
            'image' => null,
            'is_best_seller' => true,
            'is_available' => true
        ]);

        $onionRings = Menu::create([
            'name' => 'Onion Rings',
            'description' => 'Ring bawang goreng crispy dengan saus BBQ',
            'details' => 'Bahan: bawang bombay, tepung roti, tepung terigu, telur, saus BBQ.',
            'price' => 18000,
            'category' => 'snack',
            'stock' => 0, // Stok habis
            'image' => null,
            'is_best_seller' => false,
            'is_available' => false
        ]);

        $kerupuk = Menu::create([
            'name' => 'Kerupuk Udang',
            'description' => 'Kerupuk udang renyah sebagai pelengkap',
            'details' => 'Bahan: kerupuk udang, minyak goreng.',
            'price' => 5000,
            'category' => 'snack',
            'stock' => null, // Tidak dikelola stok
            'image' => null,
            'is_best_seller' => false,
            'is_available' => true
        ]);

        $pisangGoreng = Menu::create([
            'name' => 'Pisang Goreng',
            'description' => 'Pisang goreng crispy dengan madu dan keju',
            'details' => 'Bahan: pisang kepok, tepung terigu, gula, keju parut, madu.',
            'price' => 12000,
            'category' => 'snack',
            'stock' => 25,
            'image' => null,
            'is_best_seller' => false,
            'is_available' => true
        ]);

        // Sample orders untuk testing
        $order1 = Order::create([
            'table_id' => 1,
            'customer_name' => 'Budi Santoso',
            'total_amount' => 48000,
            'status' => 'completed',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_code' => 'ORD-' . Str::random(8),
            'created_at' => Carbon::now()->subHours(2)
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'menu_id' => $nasiGoreng->id,
            'quantity' => 1,
            'price' => 25000
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'menu_id' => $esTeh->id,
            'quantity' => 2,
            'price' => 8000
        ]);

        $order2 = Order::create([
            'table_id' => 2,
            'customer_name' => 'Sari Dewi',
            'total_amount' => 67000,
            'status' => 'processing',
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'order_code' => 'ORD-' . Str::random(8),
            'created_at' => Carbon::now()->subHours(1)
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'menu_id' => $mieAyam->id,
            'quantity' => 2,
            'price' => 20000
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'menu_id' => $jusAlpukat->id,
            'quantity' => 1,
            'price' => 15000
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'menu_id' => $kentangGoreng->id,
            'quantity' => 1,
            'price' => 15000
        ]);

        $order3 = Order::create([
            'table_id' => 3,
            'customer_name' => 'Ahmad Fauzi',
            'total_amount' => 35000,
            'status' => 'pending',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_code' => 'ORD-' . Str::random(8),
            'created_at' => Carbon::now()->subMinutes(30)
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'menu_id' => $gadoGado->id,
            'quantity' => 1,
            'price' => 18000
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'menu_id' => $kopiHitam->id,
            'quantity' => 1,
            'price' => 12000
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'menu_id' => $kerupuk->id,
            'quantity' => 1,
            'price' => 5000
        ]);

        // Order untuk testing best seller
        $order4 = Order::create([
            'table_id' => 4,
            'customer_name' => 'Customer Best Seller',
            'total_amount' => 125000,
            'status' => 'completed',
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'order_code' => 'ORD-' . Str::random(8),
            'created_at' => Carbon::now()->subDays(1)
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'menu_id' => $nasiGoreng->id,
            'quantity' => 3,
            'price' => 25000
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'menu_id' => $esTeh->id,
            'quantity' => 5,
            'price' => 8000
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Login: admin@furawacafe.com / password123');
        $this->command->info('Total Menus: ' . Menu::count());
        $this->command->info('Total Tables: ' . Table::count());
        $this->command->info('Total Orders: ' . Order::count());
    }
}