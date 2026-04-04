<?php

namespace postblocks\actions;

/**
* Абстрактный базовый класс для всех действий модуля постблоков
* @package postblocks\actions
*/
abstract class PostBlockAction {
    
    protected $db;
    protected $params;
    protected $controller;
    protected $postBlockManager;
    protected $postBlockModel;
    protected $systemName;
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
        $this->postBlockManager = new \PostBlockManager($db);
        $this->postBlockModel = new \PostBlockModel($db);
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
    * Устанавливает системное имя постблока
    * @param string $systemName Системное имя постблока
    * @return void
    */
    public function setSystemName($systemName) {
        $this->systemName = $systemName;
    }
    
    /**
    * Абстрактный метод выполнения действия
    * @return void
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
        if ($this->controller) {
            if (!isset($data['breadcrumbs'])) {
                $data['breadcrumbs'] = $this->breadcrumbs;
            }
            if (!isset($data['title']) && $this->pageTitle) {
                $data['title'] = $this->pageTitle;
            }
            $this->controller->render($template, $data);
        } else {
            throw new \Exception('Controller not set for Action');
        }
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
    * Проверяет, имеет ли текущий пользователь права администратора
    * @return bool true если пользователь администратор, false в противном случае
    */
    protected function checkAdminAccess() {
        return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }
    
    /**
    * Отправляет JSON-ответ и завершает выполнение
    * @param array $data Данные для JSON-ответа
    * @return void
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