<?php

namespace tags\actions;

/**
* Действие удаления тега в административной панели
* @package tags\actions
* @extends TagAction
*/
class Delete extends TagAction {
    
    /**
    * Метод выполнения удаления тега
    * @return void
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID тега не указан');
            $this->redirect(ADMIN_URL . '/tags');
            return;
        }
        
        try {
            $this->tagModel->delete($id);

            \Notification::success('Тег успешно удален');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении тега');
        }
        
        $this->redirect(ADMIN_URL . '/tags');
    }
}