<?php

/**
* Контроллер управления пакетами (аддонами) в административной панели
* @package controllers\addons
*/
class AdminAddonsController extends Controller {
    
    private $addonModel;
    
    /**
    * Конструктор контроллера
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->addonModel = new AddonModel($db);
        
        if (!$this->checkAdminAccess()) {
            Notification::error('У вас нет прав доступа к этому разделу');
            $this->redirect(ADMIN_URL . '/login');
            exit;
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
    * Главная страница управления пакетами
    */
    public function adminIndexAction() {
        $action = new \addons\actions\AdminIndex($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Страница установки нового пакета
    */
    public function installAction() {
        $action = new \addons\actions\AdminInstall($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Процесс загрузки и установки пакета (AJAX)
    */
    public function uploadAction() {
        $action = new \addons\actions\AdminUpload($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Удаление установленного пакета
    */
    public function deleteAction($id) {
        $action = new \addons\actions\AdminDelete($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Получение информации о пакете (AJAX)
    */
    public function infoAction($id) {
        $action = new \addons\actions\AdminInfo($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Проверка обновлений для пакета
    */
    public function checkUpdatesAction() {
        $action = new \addons\actions\AdminCheckUpdates($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Анализ пакета без установки (AJAX)
    */
    public function analyzeAction() {
        $action = new \addons\actions\AdminAnalyze($this->db);
        $action->setController($this);
        return $action->execute();
    }

}
