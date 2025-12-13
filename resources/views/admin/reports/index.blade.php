@extends('layouts.admin')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 uppercase tracking-wide">Laporan Transaksi</h1>
        <p class="text-gray-600 mt-2">Analisis data penjualan dan keuangan</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
        <div class="flex flex-col gap-4">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800">Filter Laporan</h2>
            <div class="flex flex-col gap-3">
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Periode</label>
                    <select id="periodSelect" class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="today">Hari Ini</option>
                        <option value="yesterday">Kemarin</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="year">Tahun Ini</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                
                <div id="customDateRange" class="hidden">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Rentang Tanggal</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="date" id="startDate" class="flex-1 border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="self-center text-gray-500 text-xs sm:text-sm">s/d</span>
                        <input type="date" id="endDate" class="flex-1 border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <button onclick="filterReports()" class="w-full sm:w-auto bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center text-xs sm:text-sm">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards - Elegant Design -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
        <!-- Total Pendapatan -->
        <div class="group relative bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl shadow-lg">
                        <i class="fas fa-money-bill-wave text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-lg md:text-2xl font-bold text-gray-800 break-words" id="totalRevenue">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Pendapatan</h3>
                <div class="mt-2 flex items-center text-xs text-green-700">
                    <i class="fas fa-arrow-up text-green-500 mr-2"></i>
                    <span>Revenue</span>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="group relative bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl shadow-lg">
                        <i class="fas fa-shopping-cart text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800" id="totalTransactions">{{ $totalTransactions }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Transaksi</h3>
                <div class="mt-2 flex items-center text-xs text-blue-700">
                    <i class="fas fa-receipt text-blue-500 mr-2"></i>
                    <span>Pesanan</span>
                </div>
            </div>
        </div>

        <!-- Rata-rata Transaksi -->
        <div class="group relative bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl shadow-lg">
                        <i class="fas fa-chart-line text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-lg md:text-2xl font-bold text-gray-800 break-words" id="averageTransaction">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide">Rata-rata Transaksi</h3>
                <div class="mt-2 flex items-center text-xs text-purple-700">
                    <i class="fas fa-calculator text-purple-500 mr-2"></i>
                    <span>Per pesanan</span>
                </div>
            </div>
        </div>

        <!-- Transaksi Tertinggi -->
        <div class="group relative bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl shadow-lg">
                        <i class="fas fa-trophy text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-lg md:text-2xl font-bold text-gray-800 break-words" id="highestTransaction">Rp {{ number_format($highestTransaction, 0, ',', '.') }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide">Transaksi Tertinggi</h3>
                <div class="mt-2 flex items-center text-xs text-yellow-700">
                    <i class="fas fa-crown text-yellow-500 mr-2"></i>
                    <span>Highest value</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Pendapatan per Hari -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendapatan per Hari</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Transaksi per Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Status Transaksi</h3>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Menu Terlaris</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Menu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Terjual
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Pendapatan
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="topProductsBody">
                    @forelse($topProducts as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-full object-cover">
                                @else
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-400"></i>
                                </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ $product->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->total_sold }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            Rp {{ number_format($product->revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada data menu
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-3 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Detail Transaksi</h3>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ route('admin.reports.export.excel', array_merge(request()->all())) }}" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center text-xs sm:text-sm">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel (Detail)
                    </a>
                </div>
            </div>
            
            <!-- Search & Filter -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col gap-2 sm:gap-3">
                <!-- Hidden inputs untuk tanggal -->
                <input type="hidden" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                <input type="hidden" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari no. pesanan, customer, meja..." 
                           class="w-full px-3 sm:px-4 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Filters Row -->
                <div class="grid grid-cols-2 sm:flex gap-2">
                    <!-- Status Filter -->
                    <select name="status_filter" class="px-2 sm:px-4 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status_filter') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status_filter') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status_filter') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    
                    <!-- Payment Filter -->
                    <select name="payment_filter" class="px-2 sm:px-4 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Pembayaran</option>
                        <option value="paid" {{ request('payment_filter') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('payment_filter') == 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                    </select>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-blue-600 text-white text-xs sm:text-sm rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-1"></i>Cari
                    </button>
                    
                    @if(request()->hasAny(['search', 'status_filter', 'payment_filter']))
                    <a href="{{ route('admin.reports.index', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
                       class="flex-shrink-0 px-3 sm:px-4 py-2 bg-gray-200 text-gray-700 text-xs sm:text-sm rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                No. Pesanan
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Tanggal
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Customer
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Meja
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Items
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Total
                            </th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="transactionsBody">
                        @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                                #{{ $transaction->order_number }}
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                {{ $transaction->created_at->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                {{ $transaction->customer_name }}
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                {{ $transaction->table_number }}
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                {{ $transaction->orderItems->count() }} item
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-semibold text-gray-900">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($transaction->status == 'completed') bg-green-100 text-green-800
                                    @elseif($transaction->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($transaction->status == 'paid') bg-yellow-100 text-yellow-800
                                    @elseif($transaction->status == 'pending') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $transaction->getStatusText() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-xl p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Memproses...</p>
        </div>
    </div>
</div>

<script>
// Initialize charts
let revenueChart = null;
let statusChart = null;

function initCharts(revenueData, statusData) {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) revenueChart.destroy();
    
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: revenueData.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    if (statusChart) statusChart.destroy();
    
    statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.labels,
            datasets: [{
                data: statusData.data,
                backgroundColor: [
                    '#ef4444', // pending - red
                    '#f59e0b', // paid - yellow
                    '#3b82f6', // processing - blue
                    '#10b981'  // completed - green
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Filter functionality
document.getElementById('periodSelect').addEventListener('change', function() {
    const customDateRange = document.getElementById('customDateRange');
    if (this.value === 'custom') {
        customDateRange.classList.remove('hidden');
    } else {
        customDateRange.classList.add('hidden');
    }
});

async function filterReports() {
    const period = document.getElementById('periodSelect').value;
    let startDate = null;
    let endDate = null;
    
    if (period === 'custom') {
        startDate = document.getElementById('startDate').value;
        endDate = document.getElementById('endDate').value;
        
        if (!startDate || !endDate) {
            alert('Silakan pilih rentang tanggal');
            return;
        }
    }
    
    showLoading();
    
    try {
        const response = await fetch('/admin/reports/filter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                period: period,
                start_date: startDate,
                end_date: endDate
            })
        });
        
        const data = await response.json();
        
        // Update statistics
        document.getElementById('totalRevenue').textContent = 'Rp ' + data.totalRevenue.toLocaleString('id-ID');
        document.getElementById('totalTransactions').textContent = data.totalTransactions;
        document.getElementById('averageTransaction').textContent = 'Rp ' + data.averageTransaction.toLocaleString('id-ID');
        document.getElementById('highestTransaction').textContent = 'Rp ' + data.highestTransaction.toLocaleString('id-ID');
        
        // Update top products
        let topProductsHtml = '';
        if (data.topProducts.length > 0) {
            data.topProducts.forEach(product => {
                topProductsHtml += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-utensils text-gray-400"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${product.name}</div>
                                <div class="text-sm text-gray-500">Rp ${product.price.toLocaleString('id-ID')}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            ${product.category}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${product.total_sold}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        Rp ${product.revenue.toLocaleString('id-ID')}
                    </td>
                </tr>`;
            });
        } else {
            topProductsHtml = `
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                    Tidak ada data menu
                </td>
            </tr>`;
        }
        document.getElementById('topProductsBody').innerHTML = topProductsHtml;
        
        // Update transactions
        let transactionsHtml = '';
        if (data.transactions.data.length > 0) {
            data.transactions.data.forEach(transaction => {
                const statusClass = getStatusClass(transaction.status);
                const statusText = getStatusText(transaction.status);
                
                transactionsHtml += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #${transaction.order_number}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(transaction.created_at).toLocaleDateString('id-ID', { 
                            day: 'numeric', 
                            month: 'short', 
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${transaction.customer_name}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${transaction.table_number}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${transaction.items_count} item
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        Rp ${transaction.total_amount.toLocaleString('id-ID')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                </tr>`;
            });
        } else {
            transactionsHtml = `
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    Tidak ada transaksi
                </td>
            </tr>`;
        }
        document.getElementById('transactionsBody').innerHTML = transactionsHtml;
        
        // Update charts
        initCharts(data.revenueData, data.statusData);
        
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memfilter data');
    } finally {
        hideLoading();
    }
}

function getStatusClass(status) {
    switch(status) {
        case 'completed': return 'bg-green-100 text-green-800';
        case 'processing': return 'bg-blue-100 text-blue-800';
        case 'paid': return 'bg-yellow-100 text-yellow-800';
        case 'pending': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusText(status) {
    const statusMap = {
        'pending': 'Menunggu',
        'paid': 'Dibayar',
        'processing': 'Diproses',
        'completed': 'Selesai'
    };
    return statusMap[status] || status;
}

function exportToExcel() {
    showLoading();
    
    const period = document.getElementById('periodSelect').value;
    let startDate = null;
    let endDate = null;
    
    if (period === 'custom') {
        startDate = document.getElementById('startDate').value;
        endDate = document.getElementById('endDate').value;
    }
    
    // Create form for download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/reports/export';
    form.style.display = 'none';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const periodInput = document.createElement('input');
    periodInput.type = 'hidden';
    periodInput.name = 'period';
    periodInput.value = period;
    form.appendChild(periodInput);
    
    if (startDate) {
        const startInput = document.createElement('input');
        startInput.type = 'hidden';
        startInput.name = 'start_date';
        startInput.value = startDate;
        form.appendChild(startInput);
    }
    
    if (endDate) {
        const endInput = document.createElement('input');
        endInput.type = 'hidden';
        endInput.name = 'end_date';
        endInput.value = endDate;
        form.appendChild(endInput);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    setTimeout(hideLoading, 1000);
}

function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    const revenueData = @json($revenueData);
    const statusData = @json($statusData);
    
    if (revenueData && statusData) {
        initCharts(revenueData, statusData);
    }
});
</script>
@endsection