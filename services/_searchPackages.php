<?php
header('Content-Type: application/json');
require_once '../app/_dbConnection.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Gunakan prepared statement
    $stmt = $conn->prepare("SELECT * FROM packages WHERE package_name LIKE ? LIMIT 5");
    $likeQuery = "%$query%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = [
            'package_name' => htmlspecialchars($row['package_name']),
            'package_location' => htmlspecialchars($row['package_location']),
        ];
    }

    echo json_encode($results);
} else {
    echo json_encode(['error' => 'Invalid search query']);
}
?>