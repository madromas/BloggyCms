<?php

/**
* Контроллер архива записей блога
*/
class ArchiveController extends Controller {

    private $postModel;

    protected $controllerInfo = [
        'name' => 'Архив записей блога',
        'author' => 'BloggyCMS',
        'version' => '1.0.0',
        'has_settings' => false,
        'description' => 'Отображает архив всех постов на сайте'
    ];
    
    /**
    * Конструктор контроллера архива
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->postModel = new PostModel($db);
    }
    
    /**
    * Отображение архива постов
    */
    public function indexAction() {
        $action = new \archive\actions\Index($this->db);
        $action->setController($this);
        return $action->execute();
    }
}