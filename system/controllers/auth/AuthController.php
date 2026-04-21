<?php

/**
* Контроллер аутентификации и регистрации
*/
class AuthController extends Controller {

    private $userModel;

    protected $controllerInfo = [
        'name' => LANG_CONTROLLER_AUTH_MANIFEST_NAME,
        'author' => 'BloggyCMS', 
        'version' => '1.0.0',
        'has_settings' => true,
        'description' => LANG_CONTROLLER_AUTH_MANIFEST_DESCRIPTION
    ];
    
    /**
    * Конструктор контроллера аутентификации
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
    }
    
    /**
    * Действие входа пользователя в систему
    * @return mixed Результат выполнения действия авторизации
    */
    public function loginAction() {
        $action = new \auth\actions\Login($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие регистрации нового пользователя
    * @return mixed Результат выполнения действия регистрации
    */
    public function registerAction() {
        $action = new \auth\actions\Register($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие выхода пользователя из системы
    * @return mixed Результат выполнения действия выхода
    */
    public function logoutAction() {
        $action = new \auth\actions\Logout($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Действие входа в административную панель
    * @return mixed Результат выполнения действия административного входа
    */
    public function adminLoginAction() {
        $action = new \auth\actions\AdminLogin($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Действие восстановления пароля
    * @return mixed Результат выполнения действия восстановления пароля
    */
    public function forgotPasswordAction() {
        $action = new \auth\actions\ForgotPassword($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Действие сброса пароля
    * @return mixed Результат выполнения действия сброса пароля
    */
    public function resetPasswordAction() {
        $action = new \auth\actions\ResetPassword($this->db);
        $action->setController($this);
        return $action->execute();
    }
}