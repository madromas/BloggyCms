<?php

/**
* Контроллер поиска на фронтенде
* @package Controllers
*/
class SearchController extends Controller {
    
    private $searchModel;
    
    /**
    * Конструктор контроллера
    * @param object $db Подключение к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->searchModel = new SearchModel($db);
    }
    
    /**
    * Основное действие для поиска
    * @return void
    */
    public function indexAction() {
        $action = new \search\actions\Index($this->db);
        $action->setController($this);
        return $action->execute();
    }
}