<?php
session_start();
if (!isset($_SESSION["logged_in"]) || !isset($_SESSION["is_admin"])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['submit'])) {
    include_once("../../app/_dbConnection.php");

    $packages = new Packages();

    // Ambil data dari form
    $package_name = mysqli_real_escape_string($packages->conn, $_POST['package_name']);
    $package_desc = mysqli_real_escape_string($packages->conn, $_POST['package_desc']);
    $package_start = mysqli_real_escape_string($packages->conn, $_POST['start']);
    $package_end = mysqli_real_escape_string($packages->conn, $_POST['end']);
    $package_price = (int)mysqli_real_escape_string($packages->conn, $_POST['price']);
    $package_capacity = (int)mysqli_real_escape_string($packages->conn, $_POST['capacity']);
    $package_location = mysqli_real_escape_string($packages->conn, $_POST['loc']);
    $map_loc = mysqli_real_escape_string($packages->conn, $_POST['map']);
    $master_image = mysqli_real_escape_string($packages->conn, $_POST['master-img']);
    $extra_image_1 = mysqli_real_escape_string($packages->conn, $_POST['ex1']);
    $extra_image_2 = mysqli_real_escape_string($packages->conn, $_POST['ex2']);
    $discount_percentage = isset($_POST['discount_percentage']) ? (float)mysqli_real_escape_string($packages->conn, $_POST['discount_percentage']) : 0;

    // Ambil data fitur (checkbox)
    $features = isset($_POST['features']) ? $_POST['features'] : [];
    $is_hotel = in_array('hotel', $features) ? 1 : 0;
    $is_transport = in_array('transport', $features) ? 1 : 0;
    $is_food = in_array('food', $features) ? 1 : 0;
    $is_guide = in_array('guide', $features) ? 1 : 0;

    // Panggil metode createPackage dari kelas Packages
    $result = $packages->createPackage(
        $package_name,
        $package_desc,
        $package_start,
        $package_end,
        $package_price,
        $discount_percentage,
        $package_location,
        $is_hotel,
        $is_transport,
        $is_food,
        $is_guide,
        $package_capacity,
        $map_loc,
        $master_image,
        $extra_image_1,
        $extra_image_2
    );

    // Cek hasil dari metode createPackage
    if ($result == "200") {
        echo '<script>alert("Package created successfully!"); location.href = "../packages.php";</script>';
    } else {
        echo '<script>alert("Failed to create package: Error ' . $result . '"); location.href = "../create_package.php";</script>';
    }
} else {
    header("Location: ../packages.php");
    exit();
}
?>