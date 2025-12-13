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
    <script src="https://cdn.tailwindcss.com"></script>
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
        background: #fafbfc;
    }
    
    /* ============== UTILITY CLASSES ============== */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* ============== CONTAINER RESPONSIVE ============== */
    .main-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    /* Mobile: Better spacing */
    @media (max-width: 767px) {
        .main-container {
            padding: 0 1.25rem;
        }
    }
    
    @media (min-width: 768px) {
        .main-container {
            padding: 0 2rem;
        }
    }
    
    @media (min-width: 1024px) {
        .main-container {
            padding: 0 3rem;
        }
    }
    
    /* ============== MENU ITEM CARDS - TOTAL REDESIGN ============== */
    .menu-item {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        position: relative;
        border: none;
    }
    
    .menu-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }
    
    .menu-item:hover::before {
        transform: scaleX(1);
    }
    
    .menu-item:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -8px rgba(99, 102, 241, 0.25), 0 0 0 1px rgba(99, 102, 241, 0.1);
    }
    
    /* Card dengan image di samping untuk desktop */
    @media (min-width: 1024px) {
        .menu-item-horizontal {
            flex-direction: row;
        }
        
        .menu-item-horizontal .menu-image-container {
            width: 40%;
            height: auto;
            min-height: 100%;
        }
        
        .menu-item-horizontal .menu-content {
            width: 60%;
            padding: 1.5rem;
        }
    }
    
    /* Base grid layout */
    .menu-grid {
        display: grid;
        width: 100%;
    }
    
    /* Ensure cards have minimum width to prevent content cramping */
    .menu-item {
        min-width: 0; /* Allow flex shrinking */
        width: 100%;
    }
    
    /* Mobile: 2 columns with better spacing */
    @media (max-width: 767px) {
        .menu-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            padding: 0 0.75rem;
        }
        
        /* Extra spacing for main container on mobile */
        .main-container {
            padding: 0 1rem;
        }
    }
    
    /* Tablet: 2 columns with improved spacing */
    @media (min-width: 768px) and (max-width: 1023px) {
        .menu-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.75rem;
            padding: 0 1rem;
        }
    }
    
    /* Desktop: 3 columns with optimal spacing */
    @media (min-width: 1024px) {
        .menu-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 2.25rem;
            padding: 0 1rem;
        }
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
    
    /* Carousel Styles - Responsive */
    .carousel-container {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        height: 200px;
        box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
    
    @media (min-width: 768px) {
        .carousel-container {
            height: 280px;
            border-radius: 24px;
        }
    }
    
    @media (min-width: 1024px) {
        .carousel-container {
            height: 360px;
            border-radius: 28px;
        }
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
    
    /* Horizontal Category Styles - Modern & Responsive */
    .category-scroll {
        display: flex;
        overflow-x: auto;
        gap: 14px;
        padding: 8px 2rem 16px 2rem;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .category-scroll::-webkit-scrollbar {
        display: none;
    }
    
    .category-card {
        flex: 0 0 auto;
        width: 70px;
        height: 70px;
        border-radius: 10px;
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
        padding: 8px 4px;
        text-align: center;
    }
    
    .category-name {
        font-weight: 600;
        font-size: 11px;
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
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }
        
        .category-card {
            width: 65px;
            height: 65px;
        }
        
        .category-scroll {
            padding: 8px 2rem 16px 2rem;
            gap: 12px;
        }
        
        .menu-image-container {
            height: 150px;
        }
        
        /* Ensure menu items have proper spacing on mobile */
        .menu-item {
            margin: 0;
        }
        
        /* Better spacing for 2-column layout on mobile */
        .menu-grid {
            padding: 0 0.75rem;
        }
        
        /* Better section header spacing on mobile */
        .makanan-section h2,
        .minuman-section h2,
        .snack-section h2 {
            margin-left: 1rem;
            margin-right: 1rem;
        }
        
        /* Section container spacing */
        .makanan-section,
        .minuman-section,
        .snack-section {
            margin-bottom: 1.5rem;
        }
    }

    /* Menu Image Container - Dramatic Redesign */
    .menu-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        overflow: hidden;
    }
    
    @media (min-width: 768px) {
        .menu-image-container {
            height: 220px;
        }
    }
    
    @media (min-width: 1024px) {
        .menu-image-container {
            height: 240px;
        }
    }
    
    .menu-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        filter: brightness(0.95);
    }
    
    .menu-item:hover .menu-image {
        transform: scale(1.15) rotate(2deg);
        filter: brightness(1.05);
    }
    
    /* Gradient overlay pada image */
    .menu-image-container::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50%;
        background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    .menu-item:hover .menu-image-container::after {
        opacity: 1;
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
    
    /* ============== MODERN ALERT MODAL ============== */
    .modern-alert-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn 0.2s ease-out;
    }
    
    .modern-alert-content {
        background: white;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        max-width: 340px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .modern-alert-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }
    
    .modern-alert-icon.warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    }
    
    .modern-alert-icon.error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    }
    
    .modern-alert-icon.success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    }
    
    .modern-alert-icon.info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    }
    
    .modern-alert-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        text-align: center;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    .modern-alert-message {
        font-size: 0.875rem;
        color: #6b7280;
        text-align: center;
        line-height: 1.5;
        margin-bottom: 1.25rem;
    }
    
    .modern-alert-button {
        width: 100%;
        padding: 0.75rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    
    .modern-alert-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
    }
    
    .modern-alert-button:active {
        transform: translateY(0);
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    
    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
    }
    
    .modern-alert-overlay.closing {
        animation: fadeOut 0.2s ease-out forwards;
    }
    
    .modern-alert-overlay.closing .modern-alert-content {
        animation: slideDown 0.2s ease-out forwards;
    }

    /* Stock Error Modal Specific Styles */
    .modern-alert-content.stock-error {
        max-width: 380px;
    }

    .stock-error-details {
        text-align: center;
        margin-bottom: 1.25rem;
    }

    .stock-error-details .menu-name {
        font-weight: 600;
        color: #dc2626;
        font-size: 1rem;
        margin-bottom: 1rem;
    }

    .stock-info {
        background: #f9fafb;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .stock-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .stock-row:last-child {
        margin-bottom: 0;
    }

    .stock-row .available {
        font-weight: 600;
        color: #059669;
    }

    .stock-row .requested {
        font-weight: 600;
        color: #dc2626;
    }

    .help-text {
        font-size: 0.75rem;
        color: #6b7280;
        font-style: italic;
    }
    
    /* Responsive untuk mobile */
    @media (max-width: 640px) {
        .modern-alert-content {
            max-width: 300px;
            padding: 1.25rem 1.5rem;
        }
        
        .modern-alert-icon {
            width: 48px;
            height: 48px;
            font-size: 24px;
            margin-bottom: 0.875rem;
        }
        
        .modern-alert-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .modern-alert-message {
            font-size: 0.8125rem;
            margin-bottom: 1rem;
        }
        
        .modern-alert-button {
            padding: 0.625rem;
            font-size: 0.8125rem;
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
</style>
</head>
<body class="bg-gray-50 pb-20 md:pb-8">
    <!-- Header - Responsive Design with Better Height -->
    <div class="bg-white shadow-sm sticky top-0 z-30 backdrop-blur-sm bg-opacity-95 border-b border-gray-100">
        <div class="main-container py-6 md:py-8 lg:py-10">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-base md:text-lg lg:text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">FURAWA CAFE</h1>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div id="cartSummary" class="hidden">
                        <span id="cartCount" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full w-5 h-5 md:w-6 md:h-6 flex items-center justify-center text-xs font-semibold shadow-lg">0</span>
                    </div>
                    <button id="cartButton" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 md:px-4 py-2 md:py-2.5 rounded-lg hover:shadow-lg transition-all duration-300 text-sm">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="hidden sm:inline ml-2">Keranjang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container py-6 md:py-8 lg:py-10">
       
    <!-- Carousel Wrapper - Responsive -->
    <div class="carousel-container mt-4 md:mt-6 lg:mt-8">

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

        <!-- Search Bar - Responsive Design -->
        <div class="mb-4 md:mb-6 lg:mb-8">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Cari menu favorit Anda..." 
                       class="w-full px-4 md:px-5 py-3 md:py-3.5 pl-11 md:pl-12 pr-11 md:pr-12 bg-white border-2 border-gray-200 rounded-xl md:rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-300 shadow-sm text-sm md:text-base">
                <div class="absolute left-4 md:left-5 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-search text-indigo-400 text-sm md:text-base"></i>
                </div>
                <div class="absolute right-4 md:right-5 top-1/2 transform -translate-y-1/2">
                    <button id="clearSearch" class="text-gray-400 hover:text-indigo-600 transition-colors hidden">
                        <i class="fas fa-times-circle text-sm md:text-base"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Horizontal Categories - Responsive Design -->
        <div class="mb-5 md:mb-6 lg:mb-8">
            <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-2 md:mb-3 flex items-center px-4 md:px-0">
                <span class="w-1 h-5 md:h-6 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full mr-2 md:mr-3"></span>
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
            <div class="mb-6 md:mb-8 makanan-section">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                        🍽️
                    </div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 ml-3">Makanan</h2>
                </div>
                <div class="menu-grid grid">
                    @foreach($categories['makanan'] as $menu)
                    @php
                        // Generate admin image URL
                        $imageUrl = $menu->image ? asset('storage/' . $menu->image) : null;
                        $fallbackImage = 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60';
                        $isOutOfStock = $menu->stock !== null && $menu->stock == 0;
                        $category = 'makanan';
                    @endphp
                    
                    @include('customer.partials.menu-card-new')
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Minuman Section - Minimalist Design -->
            @if(isset($categories['minuman']) && count($categories['minuman']) > 0)
            <div class="mb-6 md:mb-8 minuman-section">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                        🥤
                    </div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 ml-3">Minuman</h2>
                </div>
                <div class="menu-grid grid">
                    @foreach($categories['minuman'] as $menu)
                    @php
                        // Generate admin image URL
                        $imageUrl = $menu->image ? asset('storage/' . $menu->image) : null;
                        $fallbackImage = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60';
                        $isOutOfStock = $menu->stock !== null && $menu->stock == 0;
                        $category = 'minuman';
                    @endphp
                    
                    @include('customer.partials.menu-card-new')
                    
                    <div style="display:none;" class="menu-item bg-white rounded-xl shadow-md hover:shadow-xl p-4 relative {{ $isOutOfStock ? 'disabled' : '' }}" 
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
            <div class="mb-6 md:mb-8 snack-section">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-xl shadow-md">
                        🍰
                    </div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 ml-3">Dessert</h2>
                </div>
                <div class="menu-grid grid">
                    @foreach($categories['snack'] as $menu)
                    @php
                        // Generate admin image URL
                        $imageUrl = $menu->image ? asset('storage/' . $menu->image) : null;
                        $fallbackImage = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60';
                        $isOutOfStock = $menu->stock !== null && $menu->stock == 0;
                        $category = 'snack';
                    @endphp
                    
                    @include('customer.partials.menu-card-new')
                    
                    <div style="display:none;" class="menu-item bg-white rounded-xl shadow-md hover:shadow-xl p-4 relative {{ $isOutOfStock ? 'disabled' : '' }}" 
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
                        
                        <!-- Payment Method Selection - Cash Only -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-4">
                                <input type="hidden" name="payment_method" value="cash">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-800 text-base block">Bayar Tunai di Kasir</span>
                                        <p class="text-sm text-gray-600">Tunjukkan kode pesanan ke kasir untuk pembayaran</p>
                                    </div>
                                </div>
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
                <div class="space-y-2">
                    <button id="closeSuccess" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg hover:shadow-lg transition-all duration-300 font-semibold text-sm">
                        Kembali ke Menu
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Struk dapat diakses setelah pesanan selesai
                    </p>
                </div>
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



    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-md max-h-[85vh] overflow-hidden shadow-2xl border border-gray-200 flex flex-col">
                <!-- Header -->
                <div class="flex-shrink-0 bg-gradient-to-r from-indigo-600 to-purple-600 p-4 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-white">Detail Pesanan</h3>
                            <p class="text-indigo-100 text-sm" id="orderDetailsSubtitle">Pesanan terbaru Anda</p>
                        </div>
                        <button id="closeOrderDetails" class="text-white hover:bg-white hover:bg-opacity-20 w-8 h-8 rounded-full flex items-center justify-center transition-all">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4" id="orderDetailsContent">
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-receipt text-3xl mb-3 opacity-50"></i>
                        <p class="font-medium">Belum ada pesanan</p>
                        <p class="text-sm text-gray-400 mt-1">Pesanan Anda akan muncul di sini</p>
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

    <!-- Footer Navigation - Responsive Design -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 z-40 shadow-lg backdrop-blur-lg bg-opacity-95 md:hidden">
        <div class="container mx-auto">
            <div class="flex justify-around items-center py-2.5">
                <a href="/menu" class="flex flex-col items-center text-indigo-600 transition-all duration-300 py-1 px-3">
                    <i class="fas fa-utensils text-lg mb-1"></i>
                    <span class="text-xs font-medium">Menu</span>
                </a>
                
                <button id="footerCartButton" class="flex flex-col items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 py-1 px-3 relative">
                    <i class="fas fa-shopping-cart text-lg mb-1"></i>
                    <span id="footerCartCount" class="absolute -top-1 right-1 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold hidden shadow-lg">0</span>
                    <span class="text-xs font-medium">Keranjang</span>
                </button>
                
                <button id="footerOrderButton" class="flex flex-col items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 py-1 px-3 relative">
                    <i class="fas fa-receipt text-lg mb-1"></i>
                    <span id="footerOrderBadge" class="absolute -top-1 right-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold hidden shadow-lg">0</span>
                    <span class="text-xs font-medium">Pesanan</span>
                </button>
                
                <a href="/" class="flex flex-col items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 py-1 px-3">
                    <i class="fas fa-home text-lg mb-1"></i>
                    <span class="text-xs font-medium">Home</span>
                </a>
            </div>
        </div>
    </div>

    <script>
    // ==================== VARIABLES - REDESIGNED FOR TEMPERATURE VARIANTS ====================
    let cart = {}; // Cart: { "menuId_temperature": quantity } - supports multiple temps per menu
    let tempQuantity = {}; // Temporary: { "menuId_temperature": quantity }
    let selectedTemperature = {}; // Current selected temperature per menu: { menuId: "ice"/"hot" }
    let currentOrderData = null;
    let serviceChargePercentage = 3; // Default 3%, will be loaded from API
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const totalSlides = slides.length;
    let carouselInterval = null;
    
    // ==================== MODERN ALERT FUNCTION ====================
    function showModernAlert(message, type = 'warning') {
        // Icon mapping dengan emoji yang lebih friendly
        const icons = {
            warning: '⚠️',
            error: '😔',
            success: '✨',
            info: 'ℹ️'
        };
        
        // Title mapping yang lebih casual
        const titles = {
            warning: 'Ups!',
            error: 'Waduh!',
            success: 'Yeay!',
            info: 'Info'
        };
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'modern-alert-overlay';
        overlay.innerHTML = `
            <div class="modern-alert-content">
                <div class="modern-alert-icon ${type}">
                    ${icons[type]}
                </div>
                <h3 class="modern-alert-title">${titles[type]}</h3>
                <p class="modern-alert-message">${message}</p>
                <button class="modern-alert-button">
                    Oke, Mengerti
                </button>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Close on button click
        const button = overlay.querySelector('.modern-alert-button');
        button.addEventListener('click', () => {
            overlay.classList.add('closing');
            setTimeout(() => {
                document.body.removeChild(overlay);
            }, 200);
        });
        
        // Close on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.add('closing');
                setTimeout(() => {
                    document.body.removeChild(overlay);
                }, 200);
            }
        });
    }

    function showStockErrorModal(errorData) {
        // Create unique stock error modal
        const overlay = document.createElement('div');
        overlay.className = 'modern-alert-overlay';
        overlay.innerHTML = `
            <div class="modern-alert-content stock-error">
                <div class="modern-alert-icon error">
                    📦
                </div>
                <h3 class="modern-alert-title">Stok Tidak Mencukupi!</h3>
                <div class="stock-error-details">
                    <p class="menu-name">${errorData.menu_name}</p>
                    <div class="stock-info">
                        <div class="stock-row">
                            <span>Stok tersedia:</span>
                            <span class="available">${errorData.available_stock}</span>
                        </div>
                        <div class="stock-row">
                            <span>Yang dipesan:</span>
                            <span class="requested">${errorData.requested_quantity}</span>
                        </div>
                    </div>
                    <p class="help-text">Silakan kurangi jumlah pesanan atau pilih menu lain 🙏</p>
                </div>
                <button class="modern-alert-button">
                    Oke, Saya Mengerti
                </button>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Close on button click
        const button = overlay.querySelector('.modern-alert-button');
        button.addEventListener('click', () => {
            overlay.classList.add('closing');
            setTimeout(() => {
                document.body.removeChild(overlay);
            }, 200);
        });
        
        // Close on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.add('closing');
                setTimeout(() => {
                    document.body.removeChild(overlay);
                }, 200);
            }
        });
    }
    
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
    
    // Function seperti PHP in_array - FIXED VERSION
    function in_array(needle, haystack) {
        return haystack.includes(needle);
    }
    
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
            // Show quantity for currently selected temperature
            const currentTemp = selectedTemperature[menuId] || 'normal';
            const cartKey = `${menuId}_${currentTemp}`;
            display.textContent = tempQuantity[cartKey] || 0;
        }
    }

    function updateAllQuantityDisplays() {
        document.querySelectorAll('.quantity-display').forEach(display => {
            const menuId = display.dataset.menuId;
            // Show quantity for currently selected temperature
            const currentTemp = selectedTemperature[menuId] || 'normal';
            const cartKey = `${menuId}_${currentTemp}`;
            display.textContent = tempQuantity[cartKey] || 0;
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
        
        // Update order badge
        updateOrderBadge();
    }
    
    function updateOrderBadge() {
        const myOrderCodes = getMyOrderCodes();
        const orderBadge = document.getElementById('footerOrderBadge');
        
        // Selalu sembunyikan badge, tidak menampilkan angka apapun
        orderBadge.classList.add('hidden');
    }

    function calculateTotal() {
        let subtotal = 0;
        let totalPpn = 0;
        let serviceCharge = 0;
        
        for (const cartKey in cart) {
            if (cart[cartKey] > 0) {
                const [menuId, temperature] = cartKey.split('_');
                const menuItem = document.querySelector(`.menu-item[data-menu-id="${menuId}"]`);
                if (menuItem) {
                    const basePrice = parseFloat(menuItem.dataset.basePrice || 0);
                    const ppnAmount = parseFloat(menuItem.dataset.ppnAmount || 0);
                    const quantity = cart[cartKey];
                    
                    subtotal += basePrice * quantity;
                    totalPpn += ppnAmount * quantity;
                } else {
                    console.error('Menu item not found for menuId:', menuId);
                }
            }
        }
        
        // Service charge berdasarkan setting admin
        serviceCharge = subtotal * (serviceChargePercentage / 100);
        
        return {
            subtotal: subtotal,
            ppn: totalPpn,
            serviceCharge: serviceCharge,
            total: subtotal + totalPpn + serviceCharge
        };
    }

    function showCart() {
        const modal = document.getElementById('cartModal');
        const cartItems = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        const cartTotal = document.getElementById('cartTotal');
        
        cartItems.innerHTML = '';
        
        let hasItems = false;
        
        for (const cartKey in cart) {
            const quantity = cart[cartKey];
            if (quantity > 0) {
                hasItems = true;
                const [menuId, temperature] = cartKey.split('_');
                const menuItem = document.querySelector(`.menu-item[data-menu-id="${menuId}"]`);
                
                if (menuItem) {
                    const name = menuItem.querySelector('h3').textContent;
                    const basePrice = parseFloat(menuItem.dataset.basePrice || 0);
                    const ppnAmount = parseFloat(menuItem.dataset.ppnAmount || 0);
                    const finalPrice = basePrice + ppnAmount;
                    const itemSubtotal = finalPrice * quantity;
                    
                    // Format temperature display
                    let tempDisplay = '';
                    if (temperature === 'ice') {
                        tempDisplay = ' 🧊 Ice';
                    } else if (temperature === 'hot') {
                        tempDisplay = ' 🔥 Hot';
                    }
                    
                    const cartItem = document.createElement('div');
                    cartItem.className = 'flex justify-between items-center p-3 bg-gray-50 rounded-lg';
                    
                    let priceDisplay = `Rp ${basePrice.toLocaleString('id-ID')}`;
                    if (ppnAmount > 0) {
                        priceDisplay += ` + PPN Rp ${ppnAmount.toLocaleString('id-ID')}`;
                    }
                    
                    cartItem.innerHTML = `
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">${name}${tempDisplay}</h4>
                            <p class="text-sm text-gray-600">${priceDisplay} × ${quantity}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">Rp ${itemSubtotal.toLocaleString('id-ID')}</p>
                            <button class="remove-item text-red-500 text-sm mt-1 hover:text-red-700 transition-colors" data-cart-key="${cartKey}">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </div>
                    `;
                    cartItems.appendChild(cartItem);
                }
            }
        }
        
        if (hasItems) {
            emptyCart.classList.add('hidden');
            
            // Calculate totals
            const totals = calculateTotal();
            
            // Update cart total display with breakdown
            cartTotal.innerHTML = `
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>Rp ${totals.subtotal.toLocaleString('id-ID')}</span>
                    </div>
                    ${totals.ppn > 0 ? `
                    <div class="flex justify-between">
                        <span>PPN:</span>
                        <span>Rp ${totals.ppn.toLocaleString('id-ID')}</span>
                    </div>
                    ` : ''}
                    <div class="flex justify-between">
                        <span>Service Charge (${serviceChargePercentage}%):</span>
                        <span>Rp ${totals.serviceCharge.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span class="text-indigo-600">Rp ${totals.total.toLocaleString('id-ID')}</span>
                    </div>
                </div>
            `;
            
            // Add event listeners for remove buttons
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const cartKey = this.dataset.cartKey;
                    const [menuId, temperature] = cartKey.split('_');
                    delete cart[cartKey];
                    // Reset tempQuantity juga
                    tempQuantity[cartKey] = 0;
                    updateCartSummary();
                    updateQuantityDisplay(menuId);
                    showCart();
                });
            });
        } else {
            emptyCart.classList.remove('hidden');
            cartTotal.innerHTML = '<div class="text-center text-gray-500">Rp 0</div>';
        }
        
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
        
        // Order Summary with breakdown
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
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span>Rp ${orderData.subtotal.toLocaleString('id-ID')}</span>
                        </div>
                        ${orderData.ppn > 0 ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">PPN:</span>
                            <span>Rp ${orderData.ppn.toLocaleString('id-ID')}</span>
                        </div>
                        ` : ''}
                        <div class="flex justify-between">
                            <span class="text-gray-600">Service Charge (${serviceChargePercentage}%):</span>
                            <span>Rp ${orderData.serviceCharge.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between font-bold text-base border-t pt-2 mt-2">
                            <span class="text-gray-800">Total Pembayaran:</span>
                            <span class="text-blue-600">Rp ${orderData.total_amount.toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Payment Method - Cash Only
        paymentHtml += `
            <div class="payment-section">
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-6 rounded-2xl border-2 border-indigo-200 mb-4">
                    <div class="flex items-center mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">Bayar Tunai di Kasir</h4>
                            <p class="text-sm text-gray-600">Tunjukkan kode pesanan ke kasir</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded-xl border border-indigo-100">
                        <div class="text-center mb-4">
                            <i class="fas fa-cash-register text-5xl text-indigo-600 mb-3"></i>
                            <h5 class="font-bold text-indigo-900 text-lg mb-2">Langkah Pembayaran</h5>
                        </div>
                        
                        <div class="space-y-3 text-left">
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0 mt-0.5">1</div>
                                <p class="text-sm text-gray-700">Tunjukkan <strong>kode pesanan</strong> ke kasir</p>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0 mt-0.5">2</div>
                                <p class="text-sm text-gray-700">Lakukan pembayaran tunai sebesar <strong>Rp ${orderData.total_amount.toLocaleString('id-ID')}</strong></p>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0 mt-0.5">3</div>
                                <p class="text-sm text-gray-700">Pesanan akan segera diproses setelah pembayaran</p>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg mt-4 border border-green-200">
                            <p class="text-sm text-green-700 flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                Pesanan Anda sudah masuk ke sistem
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
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

        
        // Format data untuk server
        const formattedData = {
            customer_name: orderData.customer_name,
            table_id: parseInt(orderData.table_id),
            payment_method: orderData.payment_method,
            subtotal: parseFloat(orderData.subtotal),
            ppn_amount: parseFloat(orderData.ppn),
            service_charge: parseFloat(orderData.serviceCharge),
            total_amount: parseFloat(orderData.total_amount),
            items: []
        };
        
        // Konversi cart ke array items (with temperature variants)
        console.log('DEBUG: Cart data before processing:', orderData.items);
        
        for (const cartKey in orderData.items) {
            if (orderData.items[cartKey] > 0) {
                const [menuId, temperature] = cartKey.split('_');
                const item = {
                    menu_id: parseInt(menuId),
                    quantity: parseInt(orderData.items[cartKey])
                };
                
                // Add temperature (ice, hot, or normal)
                if (temperature && temperature !== 'normal') {
                    item.temperature = temperature;
                }
                
                console.log('DEBUG: Processing cart item:', {
                    cartKey,
                    menuId,
                    temperature,
                    finalItem: item
                });
                
                formattedData.items.push(item);
            }
        }
        
        console.log('DEBUG: Final formatted data:', formattedData);
        

        
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
                return response.json().then(data => {
                    throw new Error(JSON.stringify(data));
                }).catch(() => {
                    throw new Error(`Server error ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {

            hideLoading();
            hidePayment();
            
            if (data.success) {
                // Simpan order code ke localStorage untuk tracking
                saveMyOrderCode(data.order.order_code);
                
                // Simpan detail pesanan ke localStorage
                const orderDetails = {
                    order_code: data.order.order_code,
                    customer_name: data.order.customer_name,
                    table_id: data.order.table_id,
                    total_amount: data.order.total_amount,
                    items: cart, // Simpan items yang dipesan
                    created_at: new Date().toISOString(),
                    payment_method: currentOrderData.payment_method
                };
                localStorage.setItem(`order_${data.order.order_code}`, JSON.stringify(orderDetails));
                
                showSuccessModal(data.order);
                // Reset cart dan tempQuantity
                cart = {};
                tempQuantity = {};
                selectedTemperature = {};
                updateCartSummary();
                updateAllQuantityDisplays();
            } else {
                showModernAlert('Waduh, pesanannya gagal nih. ' + (data.message || 'Coba lagi ya!'), 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            
            // Try to parse error message for better handling
            try {
                const errorData = JSON.parse(error.message);
                if (errorData.error_type === 'stock_insufficient') {
                    // Show special stock error notification
                    showStockErrorModal(errorData);
                } else {
                    showModernAlert(errorData.message || 'Terjadi kesalahan saat memproses pesanan', 'error');
                }
            } catch (parseError) {
                // Fallback for non-JSON errors
                if (error.message.includes('Stok') && error.message.includes('tidak cukup')) {
                    showModernAlert(error.message, 'error');
                } else {
                    showModernAlert('Koneksi bermasalah nih. Cek internet kamu ya! 📶', 'error');
                }
            }
        });
    }

    function showSuccessModal(order) {
        const modal = document.getElementById('successModal');
        const message = document.getElementById('successMessage');
        
        // Karena sekarang hanya cash, tampilkan pesan yang jelas
        const messageText = `✅ Pesanan berhasil dibuat!\n\n📋 Kode Pesanan: ${order.order_code}\n\n💰 Silakan tunjukkan kode ini ke kasir untuk pembayaran.\n\nPesanan akan diproses setelah pembayaran dikonfirmasi.`;
        
        message.textContent = messageText;
        modal.classList.remove('hidden');
    }

    // ==================== ORDER STATUS FUNCTIONS ====================
    let orderStatusInterval = null;

    
    // ==================== ORDER DETAILS MODAL ====================
    function showOrderDetails() {
        const modal = document.getElementById('orderDetailsModal');
        const content = document.getElementById('orderDetailsContent');
        const subtitle = document.getElementById('orderDetailsSubtitle');
        
        // Ambil pesanan terbaru dari localStorage
        const myOrderCodes = getMyOrderCodes();
        
        if (myOrderCodes.length === 0) {
            // Tidak ada pesanan
            content.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-receipt text-3xl mb-3 opacity-50"></i>
                    <p class="font-medium">Belum ada pesanan</p>
                    <p class="text-sm text-gray-400 mt-1">Pesanan Anda akan muncul di sini setelah melakukan pemesanan</p>
                    <button onclick="hideOrderDetails()" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors">
                        Mulai Pesan
                    </button>
                </div>
            `;
            subtitle.textContent = 'Belum ada pesanan';
        } else {
            // Ada pesanan, tampilkan yang terbaru
            const latestOrderCode = myOrderCodes[myOrderCodes.length - 1];
            subtitle.textContent = `Kode: ${latestOrderCode}`;
            
            // Tampilkan loading dulu
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin w-8 h-8 border-2 border-indigo-600 border-t-transparent rounded-full mx-auto mb-3"></div>
                    <p class="text-gray-600">Memuat detail pesanan...</p>
                </div>
            `;
            
            // Load detail pesanan dari server (jika diperlukan) atau dari localStorage
            loadOrderDetails(latestOrderCode);
        }
        
        modal.classList.remove('hidden');
    }
    
    function hideOrderDetails() {
        document.getElementById('orderDetailsModal').classList.add('hidden');
    }
    
    function loadOrderDetails(orderCode) {
        const content = document.getElementById('orderDetailsContent');
        
        // Untuk sementara, tampilkan info pesanan dari localStorage
        // Nanti bisa ditambahkan fetch ke server jika diperlukan
        const orderData = localStorage.getItem(`order_${orderCode}`);
        
        if (orderData) {
            try {
                const order = JSON.parse(orderData);
                displayOrderDetails(order, orderCode);
            } catch (e) {
                displayOrderNotFound(orderCode);
            }
        } else {
            displayOrderNotFound(orderCode);
        }
    }
    
    function displayOrderDetails(orderData, orderCode) {
        const content = document.getElementById('orderDetailsContent');
        
        // Hitung total items
        const totalItems = Object.values(orderData.items || {}).reduce((sum, item) => sum + item.quantity, 0);
        
        let itemsHtml = '';
        if (orderData.items && Object.keys(orderData.items).length > 0) {
            let itemIndex = 1;
            for (const [menuId, item] in Object.entries(orderData.items)) {
                const temperature = item.temperature ? 
                    (item.temperature === 'ice' ? '🧊 Ice' : '🔥 Hot') : 
                    '🌡️ Normal';
                
                itemsHtml += `
                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold">
                                ${itemIndex}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">${item.name}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">${temperature}</span>
                                    <span class="text-xs text-gray-500">${item.quantity}x</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Rp ${parseInt(item.price).toLocaleString('id-ID')} / item</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-indigo-600">Rp ${parseInt(item.price * item.quantity).toLocaleString('id-ID')}</p>
                            </div>
                        </div>
                    </div>
                `;
                itemIndex++;
            }
        }
        
        content.innerHTML = `
            <div class="space-y-4">
                <!-- Order Info -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-gray-900">Nomor Pesanan</h4>
                        <span class="bg-indigo-600 text-white px-3 py-1 rounded-full text-sm font-mono">${orderCode}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Customer</p>
                            <p class="font-semibold">${orderData.customer_name || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Meja</p>
                            <p class="font-semibold">Meja ${orderData.table_id || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Items -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-gray-900">Item Pesanan</h4>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">${totalItems} item</span>
                    </div>
                    ${itemsHtml || '<p class="text-gray-500 text-center py-4">Tidak ada item</p>'}
                </div>
                
                <!-- Total -->
                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-4 border border-green-200">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="text-xl font-bold text-green-600">Rp ${parseInt(orderData.total_amount || 0).toLocaleString('id-ID')}</span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex gap-2 pt-2">
                    <a href="/orders/track/${orderCode}" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl text-center font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Lihat Detail
                    </a>
                    <button onclick="hideOrderDetails()" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
    }
    
    function displayOrderNotFound(orderCode) {
        // Langsung redirect ke halaman tracking
        window.location.href = `/orders/track/${orderCode}`;
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
            
            fetch(`/orders/${orderCode}/cancel`, {
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
                    showModernAlert('Pesanan berhasil dibatalkan! 👍', 'success');
                    // Reset cart dan temperature selections jika ada
                    cart = {};
                    tempQuantity = {};
                    selectedTemperature = {};
                    updateCartSummary();
                    updateAllQuantityDisplays();
                    
                    // Redirect ke halaman tracking pesanan
                    window.location.href = `/orders/track/${data.order.order_code}`;
                } else {
                    showModernAlert('Gagal batalkan pesanan: ' + data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showModernAlert('Ada masalah saat batalkan pesanan. Coba lagi ya! 🙏', 'error');
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
    
    // Load service charge percentage from API
    async function loadServiceChargePercentage() {
        try {
            const response = await fetch('/api/settings/service-charge');
            const data = await response.json();
            serviceChargePercentage = data.service_charge_percentage || 10;
            console.log('Service charge loaded:', serviceChargePercentage + '%');
        } catch (error) {
            console.error('Failed to load service charge:', error);
            serviceChargePercentage = 3; // Fallback to 3%
        }
    }

    // Carousel events
    document.addEventListener('DOMContentLoaded', function() {
        // Load service charge first
        loadServiceChargePercentage();
        
        // Update order badge on page load
        updateOrderBadge();
        
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

    // Quantity controls
    document.addEventListener('click', function(e) {
        // Quantity buttons - hanya update temporary quantity
        if (e.target.closest('.quantity-btn')) {
            const btn = e.target.closest('.quantity-btn');
            const menuId = btn.dataset.menuId;
            const isIncrease = btn.classList.contains('increase');
            
            // Get current temperature selection for this menu
            const currentTemp = selectedTemperature[menuId] || 'normal';
            const cartKey = `${menuId}_${currentTemp}`;
            

            
            // Initialize tempQuantity jika belum ada
            if (!tempQuantity[cartKey]) tempQuantity[cartKey] = 0;
            
            if (isIncrease) {
                // Cek stok sebelum menambah quantity - SIMPLE APPROACH
                const menuItem = btn.closest('.menu-item');
                
                if (menuItem) {
                    // Cari elemen yang mengandung "Stok:"
                    const allElements = menuItem.querySelectorAll('*');
                    let availableStock = null;
                    
                    for (let element of allElements) {
                        if (element.textContent && element.textContent.includes('Stok:')) {
                            const match = element.textContent.match(/Stok:\s*(\d+)/);
                            if (match) {
                                availableStock = parseInt(match[1]);
                                break;
                            }
                        }
                    }
                    
                    if (availableStock !== null) {
                        // Calculate total across ALL temperature variants for this menu
                        let totalInCartAllTemps = 0;
                        let totalTempAllTemps = 0;
                        
                        Object.keys(cart).forEach(key => {
                            if (key.startsWith(`${menuId}_`)) {
                                totalInCartAllTemps += cart[key] || 0;
                            }
                        });
                        
                        Object.keys(tempQuantity).forEach(key => {
                            if (key.startsWith(`${menuId}_`)) {
                                totalTempAllTemps += tempQuantity[key] || 0;
                            }
                        });
                        
                        const totalWanted = totalTempAllTemps + totalInCartAllTemps + 1;
                        
                        if (totalWanted > availableStock) {
                            console.log('STOCK VALIDATION TRIGGERED:', {
                                menuId,
                                availableStock,
                                totalInCartAllTemps,
                                totalTempAllTemps,
                                totalWanted
                            });
                            showModernAlert('Jumlah pesananmu melebihi stok kami! 😔', 'error');
                            return; // STOP - jangan tambah quantity
                        }
                    }
                }
                
                tempQuantity[cartKey]++;
            } else if (tempQuantity[cartKey] > 0) {
                tempQuantity[cartKey]--;
            }
            
            // Update display saja, belum masuk cart
            updateQuantityDisplay(menuId);
        }
        
        // Temperature selection
        if (e.target.closest('.temperature-btn')) {
            const btn = e.target.closest('.temperature-btn');
            const menuId = btn.dataset.menuId;
            const temperature = btn.dataset.temperature;
            
            // Update selected temperature for this menu
            selectedTemperature[menuId] = temperature;
            

            
            // Update quantity display to show quantity for this temperature
            updateQuantityDisplay(menuId);
            
            // Update button styles
            const temperatureContainer = btn.closest('.temperature-options');
            const allTempBtns = temperatureContainer.querySelectorAll('.temperature-btn');
            
            allTempBtns.forEach(tempBtn => {
                tempBtn.classList.remove('border-indigo-600', 'bg-indigo-600', 'text-white');
                tempBtn.classList.add('border-gray-300', 'bg-white', 'text-gray-600', 'hover:border-indigo-400');
            });
            
            btn.classList.remove('border-gray-300', 'bg-white', 'text-gray-600', 'hover:border-indigo-400');
            btn.classList.add('border-indigo-600', 'bg-indigo-600', 'text-white');
        }

        // Add to cart buttons - masukkan tempQuantity ke cart
        if (e.target.closest('.add-to-cart')) {
            const btn = e.target.closest('.add-to-cart');
            const menuId = btn.dataset.menuId;
            

            
            // Check if item is disabled
            const menuItem = btn.closest('.menu-item');
            if (menuItem && menuItem.classList.contains('disabled')) {
                showModernAlert('Menu ini lagi habis nih. Coba pilih menu lain ya! 😊', 'warning');
                return;
            }
            
            // Cek apakah ada quantity yang dipilih (dengan temperature variant)
            const currentTemp = selectedTemperature[menuId] || 'normal';
            const cartKey = `${menuId}_${currentTemp}`;
            const qty = tempQuantity[cartKey] || 0;
            if (qty === 0) {
                showModernAlert('Pilih jumlah pesanan dulu ya! Klik tombol + untuk menambah 😊', 'warning');
                return;
            }

            // Cek stok sebelum menambahkan ke cart - SIMPLE APPROACH
            const allElements = menuItem.querySelectorAll('*');
            let availableStock = null;
            
            for (let element of allElements) {
                if (element.textContent && element.textContent.includes('Stok:')) {
                    const match = element.textContent.match(/Stok:\s*(\d+)/);
                    if (match) {
                        availableStock = parseInt(match[1]);
                        break;
                    }
                }
            }
            
            if (availableStock !== null) {
                // Calculate total across ALL temperature variants for this menu
                let totalInCartAllTemps = 0;
                Object.keys(cart).forEach(key => {
                    if (key.startsWith(`${menuId}_`)) {
                        totalInCartAllTemps += cart[key] || 0;
                    }
                });
                
                const totalWanted = totalInCartAllTemps + qty;
                
                if (totalWanted > availableStock) {
                    console.log('ADD TO CART VALIDATION TRIGGERED:', {
                        menuId,
                        availableStock,
                        totalInCartAllTemps,
                        qty,
                        totalWanted
                    });
                    showModernAlert('Jumlah pesananmu melebihi stok kami! 😔', 'error');
                    return; // STOP - jangan tambah ke cart
                }
            }

            // Cek apakah menu memerlukan pilihan varian
            const hasTemperature = menuItem.dataset.hasTemperature === 'true';
            if (hasTemperature && !selectedTemperature[menuId]) {
                showModernAlert('Pilih varian minuman dulu ya! Ice atau Hot? 🧊🔥', 'warning');
                return;
            }
            
            // Gunakan cartKey yang sudah dihitung di atas
            
            if (!cart[cartKey]) {
                cart[cartKey] = 0;
            }
            cart[cartKey] += qty;
            
            // Reset tempQuantity setelah masuk cart
            tempQuantity[cartKey] = 0;
            

            
            updateQuantityDisplay(menuId);
            updateCartSummary();
            
            const originalText = btn.innerHTML;
            const originalClasses = btn.className;
            
            btn.innerHTML = '<i class="fas fa-check mr-1"></i>Ditambahkan';
            
            // Remove all color classes and add green
            btn.classList.remove('from-indigo-600', 'to-purple-600', 'bg-blue-600', 'hover:bg-blue-700');
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
            showModernAlert('Pilih nomor meja dulu ya! Biar kita tau mau kirim ke mana 😊', 'warning');
            return;
        }
        
        if (!customerName) {
            showModernAlert('Namanya siapa nih? Isi dulu dong biar kita bisa panggil 😄', 'warning');
            return;
        }
        
        if (Object.keys(cart).length === 0) {
            showModernAlert('Keranjangnya masih kosong. Yuk pilih menu favoritmu dulu! 🍽️', 'warning');
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
            showModernAlert('Ada menu yang stoknya habis nih. Hapus dulu ya dari keranjang 🙏', 'warning');
            return;
        }
        
        // Prepare order data with breakdown
        const totals = calculateTotal();
        currentOrderData = {
            table_id: parseInt(tableId),
            customer_name: customerName,
            payment_method: paymentMethod,
            items: cart, // Keep cart object with temperature variants (menuId_temperature format)
            subtotal: totals.subtotal,
            ppn: totals.ppn,
            serviceCharge: totals.serviceCharge,
            total_amount: totals.total
        };
        
        showPaymentModal(currentOrderData);
    });

    // Confirm payment
    document.getElementById('confirmPayment').addEventListener('click', function() {
        if (!currentOrderData) {
            showModernAlert('Data pesanan tidak ditemukan. Coba pesan lagi ya! 🙏', 'warning');
            return;
        }
        
        // Langsung proses tanpa loading overlay
        processOrder(currentOrderData);
    });

    // Success modal
    document.getElementById('closeSuccess').addEventListener('click', function() {
        document.getElementById('successModal').classList.add('hidden');
    });

    // Order details modal
    document.getElementById('footerOrderButton').addEventListener('click', showOrderDetails);
    document.getElementById('closeOrderDetails').addEventListener('click', hideOrderDetails);

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
            if (e.tarsget.id === 'successModal') document.getElementById('successModal').classList.add('hidden');
        }
        if (e.target.id === 'detailsModal' || (e.target.closest('#detailsModal') && !e.target.closest('.bg-white'))) {
            if (e.target.id === 'detailsModal') document.getElementById('detailsModal').classList.add('hidden');
        }
        if (e.target.id === 'orderDetailsModal' || (e.target.closest('#orderDetailsModal') && !e.target.closest('.bg-white'))) {
            if (e.target.id === 'orderDetailsModal') hideOrderDetails();
        }

    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCart();
            hidePayment();
            hideOrderDetails();
            document.getElementById('successModal').classList.add('hidden');
            document.getElementById('detailsModal').classList.add('hidden');
        }
    });
    </script>
</body>
</html>
