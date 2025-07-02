<!DOCTYPE html>
<html lang="en">
<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["logged_in"])) {
    echo '<script>location.href = "../index.php";</script>';
    exit;
}
?>

<?php
include_once("../app/_dbConnection.php");

if (isset($_GET['package_id']) && isset($_GET["user_id"])) {
    $user_id = filter_var($_GET["user_id"], FILTER_SANITIZE_NUMBER_INT);
    $package_id = filter_var($_GET["package_id"], FILTER_SANITIZE_NUMBER_INT);

    // Instantiate Transactions class
    $transactionInstance = new Transactions();
    
    // Verify if method exists
    if (!method_exists($transactionInstance, 'getUserTransaction')) {
        die("Error: getUserTransaction method not found in Transactions class.");
    }

    $res = $transactionInstance->getUserTransaction($user_id, $package_id);
    
    if (!$res) {
        die("Error: Failed to fetch transaction data.");
    }

    $transaction = mysqli_fetch_assoc($res);
    
    if (!$transaction) {
        die("Error: No transaction found for the given user and package.");
    }

    // Sanitize output to prevent XSS
    $trans_id = htmlspecialchars($transaction['trans_id'] ?? '');
    $visit_date = htmlspecialchars($transaction['visit_date'] ?? '');
    $package_name = htmlspecialchars($transaction['package_name'] ?? '');
    $full_name = htmlspecialchars($transaction['full_name'] ?? '');
    $address = htmlspecialchars($transaction['address'] ?? '');
    $phone = htmlspecialchars($transaction['phone'] ?? '');
    $email = htmlspecialchars($transaction['email'] ?? '');
    $quantity = (int) ($transaction['quantity'] ?? 1);
    $total_price = (float) ($transaction['total_price'] ?? 0);
    $package_price = (float) ($transaction['package_price'] ?? 0);

    // Format prices
    $formatted_package_price = 'Rp ' . number_format($package_price, 0, ',', '.');
    $formatted_total_price = 'Rp ' . number_format($total_price, 0, ',', '.');
} else {
    echo '<script>location.href = "../index.php";</script>';
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #1a2b49;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-body">
            <div class="container-sm mb-5 mt-3">
                <div class="row d-flex align-items-baseline">
                    <div class="col-xl-9">
                        <p style="color: #7e8d9f;font-size: 20px;">Invoice For >> <strong>Transaction ID: <?php echo $trans_id; ?></strong></p>
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
                                <li class="text-muted"><span class="fw-bold"><?php echo $full_name; ?></span></li>
                                <li class="text-muted"><?php echo $address; ?></li>
                                <li class="text-muted"><i class="fas fa-phone"></i> <?php echo $phone; ?></li>
                            </ul>
                        </div>
                        <div class="col-xl-4">
                            <p class="text-muted">Transaction</p>
                            <ul class="list-unstyled">
                                <li class="text-muted"><i class="fas fa-circle" style="color:#84B0CA;"></i> <span class="fw-bold">ID: </span><?php echo $trans_id; ?></li>
                                <li class="text-muted"><i class="fas fa-circle" style="color:#84B0CA;"></i> <span class="fw-bold">Transaction Date: </span><?php echo $visit_date; ?></li>
                                <li class="text-muted"><i class="fas fa-circle" style="color:#84B0CA;"></i> <span class="me-1 fw-bold">Status:</span><span class="badge bg-success text-light fw-bold">Paid</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="row my-2 mx-1 justify-content-center">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Package Name</th>
                                    <th scope="col">Tour Date</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Unit Price</th>
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td><?php echo $package_name; ?></td>
                                    <td><?php echo $visit_date; ?></td>
                                    <td><?php echo $quantity; ?></td>
                                    <td><?php echo $formatted_package_price; ?></td>
                                    <td>credit_card</td>
                                    <td><?php echo $formatted_total_price; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-xl-8">
                            <p>The payment is verified by SSL Commerz</p>
                        </div>
                        <div class="col-xl-3">
                            <ul class="list-unstyled">
                                <li class="text-muted ms-3"><span class="text-black me-4">SubTotal</span><?php echo $formatted_total_price; ?></li>
                                <li class="text-muted ms-3 mt-2"><span class="text-black me-4">Tax(0%)</span>0 IDR</li>
                            </ul>
                            <p class="text-black float-start"><span class="text-black me-3">Total Amount</span><span style="font-size: 25px;"><?php echo $formatted_total_price; ?></span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xl-10">
                            <p>Thank you for your purchase</p>
                        </div>
                        <div class="col-xl-2">
                            <button type="button" class="btn btn-outline-secondary text-capitalize print-btn">Print Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelector(".print-btn").addEventListener("click", () => {
            window.print();
        });
    </script>
</body>
</html>