<?php

namespace fragments\actions;

/**
 * Действие удаления записи фрагмента
 */
class AdminEntryDelete extends FragmentAction {
    
    public function execute() {
        $entryId = $this->params['id'] ?? null;
        
        if (!$entryId) {
            \Notification::error('ID записи не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $entry = $this->entryModel->getById($entryId);
        
        if (!$entry) {
            \Notification::error('Запись не найдена');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        try {
            $this->entryModel->delete($entryId);
            \Notification::success('Запись успешно удалена');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении записи');
        }
        
        $this->redirect(ADMIN_URL . '/fragments/entries/' . $entry['fragment_id']);
    }
}