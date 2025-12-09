<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Cheese Burger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #F8F9FA;
        }

        /* Hero Image Section */
        .hero-section {
            background: linear-gradient(180deg, #FF8E53 0%, #FF6B6B 100%);
            position: relative;
            padding: 20px 20px 80px;
            border-radius: 0 0 40px 40px;
        }

        .icon-pattern {
            position: absolute;
            font-size: 40px;
            opacity: 0.1;
            color: white;
        }

        .product-image {
            width: 100%;
            max-width: 300px;
            height: 300px;
            object-fit: cover;
            border-radius: 30px;
            margin: 20px auto;
            display: block;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        /* Info Cards */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: -50px 20px 20px;
            position: relative;
            z-index: 10;
        }

        .info-card {
            background: white;
            padding: 15px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .info-icon {
            width: 45px;
            height: 45px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 20px;
        }

        /* Content Section */
        .content-section {
            padding: 20px;
        }

        .size-option {
            padding: 12px 24px;
            border: 2px solid #E5E7EB;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .size-option.active {
            border-color: #FF6B6B;
            background: #FFF5F5;
            color: #FF6B6B;
        }

        /* Quantity Control */
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #F3F4F6;
            padding: 8px 20px;
            border-radius: 20px;
            width: fit-content;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            color: #FF6B6B;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* Add to Cart Button */
        .add-to-cart-btn {
            background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
            color: white;
            padding: 18px;
            border-radius: 25px;
            border: none;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
            transition: transform 0.2s;
        }

        .add-to-cart-btn:active {
            transform: scale(0.98);
        }

        /* Back Button */
        .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }

        .favorite-btn-detail {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <!-- Hero Section with Image -->
    <div class="hero-section">
        <!-- Icon Pattern Background -->
        <i class="fas fa-hamburger icon-pattern" style="top: 30px; left: 20px;"></i>
        <i class="fas fa-hamburger icon-pattern" style="top: 80px; right: 30px;"></i>
        <i class="fas fa-hamburger icon-pattern" style="bottom: 120px; left: 50px;"></i>
        <i class="fas fa-hamburger icon-pattern" style="bottom: 150px; right: 20px;"></i>
        <i class="fas fa-cheese icon-pattern" style="top: 150px; left: 80px;"></i>
        <i class="fas fa-cheese icon-pattern" style="top: 200px; right: 60px;"></i>
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-4" style="position: relative; z-index: 10;">
            <button class="back-btn" onclick="history.back()">
                <i class="fas fa-arrow-left text-xl"></i>
            </button>
            <button class="favorite-btn-detail">
                <i class="far fa-heart text-xl"></i>
            </button>
        </div>

        <!-- Product Image -->
        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&h=600&fit=crop" 
             alt="Cheese Burger" class="product-image">
    </div>

    <!-- Info Cards -->
    <div class="info-cards">
        <div class="info-card">
            <div class="info-icon" style="background: linear-gradient(135deg, #FFF4E5 0%, #FFE8C1 100%);">
                <i class="fas fa-star" style="color: #FFA500;"></i>
            </div>
            <p class="text-xs text-gray-500 mb-1">Rating</p>
            <p class="font-bold text-sm">4.8</p>
        </div>
        <div class="info-card">
            <div class="info-icon" style="background: linear-gradient(135deg, #FFE5E5 0%, #FFD1D1 100%);">
                <i class="fas fa-clock" style="color: #FF6B6B;"></i>
            </div>
            <p class="text-xs text-gray-500 mb-1">Waktu</p>
            <p class="font-bold text-sm">15 min</p>
        </div>
        <div class="info-card">
            <div class="info-icon" style="background: linear-gradient(135deg, #E5F4FF 0%, #C1E0FF 100%);">
                <i class="fas fa-fire" style="color: #3B82F6;"></i>
            </div>
            <p class="text-xs text-gray-500 mb-1">Kalori</p>
            <p class="font-bold text-sm">450 kcal</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
        <!-- Title and Price -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold mb-2">Cheese Burger Deluxe</h1>
            <p class="text-3xl font-bold text-orange-500">Rp 45.000</p>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-3">Deskripsi</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
                Burger lezat dengan daging sapi premium, keju cheddar meleleh, selada segar, tomat, 
                dan saus spesial kami. Disajikan dengan roti brioche yang lembut dan kentang goreng 
                renyah. Sempurna untuk makan siang atau makan malam Anda!
            </p>
        </div>

        <!-- Size Selection -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-3">Pilih Ukuran</h3>
            <div class="flex gap-3">
                <button class="size-option">Small</button>
                <button class="size-option active">Medium</button>
                <button class="size-option">Large</button>
            </div>
        </div>

        <!-- Ingredients -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg mb-3">Bahan-bahan</h3>
            <div class="flex flex-wrap gap-2">
                <span class="px-4 py-2 bg-gray-100 rounded-full text-sm">🥩 Daging Sapi</span>
                <span class="px-4 py-2 bg-gray-100 rounded-full text-sm">🧀 Keju Cheddar</span>
                <span class="px-4 py-2 bg-gray-100 rounded-full text-sm">🥬 Selada</span>
                <span class="px-4 py-2 bg-gray-100 rounded-full text-sm">🍅 Tomat</span>
                <span class="px-4 py-2 bg-gray-100 rounded-full text-sm">🍞 Roti Brioche</span>
            </div>
        </div>

        <!-- Quantity and Add to Cart -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm text-gray-500 mb-2">Jumlah</p>
                <div class="quantity-control">
                    <button class="quantity-btn">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="font-bold text-lg">1</span>
                    <button class="quantity-btn">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 mb-2">Total Harga</p>
                <p class="text-2xl font-bold text-orange-500">Rp 45.000</p>
            </div>
        </div>

        <!-- Add to Cart Button -->
        <button class="add-to-cart-btn">
            <i class="fas fa-shopping-cart mr-2"></i>
            Tambah ke Keranjang
        </button>
    </div>

    <script>
        // Size selection
        document.querySelectorAll('.size-option').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.size-option').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Quantity control
        let quantity = 1;
        const price = 45000;
        
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.querySelector('.fa-minus')) {
                    if (quantity > 1) quantity--;
                } else {
                    quantity++;
                }
                updateQuantity();
            });
        });

        function updateQuantity() {
            document.querySelector('.quantity-control span').textContent = quantity;
            document.querySelector('.text-right .text-2xl').textContent = 
                'Rp ' + (price * quantity).toLocaleString('id-ID');
        }

        // Favorite toggle
        document.querySelector('.favorite-btn-detail').addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
        });
    </script>
</body>
</html>
