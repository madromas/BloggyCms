<?php

namespace fields\actions;

/**
* Абстрактный базовый класс для действий управления полями
* @package fields\actions
* @abstract
*/
abstract class FieldAction {
    
    protected $db;
    protected $params;
    protected $controller;
    protected $fieldModel;
    protected $breadcrumbs;
    protected $pageTitle;
    
    /**
    * Конструктор базового класса действий полей
    * @param \Database $db Объект подключения к базе данных
    * @param array $params Дополнительные параметры действия
    */
    public function __construct($db, $params = []) {
        $this->db = $db;
        $this->params = $params;
        $this->fieldModel = new \FieldModel($db);
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
    * Должен быть реализован в дочерних классах
    *
    * @return mixed Результат выполнения действия
    * @abstract
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
    * Получение читаемого имени типа поля
    * @param string $type Техническое название типа поля
    * @return string Читаемое название типа поля
    */
    protected function getFieldTypeName($type) {
        $types = [
            'text' => 'Текст',
            'textarea' => 'Текстовая область',
            'number' => 'Число',
            'select' => 'Выпадающий список',
            'checkbox' => 'Чекбокс',
            'file' => 'Файл',
            'date' => 'Дата',
            'color' => 'Цвет',
            'email' => 'Email',
            'url' => 'URL'
        ];
        return $types[$type] ?? $type;
    }
    
    /**
    * Получение читаемого имени типа сущности
    * @param string $entityType Техническое название типа сущности
    * @param bool $forBreadcrumbs Для хлебных крошек (именительный падеж)
    * @return string Читаемое название типа сущности
    */
    protected function getEntityName($entityType, $forBreadcrumbs = false) {
        $names = [
            'post' => ['default' => 'Записей', 'breadcrumbs' => 'Записи'],
            'page' => ['default' => 'Страниц', 'breadcrumbs' => 'Страницы'],
            'category' => ['default' => 'Категорий', 'breadcrumbs' => 'Категории'],
            'user' => ['default' => 'Пользователей', 'breadcrumbs' => 'Пользователи']
        ];
        
        if (isset($names[$entityType])) {
            return $forBreadcrumbs ? $names[$entityType]['breadcrumbs'] : $names[$entityType]['default'];
        }
        
        return $entityType;
    }
    
    /**
    * Возвращает менеджер хлебных крошек
    * @return \BreadcrumbsManager
    */
    protected function getBreadcrumbs() {
        return $this->breadcrumbs;
    }
}