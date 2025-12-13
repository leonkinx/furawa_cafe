@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- PWA Install Button -->
<div id="pwa-install-button" style="display: none;" class="fixed bottom-6 right-6 z-50">
    <button class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 flex items-center gap-3 hover:scale-105">
        <i class="fas fa-download text-xl"></i>
        <div class="text-left">
            <p class="font-semibold text-sm">Install Aplikasi</p>
            <p class="text-xs opacity-90">Akses lebih cepat</p>
        </div>
    </button>
</div>

<div class="mb-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-wide">Overview Hari Ini</h2>
        <p class="text-gray-600 mt-2">Statistik dan Performa Restoran</p>
    </div>
    

    <!-- Stats Grid - Elegant Design -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- 1. Pendapatan Hari Ini -->
        <div class="group relative bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl shadow-lg">
                        <i class="fas fa-money-bill-wave text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-lg md:text-2xl font-bold text-gray-800 break-words">Rp {{ number_format($stats['today_income'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Pendapatan Hari Ini</h3>
                <div class="mt-2 flex items-center text-xs text-blue-700">
                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                    <span class="break-words">{{ $stats['completed_today'] ?? 0 }} pesanan selesai</span>
                </div>
            </div>
        </div>

        <!-- 2. Belum Bayar (Actionable) -->
        <a href="{{ route('admin.orders.index') }}?filter=unpaid" class="group relative bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-red-400 to-pink-500 rounded-xl shadow-lg">
                        <i class="fas fa-exclamation-circle text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $stats['unpaid_orders'] ?? 0 }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Belum Bayar</h3>
                <div class="mt-2 flex items-center text-xs text-red-700">
                    <i class="fas fa-circle text-red-500 mr-2 animate-pulse"></i>
                    <span class="break-words">Perlu ditagih</span>
                </div>
            </div>
            @if(($stats['unpaid_orders'] ?? 0) > 0)
            <div class="absolute bottom-4 right-4 text-red-400 group-hover:translate-x-1 transition-transform">
                <i class="fas fa-arrow-right"></i>
            </div>
            @endif
        </a>

        <!-- 3. Sedang Diproses (Actionable) -->
        <a href="{{ route('admin.orders.index') }}?filter=processing" class="group relative bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer" id="newOrderCard">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl shadow-lg">
                        <i class="fas fa-spinner text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800" id="newOrdersCount">{{ $stats['processing_orders'] ?? 0 }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Sedang Diproses</h3>
                <div class="mt-2 flex items-center text-xs text-yellow-700">
                    <i class="fas fa-circle text-yellow-500 mr-2 animate-pulse"></i>
                    <span class="break-words">Perlu diselesaikan</span>
                </div>
            </div>
            @if(($stats['processing_orders'] ?? 0) > 0)
            <div class="absolute bottom-4 right-4 text-yellow-400 group-hover:translate-x-1 transition-transform">
                <i class="fas fa-arrow-right"></i>
            </div>
            @endif
        </a>

        <!-- 4. Selesai Hari Ini (Achievement) -->
        <div class="group relative bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-4 md:p-6">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div class="p-2 md:p-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl shadow-lg">
                        <i class="fas fa-check-circle text-white text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl md:text-3xl font-bold text-gray-800">{{ $stats['completed_today'] ?? 0 }}</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wide break-words">Selesai Hari Ini</h3>
                <div class="mt-2 flex items-center text-xs text-green-700">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="break-words">Pesanan completed</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Pendapatan Sederhana -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2 md:mb-0">Grafik Pendapatan</h3>
        <div class="flex space-x-2">
            <select id="chartType" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="daily">Harian (7 Hari)</option>
                <option value="monthly">Bulanan (12 Bulan)</option>
                <option value="yearly">Tahunan (5 Tahun)</option>
            </select>
        </div>
    </div>
    <div class="h-80 relative">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Produk Terlaris -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
            <span class="text-sm text-gray-500">Bulan Ini</span>
        </div>
        <div class="space-y-4">
            @if($best_sellers && count($best_sellers) > 0)
                @foreach($best_sellers as $menu)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-utensils text-white text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-900">{{ $menu->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $menu->total_sold }} terjual</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($menu->total_revenue, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            @else
            <div class="text-center py-8 text-gray-500">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-chart-bar text-2xl text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-700">Belum Ada Data Penjualan</p>
                <p class="text-xs text-gray-500 mt-1">Data akan muncul setelah ada transaksi</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Pesanan Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
        </div>
        <div class="space-y-4">
            <div class="text-center py-8 text-gray-500">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-receipt text-2xl text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-700">Belum Ada Pesanan</p>
                <p class="text-xs text-gray-500 mt-1">Pesanan baru akan muncul di sini</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.menus.create') }}" class="bg-blue-50 p-4 rounded-lg text-center hover:bg-blue-100 transition duration-200">
            <i class="fas fa-plus text-blue-600 text-xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Tambah Menu</p>
        </a>
        <a href="{{ route('admin.menus.index') }}" class="bg-green-50 p-4 rounded-lg text-center hover:bg-green-100 transition duration-200">
            <i class="fas fa-utensils text-green-600 text-xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Manajemen Menu</p>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="bg-yellow-50 p-4 rounded-lg text-center hover:bg-yellow-100 transition duration-200">
            <i class="fas fa-chart-bar text-yellow-600 text-xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Lihat Laporan</p>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="bg-purple-50 p-4 rounded-lg text-center hover:bg-purple-100 transition duration-200">
            <i class="fas fa-shopping-cart text-purple-600 text-xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Pesanan</p>
        </a>
    </div>
</div>

<script>
// Data grafik dari controller
const chartData = {
    daily: {
        labels: @json($chart_data['daily']['labels'] ?? []),
        data: @json($chart_data['daily']['data'] ?? [])
    },
    monthly: {
        labels: @json($chart_data['monthly']['labels'] ?? []),
        data: @json($chart_data['monthly']['data'] ?? [])
    },
    yearly: {
        labels: @json($chart_data['yearly']['labels'] ?? []),
        data: @json($chart_data['yearly']['data'] ?? [])
    }
};

let revenueChart;

function initChart(type = 'daily') {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    if (revenueChart) {
        revenueChart.destroy();
    }

    const data = chartData[type];
    
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
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
}

document.getElementById('chartType').addEventListener('change', function() {
    initChart(this.value);
});

document.addEventListener('DOMContentLoaded', function() {
    initChart('daily');
    
    // Init audio on page load
    try {
        initAudio();
    } catch (error) {
        // Silent error
    }
    
    // Auto-refresh untuk notifikasi pesanan baru
    checkNewOrders();
    setInterval(checkNewOrders, 10000); // Check setiap 10 detik
});

// ==================== NOTIFIKASI PESANAN BARU ====================
// Track TOTAL notifications (pending + processing) untuk notifikasi
let lastTotalCount = {{ $stats['total_notifications'] ?? 0 }};
let audioInitialized = false;
let audioContext = null;

// Initialize audio context on first user interaction
function initAudio() {
    if (!audioInitialized) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        audioInitialized = true;
    }
}

// Auto-init audio on any user interaction
document.addEventListener('click', initAudio, { once: true });
document.addEventListener('keydown', initAudio, { once: true });

// Play notification sound - VOICE VERSION
function playNotificationSound() {
    try {
        // FORCE INIT audio context jika belum
        if (!audioContext) {
            initAudio();
        }
        
        // Resume if suspended
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
        
        // Play beep first untuk menarik perhatian
        playAttentionBeep();
        
        // Lalu play voice notification
        setTimeout(() => {
            playVoiceNotification();
        }, 500);
    } catch (error) {
        showVisualAlert();
    }
}

// Play voice notification menggunakan Web Speech API
function playVoiceNotification() {
    if ('speechSynthesis' in window) {
        // Cancel any ongoing speech
        window.speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance('Woi! Ada pesanan baru masuk!');
        
        // Set voice properties
        utterance.lang = 'id-ID'; // Bahasa Indonesia
        utterance.rate = 1.1; // Sedikit lebih cepat
        utterance.pitch = 1.2; // Sedikit lebih tinggi (lebih urgent)
        utterance.volume = 1.0; // Volume maksimal
        
        // Speak!
        window.speechSynthesis.speak(utterance);
    } else {
        playDingDong();
    }
}

// Play attention beep (suara pendek untuk menarik perhatian)
function playAttentionBeep() {
    try {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
        
        // Triple beep cepat: BEEP-BEEP-BEEP
        const beeps = [
            { freq: 1000, delay: 0, duration: 0.1 },
            { freq: 1000, delay: 150, duration: 0.1 },
            { freq: 1200, delay: 300, duration: 0.15 }
        ];
        
        beeps.forEach(beep => {
            setTimeout(() => {
                const osc = audioContext.createOscillator();
                const gain = audioContext.createGain();
                
                osc.connect(gain);
                gain.connect(audioContext.destination);
                
                osc.frequency.value = beep.freq;
                osc.type = 'square'; // Suara lebih tajam
                
                const now = audioContext.currentTime;
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + beep.duration);
                
                osc.start(now);
                osc.stop(now + beep.duration);
            }, beep.delay);
        });
    } catch (error) {
        // Silent error
    }
}

function playDingDong() {
    // Melodi notifikasi yang lebih panjang dan menarik
    // Pattern: C-E-G-C (naik) lalu C-G-E-C (turun) - seperti lonceng
    const melody = [
        // Naik (ascending)
        { freq: 523, delay: 0, duration: 0.25, volume: 0.3 },      // C5
        { freq: 659, delay: 250, duration: 0.25, volume: 0.35 },   // E5
        { freq: 784, delay: 500, duration: 0.25, volume: 0.4 },    // G5
        { freq: 1047, delay: 750, duration: 0.4, volume: 0.45 },   // C6 (puncak)
        
        // Turun (descending)
        { freq: 1047, delay: 1200, duration: 0.25, volume: 0.4 },  // C6
        { freq: 784, delay: 1450, duration: 0.25, volume: 0.35 },  // G5
        { freq: 659, delay: 1700, duration: 0.25, volume: 0.3 },   // E5
        { freq: 523, delay: 1950, duration: 0.5, volume: 0.35 }    // C5 (akhir, lebih panjang)
    ];
    
    melody.forEach(note => {
        setTimeout(() => {
            const osc = audioContext.createOscillator();
            const gain = audioContext.createGain();
            
            osc.connect(gain);
            gain.connect(audioContext.destination);
            
            osc.frequency.value = note.freq;
            osc.type = 'sine'; // Suara lembut seperti lonceng
            
            const now = audioContext.currentTime;
            
            // Envelope: attack - sustain - release
            gain.gain.setValueAtTime(0, now);
            gain.gain.linearRampToValueAtTime(note.volume, now + 0.02); // Attack
            gain.gain.exponentialRampToValueAtTime(0.01, now + note.duration); // Release
            
            osc.start(now);
            osc.stop(now + note.duration);
        }, note.delay);
    });
}

function showVisualAlert() {
    // Fallback visual notification
    const alertDiv = document.createElement('div');
    alertDiv.className = 'fixed top-4 right-4 bg-purple-600 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-bounce';
    alertDiv.innerHTML = '<i class="fas fa-bell mr-2"></i> PESANAN BARU MASUK!';
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Check pesanan baru - IMPROVED VERSION
function checkNewOrders() {
    fetch('/admin/orders/notifications', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Track TOTAL NOTIFICATIONS (pending + processing) untuk notifikasi
            const currentTotalCount = data.total_notifications || (data.pending_count + data.processing_count);
            const currentProcessingCount = data.processing_count || 0;
            
            // Update counter di UI (card "Sedang Diproses")
            const counterElement = document.getElementById('newOrdersCount');
            if (counterElement) {
                counterElement.textContent = currentProcessingCount;
            }
            
            // Jika ada pesanan baru masuk (pending atau processing bertambah)
            if (currentTotalCount > lastTotalCount) {
                const newOrders = currentTotalCount - lastTotalCount;
                
                // 1. PLAY SOUND
                playNotificationSound();
                
                // 2. Show browser notification
                showBrowserNotification(newOrders);
                
                // 3. Animate card
                animateNewOrderCard();
                
                // 4. Show toast notification
                showToastNotification(newOrders);
            }
            
            // Update last count
            lastTotalCount = currentTotalCount;
        }
    })
    .catch(error => {
        // Silent error
    });
}

// Show browser notification
function showBrowserNotification(newOrdersCount) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Pesanan Baru Masuk! 🔔', {
            body: `Ada ${newOrdersCount} pesanan baru yang perlu diproses`,
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: 'new-order',
            requireInteraction: false
        });
    } else if ('Notification' in window && Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                showBrowserNotification(newOrdersCount);
            }
        });
    }
}

// Animate card pesanan baru
function animateNewOrderCard() {
    const counterElement = document.getElementById('newOrdersCount');
    if (!counterElement) return;
    
    const card = counterElement.closest('.bg-white');
    if (!card) return;
    
    // Add pulsing animation
    card.classList.add('animate-pulse');
    card.style.borderColor = '#a855f7';
    card.style.borderWidth = '3px';
    card.style.boxShadow = '0 0 20px rgba(168, 85, 247, 0.5)';
    
    setTimeout(() => {
        card.classList.remove('animate-pulse');
        card.style.borderColor = '';
        card.style.borderWidth = '';
        card.style.boxShadow = '';
    }, 3000);
}

// Show toast notification
function showToastNotification(count) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 right-4 bg-blue-600 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center space-x-3 animate-bounce';
    toast.innerHTML = `
        <i class="fas fa-bell text-2xl"></i>
        <div>
            <p class="font-bold">Pesanan Baru Masuk!</p>
            <p class="text-sm">${count} pesanan baru perlu diproses</p>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.5s';
        setTimeout(() => toast.remove(), 500);
    }, 5000);
}

// Request notification permission on page load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Test notification function
function testNotification() {
    // Play sound
    playNotificationSound();
    
    // Show toast
    showToastNotification(1);
    
    // Animate card
    animateNewOrderCard();
    
    // Show browser notification
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Test Notifikasi', {
            body: 'Suara notifikasi berfungsi dengan baik!',
            icon: '/favicon.ico'
        });
    }
}
</script>
@endsection