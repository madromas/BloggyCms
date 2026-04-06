<?php

/**
* Класс для логирования событий в файл
* @package Core
*/
class Logger {
    
    /** @var string Путь к файлу лога */
    private static $logFile = __DIR__ . '/../../logs/app.log';
    
    /**
    * Инициализирует систему логирования 
    * @return void
    */
    public static function init() {
        $logDir = dirname(self::$logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }
    
    /**
    * Записывает сообщение в лог
    * @param mixed $message Сообщение для логирования (может быть массивом/объектом)
    * @param string $type Тип сообщения (INFO, ERROR, DEBUG и т.д.)
    * @return void
    */
    public static function log($message, $type = 'INFO') {
        self::init();
        
        $message = is_array($message) || is_object($message) 
            ? print_r($message, true) 
            : (string)$message;
        
        $logMessage = sprintf("[%s][%s] %s\n", 
            date('Y-m-d H:i:s'), 
            $type, 
            $message
        );
        
        error_log($logMessage, 3, self::$logFile);
    }
    
    /**
    * Записывает сообщение об ошибке 
    * @param mixed $message Сообщение об ошибке
    * @return void
    */
    public static function error($message) {
        self::log($message, 'ERROR');
    }
    
    /**
    * Записывает отладочное сообщение
    * @param mixed $message Отладочное сообщение
    * @return void
    */
    public static function debug($message) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            self::log($message, 'DEBUG');
        }
    }

    /**
    * Записывает информационное сообщение
    * @param mixed $message Сообщение для логирования
    * @return void
    */
    public static function info($message) {
        self::log($message, 'INFO');
    }
}