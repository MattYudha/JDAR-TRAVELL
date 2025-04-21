<?php
require_once '../vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

session_start();

// Setup logging
$log = new Logger('auth');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/auth.log', Logger::INFO));

// Log aktivitas logout
$log->info('User logged out', ['session_id' => session_id()]);

session_destroy();
header('Location: ../index.php');
exit;
?>