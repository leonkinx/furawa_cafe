<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan #{{ $order->order_code }} - Furawa Cafe</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-md mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('customer.menu') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            <h1 class="font-bold text-gray-800">Lacak Pesanan</h1>
            <div></div>
        </div>
    </div>

    <div class="max-w-md mx-auto p-4">
        <!-- Order Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-receipt text-white text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">#{{ $order->order_code }}</h2>
                <p class="text-gray-500 text-sm">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-medium">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Meja:</span>
                    <span class="font-medium">{{ $order->table_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-bold text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Status Progress -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Status Pesanan</h3>
            
            <div class="relative">
                <div class="flex justify-between mb-8">
                    <!-- Pending -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ in_array($order->status, ['pending', 'processing', 'completed']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas fa-clock text-sm"></i>
                        </div>
                        <span class="text-xs text-gray-600 text-center">Pending</span>
                    </div>
                    
                    <!-- Processing -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ in_array($order->status, ['processing', 'completed']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas fa-utensils text-sm"></i>
                        </div>
                        <span class="text-xs text-gray-600 text-center">Diproses</span>
                    </div>
                    
                    <!-- Completed -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ $order->status === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <span class="text-xs text-gray-600 text-center">Selesai</span>
                    </div>
                </div>
                
                <!-- Progress Line -->
                <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 -z-10" style="margin: 0 20px;">
                    <div class="h-full bg-blue-600 transition-all duration-500" style="width: {{ $order->status === 'pending' ? '0%' : ($order->status === 'processing' ? '50%' : '100%') }}"></div>
                </div>
            </div>

            <!-- Current Status -->
            <div class="text-center mt-6">
                <span class="px-4 py-2 rounded-full text-sm font-medium {{ $order->getStatusBadgeClass() }}">
                    {{ $order->getStatusText() }}
                </span>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Item Pesanan</h3>
                <span class="text-sm text-gray-500">{{ $order->orderItems->count() }} item</span>
            </div>
            
            <div class="space-y-4">
                @foreach($order->orderItems as $index => $item)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <div class="flex items-start gap-4">
                        <!-- Item Number & Image Placeholder -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        
                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 text-base mb-1">
                                        {{ $item->menu->name }}
                                    </h4>
                                    
                                    <!-- Temperature Badge -->
                                    @if($item->temperature)
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full 
                                            {{ $item->temperature === 'ice' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                            @if($item->temperature === 'ice')
                                                <i class="fas fa-snowflake mr-1"></i> Ice
                                            @elseif($item->temperature === 'hot')
                                                <i class="fas fa-fire mr-1"></i> Hot
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                    
                                    <!-- Price & Quantity Info -->
                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-tag mr-1 text-gray-400"></i>
                                            <span>Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-times mr-1 text-gray-400"></i>
                                            <span>{{ $item->quantity }} item</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Subtotal -->
                                <div class="text-right ml-4">
                                    <p class="text-lg font-bold text-indigo-600">
                                        Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Total Breakdown -->
            <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-calculator mr-2 text-gray-600"></i>
                    Rincian Pembayaran
                </h4>
                
                <div class="space-y-3">
                    @if($order->subtotal && $order->subtotal > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 flex items-center">
                            <i class="fas fa-receipt mr-2 text-gray-500 w-4"></i>
                            Subtotal
                        </span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    @if($order->ppn_amount && $order->ppn_amount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 flex items-center">
                            <i class="fas fa-percent mr-2 text-gray-500 w-4"></i>
                            PPN ({{ number_format($order->ppn_percentage, 1) }}%)
                        </span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($order->ppn_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    @if($order->service_charge && $order->service_charge > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 flex items-center">
                            <i class="fas fa-concierge-bell mr-2 text-gray-500 w-4"></i>
                            Service Charge ({{ number_format($order->service_charge_percentage, 1) }}%)
                        </span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-300 pt-3 mt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-gray-900 flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-indigo-600"></i>
                                Total Pembayaran
                            </span>
                            <span class="text-2xl font-bold text-indigo-600">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($order->status === 'completed')
        <div class="space-y-3">
            <a href="{{ route('orders.receipt', $order->order_code) }}" class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition font-medium flex items-center justify-center">
                <i class="fas fa-receipt mr-2"></i>
                Lihat & Cetak Struk
            </a>
        </div>
        @endif

        <!-- Payment Status -->
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Status Pembayaran</h3>
            <div class="text-center">
                @if($order->payment_status === 'paid')
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-green-600">Lunas</p>
                @else
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-yellow-600">Menunggu Pembayaran</p>
                    <p class="text-xs text-gray-500 mt-1">Tunjukkan kode pesanan ke kasir</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Auto Refresh -->
    <script>
    // Auto refresh every 30 seconds if order is not completed
    @if($order->status !== 'completed')
    setTimeout(() => {
        window.location.reload();
    }, 30000);
    @endif
    </script>
</body>
</html>