<?php

namespace fragments\actions;

/**
* Действие удаления фрагмента
*/
class AdminDelete extends FragmentAction {
    
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID фрагмента не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        try {
            $fragment = $this->fragmentModel->getById($id);
            
            if (!$fragment) {
                \Notification::error('Фрагмент не найден');
                $this->redirect(ADMIN_URL . '/fragments');
                return;
            }
            
            $this->entryModel->deleteByFragment($id);
            $this->fragmentModel->delete($id);
            
            \Notification::success('Фрагмент успешно удален');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении фрагмента: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/fragments');
    }
}