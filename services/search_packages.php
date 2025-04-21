<?php
header('Content-Type: application/json');
require_once '../app/Database.php'; // Path relatif dari services/ ke app/

try {
    $packages = new Packages();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $formData = $_POST;
        $destination = $formData['destination'] ?? '';
        $checkin = $formData['checkin'] ?? '';
        $checkout = $formData['checkout'] ?? '';
        $totalGuests = (int)($formData['adults'] ?? 0) + (int)($formData['children'] ?? 0);
        $accommodation = $formData['accommodation'] ?? '';
        $budget = $formData['budget'] ?? '';
        $facilities = isset($formData['facilities']) && is_array($formData['facilities']) ? $formData['facilities'] : [];

        $results = $packages->searchPackages($destination, $checkin, $checkout, $totalGuests, $accommodation, $budget, $facilities);
        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mencari paket: ' . $e->getMessage()]);
}
?>