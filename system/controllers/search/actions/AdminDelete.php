<?php

namespace search\actions;

/**
* Действие удаления конкретного поискового запроса из истории
* @package search\actions
*/
class AdminDelete extends SearchAction {
    
    /**
    * Метод выполнения удаления поискового запроса
    * @return void
    */
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID запроса не указан');
            $this->redirect(ADMIN_URL . '/search-history');
            return;
        }
        
        try {
            $this->searchModel->deleteSearchQuery($id);
            
            \Notification::success('Поисковый запрос успешно удален');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении поискового запроса');
        }
        
        $this->redirect(ADMIN_URL . '/search-history');
    }
}