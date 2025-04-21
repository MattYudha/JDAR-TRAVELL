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

// Validasi user_id
if (!isset($_SESSION['user_id'])) {
    die("Silakan login terlebih dahulu.");
}

// Validasi parameter
$user_id = filter_var($_GET['user'] ?? '', FILTER_VALIDATE_INT);
$package_id = filter_var($_GET['package'] ?? '', FILTER_VALIDATE_INT);
$quantity = filter_var($_GET['quantity'] ?? '', FILTER_VALIDATE_INT);
$visit_date = $_GET['visit_date'] ?? '';
$total_price = filter_var($_GET['total_price'] ?? '', FILTER_VALIDATE_INT);

if (!$user_id || !$package_id || !$quantity || !$visit_date || !$total_price) {
    die("Parameter tidak valid.");
}

// Setup logging
$log = new Logger('midtrans');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/midtrans.log', Logger::INFO));

// Debug kunci Midtrans
error_log("Server Key: " . ($_ENV['MIDTRANS_SERVER_KEY'] ?? 'Not set'));
error_log("Client Key: " . ($_ENV['MIDTRANS_CLIENT_KEY'] ?? 'Not set'));
error_log("Is Production: " . ($_ENV['MIDTRANS_IS_PRODUCTION'] ?? 'Not set'));

// Validasi kunci Midtrans
if (empty($_ENV['MIDTRANS_SERVER_KEY']) || empty($_ENV['MIDTRANS_CLIENT_KEY'])) {
    $log->error('Midtrans keys are missing');
    die("Error: Server Key atau Client Key tidak ditemukan.");
}

// Konfigurasi Midtrans
Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
Config::$clientKey = $_ENV['MIDTRANS_CLIENT_KEY'];
Config::$isProduction = $_ENV['MIDTRANS_IS_PRODUCTION'] === 'true';

// Ambil data pengguna
$userInstance = new Users();
$res = $userInstance->getUser($user_id);
$user = mysqli_fetch_assoc($res);
if (!$user) {
    die("Pengguna tidak ditemukan.");
}

// Ambil data paket
$packageInstance = new Packages();
$res = $packageInstance->getPackage($package_id);
$package = mysqli_fetch_assoc($res);
if (!$package) {
    die("Paket tidak ditemukan.");
}

// Data transaksi
$transaction_details = [
    'order_id' => 'JDAR_' . uniqid(),
    'gross_amount' => $total_price, // Total harga berdasarkan jumlah kamar/malam
];

$customer_details = [
    'first_name' => $user['username'],
    'email' => $user['email'],
    'phone' => $user['phone'] ?? "081234567890",
    'billing_address' => [
        'address' => $user['address'] ?? "Jl. Contoh No. 123",
        'city' => "Jakarta",
        'postal_code' => "10110",
        'country_code' => "IDN",
    ],
    'shipping_address' => [
        'address' => $user['address'] ?? "Jl. Contoh No. 123",
        'city' => "Jakarta",
        'postal_code' => "10110",
        'country_code' => "IDN",
    ],
];

$items = [
    [
        'id' => $package['package_id'],
        'price' => $package['package_price'],
        'quantity' => $quantity,
        'name' => $package['package_name'] . " ($quantity malam)",
    ]
];

$transaction_data = [
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $items,
    // Tambahkan redirect URL secara eksplisit untuk Midtrans
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
    $amount = $total_price;
    $payment_type = 'Midtrans';
    $user_id = $_SESSION['user_id'];
    $_SESSION['package_id'] = $package['package_id'];
    $package_id = $_SESSION['package_id'];
    $status = 'pending';
    $created_at = date('Y-m-d H:i:s');

    $transactionInstance = new Transactions();
    $transactionInstance->createNewTransaction($tran_id, $user_id, $package_id, $amount, $created_at, $payment_type, $status, $created_at);

    // Simpan detail pesanan ke session untuk digunakan di success.php
    $_SESSION['order_details'] = [
        'quantity' => $quantity,
        'visit_date' => $visit_date,
        'amount' => $amount,
    ];

    // Log transaksi
    $log->info('Transaction created', ['order_id' => $tran_id, 'amount' => $amount]);

    // Tampilkan halaman loading sebelum redirect
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Redirecting to Payment...</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo $_ENV['MIDTRANS_CLIENT_KEY']; ?>"></script>
    </head>
    <body class="flex items-center justify-center h-screen bg-navy-blue">
        <div class="text-center text-white">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-light-blue mx-auto mb-4"></div>
            <p class="text-lg">Redirecting to payment...</p>
        </div>
        <script>
            setTimeout(() => {
                snap.pay('<?php echo $snapToken; ?>', {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        // Perbaiki URL redirect
                        window.location.href = '/triptrip-master/success.php?order_id=' + result.order_id;
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        // Perbaiki URL redirect
                        window.location.href = '/triptrip-master/pending.php?order_id=' + result.order_id;
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        fetch('/triptrip-master/app/_deleteTransaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order_id: '<?php echo $tran_id; ?>' })
                        }).then(() => {
                            // Perbaiki URL redirect
                            window.location.href = '/triptrip-master/error.php?order_id=' + result.order_id;
                        });
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        fetch('/triptrip-master/app/_deleteTransaction.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order_id: '<?php echo $tran_id; ?>' })
                        }).then(() => {
                            // Perbaiki URL redirect
                            window.location.href = '/triptrip-master/index.php';
                        });
                    }
                });
            }, 2000);
        </script>
    </body>
    </html>
    <?php
    exit;

} catch (Exception $e) {
    $log->error('Midtrans API Error', [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'response' => $e->getResponse() ? json_encode($e->getResponse()) : 'No response'
    ]);
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>