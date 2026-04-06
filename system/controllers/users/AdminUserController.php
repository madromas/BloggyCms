<?php

/**
* Контроллер управления пользователями в административной панели
* @package Controllers
*/
class AdminUserController extends Controller {
    
    private $userModel;
    private $fieldModel;
    
    /**
    * Конструктор контроллера
    * @param object $db Подключение к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
        $this->fieldModel = new FieldModel($db);
        
        if (!$this->checkAdminAccess()) {
            Notification::error('У вас нет прав доступа к этому разделу');
            $this->redirect(ADMIN_URL . '/login');
            exit;
        }
    }

    /**
    * Проверяет, имеет ли текущий пользователь права администратора 
    * @return bool true если пользователь администратор
    */
    private function checkAdminAccess() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }

    /**
    * Отображает список всех пользователей в административной панели
    * @return void
    */
    public function adminIndexAction() {
        $action = new \users\actions\AdminIndex($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Отображает форму создания нового пользователя 
    * @return void
    */
    public function createAction() {
        $action = new \users\actions\AdminCreate($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Отображает форму редактирования существующего пользователя 
    * @param int $id ID пользователя
    * @return void
    */
    public function editAction($id) {
        $action = new \users\actions\AdminEdit($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Удаляет пользователя 
    * @param int $id ID пользователя
    * @return void
    */
    public function deleteAction($id) {
        $action = new \users\actions\AdminDelete($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Переключает статус пользователя (активен/заблокирован) 
    * @param int $id ID пользователя
    * @return void
    */
    public function toggleStatusAction($id) {
        $action = new \users\actions\AdminToggleStatus($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
}