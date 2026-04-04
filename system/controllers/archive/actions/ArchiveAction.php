<?php

namespace archive\actions;

/**
* Абстрактный класс действия для архива
*/
abstract class ArchiveAction {

    protected $db;
    protected $params;
    protected $controller;
    protected $postModel;
    protected $breadcrumbs;
    protected $pageTitle;
    
    /**
    * Конструктор действия архива
    * Инициализирует подключение к БД и модель постов
    * @param \Database $db Объект подключения к базе данных
    * @param array $params Дополнительные параметры для действия
    */
    public function __construct($db, $params = []) {
        $this->db = $db;
        $this->params = $params;
        $this->postModel = new \PostModel($db);
        $this->breadcrumbs = new \BreadcrumbsManager($db);
        $this->pageTitle = '';
        \BreadcrumbsHelper::setManager($this->breadcrumbs);
    }
    
    /**
    * Установка родительского контроллера
    * Позволяет действию получать доступ к методам контроллера
    *
    * @param \ArchiveController $controller Экземпляр контроллера архива
    */
    public function setController($controller) {
        $this->controller = $controller;
    }
    
    /**
    * Абстрактный метод выполнения действия
    * Должен быть реализован в конкретных классах-наследниках
    *
    * @return mixed Результат выполнения действия
    */
    abstract public function execute();
    
    /**
    * Добавляет элемент в хлебные крошки
    * 
    * @param string $title Название элемента
    * @param string|null $url URL элемента (null для текущего элемента)
    * @return self
    */
    protected function addBreadcrumb($title, $url = null) {
        $this->breadcrumbs->add($title, $url);
        return $this;
    }
    
    /**
    * Устанавливает заголовок страницы
    * 
    * @param string $title Заголовок
    * @return self
    */
    protected function setPageTitle($title) {
        $this->pageTitle = $title;
        return $this;
    }
    
    /**
    * Вспомогательный метод для рендеринга шаблонов
    * Делегирует рендеринг родительскому контроллеру
    *
    * @param string $template Имя шаблона для отображения
    * @param array $data Данные для передачи в шаблон
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
}