@extends('layouts.admin')

@section('title', 'Tambah Menu')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Menu Baru</h1>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
    @endif

    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="Contoh: Nasi Goreng Spesial">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select name="category" id="categorySelect" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="makanan" {{ old('category') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="minuman" {{ old('category') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                        <option value="snack" {{ old('category') == 'snack' ? 'selected' : '' }}>Dessert</option>
                    </select>
                    @error('category')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Temperature Options for Drinks -->
                <div id="temperatureOptions" class="bg-blue-50 p-4 rounded-lg border border-blue-200 {{ old('category') == 'minuman' ? '' : 'hidden' }}">
                    <label class="flex items-center mb-3">
                        <input type="checkbox" name="has_temperature_options" id="hasTemperatureOptions" value="1" 
                               {{ old('has_temperature_options') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Minuman ini memiliki pilihan varian (Ice/Hot)</span>
                    </label>
                    
                    <div id="temperatureChoices" class="{{ old('has_temperature_options') ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Varian yang Tersedia</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="temperature_options[]" value="ice" 
                                       {{ in_array('ice', old('temperature_options', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">🧊 Ice (Dingin)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="temperature_options[]" value="hot" 
                                       {{ in_array('hot', old('temperature_options', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">🔥 Hot (Panas)</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Pilih minimal satu opsi varian jika diaktifkan</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">Rp</span>
                        </div>
                        <input type="number" name="price" value="{{ old('price') }}" required min="0" step="500"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                               placeholder="25000">
                    </div>
                    @error('price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">PPN (Pajak Pertambahan Nilai)</label>
                    <div class="relative">
                        <input type="number" name="ppn_percentage" value="{{ old('ppn_percentage', 0) }}" min="0" max="100" step="0.01"
                               class="w-full pr-10 pl-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                               placeholder="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">%</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan 0 jika tidak ada PPN. Contoh: 11 untuk PPN 11%</p>
                    @error('ppn_percentage')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Management - PERBAIKAN DI SINI -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="flex items-center mb-3">
                        <input type="checkbox" name="manage_stock" id="manageStock" value="1" 
                               {{ old('manage_stock') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Kelola Stok</span>
                    </label>
                    
                    <div id="stockField" class="{{ old('manage_stock') ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                               placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">Masukkan 0 jika kosong. Kosongkan jika tidak ingin mengelola stok</p>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat *</label>
                    <textarea name="description" required rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                              placeholder="Deskripsi singkat tentang menu ini">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detail Menu (Opsional)</label>
                    <textarea name="details" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                              placeholder="Detail lengkap menu (bahan-bahan, cara penyajian, dll)">{{ old('details') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Informasi tambahan untuk ditampilkan ke pelanggan</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Menu</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition duration-200">
                        <input type="file" name="image" id="imageInput" accept="image/*" 
                               class="hidden" onchange="previewImage(event)">
                        <label for="imageInput" class="cursor-pointer">
                            <div id="imagePlaceholder" class="{{ old('image') ? 'hidden' : '' }}">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Klik untuk upload gambar</p>
                                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, GIF (Max: 2MB)</p>
                            </div>
                            <div id="imagePreviewContainer" class="{{ old('image') ? '' : 'hidden' }}">
                                <img id="previewImageElement" class="w-32 h-32 object-cover rounded-lg border mx-auto">
                                <button type="button" onclick="removeImage()" class="mt-2 text-red-600 text-sm hover:text-red-800">
                                    <i class="fas fa-trash mr-1"></i>Hapus Gambar
                                </button>
                            </div>
                        </label>
                    </div>
                    @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_best_seller" value="1" 
                               {{ old('is_best_seller') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-700 cursor-pointer">Best Seller</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" 
                               {{ old('is_available', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-700 cursor-pointer">Tersedia</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Field dengan tanda * wajib diisi
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.menus.index') }}" 
                       class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-save mr-2"></i>Simpan Menu
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Toggle stock field
document.getElementById('manageStock').addEventListener('change', function() {
    const stockField = document.getElementById('stockField');
    if (this.checked) {
        stockField.classList.remove('hidden');
        // Set default value 0 jika kosong
        const stockInput = stockField.querySelector('input[name="stock"]');
        if (!stockInput.value) {
            stockInput.value = 0;
        }
    } else {
        stockField.classList.add('hidden');
        // Kosongkan value jika tidak dikelola
        stockField.querySelector('input[name="stock"]').value = '';
    }
});

// Toggle temperature options based on category
document.getElementById('categorySelect').addEventListener('change', function() {
    const temperatureOptions = document.getElementById('temperatureOptions');
    const hasTemperatureOptions = document.getElementById('hasTemperatureOptions');
    const temperatureChoices = document.getElementById('temperatureChoices');
    
    if (this.value === 'minuman') {
        temperatureOptions.classList.remove('hidden');
    } else {
        temperatureOptions.classList.add('hidden');
        hasTemperatureOptions.checked = false;
        temperatureChoices.classList.add('hidden');
        // Uncheck all temperature options
        document.querySelectorAll('input[name="temperature_options[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
});

// Toggle temperature choices
document.getElementById('hasTemperatureOptions').addEventListener('change', function() {
    const temperatureChoices = document.getElementById('temperatureChoices');
    if (this.checked) {
        temperatureChoices.classList.remove('hidden');
    } else {
        temperatureChoices.classList.add('hidden');
        // Uncheck all temperature options
        document.querySelectorAll('input[name="temperature_options[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
});

// Image preview function
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('previewImageElement');
    const placeholder = document.getElementById('imagePlaceholder');
    const previewContainer = document.getElementById('imagePreviewContainer');
    
    if (file) {
        // Validasi ukuran file (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            event.target.value = '';
            return;
        }
        
        // Validasi tipe file
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak didukung. Gunakan JPEG, PNG, JPG, atau GIF.');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            placeholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

// Remove image function
function removeImage() {
    const fileInput = document.getElementById('imageInput');
    const placeholder = document.getElementById('imagePlaceholder');
    const previewContainer = document.getElementById('imagePreviewContainer');
    
    fileInput.value = '';
    previewContainer.classList.add('hidden');
    placeholder.classList.remove('hidden');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const manageStock = document.getElementById('manageStock');
    const stockField = document.getElementById('stockField');
    
    // Set initial state of stock field
    if (manageStock.checked) {
        stockField.classList.remove('hidden');
        const stockInput = stockField.querySelector('input[name="stock"]');
        if (!stockInput.value) {
            stockInput.value = 0;
        }
    } else {
        stockField.classList.add('hidden');
    }
    
    // Check for validation errors and show appropriate fields
    @if(old('manage_stock'))
        manageStock.checked = true;
        stockField.classList.remove('hidden');
    @endif
    
    // Handle checkbox state from old input
    @if(old('is_best_seller'))
        document.querySelector('input[name="is_best_seller"]').checked = true;
    @endif
    
    @if(old('is_available'))
        document.querySelector('input[name="is_available"]').checked = true;
    @elseif(old('is_available') === '0')
        document.querySelector('input[name="is_available"]').checked = false;
    @endif
});

// Real-time validation feedback
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('blur', function() {
        if (this.value.trim() === '' && this.hasAttribute('required')) {
            this.classList.add('border-red-300');
        } else {
            this.classList.remove('border-red-300');
        }
    });
    
    element.addEventListener('input', function() {
        this.classList.remove('border-red-300');
    });
});
</script>

<style>
/* Custom styles for better UX */
input:focus, textarea:focus, select:focus {
    outline: none;
    ring: 2px;
    ring-color: #3b82f6;
}

/* Smooth transitions */
input, textarea, select, button {
    transition: all 0.2s ease-in-out;
}

/* Hover effects */
button:hover {
    transform: translateY(-1px);
}

/* Custom checkbox styling */
input[type="checkbox"]:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

/* File upload area hover */
.border-dashed:hover {
    border-color: #60a5fa;
    background-color: #f0f9ff;
}
</style>
@endsection