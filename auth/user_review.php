<!DOCTYPE html>
<html lang="id">
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

// Get Package id
if (isset($_GET['id'])) {
    $package_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $package_id = 0;
}

// User information
include_once("../app/_dbConnection.php");
$user_id = $_SESSION['user_id'];
$userInstance = new Users();
$res = $userInstance->getUser($user_id);

// Log tipe dan isi $res untuk debugging
error_log("getUser result type: " . gettype($res));
error_log("getUser result content: " . var_export($res, true));

// Cek apakah $res adalah mysqli_result atau array
if ($res instanceof mysqli_result) {
    $user = mysqli_fetch_assoc($res);
    if (!$user) {
        error_log("No user found for user_id: $user_id");
        die("Error: User not found.");
    }
} elseif (is_array($res)) {
    // Asumsi getUser mengembalikan array (mungkin sudah di-fetch)
    $user = $res;
    if (empty($user) || !isset($user['username'])) {
        error_log("Invalid user array for user_id: $user_id");
        die("Error: Invalid user data returned.");
    }
} else {
    error_log("Unexpected getUser result type for user_id: $user_id");
    die("Error: Invalid user data returned.");
}
$username = htmlspecialchars($user['username']);

// User Purchase Check
$transactionInstance = new Transactions();
$transactions = $transactionInstance->getUserTransaction($user_id, $package_id);
if ($transactions === false) {
    error_log("getUserTransaction failed for user_id: $user_id, package_id: $package_id");
    $row_count = 0;
} elseif (!$transactions instanceof mysqli_result) {
    error_log("getUserTransaction did not return mysqli_result: " . var_export($transactions, true));
    $row_count = 0;
} else {
    $row_count = $transactions->num_rows;
}
?>

<head>
    <?php include("./components/_bootstrapHead.php") ?>
    <title>Ulasan Paket</title>
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

        .btn-cus {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            padding: 10px 30px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-cus:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .btn-cus.disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: 0;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
        }

        .form-label {
            color: #1a2b49;
            font-weight: 500;
        }

        hr {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin: 10px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
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
            <h1>Tulis Ulasan</h1>
            <div class="profile-btns">
                <a href="./user_dashboard.php" title="Profil"><i class="fa-solid fa-address-card"></i></a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <h4>Tulis Ulasan</h4>
                    <hr>
                    <?php
                    if ($row_count > 0) {
                        echo "
                        <form class='review-form' method='post' action='./services/_review_submit.php'>
                        ";
                    } else {
                        echo "
                        <form class='review-form'>
                        ";
                    }
                    ?>
                    <div class="row">
                        <label for="desc" class="form-label">Ulasan</label>
                        <textarea required class="form-control px-2" name="desc" rows="5">Excellent!</textarea>
                    </div>
                    <hr>
                    <div class="row">
                        <label class="form-label" for="rating">Penilaian</label>
                        <input required class="form-control px-2 rating_input" placeholder="Beri nilai paket ini (1.0 - 5.0)" type="text" name="rating">
                    </div>
                    <input required type="hidden" name="package_id" value="<?php echo $package_id ?>">
                    <hr>
                    <div>
                        <?php
                        if ($row_count > 0) {
                            echo "
                            <button class='btn-cus' type='submit'>Kirim</button>
                            ";
                        } else {
                            echo "
                            <button class='btn-cus disabled' disabled>Tidak Diizinkan</button>
                            ";
                        }
                        ?>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("../components/_footer.php") ?>

    <script>
        document.querySelector(".review-form").addEventListener("submit", (e) => {
            e.preventDefault();
            document.querySelector(".rating_input").classList.remove("is-invalid");
            let val = document.querySelector(".rating_input").value;
            val = Number(val);
            if (val >= 1 && val <= 5) {
                document.querySelector(".review-form").submit();
            } else {
                document.querySelector(".rating_input").classList.add("is-invalid");
                document.querySelector(".rating_input").value = "";
            }
        });
    </script>
</body>
</html>