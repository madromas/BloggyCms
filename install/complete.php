<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['install_complete'])) {
    echo json_encode(['success' => false, 'message' => 'Установка не завершена']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'remove_install') {
    try {
        $folder = __DIR__;
        
        $htaccess = "Order Deny,Allow\nDeny from all\n";
        file_put_contents($folder . '/.htaccess', $htaccess);
        
        $newName = dirname($folder) . '/install_removed_' . time();
        if (rename($folder, $newName)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Не удалось переименовать папку']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неизвестное действие']);
}