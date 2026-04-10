<?php

/**
* Хелпер для логирования ошибок с интеграцией в режим отладки
* @package Helpers
*/
class DebugLogger {
    
    /**
    * Логирует сообщение с автоматической записью в БД при включенном режиме отладки 
    * @param string $message Сообщение для логирования
    * @param string $type Тип: 'error', 'warning', 'info', 'debug'
    */
    public static function log($message, $type = 'info', $context = []) {

        $logMessage = sprintf(
            '[%s] %s %s',
            strtoupper($type),
            $message,
            !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );
        error_log($logMessage);
        
        $debugEnabled = SettingsHelper::get('general', 'debug_mode', false);
        if ($debugEnabled && isset($GLOBALS['db'])) {
            try {
                $data = [
                    'type' => $type === 'error' ? 'error' : ($type === 'warning' ? 'warning' : 'notice'),
                    'code' => 0,
                    'message' => $message,
                    'file' => $context['file'] ?? null,
                    'line' => $context['line'] ?? null,
                    'trace' => json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)),
                    'context' => json_encode($context, JSON_UNESCAPED_UNICODE),
                    'url' => $_SERVER['REQUEST_URI'] ?? null,
                    'method' => $_SERVER['REQUEST_METHOD'] ?? null,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_id' => $_SESSION['user_id'] ?? null
                ];
                
                $debugModel = new DebugModel($GLOBALS['db']);
                $debugModel->save($data);
            } catch (Exception $e) {
                error_log('[DebugLogger] Failed to save to DB: ' . $e->getMessage());
            }
        }
    }
    
    /**
    * Логирует ошибку
    */
    public static function error($message, $context = []) {
        self::log($message, 'error', $context);
    }
    
    /**
    * Логирует предупреждение
    */
    public static function warning($message, $context = []) {
        self::log($message, 'warning', $context);
    }
    
    /**
    * Логирует информационное сообщение
    */
    public static function info($message, $context = []) {
        self::log($message, 'info', $context);
    }
    
    /**
    * Логирует отладочное сообщение (только в режиме отладки)
    */
    public static function debug($message, $context = []) {
        $debugEnabled = SettingsHelper::get('general', 'debug_mode', false);
        if ($debugEnabled) {
            self::log($message, 'debug', $context);
        }
    }
    
    /**
    * Логирует исключение
    */
    public static function exception($exception, $context = []) {
        $context = array_merge($context, [
            'exception_file' => $exception->getFile(),
            'exception_line' => $exception->getLine(),
            'exception_trace' => $exception->getTraceAsString()
        ]);
        self::log($exception->getMessage(), 'error', $context);
    }
}