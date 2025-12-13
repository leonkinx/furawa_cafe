@extends('layouts.admin')

@section('title', 'Manajemen Menu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 uppercase tracking-wide">Manajemen Menu</h1>
        <p class="text-gray-600 mt-2">Kelola menu dan stok restoran</p>
    </div>

    <!-- Stats Cards - Elegant Design -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Total Menu -->
        <div class="group relative bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl shadow-lg">
                        <i class="fas fa-utensils text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $stats['total_menus'] ?? 0 }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Total Menu</h3>
                <div class="mt-2 flex items-center text-xs text-purple-700">
                    <i class="fas fa-list text-purple-500 mr-2"></i>
                    <span class="break-words">Semua menu</span>
                </div>
            </div>
        </div>

        <!-- Menu Tersedia -->
        <div class="group relative bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl shadow-lg">
                        <i class="fas fa-check-circle text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $stats['available_menus'] ?? 0 }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Menu Tersedia</h3>
                <div class="mt-2 flex items-center text-xs text-green-700">
                    <i class="fas fa-circle text-green-500 mr-2"></i>
                    <span class="break-words">Siap dipesan</span>
                </div>
            </div>
        </div>

        <!-- Stok Habis -->
        <div class="group relative bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-red-400 to-pink-500 rounded-xl shadow-lg">
                        <i class="fas fa-exclamation-triangle text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $stats['out_of_stock'] ?? 0 }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Stok Habis</h3>
                <div class="mt-2 flex items-center text-xs text-red-700">
                    <i class="fas fa-circle text-red-500 mr-2 animate-pulse"></i>
                    <span class="break-words">Perlu restock</span>
                </div>
            </div>
        </div>

        <!-- Best Seller -->
        <div class="group relative bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl shadow-lg">
                        <i class="fas fa-star text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $stats['best_sellers'] ?? 0 }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Best Seller</h3>
                <div class="mt-2 flex items-center text-xs text-yellow-700">
                    <i class="fas fa-fire text-yellow-500 mr-2"></i>
                    <span class="break-words">Menu favorit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 mb-6">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="flex flex-col gap-2 sm:gap-3">
            <!-- Search -->
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari nama menu..." 
                       class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <!-- Filters Row -->
            <div class="grid grid-cols-2 sm:flex gap-2">
                <!-- Category -->
                <select name="category" class="px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    <option value="makanan" {{ request('category') == 'makanan' ? 'selected' : '' }}>Food</option>
                    <option value="minuman" {{ request('category') == 'minuman' ? 'selected' : '' }}>Drinks</option>
                    <option value="snack" {{ request('category') == 'snack' ? 'selected' : '' }}>Dessert</option>
                </select>
                
                <!-- Status -->
                <select name="availability" class="px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                    <option value="out_of_stock" {{ request('availability') == 'out_of_stock' ? 'selected' : '' }}>Stok Habis</option>
                </select>
                
                <!-- Best Seller -->
                <label class="flex items-center px-2 sm:px-3 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 col-span-2 sm:col-span-1">
                    <input type="checkbox" name="best_seller" value="1" {{ request('best_seller') == '1' ? 'checked' : '' }} class="mr-2 text-blue-600">
                    <span class="text-xs sm:text-sm text-gray-700 whitespace-nowrap">Best Seller</span>
                </label>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="px-3 sm:px-4 py-2 bg-blue-600 text-white text-xs sm:text-sm rounded-lg hover:bg-blue-700 flex-shrink-0">
                    <i class="fas fa-search"></i>
                </button>
                
                @if(request()->hasAny(['search', 'category', 'availability', 'best_seller']))
                <a href="{{ route('admin.menus.index') }}" class="px-3 sm:px-4 py-2 bg-gray-100 text-gray-700 text-xs sm:text-sm rounded-lg hover:bg-gray-200 flex-shrink-0">
                    <i class="fas fa-times"></i>
                </a>
                @endif
                
                <a href="{{ route('admin.menus.create') }}" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 whitespace-nowrap text-center">
                    <i class="fas fa-plus mr-1"></i>Tambah Menu
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
    @endif

    <!-- Bulk Actions Toolbar -->
    <div id="bulkActionsToolbar" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span id="selectedCount" class="text-sm font-medium text-blue-800">0 item dipilih</span>
                <button type="button" id="selectAllBtn" class="text-xs text-blue-600 hover:text-blue-800">Pilih Semua</button>
                <button type="button" id="deselectAllBtn" class="text-xs text-blue-600 hover:text-blue-800">Batal Pilih</button>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="bulkDeleteBtn" class="px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 flex items-center gap-1">
                    <i class="fas fa-trash text-xs"></i>
                    Hapus Terpilih
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulkDeleteForm" action="{{ route('admin.menus.bulk-delete') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <div id="bulkDeleteInputs"></div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Gambar</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Nama Menu</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Kategori</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Harga</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Stok</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Status</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($menus as $menu)
                        <tr class="hover:bg-gray-50" data-menu-id="{{ $menu->id }}">
                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" class="menu-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                @if($menu->image)
                                    @php
                                        $imagePath = $menu->image;
                                        $storageExists = Storage::disk('public')->exists($imagePath);
                                        $publicExists = file_exists(public_path('storage/' . $imagePath));
                                        
                                        if ($storageExists) {
                                            $imageUrl = Storage::url($imagePath);
                                        } elseif ($publicExists) {
                                            $imageUrl = asset('storage/' . $imagePath);
                                        } elseif (strpos($imagePath, 'menu-images/') === 0) {
                                            $imageUrl = asset('storage/' . $imagePath);
                                        } else {
                                            $imageUrl = 'https://via.placeholder.com/48?text=No+Image';
                                        }
                                    @endphp
                                    
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $menu->name }}" 
                                         class="w-10 h-10 sm:w-12 sm:h-12 object-cover rounded-lg"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/48?text=Error'">
                                @else
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-400 text-xs sm:text-sm"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 min-w-[150px]">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $menu->name }}</div>
                                        <div class="text-xs text-gray-500 truncate hidden sm:block">{{ Str::limit($menu->description, 40) }}</div>
                                    </div>
                                    @if($menu->is_best_seller)
                                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap self-start">
                                        <i class="fas fa-star text-xs mr-1"></i><span class="hidden sm:inline">Best Seller</span>
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium 
                                    @if($menu->category == 'makanan') bg-green-50 text-green-700
                                    @elseif($menu->category == 'minuman') bg-blue-50 text-blue-700
                                    @else bg-purple-50 text-purple-700 @endif">
                                    {{ ucfirst($menu->category) }}
                                </span>
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                <span class="text-xs sm:text-sm font-medium text-gray-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                @if($menu->stock === null)
                                    <span class="text-xs text-gray-400">-</span>
                                @else
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <span class="text-xs sm:text-sm {{ $menu->stock == 0 ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            {{ $menu->stock }}
                                        </span>
                                        <div class="flex gap-0.5 sm:gap-1">
                                            <form action="{{ route('admin.menus.update-stock', $menu->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" class="text-green-600 hover:text-green-700" title="Tambah">
                                                    <i class="fas fa-plus-circle text-xs sm:text-sm"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.menus.update-stock', $menu->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" class="text-orange-600 hover:text-orange-700 {{ $menu->stock == 0 ? 'opacity-30 cursor-not-allowed' : '' }}" 
                                                    {{ $menu->stock == 0 ? 'disabled' : '' }} title="Kurangi">
                                                    <i class="fas fa-minus-circle text-xs sm:text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                <form action="{{ route('admin.menus.toggle-availability', $menu->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-medium rounded {{ $menu->is_available ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $menu->is_available ? 'Tersedia' : 'Tidak' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                <div class="flex gap-1 sm:gap-2">
                                    <a href="{{ route('admin.menus.edit', $menu->id) }}" 
                                       class="text-blue-600 hover:text-blue-700" title="Edit">
                                        <i class="fas fa-edit text-xs sm:text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-700" 
                                                title="Hapus"
                                                onclick="return confirm('Hapus menu {{ $menu->name }}?')">
                                            <i class="fas fa-trash text-xs sm:text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="fas fa-utensils text-3xl mb-2"></i>
                                    <p class="text-sm text-gray-500">Belum ada menu</p>
                                    <a href="{{ route('admin.menus.create') }}" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-plus mr-1"></i>Tambah Menu
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($menus->hasPages())
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm">
        <div class="text-gray-600">
            Menampilkan {{ $menus->firstItem() }}-{{ $menus->lastItem() }} dari {{ $menus->total() }}
        </div>
        
        <div class="flex gap-1 flex-wrap justify-center">
            @if($menus->onFirstPage())
            <span class="px-2 sm:px-3 py-1 sm:py-1.5 bg-gray-100 text-gray-400 rounded cursor-not-allowed">
                <i class="fas fa-chevron-left text-xs"></i>
            </span>
            @else
            <a href="{{ $menus->previousPageUrl() }}" class="px-2 sm:px-3 py-1 sm:py-1.5 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            @endif

            @foreach($menus->getUrlRange(1, $menus->lastPage()) as $page => $url)
                @if($page == $menus->currentPage())
                <span class="px-2 sm:px-3 py-1 sm:py-1.5 bg-blue-600 text-white rounded font-medium">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="px-2 sm:px-3 py-1 sm:py-1.5 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach

            @if($menus->hasMorePages())
            <a href="{{ $menus->nextPageUrl() }}" class="px-2 sm:px-3 py-1 sm:py-1.5 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
            @else
            <span class="px-2 sm:px-3 py-1 sm:py-1.5 bg-gray-100 text-gray-400 rounded cursor-not-allowed">
                <i class="fas fa-chevron-right text-xs"></i>
            </span>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const menuCheckboxes = document.querySelectorAll('.menu-checkbox');
    const bulkActionsToolbar = document.getElementById('bulkActionsToolbar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    // Update toolbar visibility and selected count
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.menu-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            bulkActionsToolbar.classList.remove('hidden');
            selectedCountSpan.textContent = `${count} item dipilih`;
        } else {
            bulkActionsToolbar.classList.add('hidden');
        }
        
        // Update select all checkbox state
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === menuCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
            selectAllCheckbox.checked = false;
        }
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        menuCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateBulkActions();
    });

    // Individual checkbox change
    menuCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Select all button
    selectAllBtn.addEventListener('click', function() {
        menuCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateBulkActions();
    });

    // Deselect all button
    deselectAllBtn.addEventListener('click', function() {
        menuCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateBulkActions();
    });

    // Bulk delete button
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.menu-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count === 0) {
            alert('Pilih minimal 1 menu untuk dihapus');
            return;
        }
        
        const menuNames = Array.from(checkedBoxes).map(checkbox => {
            const row = checkbox.closest('tr');
            const nameCell = row.querySelector('td:nth-child(3) .text-gray-900');
            return nameCell ? nameCell.textContent.trim() : 'Menu';
        });
        
        const confirmMessage = count === 1 
            ? `Yakin ingin menghapus menu "${menuNames[0]}"?`
            : `Yakin ingin menghapus ${count} menu terpilih?\n\nMenu yang akan dihapus:\n${menuNames.slice(0, 5).join('\n')}${count > 5 ? `\n... dan ${count - 5} menu lainnya` : ''}`;
        
        if (confirm(confirmMessage)) {
            // Show loading state
            bulkDeleteBtn.disabled = true;
            bulkDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Menghapus...';
            
            // Clear previous inputs
            const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');
            bulkDeleteInputs.innerHTML = '';
            
            // Add selected menu IDs to hidden form
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'menu_ids[]';
                input.value = checkbox.value;
                bulkDeleteInputs.appendChild(input);
            });
            
            // Submit form
            bulkDeleteForm.submit();
        }
    });

    // Initial update
    updateBulkActions();
});
</script>

@endsection