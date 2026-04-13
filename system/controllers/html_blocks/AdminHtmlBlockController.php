<?php

/**
* Контроллер управления HTML-блоками в админ-панели
* @package controllers
*/
class AdminHtmlBlockController extends Controller {
    
    private $htmlBlockModel;
    private $blockTypeManager;
    
    /**
    * Конструктор контроллера HTML-блоков
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->htmlBlockModel = new HtmlBlockModel($db);
        $this->blockTypeManager = new HtmlBlockTypeManager($db);
    }
    
    /**
    * Действие: Главная страница управления HTML-блоками
    * @return mixed
    */
    public function adminIndexAction() {
        $action = new \html_blocks\actions\AdminIndex($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие: Выбор типа блока при создании
    * @return mixed
    */
    public function selectTypeAction() {
        $action = new \html_blocks\actions\AdminSelectType($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие: Создание нового HTML-блока
    * @return mixed
    */
    public function createAction() {
        $action = new \html_blocks\actions\AdminCreate($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие: Редактирование существующего HTML-блока
    * @param int|null $id ID редактируемого блока
    * @return mixed
    */
    public function editAction($id = null) {
        if (!$id) {
            \Notification::error('ID блока не указан');
            $this->redirect(ADMIN_URL . '/html-blocks');
            return;
        }
        
        $action = new \html_blocks\actions\AdminEdit($this->db);
        $action->setController($this);
        $action->setId($id);
        return $action->execute();
    }
    
    /**
    * Действие: Удаление HTML-блока
    * @param int|null $id ID удаляемого блока
    * @return mixed
    */
    public function deleteAction($id = null) {
        if (!$id) {
            \Notification::error('ID блока не указан');
            $this->redirect(ADMIN_URL . '/html-blocks');
            return;
        }
        
        $action = new \html_blocks\actions\AdminDelete($this->db);
        $action->setController($this);
        $action->setId($id);
        return $action->execute();
    }
    
    /**
    * Действие: Получение настроек типа блока через AJAX
    * @return mixed JSON-ответ с настройками типа блока
    */
    public function getBlockSettingsAction() {
        $action = new \html_blocks\actions\AdminGetBlockSettings($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Действие: Получение шаблонов блоков через AJAX 
    * @return mixed JSON-ответ со списком шаблонов
    */
    public function getBlockTemplatesAction() {
        $action = new \html_blocks\actions\AdminGetBlockTemplates($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Действие: Получение списка файлов ассетов для блока через AJAX
    * @return mixed JSON-ответ со списком файлов
    */
    public function getBlockAssetsAction() {
        $action = new \html_blocks\actions\AdminGetBlockAssets($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Действие: Получение списка фрагментов (AJAX)
    */
    public function getFragmentsAction() {
        $action = new \html_blocks\actions\AdminGetFragments($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Действие: Очистка кеша CSS блоков
    * @return mixed
    */
    public function clearCacheAction() {
        $action = new \html_blocks\actions\AdminClearCache($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
}