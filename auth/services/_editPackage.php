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
    $package_id = (int)$_POST['package_id'];
    $package_name = $_POST['package_name'];
    $package_desc = $_POST['package_desc'];
    $package_start = $_POST['start'];
    $package_end = $_POST['end'];
    $package_price = (int)$_POST['price'];
    $package_capacity = (int)$_POST['capacity'];
    $package_location = $_POST['loc'];
    $map_loc = $_POST['map'];
    $master_image = $_POST['master-img'];
    $extra_image_1 = $_POST['ex1'];
    $extra_image_2 = $_POST['ex2'];
    $discount_percentage = isset($_POST['discount_percentage']) ? (float)$_POST['discount_percentage'] : 0;

    // Ambil data fitur (checkbox)
    $features = isset($_POST['features']) ? $_POST['features'] : [];
    $is_hotel = in_array('hotel', $features) ? 1 : 0;
    $is_transport = in_array('transport', $features) ? 1 : 0;
    $is_food = in_array('food', $features) ? 1 : 0;
    $is_guide = in_array('guide', $features) ? 1 : 0;

    // Panggil metode updatePackage dari kelas Packages
    $result = $packages->updatePackage(
        $package_id,
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

    // Cek hasil dari metode updatePackage
    if ($result == "200") {
        echo '<script>alert("Package updated successfully!"); location.href = "../packages.php";</script>';
    } else {
        echo '<script>alert("Failed to update package: Error ' . $result . '"); location.href = "../edit_package.php?id=' . $package_id . '";</script>';
    }
} else {
    header("Location: ../packages.php");
    exit();
}
?>