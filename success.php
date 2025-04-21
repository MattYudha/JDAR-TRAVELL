<?php
// Debugging
file_put_contents(__DIR__ . '/debug.log', "success.php diakses pada " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

require_once __DIR__ . '/vendor/autoload.php';

// Muat file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

use Midtrans\Config;
use Midtrans\Transaction;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Masukkan koneksi database
include_once("./app/_dbConnection.php");

// Mulai session
session_start();

// Validasi session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['package_id'])) {
    die("Session tidak valid. Silakan login terlebih dahulu.");
}

// Setup logging
$log = new Logger('success');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/success.log', Logger::INFO));

// Debug kunci Midtrans
error_log("Success.php - Server Key: " . ($_ENV['MIDTRANS_SERVER_KEY'] ?? 'Tidak ada'));
error_log("Success.php - Client Key: " . ($_ENV['MIDTRANS_CLIENT_KEY'] ?? 'Tidak ada'));

// Validasi kunci Midtrans
if (empty($_ENV['MIDTRANS_SERVER_KEY']) || empty($_ENV['MIDTRANS_CLIENT_KEY'])) {
    $log->error('Kunci Midtrans tidak ditemukan');
    die("Error: Server Key atau Client Key tidak ditemukan.");
}

// Konfigurasi Midtrans
Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
Config::$clientKey = $_ENV['MIDTRANS_CLIENT_KEY'];
Config::$isProduction = $_ENV['MIDTRANS_IS_PRODUCTION'] === 'true';

// Fungsi untuk mengirim email konfirmasi
function smtp_mailer($to, $subject, $msg, $log) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($_ENV['SMTP_USERNAME'], 'JDAR Travel');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $msg;

        $mail->send();
        $log->info('Email berhasil dikirim', ['kepada' => $to]);
        return true;
    } catch (Exception $e) {
        $log->error('Gagal mengirim email', ['error' => $e->getMessage()]);
        return false;
    }
}

// Ambil transaction_id dari parameter GET
$transaction_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : null;

// Ambil data dari session order_details
$quantity = $_SESSION['order_details']['quantity'] ?? 1;
$visit_date = $_SESSION['order_details']['visit_date'] ?? 'Tidak tersedia';

if (!$transaction_id) {
    $log->error('ID transaksi tidak valid');
    $errorMessage = "ID transaksi tidak valid.";
} else {
    try {
        // Ambil status transaksi dari Midtrans
        $transaction = Transaction::status($transaction_id);

        // Log status transaksi untuk debugging
        $log->info('Status transaksi dari Midtrans', [
            'transaction_id' => $transaction_id,
            'status' => $transaction->transaction_status,
            'full_response' => json_encode($transaction)
        ]);

        // Cek apakah status transaksi adalah settlement atau capture
        if (in_array($transaction->transaction_status, ['settlement', 'capture'])) {
            $tran_id = $transaction->order_id;
            $amount = $transaction->gross_amount;
            $payment_type = $transaction->payment_type;
            $user_id = $_SESSION['user_id'];
            $package_id = $_SESSION['package_id'];

            // Update card_type dan val_id di database
            $transactionInstance = new Transactions();
            $card_type = $payment_type;
            $val_id = 'completed';
            $result = $transactionInstance->updateTransaction($tran_id, $card_type, $val_id);
            if ($result !== "200") {
                $log->error('Gagal memperbarui transaksi', ['trans_id' => $tran_id]);
                $errorMessage = "Gagal memperbarui transaksi.";
            }

            // Update jumlah pembelian paket
            $packagesInstance = new Packages();
            $res = $packagesInstance->getPackage($package_id);
            if ($res && mysqli_num_rows($res) > 0) {
                $package = mysqli_fetch_assoc($res);
                $count = $package['package_booked'] + $quantity; // Sesuaikan dengan jumlah kamar/malam
                $result = $packagesInstance->updatePackagePurchase($package_id, $count);
                if ($result !== "200") {
                    $log->error('Gagal memperbarui jumlah pembelian paket', ['package_id' => $package_id]);
                    $errorMessage = "Gagal memperbarui jumlah pembelian paket.";
                }
            } else {
                $log->error('Paket tidak ditemukan', ['package_id' => $package_id]);
                $errorMessage = "Paket tidak ditemukan.";
            }

            // Ambil email pengguna
            $userInstance = new Users();
            $res = $userInstance->getUser($user_id);
            if ($res && mysqli_num_rows($res) > 0) {
                $user = mysqli_fetch_assoc($res);
                $email = $user['email'];

                // Buat konten email berupa invoice dengan detail tambahan
                $mailHtml = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #1e3a8a; text-align: center;'>Invoice Pembelian - JDAR Travel</h2>
                        <p>Terima kasih, <strong>" . htmlspecialchars($user['username']) . "</strong>, atas pembelian Anda!</p>
                        <hr style='border: 1px solid #e5e7eb; margin: 20px 0;'>
                        <h3 style='color: #1e3a8a;'>Detail Transaksi</h3>
                        <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>ID Transaksi</td>
                                <td style='padding: 8px;'>" . htmlspecialchars($tran_id) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>Tanggal Transaksi</td>
                                <td style='padding: 8px;'>" . htmlspecialchars($transaction->transaction_time) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>Nama Paket</td>
                                <td style='padding: 8px;'>" . htmlspecialchars($package['package_name']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>Lokasi</td>
                                <td style='padding: 8px;'>" . htmlspecialchars($package['package_location']) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>Tanggal Kunjungan</td>
                                <td style='padding: 8px;'>" . htmlspecialchars($visit_date) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>Jumlah Kamar/Malam</td>
                                <td style='padding: 8px;'>" . htmlspecialchars($quantity) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>Jumlah</td>
                                <td style='padding: 8px;'>Rp " . number_format($amount, 0, ',', '.') . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px; font-weight: bold;'>Metode Pembayaran</td>
                                <td style='padding: 8px;'>" . htmlspecialchars($payment_type) . "</td>
                            </tr>
                        </table>
                        <p style='text-align: center;'>
                            <a href='http://localhost/triptrip-master/auth/user_dashboard.php' style='background-color: #1e3a8a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Lihat Dashboard</a>
                        </p>
                        <p style='color: #6b7280; font-size: 12px; text-align: center;'>JDAR Travel © " . date('Y') . ". Hak cipta dilindungi.</p>
                    </div>
                ";
                $emailSent = smtp_mailer($email, 'Invoice Pembelian - JDAR Travel', $mailHtml, $log);

                // Log transaksi berhasil
                $log->info('Transaksi berhasil', [
                    'transaction_id' => $tran_id,
                    'jumlah' => $amount,
                    'email_dikirim' => $emailSent ? 'ya' : 'tidak'
                ]);
            } else {
                $log->error('Pengguna tidak ditemukan', ['user_id' => $user_id]);
                $errorMessage = "Pengguna tidak ditemukan.";
            }
        } else {
            $log->warning('Pembayaran gagal', ['transaction_id' => $transaction_id, 'status' => $transaction->transaction_status]);
            $errorMessage = "Pembayaran gagal dengan status: " . $transaction->transaction_status;
        }
    } catch (Exception $e) {
        $log->error('Gagal memproses transaksi', ['error' => $e->getMessage()]);
        $errorMessage = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Berhasil - JDAR Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        .bg-navy-blue {
            background-color: #1e3a8a;
        }
        .bg-light-blue {
            background-color: #3b82f6;
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen bg-navy-blue">
    <?php if (isset($errorMessage)): ?>
        <div class="text-center text-white bg-light-blue p-8 rounded-lg shadow-lg animate-fade-in">
            <h1 class="text-2xl font-bold mb-4">Pembayaran Gagal</h1>
            <p class="mb-4"><?php echo htmlspecialchars($errorMessage); ?></p>
            <a href="http://localhost/triptrip-master/index.php" class="bg-navy-blue hover:bg-blue-800 text-white px-4 py-2 rounded-md transition-colors duration-300">Kembali ke Beranda</a>
        </div>
    <?php else: ?>
        <div id="successPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in">
            <div class="bg-light-blue p-8 rounded-lg shadow-lg text-center text-white max-w-md w-full relative">
                <button id="closePopup" class="absolute top-2 right-2 text-white hover:text-gray-300 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <h1 class="text-2xl font-bold mb-4">Pembayaran Berhasil!</h1>
                <p class="mb-6">Terima kasih telah memilih JDAR Travel. Invoice telah dikirim ke email Anda.</p>
                <p class="mb-6">Redirecting to dashboard in 3 seconds...</p>
                <div class="flex flex-col space-y-3">
                    <a href="http://localhost/triptrip-master/auth/user_dashboard.php" class="bg-navy-blue hover:bg-blue-800 text-white px-4 py-2 rounded-md transition-colors duration-300">Lihat Dashboard</a>
                    <a href="http://localhost/triptrip-master/index.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors duration-300">Kembali ke Beranda</a>
                </div>
            </div>
        </div>

        <script>
            const closePopup = document.getElementById('closePopup');
            if (closePopup) {
                closePopup.addEventListener('click', () => {
                    window.location.href = 'http://localhost/triptrip-master/auth/user_dashboard.php';
                });
            }
            setTimeout(() => {
                window.location.href = 'http://localhost/triptrip-master/auth/user_dashboard.php';
            }, 3000);
        </script>
    <?php endif; ?>
</body>
</html>