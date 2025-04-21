<!DOCTYPE html>
<html lang="id">
<head>
    <?php include("./components/_bootstrapHead.php") ?>
    <title>Profil Pengguna</title>
    <style>
        .header {
            position: relative;
        }

        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("https://images.pexels.com/photos/1275393/pexels-photo-1275393.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1") no-repeat center center/cover;
            z-index: -1;
        }

        .fade-in-out {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .navbar-white {
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            height: 100px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            padding-left: 15px;
            margin-top: 7px;
        }

        .logo-img {
            width: 60px;
            height: auto;
            margin-right: 65px;
        }

        .nav-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 20px;
            margin-left: auto;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-links li a {
            text-decoration: none;
            color: black;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
        }

        .nav-links li a:hover {
            color: #007bff;
            border-bottom: 2px solid #007bff;
        }

        .profile-btns {
            display: flex;
            gap: 10px;
        }

        .profile-btns a {
            text-decoration: none;
            color: black;
            font-size: 16px;
        }

        .profile-btns a:hover {
            color: #007bff;
        }
    </style>
</head>

<body>
    <?php
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
    $res = $userInstance->getUser($user_id);
    $user = mysqli_fetch_assoc($res);
    $username = $user['username'];
    $email = $user['email'];
    $date_created = date_format(date_create($user['date_created']), "Y-m-d");
    $phone = $user['phone'];
    $address = $user['address'];
    $full_name = $user['full_name'];

    $transactionInstance = new Transactions();
    $res = $transactionInstance->userAllTransactions($user_id);
    $transactions = array();
    while ($row = mysqli_fetch_assoc($res)) {
        error_log("Baris transaksi: " . print_r($row, true));
        array_push($transactions, $row);
    }

    $testimonialInstance = new Testimonials();
    $res = $testimonialInstance->checkUserTestimonialStatus($user_id);
    $testimonials = array();
    while ($row = mysqli_fetch_assoc($res)) {
        array_push($testimonials, $row['package_id']);
    }
    ?>

    <div class="header fade-in-out">
        <nav id="navBar" class="navbar-white">
            <a href="../index.php" class="logo">
                <img src="logo.png" class="logo-img" alt="Logo">
            </a>
            <div class="nav-container">
                 <ul class="nav-links">
                 <li><a href="../index.php" class="active"><strong>Popular Package</strong></a></li>
                  <li><a href="../listing.php"><strong>All Package</strong></a></li>
              </ul>
                <?php
                if (isset($_SESSION["logged_in"])) {
                    echo '<div class="profile-btns">';
                    echo '<a href="./user_dashboard.php" title="Profil"><i class="fa-solid fa-address-card"></i></a>';
                    echo '<a href="../services/_logout.php" title="Keluar"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>';
                    echo '</div>';
                }
                ?>
            </div>
        </nav>
    </div>

    <div class="container mt-5">
    <div class="main-body">
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <div class="mt-3">
                                <h4><?php echo htmlspecialchars($username); ?></h4>
                                <p class="text-muted font-size-sm">Pengguna sejak: <?php echo htmlspecialchars($date_created); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row info-row">
                            <div class="col-sm-3"><h6 class="mb-0">Nama Lengkap</h6></div>
                            <div class="col-sm-9 text-secondary"><?php echo htmlspecialchars($full_name); ?></div>
                        </div>
                        <hr>
                        <div class="row info-row">
                            <div class="col-sm-3"><h6 class="mb-0">Email</h6></div>
                            <div class="col-sm-9 text-secondary"><?php echo htmlspecialchars($email); ?></div>
                        </div>
                        <hr>
                        <div class="row info-row">
                            <div class="col-sm-3"><h6 class="mb-0">Telepon</h6></div>
                            <div class="col-sm-9 text-secondary"><?php echo htmlspecialchars($phone); ?></div>
                        </div>
                        <hr>
                        <div class="row info-row">
                            <div class="col-sm-3"><h6 class="mb-0">Alamat</h6></div>
                            <div class="col-sm-9 text-secondary"><?php echo htmlspecialchars($address); ?></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-cus" href="./user_update.php">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="container">
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
                <?php
                foreach ($transactions as $record) {
                    $flag = in_array($record['package_id'], $testimonials);
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['trans_date']); ?></td>
                        <td><?php echo htmlspecialchars($record['trans_id']); ?></td>
                        <td>
                            <span class="payment-badge">
                                <?php echo htmlspecialchars($record['card_type']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($record['package_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['trans_amount']); ?></td>
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
            </tbody>
        </table>
    </div>
</div>

<?php include("../components/_footer.php"); ?>

<style>
    .container {
        max-width: 1300px;
        margin: 0 auto;
        padding: 25px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding: 20px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    h2 {
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

    .table-container {
        background: #ffffff;
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

    @media (max-width: 768px) {
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

        .nav-container {
            flex-direction: column;
            gap: 10px;
        }

        .nav-links {
            flex-direction: column;
            align-items: flex-end;
        }
    }

    body {
        background-color: #f5f7fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
        max-width: 1200px;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }
    .text-muted {
        color: #6c757d !important;
    }
    .text-secondary {
        color: #495057 !important;
        font-weight: 500;
    }
    .btn-cus {
        background-color: #007bff;
        color: white;
        border-radius: 20px;
        padding: 10px 30px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-cus:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }
    .info-row {
        padding: 10px 0;
        transition: background-color 0.3s ease;
    }
    .info-row:hover {
        background-color: #f8f9fa;
        border-radius: 10px;
    }
    h4, h6 {
        color: #333;
    }
    hr {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>

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