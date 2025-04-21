<?php
session_start();

// Validasi data dari _order_details.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$user_id = filter_var($_POST['user_id'] ?? '', FILTER_VALIDATE_INT);
$package_id = filter_var($_POST['package_id'] ?? '', FILTER_VALIDATE_INT);
$quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_INT);
$visit_date = $_POST['visit_date'] ?? '';
$price_per_night = filter_var($_POST['price_per_night'] ?? '', FILTER_VALIDATE_INT);

if (!$user_id || !$package_id || !$quantity || !$visit_date || !$price_per_night) {
    die("Data tidak lengkap.");
}

// Simpan data sementara di session
$_SESSION['order_details'] = [
    'user_id' => $user_id,
    'package_id' => $package_id,
    'quantity' => $quantity,
    'visit_date' => $visit_date,
    'price_per_night' => $price_per_night,
    'total_price' => $price_per_night * $quantity
];

// Format tanggal untuk display
$formatted_date = date('Y-m-d', strtotime($visit_date));
$display_date = date('d M Y', strtotime($visit_date));
$day_name = date('D', strtotime($visit_date));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan - JDAR Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .radio-button {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            position: relative;
        }
        .radio-button:checked {
            border-color: #3b82f6;
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
            background-color: #3b82f6;
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
            transition: .4s;
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
            transition: .4s;
        }
        input:checked + .toggle-slider {
            background-color: #3b82f6;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(16px);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header bar with back button -->
    <div class="bg-white py-4 px-4 shadow-sm flex items-center">
        <a href="javascript:history.back()" class="text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-lg font-medium ml-4">Form Pemesanan</h1>
        <div class="ml-auto">
            <button class="text-gray-700">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
    </div>
    
    <!-- Subtitle -->
    <div class="px-4 py-3 text-sm font-medium flex items-center">
        <i class="fas fa-chevron-left mr-2"></i>
        <span>Selesaikan Pemesananmu</span>
    </div>

    <!-- Main content -->
    <div class="flex-1 px-4 pb-20">
        <!-- Package Summary Card -->
        <div class="bg-white rounded-lg shadow-sm mb-4 overflow-hidden">
            <div class="flex p-4 border-b border-gray-100">
                <div class="w-16 h-16 rounded overflow-hidden bg-gray-200 mr-3 flex-shrink-0">
                    <div class="w-full h-full bg-gray-300"></div>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900">JDAR Travel Package</h3>
                    <p class="text-sm text-gray-700 mt-1">
                        <?php echo $quantity; ?> Tiket • Pax <?php echo $quantity; ?>
                    </p>
                    <p class="text-sm text-gray-700 mt-1">
                        Tanggal Dipilih<br>
                        <?php echo $day_name; ?>, <?php echo $display_date; ?>
                    </p>
                    
                    <div class="flex flex-col mt-2">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-times-circle mr-2 text-xs"></i>
                            <span>Tidak bisa refund</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 mt-1">
                            <i class="fas fa-bolt mr-2 text-xs"></i>
                            <span>Konfirmasi Instan</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 mt-1">
                            <i class="fas fa-calendar-check mr-2 text-xs"></i>
                            <span>Berlaku di tanggal terpilih</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="./_checkout.php" method="GET" id="orderForm">
            <input type="hidden" name="user" value="<?php echo $user_id; ?>">
            <input type="hidden" name="package" value="<?php echo $package_id; ?>">
            <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
            <input type="hidden" name="visit_date" value="<?php echo $visit_date; ?>">
            <input type="hidden" name="total_price" value="<?php echo $price_per_night * $quantity; ?>">

            <!-- Detail Pemesanan -->
            <div class="bg-white rounded-lg shadow-sm mb-4 p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Pemesanan</h3>
                
                <!-- Title Selection -->
                <div class="mb-4">
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
                
                <!-- Name Field -->
                <div class="mb-4">
                    <input type="text" name="full_name" placeholder="Nama lengkap" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                </div>
                
                <!-- Phone Field -->
                <div class="mb-4">
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <div class="flex items-center px-3 bg-white border-r border-gray-300">
                            <div class="flex items-center">
                                <div class="w-6 h-4 mr-1 flex items-center justify-center">
                                    <div class="w-full h-3 bg-red-600"></div>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </div>
                            <span class="ml-2 text-gray-700">+62</span>
                        </div>
                        <input type="text" name="phone" placeholder="Nomor ponsel" class="flex-1 p-3 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                    </div>
                </div>
                
                <!-- Email Field -->
                <div class="mb-4">
                    <input type="email" name="email" placeholder="Alamat email" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                </div>
                
                <!-- Country Field -->
                <div class="mb-2">
                    <div class="relative">
                        <select name="country" class="w-full border border-gray-300 rounded-lg p-3 pr-8 appearance-none focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                            <option value="Indonesia">Indonesia</option>
                            <option value="Malaysia">Malaysia</option>
                            <option value="Singapore">Singapore</option>
                            <!-- Tambahkan opsi negara lain jika diperlukan -->
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detail Pengunjung -->
            <div class="bg-white rounded-lg shadow-sm mb-4">
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Detail Pengunjung</h3>
                    
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-700">Sama dengan pemesan</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="same_as_buyer">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-3 mt-2">
                        <div class="flex justify-between items-center py-2">
                            <span class="text-blue-600 font-medium">Tiket 1 (Pax)</span>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Hanya butuh satu info pengunjung untuk semua tiket yang kamu pesan.</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Price Summary and Submit Button -->
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <span class="text-lg font-bold">IDR <?php echo number_format($price_per_night * $quantity, 0, ',', '.'); ?></span>
                        <i class="fas fa-chevron-down ml-2 text-gray-500"></i>
                    </div>
                    <div class="flex items-center text-sm text-blue-600">
                        <div class="w-4 h-4 rounded-full bg-blue-100 flex items-center justify-center mr-1">
                            <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                        </div>
                        <span>480 poin</span>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-medium py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300">
                    Lanjutkan Pembayaran
                </button>
            </div>
        </form>
    </div>

    <script>
        // Validasi form sebelum submit
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
        
        // For checkbox to copy buyer details to visitor details
        document.querySelector('input[name="same_as_buyer"]').addEventListener('change', function() {
            // Implementation would go here if you had additional visitor fields
            // For now it's just a toggle
        });
    </script>
</body>
</html>