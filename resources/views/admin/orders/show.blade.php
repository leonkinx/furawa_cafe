@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-800 inline-flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
                <p class="text-gray-500 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if($order->status === 'completed')
                <button onclick="printReceipt()" class="bg-gray-600 text-white px-4 py-2 rounded-xl hover:bg-gray-700 transition font-medium inline-flex items-center">
                    <i class="fas fa-print mr-2"></i>Cetak Struk
                </button>
                @else
                <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-xl cursor-not-allowed inline-flex items-center">
                    <i class="fas fa-print mr-2"></i>Cetak Struk
                    <span class="ml-2 text-xs">(Pesanan belum selesai)</span>
                </button>
                @endif
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->getStatusBadgeClass() }}">
                    {{ $order->getStatusText() }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Order Info & Items -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">No. Pesanan</p>
                        <p class="text-lg font-semibold text-gray-900">#{{ $order->order_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Meja</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->table_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Customer</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Metode Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($order->payment_method === 'qris')
                                QRIS
                            @elseif($order->payment_method === 'bank_transfer')
                                Transfer Bank
                            @else
                                Tunai
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Item Pesanan</h3>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $order->orderItems->count() }} item
                    </span>
                </div>
                
                <div class="space-y-4">
                    @foreach($order->orderItems as $index => $item)
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-start gap-4">
                            <!-- Item Number -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                    {{ $index + 1 }}
                                </div>
                            </div>
                            
                            <!-- Item Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 text-base mb-2">
                                            {{ $item->menu->name }}
                                        </h4>
                                        
                                        <!-- Temperature & Category Info -->
                                        <div class="flex items-center gap-2 mb-3">
                                            @if($item->temperature)
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                                    {{ $item->temperature === 'ice' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                                    @if($item->temperature === 'ice')
                                                        <i class="fas fa-snowflake mr-1"></i> Ice
                                                    @elseif($item->temperature === 'hot')
                                                        <i class="fas fa-fire mr-1"></i> Hot
                                                    @else
                                                        {{ ucfirst($item->temperature) }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs bg-gray-200 text-gray-600 rounded-full">
                                                    <i class="fas fa-thermometer-half mr-1"></i> Normal
                                                </span>
                                            @endif
                                            
                                            @if($item->menu->category)
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                                    <i class="fas fa-tag mr-1"></i> {{ ucfirst($item->menu->category) }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Price & Quantity Details -->
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div class="bg-white rounded-lg p-2 border">
                                                <div class="text-gray-500 text-xs mb-1">Harga Satuan</div>
                                                <div class="font-semibold text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="bg-white rounded-lg p-2 border">
                                                <div class="text-gray-500 text-xs mb-1">Quantity</div>
                                                <div class="font-semibold text-indigo-600">{{ $item->quantity }} pcs</div>
                                            </div>
                                            <div class="bg-white rounded-lg p-2 border">
                                                <div class="text-gray-500 text-xs mb-1">Subtotal</div>
                                                <div class="font-bold text-green-600">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Total Breakdown -->
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2 text-blue-600"></i>
                        Rincian Pembayaran
                    </h4>
                    
                    <div class="space-y-3">
                        @if($order->subtotal && $order->subtotal > 0)
                        <div class="flex items-center justify-between bg-white rounded-lg p-3">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-receipt mr-2 text-gray-500 w-4"></i>
                                Subtotal ({{ $order->orderItems->count() }} item)
                            </span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if($order->ppn_amount && $order->ppn_amount > 0)
                        <div class="flex items-center justify-between bg-white rounded-lg p-3">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-percent mr-2 text-gray-500 w-4"></i>
                                PPN ({{ number_format($order->ppn_percentage, 1) }}%)
                            </span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($order->ppn_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        @if($order->service_charge && $order->service_charge > 0)
                        <div class="flex items-center justify-between bg-white rounded-lg p-3">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-concierge-bell mr-2 text-gray-500 w-4"></i>
                                Service Charge ({{ number_format($order->service_charge_percentage, 1) }}%)
                            </span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold flex items-center">
                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                    Total Pembayaran
                                </span>
                                <span class="text-2xl font-bold">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Status -->
        <div class="space-y-6">
            <!-- Payment Status Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Pembayaran</h3>
                <div class="text-center py-4">
                    @if($order->payment_status === 'paid')
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                        </div>
                        <p class="text-lg font-semibold text-green-600">Lunas</p>
                    @else
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-clock text-yellow-600 text-3xl"></i>
                        </div>
                        <p class="text-lg font-semibold text-yellow-600">Menunggu Pembayaran</p>
                    @endif
                </div>
                
                @if($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                <button onclick="markOrderAsPaid({{ $order->id }})" 
                        class="w-full mt-4 bg-green-600 text-white px-4 py-3 rounded-xl hover:bg-green-700 transition font-medium mark-as-paid-btn"
                        data-order-id="{{ $order->id }}">
                    <i class="fas fa-money-check-alt mr-2"></i>Tandai Sudah Bayar
                </button>
                @endif
            </div>

            <!-- Order Status Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Pesanan</h3>
                
                <!-- Progress Steps -->
                <div class="relative">
                    <div class="flex justify-between mb-8">
                        <!-- Pending -->
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ in_array($order->status, ['pending', 'processing', 'completed']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="text-xs text-gray-600 text-center">Pending</span>
                        </div>
                        
                        <!-- Processing -->
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ in_array($order->status, ['processing', 'completed']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <span class="text-xs text-gray-600 text-center">Diproses</span>
                        </div>
                        
                        <!-- Completed -->
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 {{ $order->status === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="text-xs text-gray-600 text-center">Selesai</span>
                        </div>
                    </div>
                    
                    <!-- Progress Line -->
                    <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 -z-10" style="margin: 0 20px;">
                        <div class="h-full bg-blue-600 transition-all duration-500" style="width: {{ $order->status === 'pending' ? '0%' : ($order->status === 'processing' ? '50%' : '100%') }}"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2 mt-6">
                    @if($order->status === 'pending')
                        <button onclick="updateOrderStatus({{ $order->id }}, 'processing', 'Yakin ingin memproses pesanan ini?')" 
                                class="w-full bg-purple-600 text-white px-4 py-3 rounded-xl hover:bg-purple-700 transition font-medium">
                            <i class="fas fa-play mr-2"></i>Mulai Proses
                        </button>
                    @endif
                    
                    @if($order->status === 'processing')
                        <button onclick="updateOrderStatus({{ $order->id }}, 'completed', 'Yakin ingin menandai pesanan ini sebagai selesai?')" 
                                class="w-full bg-green-600 text-white px-4 py-3 rounded-xl hover:bg-green-700 transition font-medium">
                            <i class="fas fa-check mr-2"></i>Tandai Selesai
                        </button>
                    @endif
                    
                    @if(in_array($order->status, ['pending', 'processing']))
                        <button onclick="updateOrderStatus({{ $order->id }}, 'cancelled', 'Yakin ingin membatalkan pesanan ini?')" 
                                class="w-full bg-red-600 text-white px-4 py-3 rounded-xl hover:bg-red-700 transition font-medium">
                            <i class="fas fa-times mr-2"></i>Batalkan Pesanan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Receipt Template (Hidden) -->
<div id="receiptTemplate" style="display: none;">
    <div style="width: 300px; font-family: 'Courier New', monospace; padding: 20px;">
        <!-- Header -->
        <div style="text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
            <h2 style="margin: 0; font-size: 20px; font-weight: bold;">FURAWA CAFE</h2>
            <p style="margin: 5px 0; font-size: 11px;">Ruko Maddison Grande Blok J No 28</p>
            <p style="margin: 5px 0; font-size: 11px;">Kabupaten Tangerang, Banten 15334</p>
            <p style="margin: 5px 0; font-size: 12px;">IG: @furawacafe</p>
        </div>

        <!-- Order Info -->
        <div style="margin-bottom: 10px; font-size: 12px;">
            <table style="width: 100%;">
                <tr>
                    <td>No. Pesanan</td>
                    <td style="text-align: right; font-weight: bold;">#{{ $order->order_code }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td style="text-align: right;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Meja</td>
                    <td style="text-align: right;">{{ $order->table_id }}</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td style="text-align: right;">{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td style="text-align: right;">{{ auth()->user()->name ?? 'Admin' }}</td>
                </tr>
            </table>
        </div>

        <!-- Items -->
        <div style="border-top: 2px dashed #000; border-bottom: 2px dashed #000; padding: 10px 0; margin-bottom: 10px;">
            <table style="width: 100%; font-size: 12px;">
                @foreach($order->orderItems as $item)
                <tr>
                    <td colspan="3" style="padding: 5px 0;">
                        {{ $item->menu->name }}
                        @if($item->temperature)
                            @if($item->temperature === 'ice')
                                (Ice)
                            @elseif($item->temperature === 'hot')
                                (Hot)
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 10px;">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Total Breakdown -->
        <div style="margin-bottom: 10px; font-size: 12px;">
            <table style="width: 100%;">
                @if($order->subtotal && $order->subtotal > 0)
                <tr>
                    <td>Subtotal</td>
                    <td style="text-align: right;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                @if($order->ppn_amount && $order->ppn_amount > 0)
                <tr>
                    <td>PPN ({{ number_format($order->ppn_percentage, 1) }}%)</td>
                    <td style="text-align: right;">Rp {{ number_format($order->ppn_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                @if($order->service_charge && $order->service_charge > 0)
                <tr>
                    <td>Service Charge ({{ number_format($order->service_charge_percentage, 1) }}%)</td>
                    <td style="text-align: right;">Rp {{ number_format($order->service_charge, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                <tr style="border-top: 1px dashed #000;">
                    <td style="font-weight: bold; padding-top: 5px;">TOTAL</td>
                    <td style="text-align: right; font-weight: bold; font-size: 14px; padding-top: 5px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Pembayaran</td>
                    <td style="text-align: right;">
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
                    <td>Status</td>
                    <td style="text-align: right; font-weight: bold;">{{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div style="text-align: center; border-top: 2px dashed #000; padding-top: 10px; font-size: 11px;">
            <p style="margin: 5px 0;">Terima kasih atas kunjungan Anda</p>
            <p style="margin: 5px 0;">Selamat menikmati!</p>
            <p style="margin: 10px 0 0 0; font-size: 10px;">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</div>

<script>
function printReceipt() {
    // Get receipt template
    const receiptContent = document.getElementById('receiptTemplate').innerHTML;
    
    // Create new window for printing
    const printWindow = window.open('', '_blank', 'width=400,height=600');
    
    // Write content to new window
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Struk Pesanan #{{ $order->order_code }}</title>
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
                }
                body {
                    margin: 0;
                    padding: 0;
                    font-family: 'Courier New', monospace;
                }
                table {
                    border-collapse: collapse;
                }
                td {
                    padding: 2px 0;
                }
            </style>
        </head>
        <body>
            ${receiptContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for content to load then print
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        // Close window after printing
        setTimeout(() => {
            printWindow.close();
        }, 100);
    };
}
</script>

@endsection
