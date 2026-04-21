<?php

namespace auth\actions;

/**
* Действие для безопасного выхода пользователя из системы
*/
class Logout extends AuthAction {
    
    /**
    * Основной метод выполнения процесса выхода из системы
    */
    public function execute() {

        session_destroy();
        session_start();
        
        \Notification::success(LANG_ACTION_AUTH_LOGOUT_SUCCESS);
        
        $this->redirect(BASE_URL);
    }
}