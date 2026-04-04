<?php

namespace postblocks\actions;

/**
* Действие получения настроек по умолчанию для постблока
* @package postblocks\actions
*/
class AdminGetDefaultSettings extends PostBlockAction {
    
    /**
    * Метод выполнения получения настроек по умолчанию
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {
            $systemName = $_GET['system_name'] ?? '';
            
            if (empty($systemName)) {
                throw new \Exception('Системное имя блока не указано');
            }

            $postBlock = $this->postBlockManager->getPostBlock($systemName);
            if (!$postBlock || !$postBlock['class']) {
                throw new \Exception('Блок не найден');
            }

            $defaultSettings = $postBlock['class']->getDefaultSettings();

            echo json_encode([
                'success' => true,
                'settings' => $defaultSettings
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'settings' => []
            ]);
        }
    }
}