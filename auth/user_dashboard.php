<?php
// Move all session and backend logic to the top before any output
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["logged_in"])) {
    echo '<script>location.href = "../index.php"</script>';
    exit;
}
if (isset($_SESSION["is_admin"])) {
    echo '<script>location.href = "./admin_dashboard.php"</script>';
    exit;
}

include_once("../app/_dbConnection.php");
$user_id = $_SESSION['user_id'];
$userInstance = new Users();
$user = $userInstance->getUser($user_id); // Langsung gunakan array yang dikembalikan
if (!$user) {
    echo '<script>alert("Pengguna tidak ditemukan!"); location.href = "../index.php"</script>';
    exit;
}
$username = $user['username'] ?? 'Pengguna';
$email = $user['email'] ?? '-';
$date_created = isset($user['date_created']) ? date_format(date_create($user['date_created']), "Y-m-d") : '-';
$phone = $user['phone'] ?? '-';
$address = $user['address'] ?? '-';
$full_name = $user['full_name'] ?? '-';

$transactionInstance = new Transactions();
$res = $transactionInstance->userAllTransactions($user_id);
$transactions = array();
while ($row = mysqli_fetch_assoc($res)) {
    error_log("Baris transaksi: " . print_r($row, true));
    array_push($transactions, $row);
}

$reviewInstance = new Reviews(); // Ganti Testimonials dengan Reviews
$res = $reviewInstance->checkUserTestimonialStatus($user_id);
$testimonials = array();
while ($row = mysqli_fetch_assoc($res)) {
    array_push($testimonials, $row['package_id']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include("./components/_bootstrapHead.php") ?>
    <title>Profil Pengguna</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #1a2b49;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .sidebar .logo-img {
            width: 50px;
            height: auto;
            margin-right: 15px;
        }

        .sidebar .logo-text {
            font-size: 24px;
            font-weight: 700;
        }

        .sidebar .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .sidebar .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .sidebar .user-info span {
            font-size: 16px;
            font-weight: 500;
        }

        .sidebar .nav-links {
            list-style: none;
            padding: 0;
        }

        .sidebar .nav-links li {
            margin-bottom: 15px;
        }

        .sidebar .nav-links li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .sidebar .nav-links li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-links li a i {
            margin-right: 10px;
        }

        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
        }

        .sidebar .logout a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .sidebar .logout a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #1a2b49;
            margin: 0;
        }

        .header .profile-btns a {
            text-decoration: none;
            color: #1a2b49;
            font-size: 18px;
            margin-left: 15px;
        }

        .header .profile-btns a:hover {
            color: #007bff;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card h4 {
            color: #1a2b49;
            margin-bottom: 15px;
        }

        .card p {
            color: #6c757d;
            margin: 0;
        }

        .card .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            transition: background 0.3s ease;
        }

        .card .info-row:hover {
            background: #f8f9fa;
            border-radius: 8px;
        }

        .card .info-row h6 {
            margin: 0;
            color: #333;
            font-weight: 600;
        }

        .card .info-row span {
            color: #495057;
            font-weight: 500;
        }

        .card hr {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin: 10px 0;
        }

        .btn-cus {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            padding: 10px 30px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-cus:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Dashboard Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .dashboard-header h2 {
            color: #1a2b49;
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }

        .search-bar {
            display: flex;
            gap: 10px;
        }

        .search-input {
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            width: 250px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
            outline: none;
        }

        .search-btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .search-btn:hover {
            background: #0056b3;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #2c3e50;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8f9fa;
            transition: background 0.2s ease;
        }

        .payment-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #f1f3f5;
            border-radius: 15px;
            font-size: 13px;
            color: #495057;
        }

        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
        }

        .invoice-btn {
            background: #28a745;
            color: white;
        }

        .invoice-btn:hover {
            background: #218838;
            color: white;
        }

        .review-btn {
            background: #6c757d;
            color: white;
        }

        .review-btn:hover {
            background: #5a6268;
            color: white;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 500;
        }

        .completed {
            background: #28a745;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .search-input {
                width: 100%;
            }

            .action-btn {
                padding: 6px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" class="logo-img" alt="Logo">
            <span class="logo-text">User Profile</span>
        </div>
        <div class="user-info">
            <img src="logo.png" alt="User Avatar">
            <span><?php echo htmlspecialchars($username); ?><br>User</span>
        </div>
        <ul class="nav-links">
            <li><a href="../index.php"><i class="fa-solid fa-fire"></i> Popular Package</a></li>
            <li><a href="../listing.php"><i class="fa-solid fa-list"></i> All Package</a></li>
        </ul>
        <div class="logout">
            <a href="../services/_logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h1>
            <div class="profile-btns">
                <a href="./user_dashboard.php" title="Profil"><i class="fa-solid fa-address-card"></i></a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="text-center">
                        <h4><?php echo htmlspecialchars($username); ?></h4>
                        <p>Pengguna sejak: <?php echo htmlspecialchars($date_created); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <h4>Informasi Pengguna</h4>
                    <div class="info-row">
                        <h6>Nama Lengkap</h6>
                        <span><?php echo htmlspecialchars($full_name); ?></span>
                    </div>
                    <hr>
                    <div class="info-row">
                        <h6>Email</h6>
                        <span><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <hr>
                    <div class="info-row">
                        <h6>Telepon</h6>
                        <span><?php echo htmlspecialchars($phone); ?></span>
                    </div>
                    <hr>
                    <div class="info-row">
                        <h6>Alamat</h6>
                        <span><?php echo htmlspecialchars($address); ?></span>
                    </div>
                    <hr>
                    <a class="btn-cus" href="./user_update.php">Edit</a>
                </div>
            </div>
        </div>

        <div class="dashboard-header">
            <h2>Semua Riwayat Pembelian</h2>
            <div class="search-bar">
                <input type="text" placeholder="Cari transaksi..." class="search-input" id="searchInput">
                <button class="search-btn">🔍</button>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Tanggal Transaksi</th>
                        <th scope="col">ID Transaksi</th>
                        <th scope="col">Metode Pembayaran</th>
                        <th scope="col">Nama Paket</th>
                        <th scope="col">Jumlah Pembayaran</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">Ulasan</th>
                    </tr>
                </thead>
                <tbody id="transactionTable">
                    <?php if (empty($transactions)) { ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Belum ada transaksi.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($transactions as $record) { ?>
                            <?php $flag = in_array($record['package_id'], $testimonials); ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['visit_date']); ?></td>
                                <td><?php echo htmlspecialchars($record['trans_id']); ?></td>
                                <td>
                                    <span class="payment-badge">
                                        credit_card <!-- Ganti dengan kolom yang sesuai jika ada -->
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($record['package_name']); ?></td>
                                <td>Rp <?php echo number_format($record['total_price'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="./generatePDF.php?package_id=<?php echo htmlspecialchars($record['package_id']); ?>&user_id=<?php echo htmlspecialchars($user_id); ?>" 
                                       class="action-btn invoice-btn">
                                        Buat
                                    </a>
                                </td>
                                <td>
                                    <?php if ($flag) { ?>
                                        <span class="status-badge completed">Selesai</span>
                                    <?php } else { ?>
                                        <a href="./user_review.php?id=<?php echo htmlspecialchars($record['package_id']); ?>" 
                                           class="action-btn review-btn">
                                            Tulis Ulasan
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("../components/_footer.php"); ?>

    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#transactionTable tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>