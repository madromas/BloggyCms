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
                'message' => 'ID пакета не указан'
            ]);
            return;
        }
        
        try {
            $addon = $this->addonModel->getById($id);
            
            if (!$addon) {
                throw new \Exception('Пакет не найден');
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
