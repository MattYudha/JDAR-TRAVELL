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

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Semua bidang wajib diisi']);
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit;
}

// Gunakan class Auth dari _dbConnection.php
require_once '../app/_dbConnection.php';

$auth = new Auth();

// Cek apakah username sudah ada
if (!$auth->checkUserName($username)) {
    echo json_encode(['success' => false, 'message' => 'Username sudah terdaftar']);
    exit;
}

// Cek apakah email sudah ada
if (!$auth->checkEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
    exit;
}

// Buat pengguna baru
$result = $auth->createUser($username, $email, $password);

if ($result === "200") {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mendaftar']);
}
?>