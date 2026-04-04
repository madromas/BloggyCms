<?php

namespace users\actions;

/**
* Действие удаления пользователя в административной панели 
* @package users\actions
*/
class AdminDelete extends UserAction {
    
    /**
    * Метод выполнения удаления пользователя
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
                \Notification::error('Нельзя удалить собственный аккаунт');
                $this->redirect(ADMIN_URL . '/users');
                return;
            }

            $user = $this->userModel->getById($id);
            
            if (!$user) {
                \Notification::error('Пользователь не найден');
                $this->redirect(ADMIN_URL . '/users');
                return;
            }
            
            if ($user['role'] === 'admin') {
                $adminsCount = $this->userModel->db->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
                if ($adminsCount['count'] <= 1) {
                    \Notification::error('Нельзя удалить последнего администратора');
                    $this->redirect(ADMIN_URL . '/users');
                    return;
                }
            }
            
            $this->deleteUserAvatar($user);
            
            $this->userModel->delete($id);
            
            \Notification::success('Пользователь успешно удален');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении пользователя: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/users');
    }
    
    /**
    * Удаляет аватар пользователя с сервера
    * @param array $user Данные пользователя
    * @return void
    */
    protected function deleteUserAvatar($user) {
        if (!empty($user['avatar']) && $user['avatar'] !== 'default.jpg') {
            $filePath = UPLOADS_PATH . '/avatars/' . $user['avatar'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
}