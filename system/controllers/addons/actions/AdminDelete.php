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
            \Notification::error('ID пакета не указан');
            $this->redirect(ADMIN_URL . '/addons');
            return;
        }
        
        try {
            $addon = $this->addonModel->getById($id);
            
            if (!$addon) {
                throw new \Exception('Пакет не найден');
            }
            
            $this->addonModel->delete($id);
            
            \Notification::success('Пакет "' . html($addon['title']) . '" успешно удален');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении пакета: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/addons');
    }
}
