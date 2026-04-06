<?php

/**
* Контроллер управления типами HTML-блоков в админ-панели
* @package controllers
* @extends Controller
*/
class AdminHtmlBlockTypeController extends Controller {
    
    private $blockTypeManager;
    
    /**
    * Конструктор контроллера типов HTML-блоков
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->blockTypeManager = new HtmlBlockTypeManager($db);
    }
    
    /**
    * Действие: Главная страница управления типами HTML-блоков 
    * @return mixed
    */
    public function adminIndexAction() {
        $action = new \html_blocks\actions\AdminTypeIndex($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие: Удаление типа HTML-блока
    * @param string|null $systemName Системное имя удаляемого типа блока
    * @return mixed
    */
    public function deleteAction($systemName = null) {
        $action = new \html_blocks\actions\AdminTypeDelete($this->db);
        $action->setController($this);
        $action->setSystemName($systemName);
        return $action->execute();
    }
    
    /**
    * Действие: Переключение статуса типа HTML-блока
    * @param string|null $systemName Системное имя типа блока для переключения
    * @return mixed
    */
    public function toggleAction($systemName = null) {
        $action = new \html_blocks\actions\AdminTypeToggle($this->db);
        $action->setController($this);
        $action->setSystemName($systemName);
        return $action->execute();
    }
}