<?php

/**
* Контроллер управления профилями пользователей
* @package Controllers
*/
class ProfileController extends Controller {
    
    /**
    * Отображает профиль текущего авторизованного пользователя
    */
    public function indexAction() {
        $action = new \profile\actions\Index($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Отображает публичный профиль пользователя по его имени пользователя
    */
    public function showAction($username = null) {
        $action = new \profile\actions\Show($this->db);
        $action->setController($this);
        $action->setUsername($username);
        return $action->execute();
    }
    
    /**
    * Отображает форму редактирования профиля текущего пользователя
    */
    public function editAction() {
        $action = new \profile\actions\Edit($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Обрабатывает обновление данных профиля текущего пользователя
    */
    public function updateAction() {
        $action = new \profile\actions\Update($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Получает список активных сессий пользователя
    */
    public function sessionsAction() {
        $action = new \profile\actions\Sessions($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Завершает указанную сессию
    */
    public function terminateSessionAction() {
        $action = new \profile\actions\TerminateSession($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Завершает все сессии пользователя кроме текущей
    */
    public function terminateAllSessionsAction() {
        $action = new \profile\actions\TerminateAllSessions($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Удаляет аккаунт пользователя
    */
    public function deleteAction() {
        $action = new \profile\actions\Delete($this->db);
        $action->setController($this);
        return $action->execute();
    }
}