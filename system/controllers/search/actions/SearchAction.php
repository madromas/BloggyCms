<?php

namespace search\actions;

/**
* Абстрактный базовый класс для всех действий модуля поиска
* @package search\actions
*/
abstract class SearchAction {
    
    protected $db;
    protected $params;
    protected $controller;
    protected $searchModel;
    protected $breadcrumbs;
    protected $pageTitle;
    
    /**
    * Конструктор класса действия
    * @param object $db Подключение к базе данных
    * @param array $params Параметры запроса (по умолчанию [])
    */
    public function __construct($db, $params = []) {
        $this->db = $db;
        $this->params = $params;
        $this->searchModel = new \SearchModel($db);
        $this->breadcrumbs = new \BreadcrumbsManager($db);
        $this->pageTitle = '';
        \BreadcrumbsHelper::setManager($this->breadcrumbs);
    }
    
    /**
    * Устанавливает контроллер, вызывающий действие 
    * @param object $controller Контроллер
    * @return void
    */
    public function setController($controller) {
        $this->controller = $controller;
    }
    
    /**
    * Абстрактный метод выполнения действия
    * @return void
    */
    abstract public function execute();
    
    /**
    * Добавляет элемент в хлебные крошки
    * @param string $title Название элемента
    * @param string|null $url URL элемента (null для текущего элемента)
    * @return self
    */
    protected function addBreadcrumb($title, $url = null) {
        $this->breadcrumbs->add($title, $url);
        return $this;
    }
    
    /**
    * Добавляет элемент в начало хлебных крошек
    * @param string $title Название элемента
    * @param string|null $url URL элемента
    * @return self
    */
    protected function prependBreadcrumb($title, $url = null) {
        $this->breadcrumbs->prepend($title, $url);
        return $this;
    }
    
    /**
    * Очищает все хлебные крошки 
    * @return self
    */
    protected function clearBreadcrumbs() {
        $this->breadcrumbs->clear();
        return $this;
    }
    
    /**
    * Устанавливает заголовок страницы
    * @param string $title Заголовок
    * @return self
    */
    protected function setPageTitle($title) {
        $this->pageTitle = $title;
        return $this;
    }
    
    /**
    * Рендерит шаблон с переданными данными
    * @param string $template Путь к шаблону относительно папки views
    * @param array $data Данные для передачи в шаблон
    * @throws \Exception Если контроллер не установлен
    * @return void
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
    * Выполняет перенаправление на указанный URL
    * @param string $url URL для перенаправления
    * @return void
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
    * Проверяет, авторизован ли пользователь
    * @return bool true если пользователь авторизован, false в противном случае
    */
    protected function checkAuth() {
        return isset($_SESSION['user_id']);
    }
    
    /**
    * Возвращает менеджер хлебных крошек
    * @return \BreadcrumbsManager
    */
    protected function getBreadcrumbs() {
        return $this->breadcrumbs;
    }
}