<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Furawa Cafe</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Furawa Admin">
    <meta name="description" content="Admin Panel untuk Furawa Cafe - Kelola pesanan, menu, dan laporan">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/images/icon-512x512.png">
    <link rel="apple-touch-icon" href="/images/icon-192x192.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Icons - Font Awesome with fallback -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- PWA Initialization -->
    <script src="/pwa-init.js" defer></script>
    
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Animation for alerts */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-blue-800 text-white w-64 space-y-6 py-7 px-2 fixed inset-y-0 left-0 transform -translate-x-full lg:relative lg:translate-x-0 transition-all duration-300 ease-in-out z-50">
            <!-- Logo -->
            <div class="text-white flex items-center space-x-2 px-4">
                <i class="fas fa-utensils text-2xl"></i>
                <span class="text-2xl font-bold">Furawa Cafe</span>
            </div>

            <!-- Navigation -->
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="block py-2.5 px-4 rounded transition-all duration-200 hover:bg-blue-700 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="{{ route('admin.menus.index') }}" 
                   class="block py-2.5 px-4 rounded transition-all duration-200 hover:bg-blue-700 hover:text-white {{ request()->routeIs('admin.menus.*') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-list mr-2"></i>Manajemen Menu
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="block py-2.5 px-4 rounded transition-all duration-200 hover:bg-blue-700 hover:text-white {{ request()->routeIs('admin.orders.*') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-shopping-cart mr-2"></i>Pesanan
                    @php
                        // Hitung jumlah pesanan pending dan processing
                        use App\Models\Order;
                        $pendingCount = Order::where('status', 'pending')->count();
                        $processingCount = Order::where('status', 'processing')->count();
                        $totalNotifications = $pendingCount + $processingCount;
                    @endphp
                    @if($totalNotifications > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1 animate-pulse">
                        {{ $totalNotifications }}
                    </span>
                    @endif
                </a>
                <a href="{{ route('admin.reports.index') }}" 
                   class="block py-2.5 px-4 rounded transition-all duration-200 hover:bg-blue-700 hover:text-white {{ request()->routeIs('admin.reports.*') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-chart-bar mr-2"></i>Laporan
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="block py-2.5 px-4 rounded transition-all duration-200 hover:bg-blue-700 hover:text-white {{ request()->routeIs('admin.users.*') ? 'bg-blue-700' : '' }}">
                    <i class="fas fa-users-cog mr-2"></i>Akun Admin
                </a>
            </nav>
            
            <!-- User Info & Logout -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-blue-700">
                <div class="p-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div class="flex-1 truncate">
                            <p class="text-sm font-medium">{{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-blue-300 truncate">{{ Auth::user()->email ?? 'admin@furawa.com' }}</p>
                        </div>
                    </div>
                    
                    <!-- Logout Button -->
                    <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Bar -->
            <header class="bg-white shadow">
                <div class="flex justify-between items-center py-3 px-4 sm:px-6">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <!-- Mobile Menu Button (inline with title) -->
                        <button id="mobileMenuButtonInline" class="lg:hidden text-gray-700 bg-white rounded-lg p-2.5 hover:bg-gray-50 transition-all duration-200 border border-gray-200 flex-shrink-0 inline-flex items-center justify-center h-11 w-11">
                            <i class="fas fa-bars text-lg leading-none"></i>
                        </button>
                        
                        <h1 class="text-xl sm:text-2xl md:text-2xl font-bold text-gray-800 uppercase tracking-wide truncate inline-flex items-center h-11">@yield('title', 'Dashboard')</h1>
                        @if(session('success'))
                        <div class="ml-4 bg-green-100 border border-green-400 text-green-700 px-3 py-1 rounded animate-fadeIn">
                            <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
                        </div>
                        @endif
                        @if(session('error'))
                        <div class="ml-4 bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded animate-fadeIn">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ session('error') }}
                        </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700 hidden md:inline">Hai, {{ Auth::user()->name ?? 'Admin' }}</span>
                        
                        <!-- Notification Bell -->
                        <div class="relative" id="notificationDropdown">
                            <button onclick="toggleNotificationDropdown()" class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-bell text-xl"></i>
                                @php
                                    // Gunakan Order yang sudah di-import di atas (sidebar)
                                    $headerPendingCount = \App\Models\Order::where('status', 'pending')->count();
                                    $headerProcessingCount = \App\Models\Order::where('status', 'processing')->count();
                                    $headerTotalNotifications = $headerPendingCount + $headerProcessingCount;
                                @endphp
                                @if($headerTotalNotifications > 0)
                                <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse" id="notificationBadge">
                                    {{ $headerTotalNotifications }}
                                </span>
                                @endif
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="notificationMenu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
                                <div class="p-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="font-semibold text-gray-800">Notifikasi Pesanan</h3>
                                    <p class="text-xs text-gray-500 mt-1">Pesanan yang perlu diproses</p>
                                </div>
                                
                                <div id="notificationList" class="divide-y divide-gray-100">
                                    <!-- Will be populated by JavaScript -->
                                    <div class="p-4 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                        <p class="text-sm">Memuat notifikasi...</p>
                                    </div>
                                </div>
                                
                                <div class="p-3 border-t border-gray-200 bg-gray-50">
                                    <a href="{{ route('admin.orders.index') }}" class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Lihat Semua Pesanan →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-6">
                <!-- Global Loading Overlay -->
                <div id="globalLoadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="bg-white rounded-xl p-6 text-center shadow-2xl">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-600 font-medium">Memproses...</p>
                        </div>
                    </div>
                </div>
                
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-3 px-6">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <div>
                        <i class="fas fa-coffee mr-1"></i>
                        <span>Furawa Cafe Admin Panel</span>
                    </div>
                    <div>
                        <span id="currentTime"></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        // CSRF Token - PASTIKAN BISA DIAKSES DARI MANA SAJA
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Mobile menu toggle function
        function toggleMobileMenu() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
        
        // Event listener for inline button only
        const mobileMenuButtonInline = document.getElementById('mobileMenuButtonInline');
        if (mobileMenuButtonInline) {
            mobileMenuButtonInline.addEventListener('click', toggleMobileMenu);
        }
        
        if (overlay) {
            overlay.addEventListener('click', toggleMobileMenu);
        }
        
        // Close menu when clicking on a link (mobile)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleMobileMenu();
                }
            });
        });
        
        // Global loading functions
        window.showGlobalLoading = function() {
            document.getElementById('globalLoadingOverlay').classList.remove('hidden');
        };
        
        window.hideGlobalLoading = function() {
            document.getElementById('globalLoadingOverlay').classList.add('hidden');
        };
        
        // Update current time
        function updateCurrentTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = `${dateString} • ${timeString}`;
            }
        }

// ==================== ORDER STATUS FUNCTIONS ====================

/**
 * Update order status via AJAX - FIXED VERSION
 * @param {number} orderId - ID pesanan
 * @param {string} status - Status baru ('pending', 'processing', 'completed', 'cancelled')
 * @param {string} customMessage - Pesan konfirmasi kustom (opsional)
 */
window.updateOrderStatus = async function(orderId, status, customMessage = null) {
    
    // Status mapping untuk pesan konfirmasi - TIDAK ADA 'paid' di sini!
    const statusMessages = {
        'pending': 'Menunggu Konfirmasi',
        'processing': 'Sedang Diproses', 
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan'
    };
    
    // Validasi: status TIDAK BOLEH 'paid'
    if (status === 'paid') {
        alert('ERROR: "paid" HANYA untuk payment_status! Gunakan tombol "Sudah Bayar" untuk menandai pembayaran.');
        return { success: false, message: 'Status tidak valid' };
    }
    
    // Validasi status yang diperbolehkan
    const allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!allowedStatuses.includes(status)) {
        alert(`ERROR: Status "${status}" tidak valid. Gunakan: pending, processing, completed, atau cancelled`);
        return { success: false, message: 'Status tidak valid' };
    }
    
    // Pesan konfirmasi
    const message = customMessage || `Yakin ingin mengubah status pesanan menjadi "${statusMessages[status] || status}"?`;
    
    if (!confirm(message)) {
        return { success: false, message: 'Dibatalkan oleh pengguna' };
    }
    
    try {
        showGlobalLoading();
        
        console.log('Updating order status:', { orderId, status });
        
        const postData = {
            status: String(status), // Pastikan string
            _token: window.csrfToken
        };
        
        const response = await fetch(`/admin/orders/${orderId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(postData)
        });
        
        // Cek response
        const data = await response.json();
        
        hideGlobalLoading();
        
        if (!response.ok || !data.success) {
            // Tampilkan notifikasi warning/error yang user-friendly
            const message = data.message || 'Gagal memperbarui status';
            const type = data.type || 'error';
            
            if (type === 'warning') {
                showWarningMessage(message);
            } else {
                showErrorMessage(message);
            }
            
            return { success: false, message: message };
        }
        
        showSuccessMessage(data.message || 'Status berhasil diperbarui!');
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        
        return data;
        
    } catch (error) {
        hideGlobalLoading();
        
        console.error('Update order status error:', error);
        
        let errorMessage = 'Terjadi kesalahan saat memperbarui status pesanan';
        
        if (error.message.includes('1265') || error.message.includes('Data truncated')) {
            errorMessage = `⚠️ ERROR DATABASE: Status "${status}" tidak valid di database.`;
            alert(`⚠️ PERHATIAN!\n\nDatabase error! Status "${status}" tidak valid.\n\nStatus yang valid:\n- pending (Menunggu)\n- processing (Diproses)\n- completed (Selesai)\n- cancelled (Dibatalkan)\n\n"paid" hanya untuk payment_status!`);
        } else {
            errorMessage = error.message;
        }
        
        handleAjaxError(new Error(errorMessage));
        
        return { 
            success: false, 
            message: errorMessage 
        };
    }
};

/**
 * Mark order as paid via AJAX - HANYA untuk payment_status
 * @param {number} orderId - ID pesanan
 */
window.markOrderAsPaid = async function(orderId) {
    if (!confirm('Yakin ingin menandai pesanan ini sebagai SUDAH DIBAYAR?')) {
        return;
    }
    
    try {
        showGlobalLoading();
        
        const response = await fetch(`/admin/orders/${orderId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                payment_status: 'paid', // INI YANG BENAR!
                _token: window.csrfToken
            })
        });
        
        const data = await response.json();
        
        hideGlobalLoading();
        
        if (!response.ok) {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }
        
        if (!data.success) {
            throw new Error(data.message || 'Gagal memperbarui status pembayaran');
        }
        
        showSuccessMessage('Status pembayaran berhasil diperbarui! Pesanan ditandai sebagai LUNAS.');
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        
        return data;
        
    } catch (error) {
        hideGlobalLoading();
        console.error('Mark as paid error:', error);
        
        if (error.message.includes('1265') || error.message.includes('Data truncated')) {
            alert('⚠️ ERROR DATABASE: Kolom payment_status tidak menerima value "paid".\n\nSOLUSI:\n1. Buka phpMyAdmin\n2. Edit tabel orders\n3. Edit kolom payment_status\n4. Ubah menjadi ENUM dengan nilai: pending, paid, unpaid, failed');
        } else {
            handleAjaxError(error);
        }
        
        return { success: false, message: error.message };
    }
};

/**
 * Update payment status via AJAX
 * @param {number} orderId - ID pesanan
 * @param {string} paymentStatus - Status pembayaran baru ('paid', 'unpaid', 'pending', 'failed')
 */
window.updatePaymentStatus = async function(orderId, paymentStatus) {
    const statusMessages = {
        'paid': 'Lunas',
        'unpaid': 'Belum Bayar', 
        'pending': 'Menunggu Pembayaran',
        'failed': 'Gagal'
    };
    
    if (!confirm(`Yakin ingin mengubah status pembayaran menjadi "${statusMessages[paymentStatus] || paymentStatus}"?`)) {
        return;
    }
    
    try {
        showGlobalLoading();
        
        const response = await fetch(`/admin/orders/${orderId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                payment_status: paymentStatus,
                _token: window.csrfToken
            })
        });
        
        const data = await response.json();
        
        hideGlobalLoading();
        
        if (!response.ok) {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }
        
        if (!data.success) {
            throw new Error(data.message || 'Gagal memperbarui status pembayaran');
        }
        
        showSuccessMessage('Status pembayaran berhasil diperbarui!');
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        
        return data;
        
    } catch (error) {
        hideGlobalLoading();
        console.error('Update payment status error:', error);
        handleAjaxError(error);
        return { success: false, message: error.message };
    }
};

// ==================== INITIALIZE ORDER EVENTS ====================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all order status buttons
    initializeOrderButtons();
    
    // Auto refresh notifications every 30 seconds
    setInterval(updateOrderNotifications, 30000);
    
    // Initial notification update
    updateOrderNotifications();
});

/**
 * Initialize all order action buttons
 */
function initializeOrderButtons() {
    // Order status update buttons (TIDAK termasuk paid!)
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const orderId = this.dataset.orderId || this.dataset.id;
            const status = this.dataset.status;
            const statusText = this.dataset.statusText || this.textContent.trim();
            
            if (!orderId || !status) {
                console.error('Missing orderId or status:', { orderId, status });
                handleAjaxError(new Error('Tombol tidak dikonfigurasi dengan benar'));
                return;
            }
            
            // Validasi: jangan allow paid
            if (status === 'paid') {
                alert('ERROR: Gunakan tombol "Sudah Bayar" untuk menandai pembayaran!');
                return;
            }
            
            updateOrderStatus(orderId, status, `Yakin ingin mengubah status pesanan menjadi "${statusText}"?`);
        });
    });
    
    // Payment status buttons
    document.querySelectorAll('.update-payment-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const paymentStatus = this.dataset.paymentStatus;
            updatePaymentStatus(orderId, paymentStatus);
        });
    });
    
    // Mark as paid buttons
    document.querySelectorAll('.mark-as-paid-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            markOrderAsPaid(orderId);
        });
    });
}

/**
 * Update order notification badges
 */
async function updateOrderNotifications() {
    try {
        const response = await fetch('/admin/orders/notifications', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        
        if (data.success) {
            const total = data.pending_count + data.processing_count;
            
            // Update sidebar badge
            const sidebarBadge = document.querySelector('#sidebar a[href*="orders"] .bg-red-500');
            if (sidebarBadge) {
                sidebarBadge.textContent = total;
                sidebarBadge.classList.toggle('hidden', total === 0);
            }
            
            // Update header notification badge
            const headerBadge = document.getElementById('notificationBadge');
            if (headerBadge) {
                headerBadge.textContent = total;
                if (total === 0) {
                    headerBadge.classList.add('hidden');
                } else {
                    headerBadge.classList.remove('hidden');
                }
            }
            
            // Update notification list
            updateNotificationList(data.recent_orders || []);
        }
        
    } catch (error) {
        console.log('Failed to update notifications:', error);
    }
}

/**
 * Toggle notification dropdown
 */
function toggleNotificationDropdown() {
    const menu = document.getElementById('notificationMenu');
    menu.classList.toggle('hidden');
    
    // Load notifications when opened
    if (!menu.classList.contains('hidden')) {
        loadNotifications();
    }
}

/**
 * Load notifications
 */
async function loadNotifications() {
    try {
        const response = await fetch('/admin/orders/notifications', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load notifications');
        
        const data = await response.json();
        
        if (data.success) {
            updateNotificationList(data.recent_orders || []);
        }
        
    } catch (error) {
        console.error('Error loading notifications:', error);
        const listElement = document.getElementById('notificationList');
        if (listElement) {
            listElement.innerHTML = `
                <div class="p-4 text-center text-red-500">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p class="text-sm">Gagal memuat notifikasi</p>
                </div>
            `;
        }
    }
}

/**
 * Update notification list UI
 */
function updateNotificationList(orders) {
    const listElement = document.getElementById('notificationList');
    if (!listElement) return;
    
    if (orders.length === 0) {
        listElement.innerHTML = `
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-check-circle text-4xl mb-3 text-green-400"></i>
                <p class="text-sm font-medium">Semua Pesanan Sudah Diproses</p>
                <p class="text-xs mt-1">Tidak ada pesanan pending atau processing</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    orders.forEach(order => {
        const statusColor = order.status === 'pending' ? 'yellow' : 'blue';
        const statusIcon = order.status === 'pending' ? 'clock' : 'spinner';
        const statusText = order.status === 'pending' ? 'Pending' : 'Processing';
        
        html += `
            <a href="/admin/orders/${order.id}" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-${statusColor}-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-${statusIcon} text-${statusColor}-600"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                ${order.customer_name}
                            </p>
                            <span class="text-xs px-2 py-1 rounded-full bg-${statusColor}-100 text-${statusColor}-700">
                                ${statusText}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-receipt mr-1"></i>${order.order_code}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-clock mr-1"></i>${formatTimeAgo(order.created_at)}
                        </p>
                    </div>
                </div>
            </a>
        `;
    });
    
    listElement.innerHTML = html;
}

/**
 * Format time ago
 */
function formatTimeAgo(dateString) {
    try {
        // Parse date string (support multiple formats)
        let date;
        
        // Try parsing ISO format or MySQL datetime format
        if (dateString.includes('T')) {
            // ISO format: 2025-12-06T10:30:00.000000Z
            date = new Date(dateString);
        } else if (dateString.includes('-')) {
            // MySQL format: 2025-12-06 10:30:00
            date = new Date(dateString.replace(' ', 'T'));
        } else {
            date = new Date(dateString);
        }
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            console.warn('Invalid date:', dateString);
            return 'Baru saja';
        }
        
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 0) return 'Baru saja'; // Future date
        if (seconds < 60) return 'Baru saja';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' menit lalu';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' jam lalu';
        
        const days = Math.floor(seconds / 86400);
        if (days === 1) return '1 hari lalu';
        return days + ' hari lalu';
    } catch (error) {
        console.error('Error formatting time:', error, dateString);
        return 'Baru saja';
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notificationDropdown');
    const menu = document.getElementById('notificationMenu');
    
    if (dropdown && menu && !dropdown.contains(event.target)) {
        menu.classList.add('hidden');
    }
});

/**
 * Function untuk menampilkan instruksi perbaikan database
 */
window.fixDatabaseIssue = function(statusValue) {
    const instructions = `
⚠️ PERBAIKAN ERROR DATABASE ⚠️

MASALAH: Kolom 'status' atau 'payment_status' tidak menerima value yang benar.

KOLOM STATUS (order status):
- pending (Menunggu)
- processing (Diproses)  
- completed (Selesai)
- cancelled (Dibatalkan)

KOLOM PAYMENT_STATUS (status pembayaran):
- pending (Menunggu)
- paid (Lunas)
- unpaid (Belum Bayar)
- failed (Gagal)

SOLUSI:

1. Buka phpMyAdmin
2. Pilih database Anda
3. Klik tabel 'orders'
4. Klik "Structure"

5. Edit kolom 'status':
   - Type: ENUM('pending','processing','completed','cancelled')
   - Default: pending

6. Edit kolom 'payment_status':
   - Type: ENUM('pending','paid','unpaid','failed')
   - Default: pending

7. Klik Save

SETELAH PERBAIKAN:
- Gunakan tombol "Proses" untuk status processing
- Gunakan tombol "Sudah Bayar" untuk payment_status paid
- Gunakan tombol "Selesai" untuk status completed
- Gunakan tombol "Batalkan" untuk status cancelled
    `;
    
    alert(instructions);
};
// ==================== FIX DATABASE SCRIPT ====================

/**
 * Quick database fix via AJAX
 */
window.quickFixDatabase = async function() {
    if (!confirm('Yakin ingin memperbaiki struktur database secara otomatis?\nIni akan mengubah kolom status dan payment_status menjadi ENUM yang benar.')) {
        return;
    }
    
    try {
        showGlobalLoading();
        
        const response = await fetch('/admin/fix-database', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        hideGlobalLoading();
        
        if (data.success) {
            showSuccessMessage('Database berhasil diperbaiki! Silakan refresh halaman.');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(data.message || 'Gagal memperbaiki database');
        }
        
    } catch (error) {
        hideGlobalLoading();
        handleAjaxError(error);
    }
};

/**
 * Add quick fix button to page
 */
function addQuickFixButton() {
    // Check if we're on orders page
    if (!window.location.href.includes('/admin/orders')) return;
    
    // Add fix button to header
    const header = document.querySelector('header .flex.justify-between');
    if (header) {
        const fixButton = document.createElement('button');
        fixButton.className = 'bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition ml-4';
        fixButton.innerHTML = '<i class="fas fa-database mr-2"></i>Fix Database Error';
        fixButton.onclick = function() {
            window.fixDatabaseIssue('paid');
        };
        
        header.appendChild(fixButton);
    }
}
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentTime();
            setInterval(updateCurrentTime, 1000);
            
            // Auto hide ONLY session alerts (not all elements with bg-green/red)
            setTimeout(() => {
                // Only hide alerts in header, not status badges or other elements
                const headerAlerts = document.querySelectorAll('header [class*="bg-green-100"], header [class*="bg-red-100"]');
                headerAlerts.forEach(alert => {
                    if (alert.textContent.includes('berhasil') || alert.textContent.includes('Gagal') || alert.textContent.includes('error')) {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    }
                });
            }, 5000);
            
            // Close sidebar on window resize if needed
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
        
        // Global error handler for AJAX requests
        window.handleAjaxError = function(error, customMessage = null) {
            console.error('AJAX Error:', error);
            
            let message = customMessage || 'Terjadi kesalahan. Silakan coba lagi.';
            
            if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                message = 'Koneksi internet terputus. Periksa koneksi Anda.';
            } else if (error.message.includes('401') || error.message.includes('403')) {
                message = 'Sesi Anda telah habis. Silakan login kembali.';
                setTimeout(() => window.location.href = '/admin/login', 2000);
            } else if (error.message.includes('404')) {
                message = 'Data tidak ditemukan.';
            } else if (error.message.includes('500')) {
                message = 'Terjadi kesalahan server. Silakan coba lagi nanti.';
            }
            
            // Show error alert
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50 animate-fadeIn';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <strong class="font-bold">Error!</strong>
                    <span class="ml-2">${message}</span>
                </div>
            `;
            
            document.body.appendChild(errorDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                errorDiv.style.transition = 'opacity 0.5s';
                errorDiv.style.opacity = '0';
                setTimeout(() => errorDiv.remove(), 500);
            }, 5000);
        };
        
        // Global success handler
        window.showSuccessMessage = function(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50 animate-fadeIn';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong class="font-bold">Sukses!</strong>
                    <span class="ml-2">${message}</span>
                </div>
            `;
            
            document.body.appendChild(successDiv);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                successDiv.style.transition = 'opacity 0.5s';
                successDiv.style.opacity = '0';
                setTimeout(() => successDiv.remove(), 500);
            }, 3000);
        };
        
        // Global warning handler
        window.showWarningMessage = function(message) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded shadow-lg z-50 animate-fadeIn';
            warningDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="ml-2">${message}</span>
                </div>
            `;
            
            document.body.appendChild(warningDiv);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                warningDiv.style.transition = 'opacity 0.5s';
                warningDiv.style.opacity = '0';
                setTimeout(() => warningDiv.remove(), 500);
            }, 4000);
        };
        
        // Global error handler
        window.showErrorMessage = function(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50 animate-fadeIn';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong class="font-bold">Error!</strong>
                    <span class="ml-2">${message}</span>
                </div>
            `;
            
            document.body.appendChild(errorDiv);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                errorDiv.style.transition = 'opacity 0.5s';
                errorDiv.style.opacity = '0';
                setTimeout(() => errorDiv.remove(), 500);
            }, 4000);
        };
    </script>
    
    @stack('scripts')
</body>
</html>