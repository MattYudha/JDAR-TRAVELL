<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Muat file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Midtrans\Config;
use Midtrans\Snap;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Masukkan koneksi DB
include_once("../app/_dbConnection.php");

// Mulai session
session_start();

// Validasi user_id dari session
if (!isset($_SESSION['user_id'])) {
    die("Silakan login terlebih dahulu.");
}

// Validasi parameter
$user_id = filter_var($_GET['user'] ?? '', FILTER_VALIDATE_INT);
$package_id = filter_var($_GET['package'] ?? '', FILTER_VALIDATE_INT);
$quantity = filter_var($_GET['quantity'] ?? '', FILTER_VALIDATE_INT);
$visit_date = $_GET['visit_date'] ?? '';
$original_price = filter_var($_GET['original_price'] ?? '', FILTER_VALIDATE_INT);
$coupon_code = $_GET['coupon_code'] ?? ''; // Ambil coupon_code terakhir
$title = $_GET['title'] ?? '';
$full_name = $_GET['full_name'] ?? '';
$phone = $_GET['phone'] ?? '';
$email = filter_var($_GET['email'] ?? '', FILTER_VALIDATE_EMAIL);
$country = $_GET['country'] ?? '';

if (!$user_id || !$package_id || !$quantity || !$visit_date || !$original_price || !$email) {
    error_log("Parameter tidak valid: user_id=$user_id, package_id=$package_id, quantity=$quantity, visit_date=$visit_date, original_price=$original_price, email=$email");
    die("Parameter tidak valid.");
}

// Validasi bahwa user_id sesuai dengan session
if ($user_id !== $_SESSION['user_id']) {
    error_log("ID pengguna tidak cocok: GET user_id=$user_id, SESSION user_id=" . $_SESSION['user_id']);
    die("Akses tidak sah: ID pengguna tidak valid.");
}

// Setup logging
$log = new Logger('midtrans');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/midtrans.log', Logger::INFO));

// Debug kunci Midtrans
error_log("Server Key: " . ($_ENV['MIDTRANS_SERVER_KEY'] ?? 'Tidak diatur'));
error_log("Client Key: " . ($_ENV['MIDTRANS_CLIENT_KEY'] ?? 'Tidak diatur'));
error_log("Apakah Produksi: " . ($_ENV['MIDTRANS_IS_PRODUCTION'] ?? 'Tidak diatur'));

// Validasi kunci Midtrans
if (empty($_ENV['MIDTRANS_SERVER_KEY']) || empty($_ENV['MIDTRANS_CLIENT_KEY'])) {
    $log->error('Kunci Midtrans tidak ditemukan');
    die("Error: Server Key atau Client Key tidak ditemukan.");
}

// Konfigurasi Midtrans
Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
Config::$clientKey = $_ENV['MIDTRANS_CLIENT_KEY'];
Config::$isProduction = $_ENV['MIDTRANS_IS_PRODUCTION'] === 'true';

// Ambil data pengguna
$userInstance = new Users();
$user = $userInstance->getUser($user_id);
if (!$user) {
    $log->error("Pengguna tidak ditemukan untuk user_id: $user_id");
    die("Pengguna tidak ditemukan.");
}

// Ambil data paket
$packageInstance = new Packages();
$res = $packageInstance->getPackage($package_id);
$package = mysqli_fetch_assoc($res);
if (!$package) {
    $log->error("Paket tidak ditemukan untuk package_id: $package_id");
    die("Paket tidak ditemukan.");
}

// Hitung total harga awal (sebelum diskon)
$base_price = $original_price * $quantity;

// Terapkan kupon jika ada
$trans_amount = $base_price;
if (!empty($coupon_code)) {
    $couponInstance = new Coupons();
    $coupon_result = $couponInstance->applyCoupon($coupon_code, $base_price);
    if ($coupon_result['success']) {
        $trans_amount = $coupon_result['new_price'];
        $log->info("Kupon diterapkan", ['coupon_code' => $coupon_code, 'original_price' => $base_price, 'discounted_price' => $trans_amount]);
    } else {
        $log->warning("Kupon tidak valid", ['coupon_code' => $coupon_code, 'message' => $coupon_result['message']]);
    }
}

// Data transaksi untuk Midtrans
$transaction_details = [
    'order_id' => 'JDAR_' . uniqid(),
    'gross_amount' => $trans_amount,
];

$customer_details = [
    'first_name' => $title . ' ' . $full_name,
    'email' => $email,
    'phone' => $phone ?: ($user['phone'] ?? "081234567890"),
    'billing_address' => [
        'address' => $user['address'] ?? "Jl. Contoh No. 123",
        'city' => "Jakarta",
        'postal_code' => "10110",
        'country_code' => strtoupper(substr($country, 0, 3)) ?: "IDN",
    ],
    'shipping_address' => [
        'address' => $user['address'] ?? "Jl. Contoh No. 123",
        'city' => "Jakarta",
        'postal_code' => "10110",
        'country_code' => strtoupper(substr($country, 0, 3)) ?: "IDN",
    ],
];

$items = [
    [
        'id' => $package['package_id'],
        'price' => $trans_amount / $quantity,
        'quantity' => $quantity,
        'name' => $package['package_name'] . " ($quantity malam)",
    ]
];

$transaction_data = [
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $items,
    'callbacks' => [
        'finish' => 'http://localhost/triptrip-master/success.php?order_id=' . $transaction_details['order_id'],
        'unfinish' => 'http://localhost/triptrip-master/pending.php?order_id=' . $transaction_details['order_id'],
        'error' => 'http://localhost/triptrip-master/error.php?order_id=' . $transaction_details['order_id'],
    ],
];

// Proses transaksi
try {
    $snapToken = Snap::getSnapToken($transaction_data);

    // Simpan transaksi ke database
    $tran_id = $transaction_details['order_id'];
    $user_id = $_SESSION['user_id'];
    $_SESSION['package_id'] = $package['package_id'];
    $package_id = $_SESSION['package_id'];
    $status = 'pending';

    $transactionInstance = new Transactions();
    $transaction_id = $transactionInstance->createTransaction($user_id, $package_id, $quantity, $visit_date, $trans_amount, $coupon_code);

    if (!$transaction_id) {
        $log->error("Gagal membuat transaksi untuk order_id: $tran_id");
        die("Gagal menyimpan transaksi ke database.");
    }

    // Simpan detail pesanan ke session untuk digunakan di success.php
    $_SESSION['order_details'] = [
        'quantity' => $quantity,
        'visit_date' => $visit_date,
        'amount' => $trans_amount,
        'original_amount' => $base_price,
        'title' => $title,
        'full_name' => $full_name,
        'phone' => $phone,
        'email' => $email,
        'country' => $country,
    ];

    // Log transaksi
    $log->info('Transaksi berhasil dibuat', ['order_id' => $tran_id, 'amount' => $trans_amount]);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JDAR Travel</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo $_ENV['MIDTRANS_CLIENT_KEY']; ?>"></script>
        <style>
            .discount-price { color: #dc3545; text-decoration: line-through; margin-right: 10px; }
            .final-price { color: #28a745; font-weight: 700; }
        </style>
    </head>
    <body class="flex items-center justify-center h-screen bg-gray-100">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">JDAR Travel</h1>
            <?php if ($base_price > $trans_amount): ?>
                <p class="text-xl mb-2">
                    <span class="discount-price">Rp<?php echo number_format($base_price, 0, ',', '.'); ?></span>
                    <span class="final-price">Rp<?php echo number_format($trans_amount, 0, ',', '.'); ?></span>
                </p>
            <?php else: ?>
                <p class="text-xl font-bold text-gray-800 mb-2">Rp<?php echo number_format($trans_amount, 0, ',', '.'); ?></p>
            <?php endif; ?>
            <p class="text-sm text-gray-500 mb-4">ID Pesanan: <?php echo htmlspecialchars($tran_id); ?> <a href="#" class="text-blue-600">Detail</a></p>
            <p class="text-sm text-gray-500 mb-4">Pilih dalam waktu <span id="countdown">23:58:47</span></p>
            <div class="mb-4">
                <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg flex items-center justify-between px-4">
                    <span>Kartu Kredit/Debit</span>
                    <div class="flex space-x-2">
                        <img src="https://img.icons8.com/color/24/000000/visa.png" alt="Visa">
                        <img src="https://img.icons8.com/color/24/000000/mastercard.png" alt="Mastercard">
                        <img src="https://img.icons8.com/color/24/000000/jcb.png" alt="JCB">
                    </div>
                </button>
            </div>
            <div class="mb-4">
                <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg flex items-center justify-between px-4">
                    <span>Semua Metode Pembayaran</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="mb-4">
                <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg flex items-center justify-between px-4">
                    <span>GoPay/GoPay Later</span>
                    <div class="flex space-x-2">
                        <img src="https://img.icons8.com/color/24/000000/gopay.png" alt="GoPay">
                        <img src="https://img.icons8.com/color/24/000000/qris.png" alt="QRIS">
                    </div>
                </button>
            </div>
            <div class="mb-4">
                <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg flex items-center justify-between px-4">
                    <span>Virtual Account</span>
                    <div class="flex space-x-2">
                        <img src="https://img.icons8.com/color/24/000000/bca.png" alt="BCA">
                        <img src="https://img.icons8.com/color/24/000000/mandiri.png" alt="Mandiri">
                        <img src="https://img.icons8.com/color/24/000000/bni.png" alt="BNI">
                        <span class="text-gray-500 text-sm">+2</span>
                    </div>
                </button>
            </div>
            <div class="mb-4">
                <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg flex items-center justify-between px-4">
                    <span>Kartu Kredit/Debit</span>
                    <div class="flex space-x-2">
                        <img src="https://img.icons8.com/color/24/000000/visa.png" alt="Visa">
                        <img src="https://img.icons8.com/color/24/000000/mastercard.png" alt="Mastercard">
                        <img src="https://img.icons8.com/color/24/000000/jcb.png" alt="JCB">
                    </div>
                </button>
            </div>
            <div class="mb-4">
                <button class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg flex items-center justify-between px-4">
                    <span>ShopeePay/SPayLater</span>
                    <div class="flex space-x-2">
                        <img src="https://img.icons8.com/color/24/000000/shopeepay.png" alt="ShopeePay">
                        <img src="https://img.icons8.com/color/24/000000/spaylater.png" alt="SPayLater">
                        <img src="https://img.icons8.com/color/24/000000/qris.png" alt="QRIS">
                    </div>
                </button>
            </div>
        </div>
        <script>
            setTimeout(() => {
                snap.pay('<?php echo $snapToken; ?>', {
                    onSuccess: function(result) {
                        console.log('Pembayaran berhasil:', result);
                        window.location.href = '/triptrip-master/success.php?order_id=' + result.order_id;
                    },
                    onPending: function(result) {
                        console.log('Pembayaran tertunda:', result);
                        window.location.href = '/triptrip-master/pending.php?order_id=' + result.order_id;
                    },
                    onError: function(result) {
                        console.log('Pembayaran gagal:', result);
                        fetch('/triptrip-master/app/_deleteTransaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order_id: '<?php echo $tran_id; ?>' })
                        }).then(() => {
                            window.location.href = '/triptrip-master/error.php?order_id=' + result.order_id;
                        });
                    },
                    onClose: function() {
                        console.log('Jendela pembayaran ditutup');
                        fetch('/triptrip-master/app/_deleteTransaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order_id: '<?php echo $tran_id; ?>' })
                        }).then(() => {
                            window.location.href = '/triptrip-master/index.php';
                        });
                    }
                });
            }, 2000);

            // Timer hitung mundur
            let timeLeft = 23 * 3600 + 58 * 60 + 47;
            const countdownElement = document.getElementById('countdown');
            setInterval(() => {
                timeLeft--;
                const hours = Math.floor(timeLeft / 3600);
                const minutes = Math.floor((timeLeft % 3600) / 60);
                const seconds = timeLeft % 60;
                countdownElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                if (timeLeft <= 0) clearInterval();
            }, 1000);
        </script>
    </body>
    </html>
    <?php
    exit;

} catch (Exception $e) {
    $log->error('Error API Midtrans', [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'response' => 'Tidak ada respons'
    ]);
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>