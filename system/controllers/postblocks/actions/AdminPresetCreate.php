<?php

namespace postblocks\actions;

/**
* Действие создания нового пресета для постблока
* @package postblocks\actions
*/
class AdminPresetCreate extends PostBlockAction {
    
    /**
    * Метод выполнения создания пресета
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {

            $systemName = $_POST['system_name'] ?? '';
            $presetName = $_POST['preset_name'] ?? '';
            $presetTemplate = $_POST['preset_template'] ?? '';

            if (empty($systemName) || empty($presetName)) {
                throw new \Exception('Не указано системное имя блока или имя пресета');
            }

            $postBlock = $this->postBlockManager->getPostBlock($systemName);
            if (!$postBlock) {
                throw new \Exception('Блок не найден');
            }

            $existingPreset = $this->postBlockModel->getPresetByName($systemName, $presetName);
            if ($existingPreset) {
                throw new \Exception('Пресет с таким именем уже существует');
            }

            $result = $this->postBlockModel->createPreset($systemName, $presetName, $presetTemplate);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Пресет успешно создан',
                    'preset_id' => $this->db->lastInsertId()
                ]);
            } else {
                throw new \Exception('Ошибка при создании пресета');
            }

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}