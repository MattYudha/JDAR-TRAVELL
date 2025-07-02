<?php
session_start();
include_once("../app/Database.php"); // Hanya include Database.php

$user_id = filter_var($_POST['user_id'] ?? '', FILTER_VALIDATE_INT);
$package_id = filter_var($_POST['package_id'] ?? '', FILTER_VALIDATE_INT);
$quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_INT);
$visit_date = filter_var($_POST['visit_date'] ?? '', FILTER_SANITIZE_STRING);
$price_per_night = filter_var($_POST['price_per_night'] ?? '', FILTER_VALIDATE_FLOAT);
$coupon_code = filter_var($_POST['coupon_code'] ?? '', FILTER_SANITIZE_STRING);

if (!$user_id || !$package_id || !$quantity || !$visit_date || !$price_per_night || !isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: ../registration.php");
    exit;
}

$packageInstance = new Packages();
$couponInstance = new Coupons();
$res = $packageInstance->getPackage($package_id);
$package = mysqli_fetch_assoc($res);
if (!$package) {
    die("Paket tidak ditemukan.");
}

$total_price = $price_per_night * $quantity;
$original_price = $package['package_price'];
$coupon_result = ['success' => false, 'new_price' => $total_price, 'discount_amount' => 0];
if (!empty($coupon_code)) {
    $coupon_result = $couponInstance->applyCoupon($coupon_code, $total_price);
    if ($coupon_result['success']) {
        $total_price = $coupon_result['new_price'];
    }
}

$date = new DateTime($visit_date);
$day_name = $date->format('l');
$display_date = $date->format('d M Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan - JDAR Travel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #0369a1);
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0284c7, #075985);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
        }
        .progress-bar {
            height: 3px;
            background-color: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            margin: 15px 0;
        }
        .progress-step {
            height: 100%;
            background-color: #0284c7;
            width: 50%;
            transition: width 0.3s ease-in-out;
        }
        .nav-steps {
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            margin-bottom: 20px;
        }
        .nav-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 12px;
            color: #64748b;
            flex: 1;
            position: relative;
        }
        .nav-step.active {
            color: #0284c7;
            font-weight: 600;
        }
        .nav-step-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f5f9;
            border-radius: 50%;
            margin-bottom: 6px;
            transition: background-color 0.3s, color 0.3s;
        }
        .nav-step.active .nav-step-icon {
            background-color: #0284c7;
            color: white;
        }
        .radio-button {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            position: relative;
            transition: border-color 0.2s;
        }
        .radio-button:checked {
            border-color: #0284c7;
        }
        .radio-button:checked::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #0284c7;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 20px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 34px;
            transition: background-color 0.4s;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.4s;
        }
        input:checked + .toggle-slider {
            background-color: #0284c7;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(16px);
        }
        .discount-price {
            color: #dc3545;
            text-decoration: line-through;
            margin-right: 10px;
        }
        .final-price {
            color: #0284c7;
            font-weight: 700;
        }
        .fallback-image {
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 14px;
            width: 64px;
            height: 64px;
        }
        input:focus, select:focus {
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            transition: box-shadow 0.2s;
        }
        @media (max-width: 640px) {
            .nav-step-text {
                display: none;
            }
            .card {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header with Progress Bar -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="javascript:history.back()" class="text-gray-700 mr-4 hover:text-blue-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">Form Pemesanan</h1>
                </div>
                <div class="text-sm text-gray-500">Langkah 2 dari 3</div>
            </div>
            
            <div class="progress-bar">
                <div class="progress-step"></div>
            </ composed
            <div class="nav-steps">
                <div class="nav-step">
                    <div class="nav-step-icon">1</div>
                    <span class="nav-step-text">Detail</span>
                </div>
                <div class="nav-step active">
                    <div class="nav-step-icon">2</div>
                    <span class="nav-step-text">Pemesanan</span>
                </div>
                <div class="nav-step">
                    <div class="nav-step-icon">3</div>
                    <span class="nav-step-text">Konfirmasi</span>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 md:py-8">
        <div class="md:flex md:space-x-6 mb-8">
            <!-- Form Section -->
            <div class="md:w-2/3">
                <form action="./_checkout.php" method="GET" id="orderForm">
                    <input type="hidden" name="user" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="package" value="<?php echo $package_id; ?>">
                    <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
                    <input type="hidden" name="visit_date" value="<?php echo $visit_date; ?>">
                    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                    <input type="hidden" name="original_price" value="<?php echo $original_price; ?>">
                    <input type="hidden" name="coupon_code" value="<?php echo $coupon_code; ?>">

                    <!-- Paket Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 card">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Paket Terpilih</h3>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">
                                Best Deal
                            </span>
                        </div>
                        
                        <div class="mt-4 flex">
                            <div class="w-16 h-16 rounded overflow-hidden mr-3 flex-shrink-0">
                                <?php if ($package['master_image']): ?>
                                    <img src="<?php echo htmlspecialchars($package['master_image']); ?>" alt="Package Image" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="fallback-image rounded hidden">Gambar tidak tersedia</div>
                                <?php else: ?>
                                    <div class="fallback-image rounded">Gambar tidak tersedia</div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($package['package_name']); ?></h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?php echo $quantity; ?> Tiket • Pax <?php echo $quantity; ?>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?php echo $day_name; ?>, <?php echo $display_date; ?>
                                </p>
                                <div class="flex flex-wrap gap-3 mt-3">
                                    <div class="flex items-center text-gray-600 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Tidak Bisa Refund
                                    </div>
                                    <div class="flex items-center text-gray-600 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Konfirmasi Instan
                                    </div>
                                    <div class="flex items-center text-gray-600 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Berlaku di Tanggal Terpilih
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 card">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Gunakan Kupon</h3>
                        <div class="flex items-center space-x-3">
                            <input type="text" name="coupon_code" value="<?php echo htmlspecialchars($coupon_code); ?>" placeholder="Masukkan kode kupon" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all">
                            <button type="submit" class="btn-primary text-white px-4 py-3 rounded-lg font-medium">Terapkan</button>
                        </div>
                        <?php if (!empty($coupon_code)): ?>
                            <p class="mt-2 text-sm <?php echo $coupon_result['success'] ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $coupon_result['success'] ? "Kupon berhasil diterapkan! Diskon: " . number_format($coupon_result['discount_amount'], 0, ',', '.') : $coupon_result['message']; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Detail Pemesanan Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 card">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Pemesanan</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gelar</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="title" value="Tuan" class="radio-button" required>
                                    <span class="ml-2 text-gray-700">Tuan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="title" value="Nyonya" class="radio-button">
                                    <span class="ml-2 text-gray-700">Nyonya</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="title" value="Nona" class="radio-button">
                                    <span class="ml-2 text-gray-700">Nona</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="full_name" placeholder="Masukkan nama lengkap" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Ponsel</label>
                            <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                <div class="flex items-center px-3 bg-white border-r border-gray-300">
                                    <div class="flex items-center">
                                        <div class="w-6 h-4 mr-1 flex items-center justify-center">
                                            <div class="w-full h-3 bg-red-600"></div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-gray-700">+62</span>
                                </div>
                                <input type="text" name="phone" placeholder="Masukkan nomor ponsel" class="flex-1 p-3 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                            <input type="email" name="email" placeholder="Masukkan alamat email" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Negara</label>
                            <div class="relative">
                                <select name="country" class="w-full border border-gray-300 rounded-lg p-3 pr-8 appearance-none focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" required>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Malaysia">Malaysia</option>
                                    <option value="Singapore">Singapore</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pengunjung Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 card">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Pengunjung</h3>
                        
                        <div class="flex items-center justify-between py-2">
                            <span class="text-gray-700">Sama dengan pemesan</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="same_as_buyer">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-3 mt-2">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-blue-600 font-medium">Tiket 1 (Pax)</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-600 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Hanya butuh satu info pengunjung untuk semua tiket yang kamu pesan.</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full btn-primary text-white py-4 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
                            Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary Section for Desktop -->
            <div class="hidden md:block md:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6 card">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <h4 class="font-semibold"><?php echo htmlspecialchars($package['package_name']); ?></h4>
                        <p class="text-sm text-gray-600 mt-1">
                            <?php echo $day_name; ?>, <?php echo $display_date; ?> • <?php echo $quantity; ?> tiket
                        </p>
                    </div>
                    
                    <div class="space-y-3 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Harga per tiket</span>
                            <span class="text-gray-800">IDR <?php echo number_format($price_per_night, 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Jumlah tiket</span>
                            <span class="text-gray-800"><?php echo $quantity; ?></span>
                        </div>
                        <?php if ($coupon_result['success']): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Diskon Kupon</span>
                                <span class="text-green-600">-IDR <?php echo number_format($coupon_result['discount_amount'], 0, ',', '.'); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pajak & Biaya</span>
                            <span class="text-gray-800">Termasuk</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center font-semibold mb-6">
                        <span class="text-gray-800">Total pembayaran</span>
                        <div>
                            <?php if ($original_price > $price_per_night || $coupon_result['success']): ?>
                                <span class="discount-price text-lg">IDR <?php echo number_format($original_price * $quantity, 0, ',', '.'); ?></span>
                                <span class="final-price text-xl">IDR <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                            <?php else: ?>
                                <span class="text-blue-600 text-xl">IDR <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <p class="font-medium text-gray-800">Dapatkan <span class="text-blue-600">480</span> poin</p>
                            <p class="text-gray-600">dari transaksi ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('orderForm').addEventListener('submit', function(event) {
            const title = document.querySelector('input[name="title"]:checked');
            const fullName = document.querySelector('input[name="full_name"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const country = document.querySelector('select[name="country"]').value;

            if (!title || !fullName || !phone || !email || !country) {
                event.preventDefault();
                alert('Harap lengkapi semua field yang diperlukan.');
            }
        });

        document.querySelector('input[name="same_as_buyer"]').addEventListener('change', function() {
            // Implementation for copying buyer details would go here
        });
    </script>
</body>
</html>