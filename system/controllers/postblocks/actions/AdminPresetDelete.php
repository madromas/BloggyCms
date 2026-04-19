<?php

namespace postblocks\actions;

/**
* Действие удаления пресета постблока
* @package postblocks\actions
* @extends PostBlockAction
*/
class AdminPresetDelete extends PostBlockAction {
    
    /**
    * Метод выполнения удаления пресета
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {

            $presetId = $_POST['preset_id'] ?? 0;

            if (empty($presetId)) {
                throw new \Exception('Не указан ID пресета');
            }

            $result = $this->postBlockModel->deletePreset($presetId);

            echo json_encode([
                'success' => $result !== false,
                'message' => $result ? 'Пресет успешно удален' : 'Ошибка при удалении пресета'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}