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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .date-btn {
            border: 1px solid #d1d5db;
            padding: 8px 16px;
            border-radius: 9999px;
            cursor: pointer;
        }
        .date-btn.selected {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Detail Pesanan</h2>
        <form action="./_order_form.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
            <input type="hidden" name="price_per_night" value="<?php echo $price_per_night; ?>">

            <!-- Paket Terpilih -->
            <div class="mb-4">
                <h3 class="font-semibold">Paket Terpilih</h3>
                <p><?php echo htmlspecialchars($package['package_name']); ?></p>
                <p>Masa berlaku: <?php echo htmlspecialchars($package_start); ?></p>
                <p class="text-sm text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tidak bisa refund
                </p>
            </div>

            <!-- Tanggal Kunjungan -->
            <div class="mb-4">
                <h3 class="font-semibold">Tanggal Kunjungan</h3>
                <p class="text-sm text-gray-500 mb-2">Cek tanggal tersedia</p>
                <div class="flex space-x-2 overflow-x-auto">
                    <?php
                    $start_date = new DateTime($package_start);
                    for ($i = 0; $i < 5; $i++) {
                        $date = clone $start_date;
                        $date->modify("+$i day");
                        $formatted_date = $date->format('Y-m-d');
                        $display_date = $date->format('d M');
                        $day_name = $date->format('D');
                        echo "<button type='button' class='date-btn' data-date='$formatted_date'>$day_name<br>$display_date</button>";
                    }
                    ?>
                </div>
                <input type="hidden" name="visit_date" id="visit_date" required>
            </div>

            <!-- Jumlah Kamar/Malam -->
            <div class="mb-4">
                <h3 class="font-semibold">Jumlah Kamar/Malam</h3>
                <div class="flex items-center justify-between">
                    <p>IDR <?php echo number_format($price_per_night, 0, ',', '.'); ?> / malam</p>
                    <div class="flex items-center space-x-2">
                        <button type="button" id="decrease" class="w-8 h-8 bg-gray-200 rounded-full">-</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" class="w-16 text-center border rounded" readonly>
                        <button type="button" id="increase" class="w-8 h-8 bg-gray-200 rounded-full">+</button>
                    </div>
                </div>
            </div>

            <!-- Total Harga -->
            <div class="mb-4">
                <h3 class="font-semibold">Total (<span id="quantity_display">1</span> malam):</h3>
                <p>IDR <span id="total_price"><?php echo number_format($price_per_night, 0, ',', '.'); ?></span></p>
                <p class="text-sm text-blue-500">Dapat 480 poin</p>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Pesan</button>
        </form>
    </div>

    <script>
        const pricePerNight = <?php echo $price_per_night; ?>;
        const quantityInput = document.getElementById('quantity');
        const quantityDisplay = document.getElementById('quantity_display');
        const totalPriceDisplay = document.getElementById('total_price');
        const dateButtons = document.querySelectorAll('.date-btn');
        const visitDateInput = document.getElementById('visit_date');

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

        // Set default date
        dateButtons[0].click();
    </script>
</body>
</html>