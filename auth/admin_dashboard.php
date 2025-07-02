<!DOCTYPE html>
<html lang="en">

<!-- Secure route for only admin -->
<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["logged_in"])) {
    echo '<script> location.href = "../index.php" </script>';
    exit;
}
if (!isset($_SESSION["is_admin"])) {
    echo '<script> location.href = "./user_dashboard.php" </script>';
    exit;
}
?>

<?php 
include("./components/_head.php");
include_once("../app/_dbConnection.php");

// Inisialisasi instance kelas
$usersInstance = new Users();
$users = $usersInstance->getAllUsers(3);
$usersCount = $usersInstance->getUsersCount();

$packages = new Packages();
$packagesCount = $packages->getPackagesCount();

$transactionsInstance = new Transactions();
$transactions = $transactionsInstance->getAllTransactions(3); // Ambil 3 transaksi terbaru
$totalAmount = $transactionsInstance->getTotalTransactionAmount();

$couponsInstance = new Coupons();
$coupons = $couponsInstance->getActiveCoupons(3); // Ambil 3 kupon terbaru

// Cek apakah metode getCouponsCount() ada, jika tidak gunakan nilai default
$couponsCount = method_exists($couponsInstance, 'getCouponsCount') ? $couponsInstance->getCouponsCount() : 0;
?>

<body>
    <div class="side-menu">
        <ul>
            <li class="active"><a href="./admin_dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
            <li><a href="./users.php"><i class="fa-solid fa-users"></i><span>Users</span></a></li>
            <li><a href="./packages.php"><i class="fa-solid fa-cube"></i><span>Packages</span></a></li>
            <li><a href="./sales.php"><i class="fa-solid fa-money-bill-trend-up"></i><span>Sales</span></a></li>
            <li><a href="./coupons.php"><i class="fa-solid fa-tags"></i><span>Coupons</span></a></li>
        </ul>
    </div>
    <div class="container">
        <?php include("./components/_header.php") ?>
        <div class="content">
            <div class="cards">
                <div class="card">
                    <div class="box">
                        <h1><?php echo htmlspecialchars($usersCount ?? 0); ?></h1>
                        <h3>User(s)</h3>
                    </div>
                    <div class="icon-case">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="card">
                    <div class="box">
                        <h1><?php echo htmlspecialchars($packagesCount ?? 0); ?></h1>
                        <h3>Package(s)</h3>
                    </div>
                    <div class="icon-case">
                        <i class="fa-solid fa-cube"></i>
                    </div>
                </div>
                <div class="card">
                    <div class="box">
                        <h1><?php echo htmlspecialchars($totalAmount ?? 0); ?> Rp</h1>
                        <h3>Sales</h3>
                    </div>
                    <div class="icon-case">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                </div>
                <div class="card">
                    <div class="box">
                        <h1><?php echo htmlspecialchars($couponsCount); ?></h1>
                        <h3>Coupon(s)</h3>
                    </div>
                    <div class="icon-case">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
            </div>
            <div class="content-2 dashboard">
                <div class="recent-payments">
                    <div class="title">
                        <h2>Recent Payments</h2>
                        <a href="./sales.php" class="btn">View All</a>
                    </div>
                    <table>
                        <tr>
                            <th>Name</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Payment Time</th>
                        </tr>
                        <?php
                        if (is_object($transactions) && $transactions->num_rows > 0) {
                            while ($row = mysqli_fetch_assoc($transactions)) {
                                echo "
                                    <tr>
                                        <td>" . htmlspecialchars($row['username'] ?? 'Unknown') . "</td>
                                        <td>" . htmlspecialchars($row['package_name'] ?? 'Unknown') . "</td>
                                        <td>" . htmlspecialchars($row['trans_amount'] ?? 0) . " Rp</td>
                                        <td>" . htmlspecialchars($row['trans_date'] ?? 'N/A') . "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No recent payments found.</td></tr>";
                        }
                        ?>
                    </table>
                </div>
                <div class="new-users">
                    <div class="title">
                        <h2>New Users</h2>
                        <a href="./users.php" class="btn">View All</a>
                    </div>
                    <table>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Reg Time</th>
                        </tr>
                        <?php
                        if (is_object($users) && $users->num_rows > 0) {
                            while ($user = mysqli_fetch_assoc($users)) {
                                echo "
                                    <tr>
                                        <td>" . htmlspecialchars($user['username'] ?? 'Unknown') . "</td>
                                        <td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($user['date_created'] ?? 'N/A') . "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No new users found.</td></tr>";
                        }
                        ?>
                    </table>
                </div>
                <div class="new-coupons">
                    <div class="title">
                        <h2>Active Coupons</h2>
                        <a href="./coupons.php" class="btn">Manage Coupons</a>
                    </div>
                    <table>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Discount (%)</th>
                            <th>Valid Until</th>
                        </tr>
                        <?php
                        if (is_object($coupons) && $coupons->num_rows > 0) {
                            while ($coupon = mysqli_fetch_assoc($coupons)) {
                                echo "
                                    <tr>
                                        <td>" . htmlspecialchars($coupon['coupon_code'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($coupon['coupon_desc'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($coupon['discount_percentage'] ?? 0) . "%</td>
                                        <td>" . date('d M Y', strtotime($coupon['valid_until'] ?? date('Y-m-d'))) . "</td>
                                    </tr>";
                            }
                        } elseif (is_array($coupons) && count($coupons) > 0) {
                            foreach ($coupons as $coupon) {
                                echo "
                                    <tr>
                                        <td>" . htmlspecialchars($coupon['coupon_code'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($coupon['coupon_desc'] ?? 'N/A') . "</td>
                                        <td>" . htmlspecialchars($coupon['discount_percentage'] ?? 0) . "%</td>
                                        <td>" . date('d M Y', strtotime($coupon['valid_until'] ?? date('Y-m-d'))) . "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No active coupons found.</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>