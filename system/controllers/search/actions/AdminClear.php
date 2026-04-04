<?php

namespace search\actions;

/**
* Действие очистки всей истории поисковых запросов в административной панели
* @package search\actions
*/
class AdminClear extends SearchAction {
    
    /**
    * Метод выполнения очистки истории поиска
    * @return void
    */
    public function execute() {
        try {

            $this->searchModel->clearSearchHistory();
            
            \Notification::success('История поисковых запросов успешно очищена');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при очистке истории поисковых запросов');
        }
        
        $this->redirect(ADMIN_URL . '/search-history');
    }
}