<?php
header('Content-Type: application/json');
require_once '../app/Database.php'; // Pastikan path relatif dari services/ ke app/ sesuai

try {
    $packages = new Packages();
    $destinations = $packages->getDistinctDestinations();

    // Debugging: Tampilkan data untuk memastikan
    if (empty($destinations)) {
        error_log("Tidak ada destinasi ditemukan."); // Catat ke log server
        echo json_encode([]); // Kembalikan array kosong jika tidak ada data
    } else {
        echo json_encode($destinations);
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error di get_destinations.php: " . $e->getMessage()); // Catat error ke log
    echo json_encode(['error' => 'Gagal mengambil destinasi: ' . $e->getMessage()]);
}
?>