<?php

/**
* Класс для управления уведомлениями (тостами) через сессию
* @package Core
*/
class Notification {
    
    /**
    * Устанавливает уведомление об успешной операции 
    * @param string $message Текст уведомления
    * @return void
    */
    public static function success($message) {
        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => $message
        ];
    }

    /**
    * Устанавливает уведомление об ошибке
    * @param string $message Текст уведомления
    * @return void
    */
    public static function error($message) {
        $_SESSION['toast'] = [
            'type' => 'danger',
            'message' => $message
        ];
    }

    /**
    * Устанавливает предупреждающее уведомление
    * @param string $message Текст уведомления
    * @return void
    */
    public static function warning($message) {
        $_SESSION['toast'] = [
            'type' => 'warning',
            'message' => $message
        ];
    }

    /**
    * Устанавливает информационное уведомление
    * @param string $message Текст уведомления
    * @return void
    */
    public static function info($message) {
        $_SESSION['toast'] = [
            'type' => 'info',
            'message' => $message
        ];
    }
}