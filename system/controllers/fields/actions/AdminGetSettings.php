<?php

namespace fields\actions;

/**
* Действие получения настроек типа поля через AJAX
*/
class AdminGetSettings extends FieldAction {
    
    public function execute() {
        
        $type = $this->params['type'] ?? null;
        
        if (!$type) {
            echo '<div class="alert alert-warning">Тип поля не указан</div>';
            exit;
        }
        
        $config = $_POST['config'] ?? [];
        
        $fieldManager = new \FieldManager($this->db);
        
        try {
            $fieldInstance = $fieldManager->getFieldInstance($type, $config);
            
            if ($fieldInstance) {
                $settingsForm = $fieldInstance->getSettingsForm();
                echo $settingsForm;
            } else {
                echo '<div class="alert alert-warning">Настройки для этого типа поля не найдены</div>';
            }
        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">Ошибка: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        exit;
    }
}