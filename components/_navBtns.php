<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<?php
// If user is logged in
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
    // If user is not logged in
    echo '<li class="nav-item">';
    echo '<a class="nav-link register-btn" href="./registration.php">Register Now</a>';
    echo '</li>';
}
?>