<?php
session_start();
include_once("../app/_dbConnection.php");

// Validasi user_id dan package_id
$user_id = filter_var($_GET['user'] ?? '', FILTER_VALIDATE_INT);
$package_id = filter_var($_GET['package'] ?? '', FILTER_VALIDATE_INT);
if (!$user_id || !$package_id || !isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: ../registration.php");
    exit;
}

// Ambil data paket
$packageInstance = new Packages();
$res = $packageInstance->getPackage($package_id);
$package = mysqli_fetch_assoc($res);
if (!$package) {
    die("Paket tidak ditemukan.");
}

$price_per_night = $package['package_price'];
$package_start = $package['package_start'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - JDAR Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .date-selection {
            scrollbar-width: none;
        }
        .date-selection::-webkit-scrollbar {
            display: none;
        }
        .date-button {
            transition: all 0.2s ease;
        }
        .date-button.selected {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header bar with back button -->
    <div class="bg-white py-4 px-4 shadow-sm flex items-center">
        <a href="javascript:history.back()" class="text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-lg font-medium ml-4">Detail Pesanan</h1>
        <div class="ml-auto">
            <button class="text-gray-700">
                <i class="fas fa-share-alt"></i>
            </button>
        </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 px-4 py-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <form action="./_order_form.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                <input type="hidden" name="price_per_night" value="<?php echo $price_per_night; ?>">
                
                <!-- Paket Terpilih -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <h3 class="text-base font-medium text-gray-800">Paket Terpilih</h3>
                            <p class="text-lg font-bold text-gray-900 mt-1"><?php echo htmlspecialchars($package['package_name']); ?></p>
                            <div class="flex items-center text-sm text-gray-500 mt-2">
                                <i class="fas fa-times-circle mr-1 text-xs"></i>
                                <span>Tidak bisa refund</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Masa berlaku: <?php echo htmlspecialchars($package_start); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Tanggal Kunjungan -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-base font-medium text-gray-800">Tanggal Kunjungan</h3>
                        <span class="text-sm text-blue-600">Cek tanggal tersedia</span>
                    </div>
                    
                    <div class="date-selection flex space-x-2 overflow-x-auto py-2">
                        <?php
                        $start_date = new DateTime($package_start);
                        for ($i = 0; $i < 5; $i++) {
                            $date = clone $start_date;
                            $date->modify("+$i day");
                            $formatted_date = $date->format('Y-m-d');
                            $display_date = $date->format('d');
                            $month = $date->format('M');
                            $day_name = $date->format('D');
                            $today = $i === 0 ? 'Hari ini<br>' : '';
                            $selected = $i === 0 ? 'selected' : '';
                            
                            echo "
                            <div class='flex flex-col items-center'>
                                <button type='button' data-date='{$formatted_date}' class='date-button w-16 h-16 rounded-full border flex flex-col items-center justify-center text-sm {$selected}'>
                                    <span class='text-xs'>{$day_name}</span>
                                    <span class='font-medium'>{$display_date}</span>
                                    <span class='text-xs'>{$month}</span>
                                </button>
                                <span class='text-xs mt-1'>{$today}</span>
                            </div>";
                        }
                        ?>
                        <input type="hidden" name="visit_date" id="visit_date" value="<?php echo $start_date->format('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <!-- Jumlah Kamar/Malam -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-base font-medium text-gray-800">Jumlah Kamar/Malam</h3>
                        <span class="text-base font-medium text-gray-900">IDR <?php echo number_format($price_per_night, 0, ',', '.'); ?> /malam</span>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Pax</span>
                            <div class="flex items-center">
                                <button type="button" id="decrease" class="w-8 h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-700">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" class="w-10 text-center bg-transparent border-none mx-2 text-base font-medium" readonly>
                                <button type="button" id="increase" class="w-8 h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-700">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Harga -->
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-medium text-gray-800">Total (<span id="quantity_display">1</span> malam):</h3>
                        <p class="text-lg font-bold text-gray-900">IDR <span id="total_price"><?php echo number_format($price_per_night, 0, ',', '.'); ?></span></p>
                    </div>
                    <div class="flex items-center text-sm text-blue-600 mt-2">
                        <i class="fas fa-circle-info mr-1"></i>
                        <span>Dapat 480 poin</span>
                    </div>
                </div>
            
                <!-- Sticky bottom button -->
                <div class="bg-white px-4 py-3 border-t border-gray-100 sticky bottom-0">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-300">Pesan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const pricePerNight = <?php echo $price_per_night; ?>;
        const quantityInput = document.getElementById('quantity');
        const quantityDisplay = document.getElementById('quantity_display');
        const totalPriceDisplay = document.getElementById('total_price');
        const visitDateInput = document.getElementById('visit_date');
        const dateButtons = document.querySelectorAll('.date-button');

        // Update total price
        function updateTotalPrice() {
            const quantity = parseInt(quantityInput.value);
            const total = pricePerNight * quantity;
            quantityDisplay.textContent = quantity;
            totalPriceDisplay.textContent = total.toLocaleString('id-ID');
        }

        // Increase/Decrease quantity
        document.getElementById('increase').addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value);
            if (quantity < 10) {
                quantityInput.value = quantity + 1;
                updateTotalPrice();
            }
        });

        document.getElementById('decrease').addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
                updateTotalPrice();
            }
        });

        // Date selection
        dateButtons.forEach(button => {
            button.addEventListener('click', () => {
                dateButtons.forEach(btn => btn.classList.remove('selected'));
                button.classList.add('selected');
                visitDateInput.value = button.dataset.date;
            });
        });
    </script>
</body>
</html>