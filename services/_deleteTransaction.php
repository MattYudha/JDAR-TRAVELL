<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Include database connection
include_once("../app/_dbConnection.php");

// Setup logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('delete_transaction');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/delete_transaction.log', Logger::INFO));

// Ambil data dari request
$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? null;

if (!$order_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID tidak ditemukan']);
    exit;
}

try {
    $transactionInstance = new Transactions();
    // Lakukan soft delete (ubah is_deleted menjadi 1)
    $deleted = $transactionInstance->softDeleteTransaction($order_id);
    
    if ($deleted) {
        $log->info('Transaction soft deleted', ['order_id' => $order_id]);
        http_response_code(200);
        echo json_encode(['message' => 'Transaksi berhasil dihapus sementara']);
    } else {
        $log->warning('Transaction not found or not soft deleted', ['order_id' => $order_id]);
        http_response_code(404);
        echo json_encode(['error' => 'Transaksi tidak ditemukan atau tidak bisa dihapus']);
    }
} catch (Exception $e) {
    $log->error('Error soft deleting transaction', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>