<?php
if (!isset($_SESSION)) {
}

// Jika user sudah login
if (isset($_SESSION["logged_in"])) {
    echo '<li class="nav-item">';
    if (isset($_SESSION['is_admin'])) {
        echo '<a class="nav-link" href="./auth/admin_dashboard.php" title="Profile"><i class="fa-solid fa-address-card"></i> Profile</a>';
    } else {
        echo '<a class="nav-link" href="./auth/user_dashboard.php" title="Profile"><i class="fa-solid fa-address-card"></i> Profile</a>';
    }
    echo '</li>';
    echo '<li class="nav-item">';
    echo '<a class="nav-link" href="./services/_logout.php" title="Logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>';
    echo '</li>';
} else {
    // Jika user belum login
    echo '<li class="nav-item">';
    echo '<a class="nav-link btn btn-primary ms-2" href="./registration.php">Register Now</a>';
    echo '</li>';
}
?>