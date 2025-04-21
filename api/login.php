<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Token CSRF tidak valid']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email dan kata sandi wajib diisi']);
    exit;
}

// Gunakan class Auth dari _dbConnection.php
require_once '../app/_dbConnection.php';

$auth = new Auth();

// Cek status akun
if (!$auth->checkAccountStatus($email)) {
    echo json_encode(['success' => false, 'message' => 'Akun Anda tidak aktif']);
    exit;
}

// Coba login
try {
    $result = $auth->loginUser($email, $password);
    if ($result === "200") {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat login']);
    }
} catch (Exception $e) {
    $status = http_response_code();
    if ($status == 404) {
        // Periksa header untuk menentukan penyebab error
        $headers = headers_list();
        if (in_array("HTTP/1.0 404 Email Not Found", $headers)) {
            echo json_encode(['success' => false, 'message' => 'Email tidak ditemukan']);
        } elseif (in_array("HTTP/1.0 404 Password Incorrect", $headers)) {
            echo json_encode(['success' => false, 'message' => 'Kata sandi salah']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email atau kata sandi salah']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan, coba lagi']);
    }
}
?>