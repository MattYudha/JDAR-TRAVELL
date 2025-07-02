<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once("../../app/_dbConnection.php");

if (isset($_POST['desc']) && isset($_POST['rating']) && isset($_POST['package_id'])) {
    $desc = $_POST['desc'];
    $rating = floatval($_POST['rating']); // Pastikan rating adalah angka
    $package_id = intval($_POST['package_id']); // Pastikan package_id adalah integer
    $user_id = $_SESSION['user_id']; // Ambil user_id dari sesi

    // Validasi input
    if (empty($desc) || $rating < 1 || $rating > 5 || empty($package_id) || empty($user_id)) {
        echo "<script>alert('Input tidak valid!'); location.href = '../user_dashboard.php';</script>";
        exit;
    }

    $testimonialInstance = new Testimonials();
    $packageInstance = new Packages();

    // Tambahkan testimonial
    $testimonialResult = $testimonialInstance->addTestimonial($desc, $user_id, $package_id, $rating);
    
    // Perbarui rating paket menggunakan metode updateRating
    $updateResult = $packageInstance->updateRating($user_id, $package_id, $rating, $desc);

    if ($testimonialResult && $updateResult === "200") {
        echo "<script>alert('Ulasan berhasil dikirim!'); location.href = '../user_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim ulasan. Silakan coba lagi.'); location.href = '../user_dashboard.php';</script>";
    }
} else {
    echo "<script>alert('Data tidak lengkap!'); location.href = '../user_dashboard.php';</script>";
}
?>