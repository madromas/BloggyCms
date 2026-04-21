<?php

namespace addons\actions;

/**
* Действие удаления установленного пакета 
* @package addons\actions
*/
class AdminDelete extends AddonAction {
    
    /**
    * Метод выполнения
    */
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error(LANG_CONTROLLER_ADDONS_ACTION_DELETE_ID_NOT_SPECIFIED);
            $this->redirect(ADMIN_URL . '/addons');
            return;
        }
        
        try {
            $addon = $this->addonModel->getById($id);
            
            if (!$addon) {
                throw new \Exception(LANG_CONTROLLER_ADDONS_ACTION_DELETE_NOT_FOUND);
            }
            
            $this->addonModel->delete($id);
            
            \Notification::success(sprintf(LANG_CONTROLLER_ADDONS_ACTION_DELETE_SUCCESS, html($addon['title'])));
            
        } catch (\Exception $e) {
            \Notification::error(LANG_CONTROLLER_ADDONS_ACTION_DELETE_ERROR . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/addons');
    }
}