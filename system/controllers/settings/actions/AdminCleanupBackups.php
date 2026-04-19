<?php

namespace settings\actions;

/**
* Действие очистки старых резервных копий настроек 
* @package settings\actions
* @extends SettingsAction
*/
class AdminCleanupBackups extends SettingsAction {
    
    /**
    * Метод выполнения очистки резервных копий
    * @return void
    */
    public function execute() {
        
        $deletedCount = \BackupHelper::cleanupAllBackups();
        
        \Notification::success("Удалено резервных копий: {$deletedCount}");
        
        $this->redirect(ADMIN_URL . '/settings?tab=site');
    }
}