<?php

namespace fields\actions;

/**
* Действие переключения активности поля в админ-панели
* @package fields\actions
*/
class AdminToggle extends FieldAction {
    
    /**
    * Метод выполнения переключения активности поля
    * @return void
    */
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID поля не указан');
            $this->redirect(ADMIN_URL . '/fields');
            return;
        }
        
        try {
            $field = $this->fieldModel->getById($id);
            if (!$field) {
                throw new \Exception('Поле не найдено');
            }
            
            $newStatus = $field['is_active'] ? 0 : 1;
            
            $data = [
                'system_name' => $field['system_name'],
                'name' => $field['name'],
                'type' => $field['type'],
                'description' => $field['description'],
                'is_required' => $field['is_required'],
                'is_active' => $newStatus,
                'sort_order' => $field['sort_order'],
                'config' => $field['config']
            ];
            
            $result = $this->fieldModel->update($id, $data);
            
            if ($result) {
                $statusText = $newStatus ? 'включено' : 'отключено';
                \Notification::success("Поле {$statusText}");
            } else {
                throw new \Exception('Не удалось обновить поле в базе данных');
            }
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при изменении статуса поля: ' . $e->getMessage());
        }
        
        if (isset($field['entity_type'])) {
            $this->redirect(ADMIN_URL . "/fields/entity/{$field['entity_type']}");
        } else {
            $this->redirect(ADMIN_URL . '/fields');
        }
    }
}