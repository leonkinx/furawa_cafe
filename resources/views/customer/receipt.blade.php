<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $order->order_code }} - Furawa Cafe</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
            font-family: 'Courier New', monospace;
            background: white;
            padding: 20px;
        }
        
        @media (max-width: 640px) {
            .receipt-container {
                margin: 0;
                max-width: 100%;
                padding: 15px;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header Actions (No Print) -->
    <div class="no-print bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-center">
            <a href="{{ route('orders.track', $order->order_code) }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Detail
            </a>
        </div>
    </div>

    <!-- Receipt Content -->
    <div class="py-6">
        <div class="receipt-container">
            <!-- Header -->
            <div class="text-center border-b-2 border-dashed border-gray-400 pb-4 mb-4">
                <h1 class="text-xl font-bold mb-2">FURAWA CAFE</h1>
                <p class="text-xs text-gray-600 mb-1">Ruko Maddison Grande Blok J No 28</p>
                <p class="text-xs text-gray-600 mb-1">Kabupaten Tangerang, Banten 15334</p>
                <p class="text-xs text-gray-600">IG: @furawacafe</p>
            </div>

            <!-- Order Info -->
            <div class="mb-4 text-xs">
                <table class="w-full">
                    <tr>
                        <td class="py-1">No. Pesanan</td>
                        <td class="py-1 text-right font-bold">#{{ $order->order_code }}</td>
                    </tr>
                    <tr>
                        <td class="py-1">Tanggal</td>
                        <td class="py-1 text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1">Meja</td>
                        <td class="py-1 text-right">{{ $order->table_id }}</td>
                    </tr>
                    <tr>
                        <td class="py-1">Customer</td>
                        <td class="py-1 text-right">{{ $order->customer_name }}</td>
                    </tr>
                    <tr>
                        <td class="py-1">Kasir</td>
                        <td class="py-1 text-right">Admin Furawa</td>
                    </tr>
                </table>
            </div>

            <!-- Items -->
            <div class="border-t-2 border-b-2 border-dashed border-gray-400 py-4 mb-4">
                <div class="text-xs space-y-2">
                    @foreach($order->orderItems as $item)
                    <div>
                        <div class="font-medium">
                            {{ $item->menu->name }}
                            @if($item->temperature)
                                <span class="text-blue-600 font-bold">
                                    @if($item->temperature === 'ice')
                                        🧊 Ice
                                    @elseif($item->temperature === 'hot')
                                        🔥 Hot
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between pl-2">
                            <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Breakdown -->
            <div class="mb-4 text-xs">
                <table class="w-full">
                    @if($order->subtotal && $order->subtotal > 0)
                    <tr>
                        <td class="py-1">Subtotal</td>
                        <td class="py-1 text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    
                    @if($order->ppn_amount && $order->ppn_amount > 0)
                    <tr>
                        <td class="py-1">PPN ({{ number_format($order->ppn_percentage, 1) }}%)</td>
                        <td class="py-1 text-right">Rp {{ number_format($order->ppn_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    
                    @if($order->service_charge && $order->service_charge > 0)
                    <tr>
                        <td class="py-1">Service Charge ({{ number_format($order->service_charge_percentage, 1) }}%)</td>
                        <td class="py-1 text-right">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    
                    <tr class="border-t border-dashed border-gray-400">
                        <td class="py-2 font-bold text-sm">TOTAL</td>
                        <td class="py-2 text-right font-bold text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1">Pembayaran</td>
                        <td class="py-1 text-right">
                            @if($order->payment_method === 'qris')
                                QRIS
                            @elseif($order->payment_method === 'bank_transfer')
                                Transfer Bank
                            @else
                                Tunai
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1">Status</td>
                        <td class="py-1 text-right font-bold">
                            @if($order->payment_status === 'paid')
                                <span class="text-green-600">LUNAS</span>
                            @else
                                <span class="text-yellow-600">BELUM BAYAR</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div class="text-center border-t-2 border-dashed border-gray-400 pt-4 text-xs text-gray-600">
                <p class="mb-1">Terima kasih atas kunjungan Anda</p>
                <p class="mb-3">Selamat menikmati!</p>
                <p class="text-xs">{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>

    <!-- Success Message (No Print) -->
    @if($order->payment_status === 'paid')
    <div class="no-print fixed bottom-4 left-4 right-4 max-w-md mx-auto">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-center">
            <i class="fas fa-check-circle mr-2"></i>
            Pesanan Anda sudah lunas dan sedang diproses!
        </div>
    </div>
    @endif

    <script>
    // Receipt page - hanya menampilkan struk tanpa aksi tambahan
    </script>
</body>
</html>