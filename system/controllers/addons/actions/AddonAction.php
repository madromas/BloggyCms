<?php

namespace addons\actions;

/**
 * Абстрактный базовый класс для действий управления пакетами
 * 
 * @package addons\actions
 */
abstract class AddonAction {
    
    protected $db;
    protected $params;
    protected $controller;
    protected $addonModel;
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
        $this->addonModel = new \AddonModel($db);
        $this->breadcrumbs = new \BreadcrumbsManager($db);
        $this->pageTitle = '';
        \BreadcrumbsHelper::setManager($this->breadcrumbs);
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
    * @param string $title
    * @return self
    */
    protected function setPageTitle($title) {
        $this->pageTitle = $title;
        return $this;
    }
    
    /**
     * Абстрактный метод выполнения
     */
    abstract public function execute();
    
    /**
    * Рендеринг шаблона
    * @param string $template
    * @param array $data
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
