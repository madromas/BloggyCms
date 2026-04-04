<?php

namespace html_blocks\actions;

/**
* Действие переключения статуса типа HTML-блока в админ-панели
* @package html_blocks\actions
*/
class AdminTypeToggle extends HtmlBlockAction {
    
    private $systemName;
    
    /**
    * Установка системного имени типа блока
    */
    public function setSystemName($systemName) {
        $this->systemName = $systemName;
    }
    
    /**
    * Метод выполнения переключения статуса типа блока
    * @return void
    */
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа к этому разделу');
            $this->redirect(ADMIN_URL . '/login');
            return;
        }
        
        if (!$this->systemName) {
            \Notification::error('Системное имя типа блока не указано');
            $this->redirect(ADMIN_URL . '/html-blocks/types');
            return;
        }

        if ($this->systemName === 'DefaultBlock') {
            \Notification::error('Нельзя отключить дефолтный тип блока');
            $this->redirect(ADMIN_URL . '/html-blocks/types');
            return;
        }
        
        try {
            $isActive = $this->blockTypeManager->isBlockTypeActive($this->systemName);
            
            $newStatus = $isActive ? 0 : 1;
            
            $this->blockTypeManager->toggleBlockTypeStatus($this->systemName, $newStatus);
            
            $statusText = $newStatus ? 'включен' : 'отключен';
            \Notification::success("Тип блока успешно $statusText");
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при изменении статуса типа блока: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/html-blocks/types');
    }

}