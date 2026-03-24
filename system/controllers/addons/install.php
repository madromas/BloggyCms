<?php
/**
 * Скрипт установки контроллера "Установщик пакетов"
 * 
 * @var Database $db Подключение к БД
 */

// Создание таблицы для хранения пакетов (с учетом префикса)
$prefix = $db->getPrefix();
$tableName = $prefix . 'installed_addons';

$sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    system_name VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    version_major INT NOT NULL DEFAULT 1,
    version_minor INT NOT NULL DEFAULT 0,
    version_build INT NOT NULL DEFAULT 0,
    version_string VARCHAR(50) NOT NULL,
    author_name VARCHAR(255),
    author_url VARCHAR(500),
    author_email VARCHAR(255),
    description TEXT,
    type ENUM('install', 'update') NOT NULL DEFAULT 'install',
    installed_at DATETIME NOT NULL,
    updated_at DATETIME,
    is_active BOOLEAN DEFAULT 1,
    INDEX idx_system_name (system_name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$db->query($sql);

// Проверка создания таблицы
$checkSql = "SHOW TABLES LIKE '{$tableName}'";
$result = $db->fetch($checkSql);

if ($result) {
    echo "Таблица '{$tableName}' успешно создана!<br>";
} else {
    echo "Ошибка при создании таблицы '{$tableName}'<br>";
}

echo "Установка контроллера завершена.";

return true;