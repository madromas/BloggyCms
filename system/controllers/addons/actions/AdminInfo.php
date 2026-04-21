<?php

namespace addons\actions;

/**
* Действие получения информации о пакете (AJAX) 
* @package addons\actions
*/
class AdminInfo extends AddonAction {
    
    /**
    * Метод выполнения
    */
    public function execute() {
        header('Content-Type: application/json');
        
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => LANG_CONTROLLER_ADDONS_ACTION_INFO_ID_NOT_SPECIFIED
            ]);
            return;
        }
        
        try {
            $addon = $this->addonModel->getById($id);
            
            if (!$addon) {
                throw new \Exception(LANG_CONTROLLER_ADDONS_ACTION_INFO_NOT_FOUND);
            }
            
            echo json_encode([
                'success' => true,
                'addon' => $addon
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}