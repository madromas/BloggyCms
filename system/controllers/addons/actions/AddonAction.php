<?php

namespace addons\actions;

/**
 * Абстрактный базовый класс для действий управления пакетами
 * 
 * @package addons\actions
 */
abstract class AddonAction {
    
    /**
     * @var \Database Подключение к базе данных
     */
    protected $db;
    
    /**
     * @var array Параметры действия
     */
    protected $params;
    
    /**
     * @var object Контроллер
     */
    protected $controller;
    
    /**
     * @var \AddonModel Модель пакетов
     */
    protected $addonModel;
    
    /**
     * Конструктор
     * 
     * @param \Database $db
     * @param array $params
     */
    public function __construct($db, $params = []) {
        $this->db = $db;
        $this->params = $params;
        $this->addonModel = new \AddonModel($db);
    }
    
    /**
     * Установка контроллера
     * 
     * @param object $controller
     */
    public function setController($controller) {
        $this->controller = $controller;
    }
    
    /**
     * Абстрактный метод выполнения
     */
    abstract public function execute();
    
    /**
     * Рендеринг шаблона
     * 
     * @param string $template
     * @param array $data
     */
    protected function render($template, $data = []) {
        if ($this->controller) {
            $this->controller->render($template, $data);
        } else {
            throw new \Exception('Controller not set for Action');
        }
    }
    
    /**
     * Перенаправление
     * 
     * @param string $url
     */
    protected function redirect($url) {
        if ($this->controller) {
            $this->controller->redirect($url);
        } else {
            header('Location: ' . $url);
            exit;
        }
    }
    
    /**
     * Проверка прав администратора
     * 
     * @return bool
     */
    protected function checkAdminAccess() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }
}
