<?php
header('Content-Type: application/json');

$host = $_POST['db_host'] ?? '';
$port = $_POST['db_port'] ?? '3306';
$user = $_POST['db_user'] ?? '';
$pass = $_POST['db_pass'] ?? '';

try {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Подключение успешно']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}