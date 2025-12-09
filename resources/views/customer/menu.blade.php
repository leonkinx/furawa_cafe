<?php
use Illuminate\Support\Facades\Storage;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Furawa Cafe</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    /* ============== MODERN DESIGN SYSTEM ============== */
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #8b5cf6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-600: #4b5563;
        --gray-800: #1f2937;
        --gray-900: #111827;
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .menu-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }
    .menu-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.1), 0 10px 10px -5px rgba(99, 102, 241, 0.04);
        border-color: rgba(99, 102, 241, 0.2);
    }
    
    /* Best Seller Animation - Modern */
    @keyframes pulse-glow {
        0%, 100% { 
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7);
        }
        50% { 
            transform: scale(1.05);
            box-shadow: 0 0 0 8px rgba(251, 191, 36, 0);
        }
    }
    
    .best-seller-badge {
        animation: pulse-glow 2s infinite;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
    }
    
    /* Stock Habis Overlay */
    .out-of-stock {
        position: relative;
    }
    
    .out-of-stock::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 0.75rem;
        pointer-events: none;
    }
    
    /* Improved Payment Modal Styles */
    .payment-modal-content {
        max-height: 85vh;
        overflow-y: auto;
    }
    
    .payment-section {
        scroll-margin-top: 1rem;
    }
    
    .payment-option {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }
    
    .payment-option:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
    }
    
    .payment-option.selected {
        border-color: #3b82f6;
        background-color: #f0f9ff;
    }
    
    .payment-details {
        transition: all 0.3s ease;
    }
    
    /* Custom scrollbar for better mobile experience */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Carousel Styles - Minimalist */
    .carousel-container {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        height: 160px;
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1);
    }
    
    .carousel-slide {
        display: none;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.5s ease;
    }
    
    .carousel-slide.active {
        display: block;
        opacity: 1;
    }
    
    .carousel-dots {
        position: absolute;
        bottom: 10px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 8px;
        z-index: 10;
    }
    
    .carousel-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    
    .carousel-dot.active {
        background-color: white;
        transform: scale(1.2);
    }
    
    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.95);
        color: var(--gray-800);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .carousel-nav:hover {
        background: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }
    
    .carousel-prev {
        left: 10px;
    }
    
    .carousel-next {
        right: 10px;
    }
    
    /* Horizontal Category Styles - Modern */
    .category-scroll {
        display: flex;
        overflow-x: auto;
        gap: 16px;
        padding-bottom: 16px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .category-scroll::-webkit-scrollbar {
        display: none;
    }
    
    .category-card {
        flex: 0 0 auto;
        width: 110px;
        height: 110px;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .category-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 24px rgba(99, 102, 241, 0.15);
    }
    
    .category-card.active {
        box-shadow: 0 0 0 3px var(--primary);
        transform: scale(1.05);
    }
    
    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .category-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.85), rgba(0,0,0,0.3) 70%, transparent);
        color: white;
        padding: 12px 8px;
        text-align: center;
    }
    
    .category-name {
        font-weight: 600;
        font-size: 13px;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    
    /* Mobile optimizations */
    @media (max-width: 768px) {
        .payment-modal-content {
            max-height: 90vh;
            margin: 0.5rem;
        }
        
        .payment-option {
            padding: 0.75rem;
        }
        
        .payment-details {
            padding: 0.75rem;
        }
        
        .carousel-container {
            height: 160px;
            border-radius: 16px;
        }
        
        .category-card {
            width: 90px;
            height: 90px;
        }
        
        .menu-image-container {
            height: 150px;
        }
    }

    /* Menu Image Container - Minimalist */
    .menu-image-container {
        position: relative;
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .menu-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .menu-item:hover .menu-image {
        transform: scale(1.1);
    }
    
    .image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f6f7f8 0%, #e5e5e5 100%);
    }
    
    .image-placeholder i {
        color: #9ca3af;
        margin-bottom: 8px;
    }
    
    .image-placeholder span {
        color: #6b7280;
        font-size: 12px;
    }
    
    /* Cart item disabled ketika stock habis */
    .menu-item.disabled {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .menu-item.disabled::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 0.75rem;
        pointer-events: none;
    }
    
    /* Loading overlay fix */
    .loading-overlay {
        z-index: 9999;
    }
    
    /* Modern Button Styles */
    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Smooth Transitions */
    * {
        -webkit-tap-highlight-color: transparent;
    }
    
    /* Modern Shadows */
    .shadow-modern {
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.1), 0 2px 4px -1px rgba(99, 102, 241, 0.06);
    }
    
    /* Gradient Text */
    .gradient-text {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Notification Animation */
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
        transition: all 0.3s ease;
    }
    
    /* Modal Backdrop - Transparent with subtle overlay */
    #cartModal::before,
    #orderModal::before,
    #paymentModal::before,
    #successModal::before,
    #detailsModal::before {
        content: '';
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(2px);
        z-index: -1;
    }
    
    /* Auto Refresh Indicator */
    .refresh-indicator {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    /* Table Selection Buttons */
    .table-btn {
        cursor: pointer;
        user-select: none;
    }
    
    .table-btn:active {
        transform: scale(0.95);
    }
    
    .table-btn.selected {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        border-color: #6366f1;
    }
    
    /* ============== FIXED CART MODAL LAYOUT ============== */
    /* Perbaikan utama untuk membuat cart modal bisa di-scroll */
    #cartModal {
        overflow-y: auto;
    }
    
    #cartModal .modal-container {
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        width: 100%;
        max-width: 500px;
    }
    
    #cartModal .modal-header {
        flex-shrink: 0;
    }
    
    #cartModal .modal-body {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }
    
    #cartModal .modal-footer {
        flex-shrink: 0;
        max-height: 60vh;
        overflow-y: auto;
    }
    
    /* Custom scrollbar untuk cart items */
    #cartModal .modal-body::-webkit-scrollbar,
    #cartModal .modal-footer::-webkit-scrollbar {
        width: 6px;
    }
    
    #cartModal .modal-body::-webkit-scrollbar-track,
    #cartModal .modal-footer::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #cartModal .modal-body::-webkit-scrollbar-thumb,
    #cartModal .modal-footer::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    #cartModal .modal-body::-webkit-scrollbar-thumb:hover,
    #cartModal .modal-footer::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Responsif untuk mobile */
    @media (max-width: 640px) {
        #cartModal .modal-container {
            max-height: 95vh;
            margin: 0.5rem;
        }
        
        #cartModal .modal-footer {
            max-height: 50vh;
        }
    }

    /* Quantity Display Styles */
    .quantity-display {
        min-width: 40px;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        color: #1f2937;
    }
    
    .quantity-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    
    /* Alert Animation */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .shake {
        animation: shake 0.5s ease-in-out;
    }
</style>
</head>
<body class="bg-gray-50 pb-20">
    <!-- Header - Minimalist Design -->
    <div class="bg-white shadow-sm sticky top-0 z-30 backdrop-blur-sm bg-opacity-95 border-b border-gray-100">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">FURAWA CAFE</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <div id="cartSummary" class="hidden">
                        <span id="cartCount" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold shadow-lg">0</span>
                    </div>
                    <button id="cartButton" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all duration-300">
                        <i class="fas fa-shopping-cart text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-4">

    <!-- Carousel Wrapper -->
    <div class="relative w-full h-[260px] rounded-xl overflow-hidden shadow-md mb-5">

        <!-- Slide 1 -->
        <img 
            src="https://lh3.googleusercontent.com/gps-cs-s/AG0ilSzvEsFiGvmFnsSilab0m_By7hVGsrNrsEMeVnOtj1I58X0C6mwFvoRs-W_gEZEzC-GC75GvVTQy1WozrZKxMDWaIp42NFpGR2cr__O-nJ8Nl12cSLNU30JI_TCkpU0GYd9Xu5UG4huSpizR=w243-h304-n-k-no-nu"
            alt="Cafe Interior"
            class="carousel-slide absolute inset-0 w-full h-full object-cover opacity-100 transition-all duration-700"
        >

        <!-- Slide 2 -->
        <img 
            src="https://lh3.googleusercontent.com/gps-cs-s/AG0ilSzNqp7Qe3BmY-X6LWIK6rZ0ncNcSDxoF7O5MTJUJYhft2Gb1GvctT_lCn3ojX9jNiMrYR_sz6_lXx2GnVYBSGxvv6Tnqm-oK4Od9Bi0cx7OZCG0cGhtCdY1hP19SWa__VVWIQ8T=w243-h244-n-k-no-nu"
            alt="Delicious Food"
            class="carousel-slide absolute inset-0 w-full h-full object-cover opacity-0 transition-all duration-700"
        >

        <!-- Slide 3 -->
        <img 
            src="https://lh3.googleusercontent.com/gps-cs-s/AG0ilSxNqf8by0sZUYUBqXeYX4JrNLU24sf4U6WEkr2NXtugb4-OWyZzyE8cttnX3AsAYGiw2eTmAAKT3l_2pPiUh0-NlVUUy3ZyOV-rf771O8uFZkwGh85Q_RYRdiI3UoJH12f24DOf=w243-h244-n-k-no-nu"
            alt="Coffee Drinks"
            class="carousel-slide absolute inset-0 w-full h-full object-cover opacity-0 transition-all duration-700"
        >
                
                <!-- Navigation Buttons -->
                <button class="carousel-nav carousel-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-nav carousel-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
                
                <!-- Dots Indicator -->
                <div class="carousel-dots">
                    <span class="carousel-dot active" data-slide="0"></span>
                    <span class="carousel-dot" data-slide="1"></span>
                    <span class="carousel-dot" data-slide="2"></span>
                </div>
            </div>
        </div>

        <!-- Search Bar - Minimalist Design -->
        <div class="mb-5">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Cari menu favorit Anda..." 
                       class="w-full px-4 py-3 pl-11 pr-11 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-300 shadow-sm">
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-search text-indigo-400 text-sm"></i>
                </div>
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <button id="clearSearch" class="text-gray-400 hover:text-indigo-600 transition-colors hidden">
                        <i class="fas fa-times-circle text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Horizontal Categories - Minimalist Design -->
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                <span class="w-1 h-5 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full mr-2"></span>
                Kategori Menu
            </h2>
            <div class="category-scroll">
                <!-- All Categories -->
                <div class="category-card active" data-category="all">
                    <img src="https://lh3.googleusercontent.com/gps-cs-s/AG0ilSxgX8NRLdX3SHCxlpy3NJ9Eft7ao2_yCSpxIjgY4fL3s4eCYtpp2YTMSutZUPjgCqxSMHTHEElkyemR6Hgld2250ZlOXwvCR50qGRy3CjFn3-muuer7WDPv1YbZuDzCgdvt5PpL=w243-h304-n-k-no-nu" 
                         alt="All Menu" class="category-image">
                    <div class="category-overlay">
                        <p class="category-name">Semua Menu</p>
                    </div>
                </div>
                
                <!-- Food Category -->
                <div class="category-card" data-category="makanan">
                    <img src="https://lh3.googleusercontent.com/gps-cs-s/AG0ilSw7ZprvjndxSCcT7CVuwBSuhDUIP7OOtsUBwTQsaZTFYZgXVUaNkQAiIz8bmHRLBqS8pNmz8VZfjABG5dqzNP4nuy9EIe2ND2FIVP5BrvM8idvCp7rX3-ioAadLzW7kBP5ht2kE=w243-h174-n-k-no-nu" 
                         alt="Food" class="category-image">
                    <div class="category-overlay">
                        <p class="category-name">Food</p>
                    </div>
                </div>
                
                <!-- Drink Category -->
                <div class="category-card" data-category="minuman">
                    <img src="https://lh3.googleusercontent.com/p/AF1QipOVGrWvjno1PrPRibkZdhNTWmaGZORwWvZaTloY=w243-h406-n-k-no-nu" 
                         alt="Drinks" class="category-image">
                    <div class="category-overlay">
                        <p class="category-name">Drinks</p>
                    </div>
                </div>
                
                <!-- Snack Category -->
                <div class="category-card" data-category="snack">
                    <img src="https://lh3.googleusercontent.com/gps-cs-s/AG0ilSyY__nBhykTz2vV7aN6R-zSlkQu3xI6XTKPX6hAs2gR88utANAbqaf3rkFxvP17GHMRo8oHkL2W6uZpMlzX5qV4x_74XgIrZaDIsi4M5LroSqt9ygmHlrgjEXjVqCibp6RiPzBobYS7Pzdz=w243-h203-n-k-no-nu" 
                         alt="Snacks" class="category-image">
                    <div class="category-overlay">
                        <p class="category-name">Dessert</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <div id="menuContainer">
            <!-- Makanan Section - Minimalist Design -->
            @if(isset($categories['makanan']) && count($categories['makanan']) > 0)
            <div class="mb-8 makanan-section">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                        🍽️
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 ml-3">Makanan</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($categories['makanan'] as $menu)
                    @php
                        // SIMPLIFIKASI LOGIKA GAMBAR
                        $imageUrl = $menu->image ? asset('storage/' . $menu->image) : null;
                        $fallbackImage = 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60';
                        $isOutOfStock = $menu->stock !== null && $menu->stock == 0;
                    @endphp
                    
                    <div class="menu-item bg-white rounded-xl shadow-md hover:shadow-xl p-4 relative {{ $isOutOfStock ? 'disabled' : '' }}" 
                         data-category="makanan" 
                         data-name="{{ strtolower($menu->name) }}"
                         data-menu-id="{{ $menu->id }}">
                        
                        <!-- Best Seller Badge - Minimalist -->
                        @if($menu->is_best_seller)
                        <div class="absolute top-2 right-2 z-10">
                            <div class="best-seller-badge text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                                <i class="fas fa-crown mr-1 text-xs"></i>
                                Best
                            </div>
                        </div>
                        @endif
                        
                        <div class="flex flex-col">
                            <!-- Menu Image -->
                            <div class="mb-3">
                                <div class="menu-image-container">
                                    @if($imageUrl)
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $menu->name }}" 
                                         class="menu-image"
                                         onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
                                    @else
                                    <div class="image-placeholder">
                                        <i class="fas fa-utensils text-3xl"></i>
                                        <span>No Image</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Stock Badge -->
                                    @if($isOutOfStock)
                                    <div class="absolute top-2 left-2 z-10">
                                        <div class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            Habis
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Menu Info - Modern -->
                            <div class="flex justify-between items-start mb-4 flex-1">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $menu->name }}</h3>
                                    <p class="text-sm text-gray-500 leading-relaxed">{{ $menu->description }}</p>
                                    @if($menu->details)
                                    <div class="mt-2">
                                        <button class="text-indigo-600 text-xs font-medium hover:text-indigo-700 transition-colors view-details inline-flex items-center" 
                                                data-details="{{ $menu->details }}">
                                            <i class="fas fa-info-circle mr-1"></i>Detail lengkap
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                <div class="text-right ml-4">
                                    <p class="font-bold text-indigo-600 text-xl">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                    @if($menu->stock !== null)
                                    <p class="text-xs {{ $menu->stock == 0 ? 'text-red-500 font-semibold' : 'text-gray-400' }} mt-1">
                                        {{ $menu->stock == 0 ? 'Habis' : 'Stok: ' . $menu->stock }}
                                    </p>
                                    @else
                                    <p class="text-xs text-green-500 font-medium mt-1">✓ Tersedia</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Quantity Controls - Modern -->
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center space-x-3 bg-gray-50 rounded-xl px-3 py-2">
                                    <button class="quantity-btn decrease bg-white w-9 h-9 rounded-lg flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition-all shadow-sm" 
                                            data-menu-id="{{ $menu->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="quantity-display w-10 text-center font-bold text-gray-800" data-menu-id="{{ $menu->id }}">0</span>
                                    <button class="quantity-btn increase bg-white w-9 h-9 rounded-lg flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition-all shadow-sm" 
                                            data-menu-id="{{ $menu->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <button class="add-to-cart bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg text-sm font-medium transition-all duration-300 hover:scale-105" 
                                        data-menu-id="{{ $menu->id }}"
                                        {{ $isOutOfStock ? 'disabled' : '' }}>
                                    <i class="fas fa-cart-plus mr-1"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Minuman Section - Minimalist Design -->
            @if(isset($categories['minuman']) && count($categories['minuman']) > 0)
            <div class="mb-8 minuman-section">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                        🥤
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 ml-3">Minuman</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($categories['minuman'] as $menu)
                    @php
                        $imageUrl = $menu->image ? asset('storage/' . $menu->image) : null;
                        $fallbackImage = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60';
                        $isOutOfStock = $menu->stock !== null && $menu->stock == 0;
                    @endphp
                    
                    <div class="menu-item bg-white rounded-xl shadow-md hover:shadow-xl p-4 relative {{ $isOutOfStock ? 'disabled' : '' }}" 
                         data-category="minuman" 
                         data-name="{{ strtolower($menu->name) }}"
                         data-menu-id="{{ $menu->id }}">
                        
                        <!-- Best Seller Badge - Minimalist -->
                        @if($menu->is_best_seller)
                        <div class="absolute top-2 right-2 z-10">
                            <div class="best-seller-badge text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                                <i class="fas fa-crown mr-1 text-xs"></i>
                                Best
                            </div>
                        </div>
                        @endif
                        
                        <div class="flex flex-col">
                            <!-- Menu Image -->
                            <div class="mb-3">
                                <div class="menu-image-container">
                                    @if($imageUrl)
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $menu->name }}" 
                                         class="menu-image"
                                         onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
                                    @else
                                    <div class="image-placeholder">
                                        <i class="fas fa-glass-whiskey text-3xl"></i>
                                        <span>No Image</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Stock Badge -->
                                    @if($isOutOfStock)
                                    <div class="absolute top-2 left-2 z-10">
                                        <div class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            Habis
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Menu Info -->
                            <div class="flex justify-between items-start mb-3 flex-1">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $menu->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $menu->description }}</p>
                                    @if($menu->details)
                                    <div class="mt-2">
                                        <button class="text-blue-600 text-xs hover:text-blue-800 transition-colors view-details" 
                                                data-details="{{ $menu->details }}">
                                            <i class="fas fa-info-circle mr-1"></i>Lihat detail
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                <div class="text-right ml-4">
                                    <p class="font-bold text-indigo-600 text-xl">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                    @if($menu->stock !== null)
                                    <p class="text-xs {{ $menu->stock == 0 ? 'text-red-500' : 'text-gray-500' }} mt-1">
                                        Stok: {{ $menu->stock }}
                                    </p>
                                    @else
                                    <p class="text-xs text-green-500 mt-1">Tersedia</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center space-x-2">
                                    <button class="quantity-btn decrease bg-gray-200 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors" 
                                            data-menu-id="{{ $menu->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="fas fa-minus text-gray-600 text-xs"></i>
                                    </button>
                                    <span class="quantity-display w-8 text-center font-medium" data-menu-id="{{ $menu->id }}">0</span>
                                    <button class="quantity-btn increase bg-gray-200 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors" 
                                            data-menu-id="{{ $menu->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="fas fa-plus text-gray-600 text-xs"></i>
                                    </button>
                                </div>
                                <button class="add-to-cart bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg text-sm font-medium transition-all duration-300 hover:scale-105" 
                                        data-menu-id="{{ $menu->id }}"
                                        {{ $isOutOfStock ? 'disabled' : '' }}>
                                    <i class="fas fa-cart-plus mr-1"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Snack Section - Minimalist Design -->
            @if(isset($categories['snack']) && count($categories['snack']) > 0)
            <div class="mb-8 snack-section">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                        🍰
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 ml-3">Dessert</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($categories['snack'] as $menu)
                    @php
                        $imageUrl = $menu->image ? asset('storage/' . $menu->image) : null;
                        $fallbackImage = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60';
                        $isOutOfStock = $menu->stock !== null && $menu->stock == 0;
                    @endphp
                    
                    <div class="menu-item bg-white rounded-xl shadow-md hover:shadow-xl p-4 relative {{ $isOutOfStock ? 'disabled' : '' }}" 
                         data-category="snack" 
                         data-name="{{ strtolower($menu->name) }}"
                         data-menu-id="{{ $menu->id }}">
                        
                        <!-- Best Seller Badge - Minimalist -->
                        @if($menu->is_best_seller)
                        <div class="absolute top-2 right-2 z-10">
                            <div class="best-seller-badge text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center">
                                <i class="fas fa-crown mr-1 text-xs"></i>
                                Best
                            </div>
                        </div>
                        @endif
                        
                        <div class="flex flex-col">
                            <!-- Menu Image -->
                            <div class="mb-3">
                                <div class="menu-image-container">
                                    @if($imageUrl)
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $menu->name }}" 
                                         class="menu-image"
                                         onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
                                    @else
                                    <div class="image-placeholder">
                                        <i class="fas fa-cookie text-3xl"></i>
                                        <span>No Image</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Stock Badge -->
                                    @if($isOutOfStock)
                                    <div class="absolute top-2 left-2 z-10">
                                        <div class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            Habis
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Menu Info -->
                            <div class="flex justify-between items-start mb-3 flex-1">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $menu->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $menu->description }}</p>
                                    @if($menu->details)
                                    <div class="mt-2">
                                        <button class="text-blue-600 text-xs hover:text-blue-800 transition-colors view-details" 
                                                data-details="{{ $menu->details }}">
                                            <i class="fas fa-info-circle mr-1"></i>Lihat detail
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                <div class="text-right ml-4">
                                    <p class="font-bold text-indigo-600 text-xl">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                    @if($menu->stock !== null)
                                    <p class="text-xs {{ $menu->stock == 0 ? 'text-red-500' : 'text-gray-500' }} mt-1">
                                        Stok: {{ $menu->stock }}
                                    </p>
                                    @else
                                    <p class="text-xs text-green-500 mt-1">Tersedia</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center space-x-2">
                                    <button class="quantity-btn decrease bg-gray-200 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors" 
                                            data-menu-id="{{ $menu->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="fas fa-minus text-gray-600 text-xs"></i>
                                    </button>
                                    <span class="quantity-display w-8 text-center font-medium" data-menu-id="{{ $menu->id }}">0</span>
                                    <button class="quantity-btn increase bg-gray-200 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors" 
                                            data-menu-id="{{ $menu->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                        <i class="fas fa-plus text-gray-600 text-xs"></i>
                                    </button>
                                </div>
                                <button class="add-to-cart bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg text-sm font-medium transition-all duration-300 hover:scale-105" 
                                        data-menu-id="{{ $menu->id }}"
                                        {{ $isOutOfStock ? 'disabled' : '' }}>
                                    <i class="fas fa-cart-plus mr-1"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Empty State - Modern Design -->
        @if(empty($categories['makanan']) && empty($categories['minuman']) && empty($categories['snack']))
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-utensils text-5xl text-gray-400"></i>
            </div>
            <p class="text-gray-600 text-xl font-semibold mb-2">Menu Belum Tersedia</p>
            <p class="text-gray-400 text-sm">Silakan coba lagi nanti atau hubungi staff kami</p>
        </div>
        @endif
    </div>

    <!-- Cart Modal - Fixed Scroll -->
    <div id="cartModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4 pt-20 pb-4">
            <div class="modal-container bg-white rounded-3xl overflow-hidden shadow-2xl border border-gray-200">
                <div class="modal-header p-4 bg-gradient-to-r from-indigo-600 to-purple-600">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-white">Keranjang Pesanan</h3>
                        </div>
                        <button id="closeCart" class="text-white hover:bg-white hover:bg-opacity-20 w-8 h-8 rounded-full flex items-center justify-center transition-all">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
                
                <div class="modal-body p-4 bg-gray-50">
                    <div id="cartItems" class="space-y-3">
                        <!-- Cart items will be inserted here -->
                    </div>
                    <div id="emptyCart" class="text-center py-12 text-gray-500">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-4xl text-gray-300"></i>
                        </div>
                        <p class="font-semibold text-gray-700">Keranjang Kosong</p>
                        <p class="text-sm text-gray-400 mt-2">Tambahkan menu favorit Anda</p>
                    </div>
                </div>
                
                <div class="modal-footer p-4 bg-gray-50 border-t">
                    <div class="flex justify-between items-center mb-4 bg-white p-3 rounded-xl shadow-sm">
                        <span class="font-semibold text-gray-700 text-sm">Total Pembayaran</span>
                        <span id="cartTotal" class="font-bold text-xl bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Rp 0</span>
                    </div>
                    
                    <form id="orderForm">
                        @csrf
                        
                        <!-- Table Selection - Button Grid -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Meja *</label>
                            <input type="hidden" name="table_id" id="tableIdInput" required>
                            <div class="grid grid-cols-5 gap-2">
                                @for($i = 1; $i <= 10; $i++)
                                <button type="button" 
                                        class="table-btn px-3 py-3 border-2 border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-600 transition-all"
                                        data-table="{{ $i }}"
                                        onclick="selectTable({{ $i }})">
                                    {{ $i }}
                                </button>
                                @endfor
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Klik nomor meja Anda
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Pemesan *</label>
                            <input type="text" name="customer_name" required 
                                   class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all text-sm"
                                   placeholder="Masukkan nama Anda">
                        </div>
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran *</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 bg-white hover:bg-indigo-50 border-2 border-gray-200 hover:border-indigo-500 rounded-lg cursor-pointer transition-all">
                                    <input type="radio" name="payment_method" value="cash"
                                           class="text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <div class="ml-2.5">
                                        <span class="font-medium text-gray-800 text-sm">Bayar Di Kasir</span>
                                    
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 rounded-lg hover:shadow-lg font-semibold transition-all duration-300 text-sm">
                            <i class="fas fa-credit-card mr-2"></i>Lanjut ke Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal - Minimalist -->
    <div id="paymentModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4 py-8">
            <div class="bg-white rounded-2xl w-full max-w-sm max-h-[85vh] flex flex-col payment-modal-content shadow-2xl border border-gray-200">
                <!-- Header - Fixed -->
                <div class="flex-shrink-0 bg-gradient-to-r from-indigo-600 to-purple-600 p-4 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-bold text-white">Pembayaran</h3>
                        <button id="closePayment" class="text-white hover:bg-white hover:bg-opacity-20 w-8 h-8 rounded-full flex items-center justify-center transition-all">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Content - Scrollable -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <div id="paymentContent" class="space-y-3">
                        <!-- Payment content will be inserted here -->
                    </div>
                </div>
                
                <!-- Buttons - Fixed at bottom -->
                <div class="flex-shrink-0 p-4 bg-gray-50 border-t border-gray-200 rounded-b-2xl">
                    <div class="flex flex-col gap-2">
                        <button id="confirmPayment" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-4 rounded-lg hover:shadow-lg transition-all font-semibold text-sm">
                            <i class="fas fa-check mr-2"></i>Konfirmasi Pembayaran
                        </button>
                        <button id="cancelPayment" class="w-full bg-gray-500 text-white py-3 px-4 rounded-lg hover:bg-gray-600 transition-colors font-medium text-sm">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Success Modal - Minimalist Design -->
    <div id="successModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4 pt-20 pb-4">
            <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl border border-gray-200">
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pesanan Berhasil!</h3>
                <p id="successMessage" class="text-gray-600 text-sm mb-5 leading-relaxed"></p>
                <button id="closeSuccess" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg hover:shadow-lg transition-all duration-300 font-semibold text-sm">
                    Kembali ke Menu
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Details Modal - Minimalist Design -->
    <div id="detailsModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4 pt-20 pb-4">
            <div class="bg-white rounded-2xl w-full max-w-sm max-h-[65vh] overflow-hidden shadow-2xl border border-gray-200">
                <div class="p-4 bg-gradient-to-r from-indigo-600 to-purple-600">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-bold text-white">Detail Menu</h3>
                        <button id="closeDetails" class="text-white hover:bg-white hover:bg-opacity-20 w-8 h-8 rounded-full flex items-center justify-center transition-all">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="p-4 overflow-y-auto bg-gray-50">
                    <p id="detailsContent" class="text-gray-700 text-sm whitespace-pre-line leading-relaxed"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Modal - Minimalist with Auto Refresh -->
    <div id="orderModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4 py-8 pb-24">
            <div class="bg-white rounded-2xl w-full max-w-lg max-h-[80vh] overflow-hidden shadow-2xl border border-gray-200 flex flex-col">
                <!-- Header - Fixed -->
                <div class="flex-shrink-0 p-4 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-bold text-white">Pesanan Saya</h3>
                            <p class="text-xs text-indigo-100 mt-0.5 flex items-center">
                                <i class="fas fa-sync-alt text-xs mr-1 refresh-indicator"></i>
                                Auto-refresh • Hanya pesanan dari HP ini
                            </p>
                        </div>
                        <button id="closeOrder" class="text-white hover:bg-white hover:bg-opacity-20 w-8 h-8 rounded-full flex items-center justify-center transition-all">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Content - Scrollable -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <div id="orderList" class="space-y-3 pb-4">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-receipt text-2xl mb-2"></i>
                            <p class="text-sm">Memuat pesanan...</p>
                            <p class="text-xs text-gray-400 mt-1">Silakan tunggu sebentar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay - Minimalist Design -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-30 backdrop-blur-sm hidden z-50 loading-overlay">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl p-6 text-center shadow-xl">
                <div class="relative w-14 h-14 mx-auto mb-4">
                    <div class="absolute inset-0 border-4 border-indigo-200 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
                </div>
                <p class="text-gray-700 font-semibold">Memproses pesanan...</p>
                <p class="text-gray-500 text-sm mt-1">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>

    <!-- Footer Navigation - Minimalist Design -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 z-40 shadow-lg backdrop-blur-lg bg-opacity-95">
        <div class="container mx-auto">
            <div class="flex justify-around items-center py-2">
                <a href="/menu" class="flex flex-col items-center text-indigo-600 transition-all duration-300 py-1">
                    <i class="fas fa-utensils text-lg mb-1"></i>
                    <span class="text-xs font-medium">Menu</span>
                </a>
                
                <button id="footerCartButton" class="flex flex-col items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 py-1 relative">
                    <i class="fas fa-shopping-cart text-lg mb-1"></i>
                    <span id="footerCartCount" class="absolute top-0 right-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold hidden shadow-lg">0</span>
                    <span class="text-xs font-medium">Keranjang</span>
                </button>
                
                <button id="footerOrderButton" class="flex flex-col items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 py-1">
                    <i class="fas fa-receipt text-lg mb-1"></i>
                    <span class="text-xs font-medium">Pesanan</span>
                </button>
                
                <a href="/" class="flex flex-col items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 py-1">
                    <i class="fas fa-home text-lg mb-1"></i>
                    <span class="text-xs font-medium">Home</span>
                </a>
            </div>
        </div>
    </div>

    <script>
    // ==================== VARIABLES ====================
    let cart = {};
    let currentOrderData = null;
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const totalSlides = slides.length;
    let carouselInterval = null;
    
    // ==================== LOCAL STORAGE FUNCTIONS ====================
    // Simpan order code ke localStorage
    function saveMyOrderCode(orderCode) {
        let myOrders = JSON.parse(localStorage.getItem('myOrders') || '[]');
        if (!myOrders.includes(orderCode)) {
            myOrders.push(orderCode);
            localStorage.setItem('myOrders', JSON.stringify(myOrders));
        }
    }
    
    // Ambil semua order codes milik device ini
    function getMyOrderCodes() {
        return JSON.parse(localStorage.getItem('myOrders') || '[]');
    }
    
    // Cek apakah order code adalah milik device ini
    function isMyOrder(orderCode) {
        const myOrders = getMyOrderCodes();
        return myOrders.includes(orderCode);
    }

    // ==================== HELPER FUNCTIONS ====================
    
    // Function untuk select table
    function selectTable(tableNumber) {
        // Remove active class from all buttons
        document.querySelectorAll('.table-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
            btn.classList.add('border-gray-200', 'text-gray-700');
        });
        
        // Add active class to selected button
        const selectedBtn = document.querySelector(`.table-btn[data-table="${tableNumber}"]`);
        if (selectedBtn) {
            selectedBtn.classList.remove('border-gray-200', 'text-gray-700');
            selectedBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        }
        
        // Set hidden input value
        document.getElementById('tableIdInput').value = tableNumber;
    }

    // ==================== CAROUSEL FUNCTIONALITY ====================
    function showSlide(n) {
        // Remove active class from all slides and dots
        slides.forEach(slide => {
            slide.classList.remove('active');
            slide.style.opacity = '0';
        });
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Calculate the actual slide index
        currentSlide = (n + totalSlides) % totalSlides;
        
        // Add active class to current slide and dot
        setTimeout(() => {
            slides[currentSlide].classList.add('active');
            slides[currentSlide].style.opacity = '1';
        }, 50);
        dots[currentSlide].classList.add('active');
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    // Initialize carousel
    function initCarousel() {
        if (slides.length > 0) {
            showSlide(0);
            carouselInterval = setInterval(nextSlide, 5000);
        }
    }

    // ==================== CART FUNCTIONS ====================
    function updateQuantityDisplay(menuId) {
        const display = document.querySelector(`.quantity-display[data-menu-id="${menuId}"]`);
        if (display) {
            // Update display only, not cart
            const currentValue = parseInt(display.textContent) || 0;
            display.textContent = currentValue;
        }
    }

    function updateAllQuantityDisplays() {
        document.querySelectorAll('.quantity-display').forEach(display => {
            const menuId = display.dataset.menuId;
            // Reset all displays to 0
            display.textContent = '0';
        });
    }

    function updateCartSummary() {
        const totalItems = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
        const cartCount = document.getElementById('cartCount');
        const footerCartCount = document.getElementById('footerCartCount');
        const cartSummary = document.getElementById('cartSummary');
        
        if (totalItems > 0) {
            cartCount.textContent = totalItems;
            footerCartCount.textContent = totalItems;
            cartSummary.classList.remove('hidden');
            footerCartCount.classList.remove('hidden');
        } else {
            cartSummary.classList.add('hidden');
            footerCartCount.classList.add('hidden');
        }
    }

    function calculateTotal() {
        let total = 0;
        
        for (const menuId in cart) {
            if (cart[menuId] > 0) {
                const menuItem = document.querySelector(`.menu-item[data-menu-id="${menuId}"]`);
                if (menuItem) {
                    const priceElement = menuItem.querySelector('.font-bold.text-indigo-600.text-xl');
                    if (priceElement) {
                        const priceText = priceElement.textContent;
                        const price = parseInt(priceText.replace('Rp ', '').replace(/\./g, ''));
                        const quantity = cart[menuId];
                        total += price * quantity;
                    }
                }
            }
        }
        
        return total;
    }

    function showCart() {
        const modal = document.getElementById('cartModal');
        const cartItems = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        const cartTotal = document.getElementById('cartTotal');
        
        cartItems.innerHTML = '';
        
        let hasItems = false;
        let total = 0;
        
        for (const menuId in cart) {
            const quantity = cart[menuId];
            if (quantity > 0) {
                hasItems = true;
                const menuItem = document.querySelector(`.menu-item[data-menu-id="${menuId}"]`);
                
                if (menuItem) {
                    const name = menuItem.querySelector('h3').textContent;
                    const priceElement = menuItem.querySelector('.font-bold.text-indigo-600.text-xl');
                    
                    if (priceElement) {
                        const priceText = priceElement.textContent;
                        const price = parseInt(priceText.replace('Rp ', '').replace(/\./g, ''));
                        const subtotal = price * quantity;
                        total += subtotal;
                        
                        const cartItem = document.createElement('div');
                        cartItem.className = 'flex justify-between items-center p-3 bg-gray-50 rounded-lg';
                        cartItem.innerHTML = `
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">${name}</h4>
                                <p class="text-sm text-gray-600">${priceText} × ${quantity}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">Rp ${subtotal.toLocaleString('id-ID')}</p>
                                <button class="remove-item text-red-500 text-sm mt-1 hover:text-red-700 transition-colors" data-menu-id="${menuId}">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        `;
                        cartItems.appendChild(cartItem);
                    }
                }
            }
        }
        
        if (hasItems) {
            emptyCart.classList.add('hidden');
            cartTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            
            // Add event listeners for remove buttons
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const menuId = this.dataset.menuId;
                    delete cart[menuId];
                    updateCartSummary();
                    showCart();
                });
            });
        } else {
            emptyCart.classList.remove('hidden');
            cartTotal.textContent = 'Rp 0';
        }
        
        // Reset all quantity displays to 0 when cart is opened
        updateAllQuantityDisplays();
        
        modal.classList.remove('hidden');
    }

    function hideCart() {
        document.getElementById('cartModal').classList.add('hidden');
    }

    // ==================== PAYMENT FUNCTIONS ====================
    function showPaymentModal(orderData) {
        const modal = document.getElementById('paymentModal');
        const paymentContent = document.getElementById('paymentContent');
        
        let paymentHtml = '';
        
        // Progress Steps
        paymentHtml += `
            <div class="mb-6">
                <div class="flex items-center justify-center text-sm">
                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">
                            1
                        </div>
                        <div class="ml-2 text-blue-600 font-medium">Keranjang</div>
                    </div>
                    <div class="w-8 h-1 bg-blue-600 mx-1"></div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">
                            2
                        </div>
                        <div class="ml-2 text-blue-600 font-medium">Pembayaran</div>
                    </div>
                </div>
            </div>
        `;
        
        // Order Summary
        paymentHtml += `
            <div class="payment-section bg-gray-50 p-4 rounded-lg mb-4">
                <h4 class="font-semibold text-gray-800 mb-3">Ringkasan Pesanan</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Meja:</span>
                        <span class="font-medium">Meja ${orderData.table_id}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-medium">${orderData.customer_name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-blue-600">Rp ${orderData.total_amount.toLocaleString('id-ID')}</span>
                    </div>
                </div>
            </div>
        `;
        
        // Payment Method Details
        if (orderData.payment_method === 'qris') {
            paymentHtml += `
                <div class="payment-section">
                    <div class="payment-option selected p-4 rounded-lg mb-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-qrcode text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">QRIS</h4>
                                <p class="text-sm text-gray-600">Scan kode QR untuk pembayaran</p>
                            </div>
                        </div>
                        
                        <div class="payment-details">
                            <div class="text-center bg-white p-4 rounded-lg border">
                                <div class="w-48 h-48 bg-gray-100 mx-auto mb-4 flex items-center justify-center rounded-lg">
                                    <div class="text-center">
                                        <i class="fas fa-qrcode text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-xs text-gray-500">QR Code Pembayaran</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Scan QR code di atas dengan aplikasi e-wallet atau mobile banking Anda</p>
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <p class="text-sm text-yellow-700 flex items-start">
                                        <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                                        Pastikan jumlah transfer sesuai dengan total pesanan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else if (orderData.payment_method === 'bank_transfer') {
            paymentHtml += `
                <div class="payment-section">
                    <div class="payment-option selected p-4 rounded-lg mb-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-university text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Transfer Bank</h4>
                                <p class="text-sm text-gray-600">Transfer manual ke rekening bank</p>
                            </div>
                        </div>
                        
                        <div class="payment-details">
                            <div class="space-y-3 bg-white p-4 rounded-lg border">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bank Tujuan:</span>
                                    <span class="font-medium">BCA</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nomor Rekening:</span>
                                    <span class="font-medium">1234 5678 9012</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Atas Nama:</span>
                                    <span class="font-medium">Furawa Cafe</span>
                                </div>
                                <div class="bg-yellow-50 p-3 rounded-lg mt-3">
                                    <p class="text-sm text-yellow-700 flex items-start">
                                        <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                                        Harap transfer tepat sampai 3 digit terakhir. Total: <span class="font-bold">Rp ${orderData.total_amount.toLocaleString('id-ID')}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else if (orderData.payment_method === 'cash') {
            paymentHtml += `
                <div class="payment-section">
                    <div class="payment-option selected p-4 rounded-lg mb-4">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-money-bill-wave text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Tunai</h4>
                                <p class="text-sm text-gray-600">Bayar langsung di kasir</p>
                            </div>
                        </div>
                        
                        <div class="payment-details">
                            <div class="text-center bg-white p-4 rounded-lg border">
                                <i class="fas fa-cash-register text-4xl text-green-500 mb-3"></i>
                                <h5 class="font-semibold text-green-800 mb-2">Bayar di Kasir</h5>
                                <p class="text-gray-700 text-sm mb-3">Silakan tunjukkan pesanan ini ke kasir untuk melakukan pembayaran</p>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <p class="text-sm text-green-700">Pesanan Anda akan diproses setelah pembayaran di kasir</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Additional Information
        paymentHtml += `
            <div class="payment-section bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-800 mb-2">Informasi Penting</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li class="flex items-start">
                        <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                        Pesanan akan diproses setelah pembayaran dikonfirmasi
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clock mr-2 mt-0.5"></i>
                        Waktu proses pesanan: 15-20 menit
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone mr-2 mt-0.5"></i>
                        Hubungi pelayan untuk bantuan
                    </li>
                </ul>
            </div>
        `;
        
        paymentContent.innerHTML = paymentHtml;
        modal.classList.remove('hidden');
        hideCart();
        
        // Auto scroll to top
        setTimeout(() => {
            const paymentModal = document.querySelector('.payment-modal-content');
            if (paymentModal) {
                paymentModal.scrollTop = 0;
            }
        }, 100);
    }

    function hidePayment() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    // ==================== ORDER PROCESSING ====================
    function processOrder(orderData) {
        console.log('Processing order:', orderData);
        
        // Format data untuk server
        const formattedData = {
            customer_name: orderData.customer_name,
            table_id: parseInt(orderData.table_id),
            payment_method: orderData.payment_method,
            total_amount: parseFloat(orderData.total_amount),
            items: []
        };
        
        // Konversi cart ke array items
        for (const menuId in orderData.items) {
            if (orderData.items[menuId] > 0) {
                formattedData.items.push({
                    menu_id: parseInt(menuId),
                    quantity: parseInt(orderData.items[menuId])
                });
            }
        }
        
        console.log('Sending to server:', formattedData);
        
        // Kirim ke server
        fetch('/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formattedData)
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Server error ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Server response:', data);
            hideLoading();
            hidePayment();
            
            if (data.success) {
                // Simpan order code ke localStorage untuk tracking
                saveMyOrderCode(data.order.order_code);
                
                showSuccessModal(data.order);
                // Reset cart
                cart = {};
                updateCartSummary();
                updateAllQuantityDisplays();
            } else {
                alert('Gagal membuat pesanan: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        });
    }

    function showSuccessModal(order) {
        const modal = document.getElementById('successModal');
        const message = document.getElementById('successMessage');
        
        let messageText = '';
        if (currentOrderData.payment_method === 'cash') {
            messageText = `Pesanan Anda telah diterima. Silakan tunjukkan ke kasir untuk pembayaran.\nKode Pesanan: ${order.order_code}`;
        } else {
            messageText = `Pembayaran berhasil! Kode Pesanan: ${order.order_code}.\nPesanan Anda sedang diproses.`;
        }
        
        message.textContent = messageText;
        modal.classList.remove('hidden');
    }

    // ==================== ORDER STATUS FUNCTIONS ====================
    let orderStatusInterval = null;
    let lastOrderStatuses = {};
    
    function showOrderStatus() {
        const modal = document.getElementById('orderModal');
        
        // Tampilkan modal dulu
        modal.classList.remove('hidden');
        
        // Load orders pertama kali tanpa loading overlay
        loadOrders(false);
        
        // Auto refresh setiap 5 detik
        if (orderStatusInterval) {
            clearInterval(orderStatusInterval);
        }
        orderStatusInterval = setInterval(() => {
            loadOrders(false); // false = tidak show loading
        }, 5000);
    }
    
    function loadOrders(showLoadingOverlay = true) {
        if (showLoadingOverlay) {
            showLoading();
        }
        
        fetch('/api/orders/my-orders')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(allOrders => {
                if (showLoadingOverlay) {
                    hideLoading();
                }
                
                // Filter hanya pesanan milik device ini
                const myOrderCodes = getMyOrderCodes();
                const orders = allOrders.filter(order => myOrderCodes.includes(order.order_code));
                
                // Cek perubahan status dan tampilkan notifikasi
                orders.forEach(order => {
                    const oldStatus = lastOrderStatuses[order.order_code];
                    if (oldStatus && oldStatus !== order.status) {
                        showStatusChangeNotification(order);
                    }
                    lastOrderStatuses[order.order_code] = order.status;
                });
                
                const orderList = document.getElementById('orderList');
                
                if (!orders || orders.length === 0) {
                    orderList.innerHTML = `
                        <div class="text-center py-12 text-gray-500">
                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-receipt text-3xl text-indigo-300"></i>
                            </div>
                            <p class="font-bold text-gray-700 text-lg">Belum Ada Pesanan</p>
                            <p class="text-sm text-gray-500 mt-2">Pesanan yang Anda buat dari HP ini<br>akan muncul di sini</p>
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg text-left max-w-xs mx-auto">
                                <p class="text-xs text-blue-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Info:</strong> Setiap HP hanya bisa melihat pesanan yang dibuat dari HP tersebut.
                                </p>
                            </div>
                        </div>
                    `;
                } else {
                    orderList.innerHTML = orders.map(order => {
                        // Status text helper
                        const getStatusText = (status) => {
                            const texts = {
                                'pending': 'Menunggu',
                                'paid': 'Dibayar',
                                'processing': 'Diproses',
                                'completed': 'Selesai',
                                'cancelled': 'Dibatalkan'
                            };
                            return texts[status] || status;
                        };
                        
                        // Status badge class helper
                        const getStatusBadgeClass = (status) => {
                            const classes = {
                                'pending': 'bg-yellow-100 text-yellow-800',
                                'paid': 'bg-blue-100 text-blue-800',
                                'processing': 'bg-purple-100 text-purple-800',
                                'completed': 'bg-green-100 text-green-800',
                                'cancelled': 'bg-red-100 text-red-800'
                            };
                            return classes[status] || 'bg-gray-100 text-gray-800';
                        };
                        
                        return `
                            <div class="bg-white rounded-xl p-4 border border-gray-200">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-base">${order.order_code}</h4>
                                        <p class="text-sm text-gray-600 mt-1">Meja ${order.table_id} • ${order.customer_name}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${getStatusBadgeClass(order.status)}">
                                        ${getStatusText(order.status)}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                    <div>
                                        <p class="text-xs text-gray-500">Total</p>
                                        <p class="font-bold text-indigo-600 text-lg">Rp ${parseInt(order.total_amount).toLocaleString('id-ID')}</p>
                                    </div>
                                    ${order.status === 'pending' ? `
                                    <button onclick="cancelOrder('${order.order_code}')" 
                                            class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">
                                        Batalkan
                                    </button>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            })
            .catch(error => {
                if (showLoadingOverlay) {
                    hideLoading();
                }
                console.error('Error fetching orders:', error);
                
                const orderList = document.getElementById('orderList');
                if (orderList) {
                    orderList.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                            </div>
                            <p class="font-semibold text-gray-700">Gagal Memuat Pesanan</p>
                            <p class="text-xs text-gray-400 mt-1">Silakan refresh halaman atau coba lagi</p>
                            <button onclick="loadOrders(true)" class="mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i>Coba Lagi
                            </button>
                        </div>
                    `;
                }
            });
    }

    function hideOrderStatus() {
        document.getElementById('orderModal').classList.add('hidden');
        
        // Stop auto refresh ketika modal ditutup
        if (orderStatusInterval) {
            clearInterval(orderStatusInterval);
            orderStatusInterval = null;
        }
    }
    
    function showStatusChangeNotification(order) {
        const statusTexts = {
            'pending': 'Menunggu Pembayaran',
            'paid': 'Sudah Dibayar',
            'processing': 'Sedang Diproses',
            'completed': 'Selesai',
            'cancelled': 'Dibatalkan'
        };
        
        const statusIcons = {
            'pending': 'fa-clock',
            'paid': 'fa-check-circle',
            'processing': 'fa-spinner',
            'completed': 'fa-check-double',
            'cancelled': 'fa-times-circle'
        };
        
        const statusColors = {
            'pending': 'bg-yellow-500',
            'paid': 'bg-blue-500',
            'processing': 'bg-purple-500',
            'completed': 'bg-green-500',
            'cancelled': 'bg-red-500'
        };
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-20 right-4 z-50 bg-white rounded-xl shadow-2xl p-4 max-w-sm animate-slide-in';
        notification.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="${statusColors[order.status]} w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas ${statusIcons[order.status]} text-white"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 text-sm">Status Pesanan Berubah!</h4>
                    <p class="text-xs text-gray-600 mt-1">${order.order_code}</p>
                    <p class="text-sm font-semibold text-gray-800 mt-1">${statusTexts[order.status]}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    function cancelOrder(orderCode) {
        if (confirm('Yakin ingin membatalkan pesanan?')) {
            showLoading();
            
            fetch(`/api/orders/${orderCode}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    alert('Pesanan berhasil dibatalkan');
                    showOrderStatus();
                } else {
                    alert('Gagal membatalkan pesanan: ' + data.message);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membatalkan pesanan');
            });
        }
    }

    // ==================== UTILITY FUNCTIONS ====================
    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
    }

    // ==================== EVENT LISTENERS ====================
    
    // Carousel events
    document.addEventListener('DOMContentLoaded', function() {
        initCarousel();
        
        // Pause carousel on hover
        const carouselContainer = document.querySelector('.carousel-container');
        if (carouselContainer) {
            carouselContainer.addEventListener('mouseenter', () => {
                if (carouselInterval) clearInterval(carouselInterval);
            });
            
            carouselContainer.addEventListener('mouseleave', () => {
                if (slides.length > 0) {
                    carouselInterval = setInterval(nextSlide, 5000);
                }
            });
        }
        
        // Carousel navigation
        const carouselNext = document.querySelector('.carousel-next');
        const carouselPrev = document.querySelector('.carousel-prev');
        
        if (carouselNext) carouselNext.addEventListener('click', nextSlide);
        if (carouselPrev) carouselPrev.addEventListener('click', prevSlide);
        
        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showSlide(index);
            });
        });
        
        // Initialize cart
        updateCartSummary();
        updateAllQuantityDisplays();
    });

    // Category filtering
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function() {
            const category = this.dataset.category;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            
            // Update active category
            document.querySelectorAll('.category-card').forEach(c => {
                c.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filter menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                const menuName = item.dataset.name;
                const itemCategory = item.dataset.category;
                
                const matchesSearch = searchTerm === '' || menuName.includes(searchTerm);
                const matchesCategory = category === 'all' || itemCategory === category;
                
                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide section headers and grids based on visible items
            document.querySelectorAll('.makanan-section, .minuman-section, .snack-section').forEach(section => {
                const hasVisibleItems = Array.from(section.querySelectorAll('.menu-item')).some(item => 
                    item.style.display !== 'none'
                );
                // Hanya sembunyikan section jika tidak ada item yang visible
                if (hasVisibleItems) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            if (searchTerm.length > 0) {
                clearSearch.classList.remove('hidden');
            } else {
                clearSearch.classList.add('hidden');
            }
            
            const activeCategory = document.querySelector('.category-card.active')?.dataset.category || 'all';
            
            document.querySelectorAll('.menu-item').forEach(item => {
                const menuName = item.dataset.name;
                const category = item.dataset.category;
                
                const matchesSearch = menuName.includes(searchTerm);
                const matchesCategory = activeCategory === 'all' || category === activeCategory;
                
                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide section headers and grids based on visible items
            document.querySelectorAll('.makanan-section, .minuman-section, .snack-section').forEach(section => {
                const hasVisibleItems = Array.from(section.querySelectorAll('.menu-item')).some(item => 
                    item.style.display !== 'none'
                );
                if (hasVisibleItems) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    }
    
    if (clearSearch) {
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            this.classList.add('hidden');
            
            const activeCategory = document.querySelector('.category-card.active')?.dataset.category || 'all';
            
            document.querySelectorAll('.menu-item').forEach(item => {
                const category = item.dataset.category;
                if (activeCategory === 'all' || category === activeCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            document.querySelectorAll('.makanan-section, .minuman-section, .snack-section').forEach(section => {
                const hasVisibleItems = Array.from(section.querySelectorAll('.menu-item')).some(item => 
                    item.style.display !== 'none'
                );
                if (hasVisibleItems) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    }

    // Main event listener for all interactions
    document.addEventListener('click', function(e) {
        // Quantity buttons - hanya mengubah display
        if (e.target.closest('.quantity-btn')) {
            const btn = e.target.closest('.quantity-btn');
            const menuId = btn.dataset.menuId;
            const isIncrease = btn.classList.contains('increase');
            
            // Get current display value
            const display = document.querySelector(`.quantity-display[data-menu-id="${menuId}"]`);
            if (!display) return;
            
            let currentValue = parseInt(display.textContent) || 0;
            
            if (isIncrease) {
                // Check stock limit if available
                const menuItem = btn.closest('.menu-item');
                if (menuItem) {
                    const stockText = menuItem.querySelector('.text-xs.text-gray-400')?.textContent;
                    if (stockText && stockText.includes('Stok:')) {
                        const stock = parseInt(stockText.replace('Stok:', '').trim());
                        if (stock && currentValue >= stock) {
                            alert(`Stok maksimum: ${stock}`);
                            return;
                        }
                    }
                }
                currentValue++;
            } else {
                if (currentValue > 0) {
                    currentValue--;
                }
            }
            
            // Update display only (not cart)
            display.textContent = currentValue;
            
            // Update button state
            const decreaseBtn = menuItem?.querySelector(`.decrease[data-menu-id="${menuId}"]`);
            if (decreaseBtn) {
                decreaseBtn.disabled = currentValue === 0;
                decreaseBtn.classList.toggle('opacity-50', currentValue === 0);
            }
        }
        
        // Add to cart buttons - menambahkan jumlah yang ada di display ke cart
        if (e.target.closest('.add-to-cart')) {
            const btn = e.target.closest('.add-to-cart');
            const menuId = btn.dataset.menuId;
            
            // Check if item is disabled (out of stock)
            const menuItem = btn.closest('.menu-item');
            if (menuItem && menuItem.classList.contains('disabled')) {
                alert('Menu ini sedang habis stok');
                return;
            }
            
            // Get the current quantity from display
            const quantityDisplay = document.querySelector(`.quantity-display[data-menu-id="${menuId}"]`);
            if (!quantityDisplay) {
                console.error('Quantity display not found for menu:', menuId);
                return;
            }
            
            const quantityToAdd = parseInt(quantityDisplay.textContent);
            
            // If quantity is 0 or less, don't add to cart
            if (quantityToAdd <= 0) {
                // Add shake animation to alert user
                quantityDisplay.classList.add('shake');
                setTimeout(() => {
                    quantityDisplay.classList.remove('shake');
                }, 500);
                alert('Silakan tambahkan kuantitas terlebih dahulu dengan tombol +');
                return;
            }
            
            // Add the quantity to cart
            if (!cart[menuId]) {
                cart[menuId] = 0;
            }
            cart[menuId] += quantityToAdd;
            
            console.log(`Added ${quantityToAdd} to cart for menu ${menuId}. Cart now:`, cart[menuId]);
            
            // Reset quantity display to 0
            quantityDisplay.textContent = '0';
            
            // Update cart summary
            updateCartSummary();
            
            // Visual feedback
            const originalText = btn.innerHTML;
            const originalClasses = btn.className;
            
            btn.innerHTML = `<i class="fas fa-check mr-1"></i>${quantityToAdd} ditambahkan`;
            btn.classList.remove('from-indigo-600', 'to-purple-600');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.className = originalClasses;
            }, 1500);
        }
        
        // View details
        if (e.target.closest('.view-details')) {
            const btn = e.target.closest('.view-details');
            const details = btn.dataset.details;
            document.getElementById('detailsContent').textContent = details;
            document.getElementById('detailsModal').classList.remove('hidden');
        }
        
        // Remove item from cart
        if (e.target.closest('.remove-item')) {
            const btn = e.target.closest('.remove-item');
            const menuId = btn.dataset.menuId;
            delete cart[menuId];
            updateCartSummary();
            showCart();
        }
    });

    // Cart modal
    document.getElementById('cartButton').addEventListener('click', showCart);
    document.getElementById('footerCartButton').addEventListener('click', showCart);
    document.getElementById('closeCart').addEventListener('click', hideCart);

    // Payment modal
    document.getElementById('closePayment').addEventListener('click', hidePayment);
    document.getElementById('cancelPayment').addEventListener('click', hidePayment);

    // Details modal
    document.getElementById('closeDetails').addEventListener('click', function() {
        document.getElementById('detailsModal').classList.add('hidden');
    });

    // Order form
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const tableId = formData.get('table_id');
        const customerName = formData.get('customer_name');
        const paymentMethod = formData.get('payment_method');
        
        // Validasi
        if (!tableId) {
            alert('Silakan pilih nomor meja terlebih dahulu');
            return;
        }
        
        if (!customerName) {
            alert('Silakan isi nama pemesan');
            return;
        }
        
        if (Object.keys(cart).length === 0) {
            alert('Keranjang masih kosong. Silakan tambahkan menu terlebih dahulu.');
            return;
        }
        
        // Check if any item in cart is out of stock
        let hasOutOfStock = false;
        for (const menuId in cart) {
            if (cart[menuId] > 0) {
                const menuItem = document.querySelector(`.menu-item[data-menu-id="${menuId}"]`);
                if (menuItem && menuItem.classList.contains('disabled')) {
                    hasOutOfStock = true;
                    break;
                }
            }
        }
        
        if (hasOutOfStock) {
            alert('Ada item dalam keranjang yang stoknya habis. Silakan hapus item tersebut.');
            return;
        }
        
        // Prepare order data
        currentOrderData = {
            table_id: parseInt(tableId),
            customer_name: customerName,
            payment_method: paymentMethod,
            items: {...cart}, // Copy cart object
            total_amount: calculateTotal()
        };
        
        showPaymentModal(currentOrderData);
    });

    // Confirm payment
    document.getElementById('confirmPayment').addEventListener('click', function() {
        if (!currentOrderData) {
            alert('Tidak ada data pesanan yang ditemukan.');
            return;
        }
        
        // Langsung proses tanpa loading overlay
        processOrder(currentOrderData);
    });

    // Success modal
    document.getElementById('closeSuccess').addEventListener('click', function() {
        document.getElementById('successModal').classList.add('hidden');
    });

    // Order status modal
    document.getElementById('footerOrderButton').addEventListener('click', showOrderStatus);
    document.getElementById('closeOrder').addEventListener('click', hideOrderStatus);

    // Close modals when clicking outside (on backdrop)
    document.addEventListener('click', function(e) {
        // Check if click is on the modal backdrop (not on modal content)
        if (e.target.id === 'cartModal' || (e.target.closest('#cartModal') && !e.target.closest('.modal-container'))) {
            if (e.target.id === 'cartModal') hideCart();
        }
        if (e.target.id === 'paymentModal' || (e.target.closest('#paymentModal') && !e.target.closest('.bg-white'))) {
            if (e.target.id === 'paymentModal') hidePayment();
        }
        if (e.target.id === 'successModal' || (e.target.closest('#successModal') && !e.target.closest('.bg-white'))) {
            if (e.target.id === 'successModal') document.getElementById('successModal').classList.add('hidden');
        }
        if (e.target.id === 'detailsModal' || (e.target.closest('#detailsModal') && !e.target.closest('.bg-white'))) {
            if (e.target.id === 'detailsModal') document.getElementById('detailsModal').classList.add('hidden');
        }
        if (e.target.id === 'orderModal' || (e.target.closest('#orderModal') && !e.target.closest('.bg-white'))) {
            if (e.target.id === 'orderModal') hideOrderStatus();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCart();
            hidePayment();
            hideOrderStatus();
            document.getElementById('successModal').classList.add('hidden');
            document.getElementById('detailsModal').classList.add('hidden');
        }
    });
    </script>
</body>
</html>