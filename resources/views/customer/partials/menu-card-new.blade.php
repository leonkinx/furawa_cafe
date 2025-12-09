{{-- NEW CARD DESIGN - TOTAL REDESIGN --}}
<div class="menu-item group bg-white rounded-2xl shadow-lg overflow-hidden relative {{ $isOutOfStock ? 'opacity-60 pointer-events-none' : '' }}" 
     data-category="{{ $category }}" 
     data-name="{{ strtolower($menu->name) }}"
     data-menu-id="{{ $menu->id }}">
    
    {{-- Image Section with Overlay --}}
    <div class="menu-image-container relative">
        @if($imageUrl)
        <img src="{{ $imageUrl }}" 
             alt="{{ $menu->name }}" 
             class="menu-image"
             onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
        @else
        <div class="image-placeholder w-full h-full flex flex-col items-center justify-center">
            <i class="fas fa-utensils text-4xl text-white opacity-50"></i>
            <span class="text-white text-sm mt-2 opacity-50">No Image</span>
        </div>
        @endif
        
        {{-- Badges Overlay on Image --}}
        <div class="absolute top-3 left-3 right-3 flex justify-between items-start z-10">
            {{-- Stock Badge --}}
            @if($isOutOfStock)
            <div class="bg-red-500 backdrop-blur-sm bg-opacity-90 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                <i class="fas fa-times-circle mr-1"></i>Habis
            </div>
            @else
            <div></div>
            @endif
            
            {{-- Best Seller Badge --}}
            @if($menu->is_best_seller)
            <div class="best-seller-badge text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center shadow-lg backdrop-blur-sm">
                <i class="fas fa-crown mr-1"></i>
                Best Seller
            </div>
            @endif
        </div>
        
        {{-- Price Tag on Image (Bottom Right) --}}
        <div class="absolute bottom-3 right-3 z-10">
            <div class="bg-white backdrop-blur-md bg-opacity-95 rounded-xl px-4 py-2 shadow-xl">
                <p class="text-xs text-gray-500 font-medium">Harga</p>
                <p class="font-black text-indigo-600 text-lg leading-none">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="p-4 md:p-5">
        {{-- Title & Description --}}
        <div class="mb-4">
            <h3 class="font-bold text-gray-900 text-lg md:text-xl mb-2 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                {{ $menu->name }}
            </h3>
            <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 mb-2">
                {{ $menu->description }}
            </p>
            
            {{-- Meta Info --}}
            <div class="flex items-center gap-3 text-xs">
                @if($menu->stock !== null && !$isOutOfStock)
                <span class="inline-flex items-center text-gray-400">
                    <i class="fas fa-box mr-1"></i>
                    Stok: {{ $menu->stock }}
                </span>
                @elseif(!$isOutOfStock)
                <span class="inline-flex items-center text-green-500 font-medium">
                    <i class="fas fa-check-circle mr-1"></i>
                    Tersedia
                </span>
                @endif
                
                @if($menu->details)
                <button class="text-indigo-600 font-medium hover:text-indigo-700 transition-colors view-details inline-flex items-center" 
                        data-details="{{ $menu->details }}">
                    <i class="fas fa-info-circle mr-1"></i>Info Detail
                </button>
                @endif
            </div>
        </div>
        
        {{-- Action Section --}}
        <div class="flex items-center gap-3">
            {{-- Quantity Controls --}}
            <div class="flex items-center bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl px-1 py-1 gap-1 flex-1">
                <button class="quantity-btn decrease bg-white w-9 h-9 rounded-lg flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" 
                        data-menu-id="{{ $menu->id }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <i class="fas fa-minus text-sm"></i>
                </button>
                <span class="quantity-display flex-1 text-center font-black text-gray-900 text-lg" data-menu-id="{{ $menu->id }}">0</span>
                <button class="quantity-btn increase bg-white w-9 h-9 rounded-lg flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" 
                        data-menu-id="{{ $menu->id }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <i class="fas fa-plus text-sm"></i>
                </button>
            </div>
            
            {{-- Add to Cart Button --}}
            <button class="add-to-cart bg-gradient-to-r from-indigo-600 to-purple-600 text-white w-12 h-12 md:w-auto md:px-6 md:h-12 rounded-xl hover:shadow-2xl hover:scale-110 text-sm font-bold transition-all duration-300 flex items-center justify-center group" 
                    data-menu-id="{{ $menu->id }}"
                    {{ $isOutOfStock ? 'disabled' : '' }}>
                <i class="fas fa-shopping-cart text-lg md:mr-2"></i>
                <span class="hidden md:inline">Pesan</span>
            </button>
        </div>
    </div>
</div>
