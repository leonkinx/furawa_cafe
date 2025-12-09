<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furawa Cafe</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
        }
        
        .logo-wrapper {
            text-align: center;
            margin-bottom: 4rem;
            animation: fadeIn 0.8s ease-out;
        }
        
        .brand-name {
            font-size: 3rem;
            font-weight: 700;
            color: #1545adff;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .tagline {
            font-size: 1rem;
            color: #6b7280;
            font-weight: 400;
            line-height: 1.6;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .btn-menu {
            display: block;
            width: 100%;
            background: #2862ddff;
            color: #ffffff;
            padding: 1.25rem 2rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(9, 69, 138, 0.1);
            animation: fadeIn 1s ease-out;
        }
        
        .btn-menu:hover {
            background: #054195ff;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
        }
        
        .btn-menu:active {
            transform: translateY(0);
        }
        
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }
            
            .brand-name {
                font-size: 2rem;
                letter-spacing: 0.08em;
            }
            
            .tagline {
                font-size: 0.875rem;
            }
            
            .logo-wrapper {
                margin-bottom: 3rem;
            }
            
            .btn-menu {
                padding: 1rem 1.5rem;
                font-size: 0.9375rem;
            }
        }
        
        /* Ultra clean - no decorations */
        .divider {
            width: 60px;
            height: 1px;
            background: #e5e7eb;
            margin: 2rem auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand Section -->
        <div class="logo-wrapper">
            <h1 class="brand-name">FURAWA CAFE</h1>
            <h4 class="brand-name">Welcome</h4>
            <p class="tagline">Nikmati Cita Rasa Jepang dalam Setiap Sajian</p>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Button -->
        <a href="/menu" class="btn-menu">
            <span class="btn-icon">
                <i class="fas fa-utensils"></i>
                <span>Lihat Menu</span>
                <i class="fas fa-arrow-right"></i>
            </span>
        </a>
    </div>
</body>
</html>
