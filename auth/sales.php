<!DOCTYPE html>
<html lang="en">

<!-- Secure route for only admin -->
<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["logged_in"])) {
    echo '<script> location.href = "./index.php" </script>';
    exit;
}
if (!isset($_SESSION["is_admin"])) {
    echo '<script> location.href = "../user_dashboard.php" </script>';
    exit;
}
?>

<?php 
include("./components/_head.php");
include_once("../app/_dbConnection.php");

$transactionsInstance = new Transactions();
$transactions = $transactionsInstance->getAllTransactions(); // Ambil semua transaksi
$totalAmount = $transactionsInstance->getTotalTransactionAmount();
?>

<body>
    <div class="side-menu">
        <ul>
            <li><a href="./admin_dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
            <li><a href="./users.php"><i class="fa-solid fa-users"></i><span>Users</span></a></li>
            <li><a href="./packages.php"><i class="fa-solid fa-cube"></i><span>Packages</span></a></li>
            <li class="active"><a href="./sales.php"><i class="fa-solid fa-money-bill-trend-up"></i><span>Sales</span></a></li>
        </ul>
    </div>
    <div class="container">
        <?php include("./components/_header.php") ?>
        <div class="content">
            <div class="cards">
                <div class="card">
                    <div class="box">
                        <h1><?php echo htmlspecialchars($totalAmount ?? 0); ?> Rp</h1>
                        <h3>Sales</h3>
                    </div>
                    <div class="icon-case">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                </div>
                <div class='card report-btn'>
                    <div class='box'>
                        <a href="./sales_pdf.php?param=7"> Generate report of last 7 days </a>
                    </div>
                </div>
                <div class='card report-btn'>
                    <div class='box'>
                        <a href="./sales_pdf.php?param=30"> Generate report of last 30 days </a>
                    </div>
                </div>
            </div>
            <div class="content-2">
                <div class="new-users">
                    <div class="title">
                        <h2>All Transactions</h2>
                    </div>
                    <table>
                        <tr>
                            <th>Name</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Method</th>
                        </tr>
                        <?php
                        if (is_object($transactions) && $transactions->num_rows > 0) {
                            while ($row = mysqli_fetch_assoc($transactions)) {
                                echo "
                                    <tr>
                                        <td>" . htmlspecialchars($row['username'] ?? 'Unknown') . "</td>
                                        <td>" . htmlspecialchars($row['package_name'] ?? 'Unknown') . "</td>
                                        <td>" . htmlspecialchars($row['total_price'] ?? 0) . " Rp</td>
                                        <td>" . htmlspecialchars($row['card_type'] ?? 'N/A') . "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No transactions found.</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>