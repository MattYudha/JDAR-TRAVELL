<?php
include_once "../app/_dbConnection.php";

if (!isset($_POST['package_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID paket diperlukan']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["logged_in"])) {
    http_response_code(403);
    echo json_encode(['error' => 'Silakan login terlebih dahulu']);
    exit;
}

$package_id = (int)$_POST['package_id'];
$packagesInstance = new Packages();
$res = $packagesInstance->getPackage($package_id);

if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Paket tidak ditemukan']);
    exit;
}

$row = mysqli_fetch_assoc($res);
$package_start = $row['package_start'];
$curr_date = date("Y-m-d");

$datetime1 = strtotime($package_start);
$datetime2 = strtotime($curr_date);
$diff = $datetime1 - $datetime2;

if ($diff <= 0) {
    http_response_code(410);
    echo json_encode(['error' => 'Paket sudah berakhir']);
    exit;
}

$package_capacity = $row['package_capacity'];
$package_booked = $row['package_booked'];
$available_slots = $package_capacity - $package_booked;

http_response_code(200);
echo json_encode(['available_slots' => $available_slots]);
?>