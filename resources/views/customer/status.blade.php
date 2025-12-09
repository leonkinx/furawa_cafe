<!-- resources/views/customer/order-status.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pesanan - Furawa Cafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">FURAWA CAFE</h1>
                    <p class="text-sm text-gray-600">Cek Status Pesanan Anda</p>
                </div>
                <a href="/menu" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke Menu
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Check Order Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Cek Status Pesanan</h2>
                <p class="text-gray-600 mb-6">Masukkan kode pesanan Anda untuk melihat status pesanan</p>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-receipt mr-2"></i>Kode Pesanan
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" 
                               id="orderCodeInput"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: ORD-ABC123XY"
                               required>
                        <button onclick="checkOrderStatus()" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                            <i class="fas fa-search mr-2"></i>Cek Status
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Kode pesanan dikirimkan setelah Anda menyelesaikan pemesanan
                    </p>
                </div>
                
                <!-- Order Example -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-800 font-medium mb-1">
                        <i class="fas fa-lightbulb mr-2"></i>Contoh kode pesanan:
                    </p>
                    <div class="text-sm text-blue-700">
                        <p>• ORD-ABC123XY</p>
                        <p>• ORD-DEF456GH</p>
                    </div>
                </div>
            </div>

            <!-- Order Result -->
            <div id="orderResult" class="hidden">
                <!-- Result will be loaded here -->
            </div>

            <!-- Loading -->
            <div id="loading" class="hidden text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Mencari pesanan...</p>
            </div>
        </div>
    </div>

    <script>
        async function checkOrderStatus() {
            const orderCode = document.getElementById('orderCodeInput').value.trim();
            
            if (!orderCode) {
                alert('Masukkan kode pesanan terlebih dahulu');
                return;
            }
            
            // Show loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('orderResult').classList.add('hidden');
            
            try {
                const response = await fetch('/orders/check-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ order_code: orderCode })
                });
                
                const data = await response.json();
                
                // Hide loading
                document.getElementById('loading').classList.add('hidden');
                
                if (data.success) {
                    displayOrderResult(data);
                } else {
                    showError(data.message);
                }
                
            } catch (error) {
                document.getElementById('loading').classList.add('hidden');
                console.error('Error:', error);
                showError('Terjadi kesalahan. Periksa koneksi internet Anda.');
            }
        }
        
        function displayOrderResult(data) {
            const order = data.order;
            
            const statusBadgeClass = getStatusBadgeClass(order.status);
            const statusText = data.status_text;
            
            let html = `
                <div class="bg-white rounded-xl shadow-lg p-6 animate-fadeIn">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Pesanan #${order.order_code}</h3>
                            <p class="text-gray-600">${order.customer_name} • Meja ${order.table_id}</p>
                            <p class="text-sm text-gray-500 mt-1">${data.formatted_date}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold ${statusBadgeClass}">
                            ${statusText}
                        </span>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Menunggu</span>
                            <span>Diproses</span>
                            <span>Selesai</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 transition-all duration-500" 
                                 style="width: ${getProgressWidth(order.status)}%"></div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3">Detail Pesanan</h4>
                        <div class="space-y-2">
            `;
            
            data.order_items.forEach(item => {
                html += `
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">${item.menu.name} × ${item.quantity}</p>
                            <p class="text-sm text-gray-600">@ Rp ${formatNumber(item.price)}</p>
                        </div>
                        <p class="font-semibold text-gray-800">Rp ${formatNumber(item.price * item.quantity)}</p>
                    </div>
                `;
            });
            
            html += `
                        </div>
                    </div>
                    
                    <!-- Total & Payment -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-bold text-gray-800">Total Pembayaran</p>
                                <p class="text-sm text-gray-600">${order.payment_method === 'cash' ? 'Tunai (Bayar di Kasir)' : 'Pembayaran Digital'}</p>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">${data.formatted_total}</p>
                        </div>
                        <div class="mt-3 flex justify-between text-sm">
                            <span class="text-gray-600">Status Pembayaran:</span>
                            <span class="font-medium ${order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'}">
                                ${data.payment_status_text}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-6 flex space-x-3">
                        <a href="/menu" 
                           class="flex-1 bg-gray-600 text-white py-3 px-4 rounded-lg text-center hover:bg-gray-700 transition">
                            <i class="fas fa-utensils mr-2"></i>Pesan Lagi
                        </a>
                        <button onclick="window.location.reload()" 
                                class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-sync mr-2"></i>Cek Pesanan Lain
                        </button>
                    </div>
                </div>
            `;
            
            document.getElementById('orderResult').innerHTML = html;
            document.getElementById('orderResult').classList.remove('hidden');
            
            // Scroll to result
            document.getElementById('orderResult').scrollIntoView({ behavior: 'smooth' });
        }
        
        function showError(message) {
            const html = `
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg animate-fadeIn">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">${message}</p>
                            <p class="text-xs text-red-600 mt-1">Pastikan kode pesanan yang dimasukkan benar</p>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('orderResult').innerHTML = html;
            document.getElementById('orderResult').classList.remove('hidden');
        }
        
        function getStatusBadgeClass(status) {
            switch(status) {
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                case 'paid': return 'bg-blue-100 text-blue-800';
                case 'processing': return 'bg-purple-100 text-purple-800';
                case 'completed': return 'bg-green-100 text-green-800';
                case 'cancelled': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }
        
        function getProgressWidth(status) {
            switch(status) {
                case 'pending': return 20;
                case 'paid': return 40;
                case 'processing': return 70;
                case 'completed': return 100;
                case 'cancelled': return 100;
                default: return 20;
            }
        }
        
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Add CSRF token meta tag if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
        
        // Enter key to check order
        document.getElementById('orderCodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                checkOrderStatus();
            }
        });
    </script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</body>
</html>