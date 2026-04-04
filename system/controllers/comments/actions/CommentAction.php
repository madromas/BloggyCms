<?php

namespace comments\actions;

/**
* Абстрактный базовый класс для действий с комментариями
* @package comments\actions
* @abstract
*/
abstract class CommentAction {
    
    protected $db;
    protected $params;
    protected $controller;
    protected $commentModel;
    protected $postModel;
    protected $userModel;
    protected $categoryModel;
    protected $breadcrumbs;
    protected $pageTitle;
    
    /**
    * Конструктор базового класса действий комментариев
    * @param \Database $db Объект подключения к базе данных
    * @param array $params Дополнительные параметры действия
    */
    public function __construct($db, $params = []) {
        $this->db = $db;
        $this->params = $params;

        \AuthHelper::init();

        $this->commentModel = new \CommentModel($db);
        $this->postModel = new \PostModel($db);
        $this->userModel = new \UserModel($db);
        $this->categoryModel = new \CategoryModel($db);
        $this->breadcrumbs = new \BreadcrumbsManager($db);
        $this->pageTitle = '';
        
        \BreadcrumbsHelper::setManager($this->breadcrumbs);
    }
    
    /**
    * Установка контроллера для действия
    * @param object $controller Объект контроллера
    * @return void
    */
    public function setController($controller) {
        $this->controller = $controller;
    }
    
    /**
    * Абстрактный метод выполнения действия
    * @return mixed Результат выполнения действия
    * @abstract
    */
    abstract public function execute();
    
    /**
    * Добавляет элемент в хлебные крошки
    * @param string $title Название элемента
    * @param string|null $url URL элемента
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
    * Рендеринг шаблона с данными
    * @param string $template Путь к файлу шаблона
    * @param array $data Массив данных для передачи в шаблон
    * @return void
    * @throws \Exception Если контроллер не установлен
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
    * Перенаправление на указанный URL
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
    * Проверка доступа администратора
    * @return bool true если пользователь имеет административные права
    */
    protected function checkAdminAccess() {
        return \Auth::isAdmin();
    }
    
    /**
    * Проверка типа запроса
    * @return bool true если запрос является AJAX-запросом
    */
    protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
    * Получение ID текущего пользователя
    * @return int|null ID пользователя или null если пользователь не авторизован
    */
    protected function getCurrentUserId() {
        return \Auth::getUserId();
    }
    
    /**
    * Проверка статуса администратора
    * @return bool true если пользователь является администратором
    */
    protected function isAdmin() {
        return \Auth::isAdmin();
    }
    
    /**
    * Возвращает менеджер хлебных крошек
    * @return \BreadcrumbsManager
    */
    protected function getBreadcrumbs() {
        return $this->breadcrumbs;
    }
}