<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furawa Cafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center mb-4">
                <i class="fas fa-utensils text-4xl text-blue-600 mr-3"></i>
                <h1 class="text-4xl font-bold text-gray-800">FURAWA CAFE</h1>
            </div>
            <p class="text-gray-600 text-lg">Selamat datang! Silakan pilih meja Anda</p>
        </div>

        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center">
                <div class="w-32 h-32 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-utensils text-blue-600 text-4xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Pilih Meja</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Meja</label>
                    <select id="tableSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">Meja {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <button onclick="goToMenu()" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    <i class="fas fa-arrow-right mr-2"></i>Lihat Menu
                </button>

                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pilih nomor meja Anda sebelum memesan
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function goToMenu() {
        const tableNumber = document.getElementById('tableSelect').value;
        window.location.href = '/menu?table=' + tableNumber;
    }
    function processOrder(orderData) {
    console.log('Sending order data to server:', orderData);
    
    showLoading();
    
    // Prepare items array from cart
    const items = [];
    Object.keys(cart).forEach(menuId => {
        if (cart[menuId] > 0) {
            items.push({
                menu_id: parseInt(menuId),
                quantity: cart[menuId]
            });
        }
    });
    
    // Prepare final order data
    const finalOrderData = {
        customer_name: orderData.customer_name,
        table_number: orderData.table_number,
        payment_method: orderData.payment_method,
        items: items,
        total_amount: orderData.total_amount
    };
    
    console.log('Final order data to send:', finalOrderData);
    
    // Send to server
    fetch('/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(finalOrderData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json().then(data => {
            console.log('Response data:', data);
            return {status: response.status, data};
        }).catch(e => {
            console.log('Error parsing JSON:', e);
            return response.text().then(text => {
                console.log('Response text:', text);
                return {status: response.status, data: {success: false, message: text}};
            });
        });
    })
    .then(result => {
        hideLoading();
        hidePayment();
        
        console.log('Final result:', result);
        
        if (result.data.success) {
            // Reset cart
            cart = {};
            updateCartSummary();
            updateAllQuantityDisplays();
            
            // Show success modal
            showSuccessModal(result.data.order);
        } else {
            alert('❌ Gagal memproses pesanan: ' + (result.data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Fetch error:', error);
        alert('❌ Terjadi kesalahan jaringan: ' + error.message);
    });
}
    </script>
</body>
</html>