<?php

/**
* Контроллер управления отладкой
* @package controllers
*/
class DebugController extends Controller {
    
    private $debugModel;
    
    protected $controllerInfo = [
        'name' => 'Отладка',
        'author' => 'BloggyCMS',
        'version' => '1.0.0',
        'has_settings' => false,
        'description' => 'Просмотр и управление ошибками системы в режиме отладки'
    ];
    
    /**
    * Конструктор
    * @param Database $db
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->debugModel = new DebugModel($db);
        
        $currentAction = $_GET['action'] ?? '';
        if (strpos($currentAction, 'admin') === 0) {
            if (!$this->checkAdminAccess()) {
                if ($this->isAjaxRequest()) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    die(json_encode([
                        'success' => false,
                        'message' => 'Доступ запрещен'
                    ]));
                } else {
                    Notification::error('У вас нет прав доступа к этому разделу');
                    $this->redirect(ADMIN_URL . '/login');
                    exit;
                }
            }
        }
    }
    
    /**
    * Проверка прав администратора
    * @return bool
    */
    private function checkAdminAccess() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }
    
    /**
    * Проверка AJAX запроса
    * @return bool
    */
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
    * Главная страница отладки в админке
    */
    public function adminIndexAction() {
        $action = new \debug\actions\AdminIndex($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Получение списка ошибок (AJAX)
    */
    public function adminGetLogsAction() {
        $action = new \debug\actions\AdminGetLogs($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Получение деталей ошибки (AJAX)
    */
    public function adminGetLogAction($id) {
        $action = new \debug\actions\AdminGetLog($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Удаление ошибки (AJAX)
    */
    public function adminDeleteAction($id) {
        $action = new \debug\actions\AdminDelete($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Удаление всех ошибок (AJAX)
    */
    public function adminDeleteAllAction() {
        $action = new \debug\actions\AdminDeleteAll($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Отметка ошибки как исправленной (AJAX)
    */
    public function adminMarkFixedAction($id) {
        $action = new \debug\actions\AdminMarkFixed($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Получение статистики (AJAX)
    */
    public function adminStatsAction() {
        $action = new \debug\actions\AdminStats($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Переключение режима отладки (AJAX)
    */
    public function adminToggleAction() {
        $action = new \debug\actions\AdminToggle($this->db);
        $action->setController($this);
        return $action->execute();
    }
}