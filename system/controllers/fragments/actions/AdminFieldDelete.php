<?php

namespace fragments\actions;

/**
* Действие удаления поля фрагмента
*/
class AdminFieldDelete extends FragmentAction {
    
    public function execute() {
        $fieldId = $this->params['id'] ?? null;
        
        if (!$fieldId) {
            \Notification::error('ID поля не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $field = $this->fragmentModel->getFieldById($fieldId);
        
        if (!$field) {
            \Notification::error('Поле не найдено');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        try {
            $this->fragmentModel->deleteField($fieldId);
            \Notification::success('Поле успешно удалено');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении поля: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/fragments/fields/' . $field['fragment_id']);
    }
}