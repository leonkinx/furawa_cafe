@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 uppercase tracking-wide">Manajemen Pesanan</h1>
            <p class="text-gray-600 mt-2">Kelola dan pantau semua pesanan</p>
        </div>

        <!-- Stats Cards - Elegant Design -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
            <!-- Pending Card -->
            <div class="group relative bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative p-4 md:p-6">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl shadow-lg">
                            <i class="fas fa-clock text-white text-xl md:text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $pendingCount }}</p>
                        </div>
                    </div>
                    <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Menunggu Konfirmasi</h3>
                    <div class="mt-2 flex items-center text-xs text-yellow-700">
                        <i class="fas fa-circle text-yellow-500 mr-2 animate-pulse"></i>
                        <span class="break-words">Perlu ditindaklanjuti</span>
                    </div>
                </div>
            </div>

            <!-- Processing Card -->
            <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative p-4 md:p-6">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl shadow-lg">
                            <i class="fas fa-utensils text-white text-xl md:text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $processingCount }}</p>
                        </div>
                    </div>
                    <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Sedang Diproses</h3>
                    <div class="mt-2 flex items-center text-xs text-blue-700">
                        <i class="fas fa-circle text-blue-500 mr-2 animate-pulse"></i>
                        <span class="break-words">Dalam pengerjaan</span>
                    </div>
                </div>
            </div>

            <!-- Total Card -->
            <div class="group relative bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative p-4 md:p-6">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <div class="p-2 md:p-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl shadow-lg">
                            <i class="fas fa-shopping-cart text-white text-xl md:text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $orders->total() }}</p>
                        </div>
                    </div>
                    <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Total Pesanan</h3>
                    <div class="mt-2 flex items-center text-xs text-green-700">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="break-words">
                            @if(($dateFilter ?? 'today') == 'today')
                                Hari ini
                            @elseif(($dateFilter ?? 'today') == 'yesterday')
                                Kemarin
                            @elseif(($dateFilter ?? 'today') == '7days')
                                7 hari terakhir
                            @elseif(($dateFilter ?? 'today') == 'this_month')
                                Bulan ini
                            @else
                                Semua periode
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header dengan Filter -->
        <div class="px-3 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col gap-4">
                <!-- Title & Info -->
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Daftar Pesanan</h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                        @if(($dateFilter ?? 'today') == 'today')
                            Hari ini, {{ now()->format('d M Y') }}
                        @elseif(($dateFilter ?? 'today') == 'yesterday')
                            Kemarin, {{ now()->subDay()->format('d M Y') }}
                        @elseif(($dateFilter ?? 'today') == '7days')
                            7 hari terakhir
                        @elseif(($dateFilter ?? 'today') == 'this_month')
                            {{ now()->format('F Y') }}
                        @elseif(($dateFilter ?? 'today') == 'all')
                            Semua periode
                        @endif
                        • {{ $orders->total() }} pesanan
                    </p>
                </div>
                
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <!-- Date Filter Dropdown -->
                    <select onchange="window.location.href=this.value" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent flex-1 sm:flex-none">
                        <option value="{{ route('admin.orders.index', array_merge(request()->except('date_filter'), ['date_filter' => 'today'])) }}" {{ ($dateFilter ?? 'today') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="{{ route('admin.orders.index', array_merge(request()->except('date_filter'), ['date_filter' => 'yesterday'])) }}" {{ ($dateFilter ?? 'today') == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                        <option value="{{ route('admin.orders.index', array_merge(request()->except('date_filter'), ['date_filter' => '7days'])) }}" {{ ($dateFilter ?? 'today') == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="{{ route('admin.orders.index', array_merge(request()->except('date_filter'), ['date_filter' => 'this_month'])) }}" {{ ($dateFilter ?? 'today') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="{{ route('admin.orders.index', array_merge(request()->except('date_filter'), ['date_filter' => 'all'])) }}" {{ ($dateFilter ?? 'today') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                    
                    <!-- Status Filter -->
                    <select onchange="window.location.href=this.value" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent flex-1 sm:flex-none">
                        <option value="{{ route('admin.orders.index', ['date_filter' => $dateFilter ?? 'today']) }}" {{ !request('filter') ? 'selected' : '' }}>Semua Status</option>
                        <option value="{{ route('admin.orders.index', ['filter' => 'pending', 'date_filter' => $dateFilter ?? 'today']) }}" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="{{ route('admin.orders.index', ['filter' => 'processing', 'date_filter' => $dateFilter ?? 'today']) }}" {{ request('filter') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="{{ route('admin.orders.index', ['filter' => 'completed', 'date_filter' => $dateFilter ?? 'today']) }}" {{ request('filter') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="{{ route('admin.orders.index', ['filter' => 'cancelled', 'date_filter' => $dateFilter ?? 'today']) }}" {{ request('filter') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="{{ route('admin.orders.index', ['filter' => 'unpaid', 'date_filter' => $dateFilter ?? 'today']) }}" {{ request('filter') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    </select>
                    
                    <!-- Search -->
                    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-1">
                        <input type="hidden" name="date_filter" value="{{ $dateFilter ?? 'today' }}">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari..." 
                               class="px-3 py-2 border border-gray-300 rounded-l-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent flex-1">
                        <button type="submit" class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition">
                            <i class="fas fa-search text-xs sm:text-sm"></i>
                        </button>
                    </form>
                    
                    @if(request('search'))
                    <a href="{{ route('admin.orders.index', ['date_filter' => $dateFilter ?? 'today']) }}" class="px-3 sm:px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-xs sm:text-sm flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">NO. PESANAN</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">MEJA & CUSTOMER</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">ITEMS</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">TOTAL</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">STATUS</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">WAKTU</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="text-xs sm:text-sm font-mono font-medium text-gray-900">#{{ $order->order_code }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="text-xs sm:text-sm font-medium text-gray-900 whitespace-nowrap">Meja {{ $order->table_id }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-[120px] sm:max-w-none">{{ $order->customer_name }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="text-xs sm:text-sm text-gray-700 min-w-[150px]">
                                    @foreach($order->orderItems->take(2) as $item)
                                    <div class="truncate">{{ $item->menu->name }} × {{ $item->quantity }}</div>
                                    @endforeach
                                    @if($order->orderItems->count() > 2)
                                    <div class="text-xs text-gray-400 mt-1">+{{ $order->orderItems->count() - 2 }} item lainnya</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="text-xs sm:text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $order->getStatusBadgeClass() }}">
                                    {{ $order->getStatusText() }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="text-xs sm:text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="inline-flex items-center px-2 sm:px-3 py-1.5 bg-blue-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-eye mr-1 sm:mr-2"></i><span class="hidden sm:inline">Detail</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="text-sm font-medium text-gray-500">Tidak ada pesanan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-3 sm:px-6 py-4 border-t border-gray-200 bg-white">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs sm:text-sm text-gray-600">
                    {{ $orders->firstItem() }}-{{ $orders->lastItem() }} dari {{ $orders->total() }}
                </div>
                
                <div class="flex gap-1 flex-wrap justify-center">
                    @if ($orders->onFirstPage())
                        <span class="px-2 sm:px-3 py-1.5 sm:py-2 text-gray-400 cursor-not-allowed text-xs sm:text-sm">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $orders->appends(request()->except('page'))->previousPageUrl() }}" 
                           class="px-2 sm:px-3 py-1.5 sm:py-2 text-gray-700 hover:bg-gray-100 rounded transition text-xs sm:text-sm">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif
                    
                    @foreach ($orders->appends(request()->except('page'))->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <span class="px-2 sm:px-3 py-1.5 sm:py-2 bg-blue-600 text-white rounded font-medium text-xs sm:text-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-2 sm:px-3 py-1.5 sm:py-2 text-gray-700 hover:bg-gray-100 rounded transition text-xs sm:text-sm">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->appends(request()->except('page'))->nextPageUrl() }}" 
                           class="px-2 sm:px-3 py-1.5 sm:py-2 text-gray-700 hover:bg-gray-100 rounded transition text-xs sm:text-sm">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-2 sm:px-3 py-1.5 sm:py-2 text-gray-400 cursor-not-allowed text-xs sm:text-sm">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection