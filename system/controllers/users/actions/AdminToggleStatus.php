<?php

namespace users\actions;

/**
* Действие переключения статуса пользователя в административной панели
* @package users\actions
*/
class AdminToggleStatus extends UserAction {
    
    /**
    * Метод выполнения переключения статуса пользователя
    * @return void
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID пользователя не указан');
            $this->redirect(ADMIN_URL . '/users');
            return;
        }
        
        try {
            if ($id == $this->getCurrentUserId()) {
                \Notification::error('Нельзя изменить статус собственного аккаунта');
                $this->redirect(ADMIN_URL . '/users');
                return;
            }

            $user = $this->userModel->getById($id);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }
            
            $newStatus = $user['status'] === 'active' ? 'banned' : 'active';
            
            $this->userModel->update($id, ['status' => $newStatus]);
            
            $statusText = $newStatus === 'active' ? 'активирован' : 'заблокирован';
            \Notification::success("Пользователь {$statusText}");
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при изменении статуса пользователя: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/users');
    }
}