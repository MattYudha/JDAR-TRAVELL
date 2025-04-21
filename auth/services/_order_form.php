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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan - JDAR Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Form Pemesanan</h2>
        <form action="./_checkout.php" method="GET">
            <input type="hidden" name="user" value="<?php echo $user_id; ?>">
            <input type="hidden" name="package" value="<?php echo $package_id; ?>">
            <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
            <input type="hidden" name="visit_date" value="<?php echo $visit_date; ?>">
            <input type="hidden" name="total_price" value="<?php echo $price_per_night * $quantity; ?>">

            <!-- Detail Pesanan -->
            <div class="mb-4">
                <h3 class="font-semibold">Detail Pesanan</h3>
                <p>Tanggal: <?php echo $visit_date; ?></p>
                <p>Jumlah Kamar/Malam: <?php echo $quantity; ?></p>
                <p>Total: IDR <?php echo number_format($price_per_night * $quantity, 0, ',', '.'); ?></p>
            </div>

            <!-- Detail Pengunjung -->
            <div class="mb-4">
                <h3 class="font-semibold">Detail Pengunjung</h3>
                <div class="flex space-x-4 mb-2">
                    <label><input type="radio" name="title" value="Tuan" required> Tuan</label>
                    <label><input type="radio" name="title" value="Nyonya"> Nyonya</label>
                    <label><input type="radio" name="title" value="Nona"> Nona</label>
                </div>
                <div class="mb-2">
                    <label class="block text-sm">Nama Lengkap</label>
                    <input type="text" name="full_name" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-2">
                    <label class="block text-sm">Nomor Ponsel</label>
                    <div class="flex">
                        <span class="border rounded-l p-2 bg-gray-100">+62</span>
                        <input type="text" name="phone" class="w-full border rounded-r p-2" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="block text-sm">Alamat Email</label>
                    <input type="email" name="email" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-2">
                    <label class="block text-sm">Negara Tempat Tinggal</label>
                    <select name="country" class="w-full border rounded p-2" required>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Singapore">Singapore</option>
                        <!-- Tambahkan opsi negara lain jika diperlukan -->
                    </select>
                </div>
            </div>

            <!-- Opsi Tambahan -->
            <div class="mb-4">
                <h3 class="font-semibold">Detail Pengunjung</h3>
                <label class="flex items-center">
                    <input type="checkbox" name="same_as_buyer">
                    <span class="ml-2">Sama dengan pemesan</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Lanjutkan Pembayaran</button>
        </form>
    </div>
</body>
</html>