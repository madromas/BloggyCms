<?php

/**
* Контроллер управления иконками в админ-панели
* @package controllers
* @extends Controller
*/
class AdminIconController extends Controller {
    
    /**
    * Действие: Главная страница управления иконками
    * @return mixed
    */
    public function adminIndexAction() {
        $action = new \icons\actions\AdminIndex();
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие: Получение данных об иконках через AJAX 
    * @return mixed JSON-ответ с данными об иконках
    */
    public function adminIconsDataAction() {
        $action = new \icons\actions\AdminIconsData();
        $action->setController($this);
        return $action->execute();
    }
}