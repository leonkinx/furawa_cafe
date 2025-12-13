{{-- NEW CARD DESIGN - TOTAL REDESIGN --}}
<div class="menu-item group bg-white rounded-2xl shadow-lg overflow-hidden relative {{ $isOutOfStock ? 'opacity-60 pointer-events-none' : '' }}" 
     data-category="{{ $category }}" 
     data-name="{{ strtolower($menu->name) }}"
     data-menu-id="{{ $menu->id }}"
     data-base-price="{{ $menu->price }}"
     data-ppn-percentage="{{ $menu->ppn_percentage ?? 0 }}"
     data-ppn-amount="{{ $menu->ppn_amount ?? 0 }}"
     data-final-price="{{ $menu->final_price }}"
     data-has-temperature="{{ $menu->has_temperature_options ? 'true' : 'false' }}"
     data-temperature-options="{{ $menu->temperature_options ? json_encode($menu->temperature_options) : '[]' }}">
    
    {{-- Image Section with Overlay --}}
    <div class="menu-image-container relative">
        @php
            // Generate admin image URL - prioritize admin uploads
            $adminImageUrl = null;
            if ($menu->image) {
                // Always use storage URL for uploaded images
                $adminImageUrl = asset('storage/' . $menu->image);
            }
        @endphp
        
        <img src="{{ $adminImageUrl ?: $fallbackImage }}" 
             alt="{{ $menu->name }}" 
             class="menu-image"
             loading="lazy"
             onerror="this.onerror=null; this.src='{{ $fallbackImage }}';"
             data-admin-image="{{ $menu->image ?? 'none' }}"
             data-admin-url="{{ $adminImageUrl ?? 'none' }}">
        
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
        

    </div>

    {{-- Content Section --}}
    <div class="p-4 md:p-5">
        {{-- Title --}}
        <div class="mb-2">
            <h3 class="font-semibold text-gray-900 text-sm md:text-base leading-tight group-hover:text-indigo-600 transition-colors">
                {{ $menu->name }}
            </h3>
        </div>
        
        {{-- Price --}}
        <div class="mb-3">
            @if($menu->ppn_percentage > 0)
                <p class="font-semibold text-indigo-600 text-sm">Rp {{ number_format($menu->final_price, 0, ',', '.') }}</p>
                <div class="text-xs text-gray-500 mt-1">
                    <div>Base: Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                    <div>PPN {{ $menu->ppn_percentage }}%: Rp {{ number_format($menu->ppn_amount, 0, ',', '.') }}</div>
                </div>
            @else
                <p class="font-semibold text-indigo-600 text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
            @endif
        </div>
        
        {{-- Description --}}
        <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 mb-3">
            {{ $menu->description }}
        </p>

        {{-- Temperature Options for Drinks --}}
        @if($menu->category === 'minuman' && $menu->has_temperature_options && $menu->temperature_options)
        <div class="temperature-options mb-3" data-menu-id="{{ $menu->id }}">
            <p class="text-xs font-medium text-gray-700 mb-2">Pilih Varian: <span class="text-red-500">*</span></p>
            <div class="flex gap-2">
                @foreach($menu->temperature_options as $temp)
                <button class="temperature-btn px-3 py-1 text-xs rounded-full border-2 transition-all duration-200 border-gray-300 bg-white text-gray-600 hover:border-indigo-400"
                        data-temperature="{{ $temp }}"
                        data-menu-id="{{ $menu->id }}">
                    @if($temp === 'ice')
                        🧊 Ice
                    @elseif($temp === 'hot')
                        🔥 Hot
                    @endif
                </button>
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- Meta Info --}}
        <div class="flex items-center gap-3 text-xs mb-4">
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
        
        {{-- Action Section - Ultra Compact --}}
        <div class="flex items-stretch gap-1">
            {{-- Quantity Controls - Ultra Compact --}}
            <div class="flex items-center bg-gray-100 rounded-md overflow-hidden flex-shrink-0">
                <button class="quantity-btn decrease bg-transparent w-6 h-6 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all active:scale-95" 
                        data-menu-id="{{ $menu->id }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <i class="fas fa-minus" style="font-size: 10px;"></i>
                </button>
                <span class="quantity-display w-6 text-center font-semibold text-gray-900 text-xs bg-white" data-menu-id="{{ $menu->id }}">0</span>
                <button class="quantity-btn increase bg-transparent w-6 h-6 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all active:scale-95" 
                        data-menu-id="{{ $menu->id }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <i class="fas fa-plus" style="font-size: 10px;"></i>
                </button>
            </div>
            
            {{-- Add to Cart Button - Full Width --}}
            <button class="add-to-cart bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex-1 h-6 rounded-md hover:shadow-lg hover:scale-105 text-xs font-semibold transition-all duration-300 flex items-center justify-center gap-1 active:scale-95" 
                    data-menu-id="{{ $menu->id }}"
                    {{ $isOutOfStock ? 'disabled' : '' }}>
                <i class="fas fa-shopping-cart" style="font-size: 10px;"></i>
                <span>Pesan</span>
            </button>
        </div>
    </div>
</div>
