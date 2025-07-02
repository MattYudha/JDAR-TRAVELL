<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["logged_in"])) {
    header("Location: ./index.php");
    exit;
}

if (isset($_SESSION["is_admin"])) {
    header("Location: ./admin_dashboard.php");
    exit;
}

// Include _dbConnection.php
$dbConnectionPath = './app/_dbConnection.php';
if (!file_exists($dbConnectionPath)) {
    $errorMsg = "Error: File koneksi database tidak ditemukan di $dbConnectionPath. Harap periksa jalur file.";
    error_log($errorMsg);
    die($errorMsg);
}
require_once($dbConnectionPath);

// Load PHPMailer and Dotenv
require_once __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

// Setup logging
$log = new Logger('success');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/success.log', Logger::INFO));

// Function to send email confirmation
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

$user_id = $_SESSION['user_id'];
$userInstance = new Users();
$res = $userInstance->getUser($user_id);

// Periksa apakah $res adalah array dan tidak kosong
if (!$res || empty($res)) {
    error_log("Gagal mengambil data pengguna untuk user_id: $user_id");
    die("Error: Data pengguna tidak ditemukan.");
}

// Karena $res sudah array, kita tidak perlu mysqli_fetch_assoc() lagi
$user = $res; // $res sudah berupa array asosiatif
$username = $user['username'] ?? 'Tidak Diketahui';
$email = $user['email'] ?? 'Tidak Tersedia';
$date_created = $user['date_created'] ? date_format(date_create($user['date_created']), "Y-m-d") : 'Tidak Tersedia';
$phone = $user['phone'] ?? 'Tidak Tersedia';
$address = $user['address'] ?? 'Tidak Tersedia';
$full_name = $user['full_name'] ?? 'Tidak Tersedia';

$transactionInstance = new Transactions();
$transactionsRes = $transactionInstance->userAllTransactions($user_id);
$transactions = [];
if (is_array($transactionsRes)) {
    $transactions = $transactionsRes; // Jika sudah array, langsung gunakan
} else {
    // Jika hasilnya adalah mysqli_result, konversi ke array
    while ($row = mysqli_fetch_assoc($transactionsRes)) {
        $packageInstance = new Packages();
        $packageRes = $packageInstance->getPackage($row['package_id']);
        $package = $packageRes && $packageRes->num_rows > 0 ? mysqli_fetch_assoc($packageRes) : [];
        $row['package_name'] = $package['package_name'] ?? 'Paket Tidak Diketahui';
        $row['original_amount'] = $row['total_price']; // Gunakan total_price
        $transactions[] = $row;
    }
}

$testimonialInstance = new Testimonials();
$testimonialRes = $testimonialInstance->checkUserTestimonialStatus($user_id);
$testimonials = [];
if (is_array($testimonialRes)) {
    foreach ($testimonialRes as $row) {
        $testimonials[] = $row['package_id'];
    }
} else {
    while ($row = mysqli_fetch_assoc($testimonialRes)) {
        $testimonials[] = $row['package_id'];
    }
}

// Send email confirmation for the latest transaction
if (!empty($transactions)) {
    $latest_transaction = end($transactions); // Get the most recent transaction
    $tran_id = $latest_transaction['trans_id'];
    $amount = $latest_transaction['total_price']; // Ganti trans_amount dengan total_price
    $payment_type = 'credit_card'; // Kolom payment_type tidak ada, gunakan teks statis
    $package_id = $latest_transaction['package_id'];
    $quantity = $latest_transaction['quantity']; // Ambil langsung dari transaksi
    $visit_date = $latest_transaction['visit_date']; // Ambil langsung dari transaksi
    $created_at = $latest_transaction['created_at'] ?? date('Y-m-d H:i:s'); // Gunakan created_at

    // Get package details
    $packageInstance = new Packages();
    $packageRes = $packageInstance->getPackage($package_id);
    $package = $packageRes && $packageRes->num_rows > 0 ? mysqli_fetch_assoc($packageRes) : [];

    // Create email content
    $mailHtml = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #1e3a8a; text-align: center;'>Invoice Pembelian - JDAR Travel</h2>
            <p>Terima kasih, <strong>" . htmlspecialchars($username) . "</strong>, atas pembelian Anda!</p>
            <hr style='border: 1px solid #e5e7eb; margin: 20px 0;'>
            <h3 style='color: #1e3a8a;'>Detail Transaksi</h3>
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <tr>
                    <td style='padding: 8px; font-weight: bold;'>ID Transaksi</td>
                    <td style='padding: 8px;'>" . htmlspecialchars($tran_id) . "</td>
                </tr>
                <tr>
                    <td style='padding: 8px; font-weight: bold;'>Tanggal Transaksi</td>
                    <td style='padding: 8px;'>" . htmlspecialchars($created_at) . "</td>
                </tr>
                <tr>
                    <td style='padding: 8px; font-weight: bold;'>Nama Paket</td>
                    <td style='padding: 8px;'>" . htmlspecialchars($package['package_name'] ?? 'Unknown') . "</td>
                </tr>
                <tr>
                    <td style='padding: 8px; font-weight: bold;'>Lokasi</td>
                    <td style='padding: 8px;'>" . htmlspecialchars($package['package_location'] ?? 'Unknown') . "</td>
                </tr>
                <tr>
                    <td style='padding: 8px; font-weight: bold;'>Tanggal Kunjungan</td>
                    <td style='padding: 8px;'>" . htmlspecialchars($visit_date) . "</td>
                </tr>
                <tr>
                    <td style='padding: 8px; font-weight: bold;'>Jumlah Tiket</td>
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

    // Send email
    $emailSent = smtp_mailer($email, 'Invoice Pembelian - JDAR Travel', $mailHtml, $log);
    $log->info('Transaksi berhasil', [
        'transaction_id' => $tran_id,
        'jumlah' => $amount,
        'email_dikirim' => $emailSent ? 'ya' : 'tidak'
    ]);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - JDAR Travel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .checkmark-circle {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-in-out;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        .checkmark-circle i {
            color: white;
            font-size: 40px;
        }
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        .countdown {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }
        .countdown span {
            color: #007bff;
            font-weight: 600;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.3s ease;
            margin-top: 20px;
        }
        .button:hover {
            background: #0056b3;
        }
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #f44336;
            animation: confettiFall 5s infinite;
        }
        @keyframes confettiFall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        .confetti:nth-child(2n) { background: #ffeb3b; }
        .confetti:nth-child(3n) { background: #2196f3; }
        .confetti:nth-child(4n) { background: #4caf50; }
    </style>
</head>
<body>
    <!-- Confetti Animation -->
    <div class="confetti" style="left: 10%; animation-delay: 0s;"></div>
    <div class="confetti" style="left: 20%; animation-delay: 0.5s;"></div>
    <div class="confetti" style="left: 30%; animation-delay: 1s;"></div>
    <div class="confetti" style="left: 40%; animation-delay: 1.5s;"></div>
    <div class="confetti" style="left: 50%; animation-delay: 2s;"></div>
    <div class="confetti" style="left: 60%; animation-delay: 2.5s;"></div>
    <div class="confetti" style="left: 70%; animation-delay: 3s;"></div>
    <div class="confetti" style="left: 80%; animation-delay: 3.5s;"></div>
    <div class="confetti" style="left: 90%; animation-delay: 4s;"></div>

    <div class="container">
        <div class="checkmark-circle">
            <i class="fas fa-check"></i>
        </div>
        <h1>Pembayaran Berhasil!</h1>
        <p>Terima kasih telah mempercayai JDAR Travel untuk perjalanan Anda.</p>
        <div class="countdown">
            Kembali ke dashboard dalam <span id="countdown">10</span> detik...
        </div>
        <a href="/triptrip-master/auth/user_dashboard.php" class="button">Kembali Sekarang</a>
    </div>

    <script>
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');

        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '/triptrip-master/auth/user_dashboard.php';
            }
        }, 1000);
    </script>
</body>
</html>