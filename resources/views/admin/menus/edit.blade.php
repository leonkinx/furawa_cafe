@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Menu</h1>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="makanan" {{ old('category', $menu->category) == 'makanan' ? 'selected' : '' }}>Food</option>
                        <option value="minuman" {{ old('category', $menu->category) == 'minuman' ? 'selected' : '' }}>Drink</option>
                        <option value="snack" {{ old('category', $menu->category) == 'snack' ? 'selected' : '' }}>Dessert</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga *</label>
                    <input type="number" name="price" value="{{ old('price', $menu->price) }}" required min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <!-- Stock Management -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="flex items-center mb-3">
                        <input type="checkbox" name="manage_stock" id="manageStock" value="1" 
                               {{ $menu->stock !== null ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Kelola Stok</span>
                    </label>
                    
                    <div id="stockField" class="{{ $menu->stock !== null ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                        <input type="number" name="stock" value="{{ old('stock', $menu->stock ?? 0) }}" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat *</label>
                    <textarea name="description" required rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('description', $menu->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detail Menu</label>
                    <textarea name="details" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('details', $menu->details) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Menu</label>
                    @if($menu->image)
                    <div class="mb-2">
                        <!-- TAMPILKAN GAMBAR DENGAN CARA YANG BENAR -->
                        @php
                            // Cara 1: Cek di storage/public
                            if (Storage::disk('public')->exists($menu->image)) {
                                $imageUrl = Storage::url($menu->image);
                            } 
                            // Cara 2: Coba akses langsung
                            elseif (file_exists(public_path('storage/' . $menu->image))) {
                                $imageUrl = asset('storage/' . $menu->image);
                            }
                            // Cara 3: Jika ada prefix menu-images/
                            elseif (strpos($menu->image, 'menu-images/') === 0 && file_exists(public_path('storage/' . $menu->image))) {
                                $imageUrl = asset('storage/' . $menu->image);
                            }
                            // Cara 4: Default placeholder
                            else {
                                $imageUrl = 'https://via.placeholder.com/150?text=Image+Not+Found';
                            }
                        @endphp
                        
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $menu->name }}" 
                             class="w-32 h-32 object-cover rounded-lg border"
                             onerror="this.src='https://via.placeholder.com/128?text=Gambar+Error'">
                        <p class="text-sm text-gray-500 mt-1">Gambar saat ini</p>
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, JPEG, GIF (Max: 2MB)</p>
                </div>

                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_best_seller" value="1" 
                               {{ $menu->is_best_seller ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Best Seller</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_available" value="1" 
                               {{ $menu->is_available ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Tersedia</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.menus.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Update Menu
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('manageStock').addEventListener('change', function() {
    const stockField = document.getElementById('stockField');
    if (this.checked) {
        stockField.classList.remove('hidden');
    } else {
        stockField.classList.add('hidden');
    }
});
</script>
@endsection