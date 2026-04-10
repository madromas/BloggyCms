<?php

namespace debug\actions;

/**
* Абстрактный базовый класс для действий контроллера отладки
* @package debug\actions
*/
abstract class DebugAction {
    
    protected $db;
    protected $params;
    protected $controller;
    protected $debugModel;
    protected $breadcrumbs;
    protected $pageTitle;
    
    /**
    * Конструктор
    * @param \Database $db
    * @param array $params
    */
    public function __construct($db, $params = []) {
        $this->db = $db;
        $this->params = $params;
        $this->debugModel = new \DebugModel($db);
        $this->breadcrumbs = new \BreadcrumbsManager($db);
        $this->pageTitle = '';
        \BreadcrumbsHelper::setManager($this->breadcrumbs);
    }
    
    /**
    * Установка контроллера
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
    * Добавляет элемент в хлебные крошки
    * @param string $title
    * @param string|null $url
    * @return self
    */
    protected function addBreadcrumb($title, $url = null) {
        $this->breadcrumbs->add($title, $url);
        return $this;
    }
    
    /**
    * Устанавливает заголовок страницы
    * @param string $title
    * @return self
    */
    protected function setPageTitle($title) {
        $this->pageTitle = $title;
        return $this;
    }
    
    /**
    * Рендеринг шаблона
    * @param string $template
    * @param array $data
    */
    protected function render($template, $data = []) {
        if (!$this->controller) {
            throw new \Exception('Controller not set for Action');
        }
        
        if (!isset($data['breadcrumbs'])) {
            $data['breadcrumbs'] = $this->breadcrumbs;
        }
        
        if (!isset($data['title']) && $this->pageTitle) {
            $data['title'] = $this->pageTitle;
        }
        
        $this->controller->render($template, $data);
    }
    
    /**
    * Перенаправление
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
    * @return bool
    */
    protected function checkAdminAccess() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }
    
    /**
    * Проверка AJAX запроса
    * @return bool
    */
    protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
    * Отправка JSON ответа
    * @param array $data
    */
    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
    * Возвращает менеджер хлебных крошек
    * @return \BreadcrumbsManager
    */
    protected function getBreadcrumbs() {
        return $this->breadcrumbs;
    }
}