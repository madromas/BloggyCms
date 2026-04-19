<?php

/**
* Абстрактный базовый класс для middleware
*/
abstract class Middleware {
    
    /**
    * @var mixed Подключение к базе данных
    */
    protected $db;
    
    /**
    * @var array Параметры маршрута
    */
    protected $routeParams;
    
    /**
    * Конструктор
    * @param mixed $db Подключение к БД
    * @param array $routeParams Параметры маршрута
    */
    public function __construct($db = null, $routeParams = []) {
        $this->db = $db;
        $this->routeParams = $routeParams;
    }
    
    /**
    * Обработка middleware
    * @return bool true если выполнение может продолжаться, false если нужно прервать
    */
    abstract public function handle(): bool;
    
    /**
    * Прерывание выполнения с редиректом
    * @param string $url URL для редиректа
    * @param string|null $message Сообщение об ошибке
    */
    protected function redirect($url, $message = null) {
        if ($message) {
            \Notification::error($message);
        }
        header('Location: ' . $url);
        exit;
    }
    
    /**
    * Прерывание выполнения с JSON-ответом (для AJAX)
    * @param string $message Сообщение об ошибке
    * @param int $statusCode HTTP статус код
    */
    protected function jsonError($message, $statusCode = 403) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
        exit;
    }
}