<?php

namespace users\actions\groups;

/**
* Действие удаления группы пользователей в административной панели
* @package users\actions\groups
*/
class AdminGroupDelete extends AdminGroupAction {
    
    /**
    * Метод выполнения удаления группы
    * @return void
    */
    public function execute() {
        try {

            $id = $this->params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID группы не указан');
            }

            $this->userModel->deleteGroup($id);
            
            \Notification::success('Группа успешно удалена');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении группы');
        }
        
        $this->redirect(ADMIN_URL . '/user-groups');
    }
}