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
    echo '<script> location.href = "../user_dashboard.php" </script>';
    exit;
}
?>

<?php
// Debug: Pastikan file _dbConnection.php di-include
$path = realpath("../app/_dbConnection.php");
if (!$path || !file_exists($path)) {
    die("Error: File _dbConnection.php not found at path: ../app/_dbConnection.php");
}
include_once("../app/_dbConnection.php");

// Debug: Pastikan kelas Transactions dimuat dan metode tersedia
$transitionInstance = new Transactions();
if (!method_exists($transitionInstance, 'getRangedTransitions')) {
    die("Error: Method getRangedTransitions() not found in Transactions class.");
}
if (!method_exists($transitionInstance, 'getRangedTransitionsTotal')) {
    die("Error: Method getRangedTransitionsTotal() not found in Transactions class.");
}

date_default_timezone_set("Asia/Dhaka");

if (isset($_GET['param'])) {
    $days = (int)$_GET["param"];
    $transitions = $transitionInstance->getRangedTransitions($days);
    $date_created = date("Y-m-d H:i");
    $total = $transitionInstance->getRangedTransitionsTotal($days);
} else {
    // Default jika param tidak ada
    $days = 30; // Default ke 30 hari
    $transitions = $transitionInstance->getRangedTransitions($days);
    $date_created = date("Y-m-d H:i");
    $total = $transitionInstance->getRangedTransitionsTotal($days);
}
?>

<!-- User Information -->
<?php
$user_id = $_SESSION['user_id'];
$userInstance = new Users();
$res = $userInstance->getUser($user_id);
$user = mysqli_fetch_assoc($res);
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="card">
        <div class="card-body">
            <div class="container-sm mb-5 mt-3">
                <div class="row d-flex align-items-baseline">
                    <div class="col-xl-9">
                        <p style="color: #7e8d9f;font-size: 20px;">Sales Report of last >> <strong><?php echo $days ?> Days</strong></p>
                    </div>
                    <hr>
                </div>
                <div class="container">
                    <div class="col-md-12">
                        <div class="text-center">
                            <i class="fab fa-mdb fa-4x ms-0"></i>
                            <p class="pt-0 brand">JDAR TRAVEL</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8">
                            <ul class="list-unstyled">
                                <li class="text-muted"><span class="fw-bold">Print Date: </span><?php echo htmlspecialchars($date_created); ?></li>
                                <li class="text-muted"><span class="fw-bold">Print By: </span><?php echo htmlspecialchars($user['username']); ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row my-2 mx-1 justify-content-center">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Payment Date</th>
                                    <th scope="col">Package</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($transitions && $transitions->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($transitions)) {
                                        echo "
                                            <tr>
                                                <td>" . htmlspecialchars($row['created_at']) . "</td>
                                                <td>" . htmlspecialchars($row['package_name']) . "</td>
                                                <td>" . htmlspecialchars($row['username']) . "</td>
                                                <td>" . htmlspecialchars($row['total_price']) . " Rp</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No transactions found for the last $days days.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-xl-8">
                            <p>All payments are verified by SSL Commerz</p>
                        </div>
                        <div class="col-xl-3">
                            <ul class="list-unstyled">
                                <li class="text-muted ms-3"><span class="text-black me-4">SubTotal</span><?php echo htmlspecialchars($total); ?> Rp</li>
                                <li class="text-muted ms-3 mt-2"><span class="text-black me-4">Tax(0%)</span>0 Rp</li>
                            </ul>
                            <p class="text-black float-start"><span class="text-black me-3">Total Amount</span><span style="font-size: 25px;"><?php echo htmlspecialchars($total); ?></span> Rp</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xl-2">
                            <button type="button" class="btn btn-outline-secondary text-capitalize print-btn">Print Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelector(".print-btn").addEventListener("click", e => {
            window.print();
        });
    </script>
</body>
</html>