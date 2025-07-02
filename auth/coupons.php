<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["logged_in"])) {
    echo '<script> location.href = "../index.php" </script>';
    exit;
}
if (!isset($_SESSION["is_admin"])) {
    echo '<script> location.href = "../user_dashboard.php" </script>';
    exit;
}
?>

<?php 
include_once("../app/_dbConnection.php");

// Inisialisasi instance kelas Coupons
$couponsInstance = new Coupons();

// Proses tambah kupon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    $coupon_desc = $_POST['coupon_desc'];
    $discount_percentage = (int)$_POST['discount_percentage'];
    $valid_until = $_POST['valid_until'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $couponsInstance->getConnection()->prepare(
        "INSERT INTO coupons (coupon_code, coupon_desc, discount_percentage, valid_until, is_active) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssisi", $coupon_code, $coupon_desc, $discount_percentage, $valid_until, $is_active);

    if ($stmt->execute()) {
        echo '<script> location.href = "./coupons.php" </script>';
        exit;
    } else {
        echo '<script> alert("Gagal menambahkan kupon: ' . $stmt->error . '"); </script>';
    }
    $stmt->close();
}

// Proses hapus kupon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_coupon'])) {
    if (isset($_POST['coupon_id'])) {
        $coupon_id = (int)$_POST['coupon_id'];
        $stmt = $couponsInstance->getConnection()->prepare("DELETE FROM coupons WHERE coupon_id = ?");
        $stmt->bind_param("i", $coupon_id);

        if ($stmt->execute()) {
            echo '<script> location.href = "./coupons.php" </script>';
            exit;
        } else {
            echo '<script> alert("Gagal menghapus kupon: ' . $stmt->error . '"); </script>';
        }
        $stmt->close();
    }
}

// Proses logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    echo '<script> location.href = "../index.php" </script>';
    exit;
}

// Ambil semua kupon
$coupons = $couponsInstance->getConnection()->query("SELECT * FROM coupons");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDIRTRIP - Coupons Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }
        
        .side-menu {
            width: 220px;
            background: linear-gradient(180deg, #0077b6 0%, #023e8a 100%);
            color: white;
            padding: 20px 0;
            height: 100vh;
            position: fixed;
        }
        
        .side-menu ul {
            list-style: none;
        }
        
        .side-menu li {
            padding: 15px 20px;
            margin: 5px 0;
            transition: 0.3s;
        }
        
        .side-menu li:hover, .side-menu li.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 5px solid white;
        }
        
        .side-menu a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .side-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .container {
            flex: 1;
            margin-left: 220px;
            display: flex;
            flex-direction: column;
            width: calc(100% - 220px);
        }
        
        .header {
            background-color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            font-family: 'Brush Script MT', cursive;
        }
        
        .logout-btn {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #0b5ed7;
        }
        
        .content {
            padding: 30px;
            flex: 1;
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .card h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-control {
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 100%;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .checkbox-group label {
            margin-left: 10px;
        }
        
        .btn {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        
        .btn:hover {
            background-color: #0b5ed7;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        
        .delete-btn:hover {
            background-color: #c82333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            color: #333;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-active {
            background-color: #198754;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
        }
        
        .status-inactive {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
        }
        
        .stats {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 16px;
        }
        
        .stats-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="side-menu">
        <ul>
            <li class="active"><a href="./admin_dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
            <li><a href="./users.php"><i class="fa-solid fa-users"></i><span>Users</span></a></li>
            <li><a href="./packages.php"><i class="fa-solid fa-cube"></i><span>Packages</span></a></li>
            <li><a href="./sales.php"><i class="fa-solid fa-money-bill-trend-up"></i><span>Sales</span></a></li>
            <li class="active"><a href="./coupons.php"><i class="fa-solid fa-tags"></i><span>Coupons</span></a></li>
        </ul>
    </div>
    
    <div class="container">
        <div class="header">
            <div class="logo">JDIRTRIP</div>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn">Logout</button>
            </form>
        </div>
        
        <div class="content">
            <div class="card">
                <div style="display: flex; justify-content: center; gap: 20px;">
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; padding: 20px;">
                        <div class="stats-icon">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <div class="stats">
                            <?php echo $coupons ? $coupons->num_rows : 0; ?>
                        </div>
                        <div class="stats-label">Coupon(s)</div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h2>All Coupons</h2>
                
                <form method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control" name="coupon_code" placeholder="Kode Kupon" required>
                        <input type="text" class="form-control" name="coupon_desc" placeholder="Deskripsi Kupon" required>
                        <input type="number" class="form-control" name="discount_percentage" placeholder="Persentase Diskon (%)" min="0" max="100" required>
                        <input type="date" class="form-control" name="valid_until" required>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" checked>
                        <label for="is_active">Aktif</label>
                    </div>
                    
                    <button type="submit" name="add_coupon" class="btn">Tambah Kupon</button>
                </form>
                
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Discount (%)</th>
                            <th>Valid Until</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($coupons && $coupons->num_rows > 0) {
                            while ($coupon = mysqli_fetch_assoc($coupons)) {
                                echo "
                                    <tr>
                                        <td>" . htmlspecialchars($coupon['coupon_code'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($coupon['coupon_desc'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($coupon['discount_percentage'] ?? 0) . "%</td>
                                        <td>" . date('d M Y', strtotime($coupon['valid_until'] ?? date('Y-m-d'))) . "</td>
                                        <td>" . ($coupon['is_active'] ? '<span class="status-active">Aktif</span>' : '<span class="status-inactive">Tidak Aktif</span>') . "</td>
                                        <td>
                                            <form method='POST' style='display: inline;'>
                                                <input type='hidden' name='coupon_id' value='" . htmlspecialchars($coupon['coupon_id'] ?? '') . "'>
                                                <button type='submit' name='delete_coupon' class='delete-btn'>Hapus</button>
                                            </form>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Tidak ada kupon ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>