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
$username = $user['username'];
$email = $user['email'];
$phone = $user['phone'];
$address = $user['address'];
$full_name = $user['full_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("./components/_bootstrapHead.php") ?>
    <title>Package Review</title>
    <link rel="stylesheet" href="../styles/style.css"> <!-- Tambahkan link ke file style.css -->
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

        .card h2 {
            color: #1a2b49;
            margin-bottom: 15px;
            text-align: center;
        }

        .card hr {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin: 10px 0;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
            outline: none;
        }

        .btn-cus {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            padding: 10px 30px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-cus:hover {
            background-color: #0056b3;
            transform: scale(1.05);
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
            <li><a href="../mainpage.html"><i class="fa-solid fa-info-circle"></i> About JDAR</a></li>
        </ul>
        <div class="logout">
            <a href="../services/_logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Edit Informasi Anda</h1>
            <div class="profile-btns">
                <a href="./user_dashboard.php" title="Profil"><i class="fa-solid fa-address-card"></i></a>
            </div>
        </div>

        <div class="card">
            <h2>Edit Your Current Information</h2>
            <hr>
            <form class="review-form" method="post" action="./services/_user_update.php">
                <div class="row mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input required class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($full_name); ?>">
                </div>
                <div class="row mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input required class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="row mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input required class="form-control" type="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                </div>
                <div class="row mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea required class="form-control" id="address" name="address" rows="5"><?php echo htmlspecialchars($address); ?></textarea>
                </div>
                <hr>
                <div class="text-center">
                    <button class="btn-cus" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

    <?php include("../components/_footer.php") ?>
</body>
</html>