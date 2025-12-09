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
                <button onclick="printReceipt()" class="bg-gray-600 text-white px-4 py-2 rounded-xl hover:bg-gray-700 transition font-medium inline-flex items-center">
                    <i class="fas fa-print mr-2"></i>Cetak Struk
                </button>
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
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Item Pesanan</h3>
                <div class="space-y-3">
                    @foreach($order->orderItems as $item)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold">
                                {{ $item->quantity }}x
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $item->menu->name }}</h4>
                                <p class="text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }} / item</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Total -->
                <div class="mt-6 pt-4 border-t-2 border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
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
                    <td colspan="3" style="padding: 5px 0;">{{ $item->menu->name }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 10px;">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Total -->
        <div style="margin-bottom: 10px; font-size: 14px;">
            <table style="width: 100%;">
                <tr>
                    <td style="font-weight: bold;">TOTAL</td>
                    <td style="text-align: right; font-weight: bold; font-size: 16px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
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
