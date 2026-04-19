<?php

namespace postblocks\actions;

/**
* Действие обновления существующего пресета постблока
* @package postblocks\actions
*/
class AdminPresetUpdate extends PostBlockAction {
    
    /**
    * Метод выполнения обновления пресета
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {

            $presetId = $_POST['preset_id'] ?? 0;
            $presetName = $_POST['preset_name'] ?? '';
            $presetTemplate = $_POST['preset_template'] ?? '';

            if (empty($presetId) || empty($presetName)) {
                throw new \Exception('Не указаны обязательные параметры');
            }

            $preset = $this->postBlockModel->getPreset($presetId);
            if (!$preset) {
                throw new \Exception('Пресет не найден');
            }

            $existingPreset = $this->postBlockModel->getPresetByName($preset['block_system_name'], $presetName);
            if ($existingPreset && $existingPreset['id'] != $presetId) {
                throw new \Exception('Пресет с таким именем уже существует');
            }

            $result = $this->postBlockModel->updatePreset($presetId, $presetName, $presetTemplate);

            echo json_encode([
                'success' => $result !== false,
                'message' => $result ? 'Пресет успешно обновлен' : 'Ошибка при обновлении пресета'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}