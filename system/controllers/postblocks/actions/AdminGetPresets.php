<?php

namespace postblocks\actions;

/**
* Действие получения списка пресетов для постблока
* @package postblocks\actions
*/
class AdminGetPresets extends PostBlockAction {
    
    /**
    * Метод выполнения получения списка пресетов
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {
            if (!$this->checkAdminAccess()) {
                throw new \Exception('Доступ запрещен');
            }

            $systemName = $_GET['system_name'] ?? '';

            if (empty($systemName)) {
                throw new \Exception('Не указано системное имя блока');
            }

            $presets = $this->postBlockModel->getBlockPresets($systemName);

            echo json_encode([
                'success' => true,
                'presets' => $presets
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}