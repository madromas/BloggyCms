<?php

namespace users\actions\achievements;

/**
* Действие переключения статуса активности достижения (ачивки) в административной панели
* @package users\actions\achievements
*/
class AdminAchievementToggle extends AdminAchievementAction {
    
    /**
    * Метод выполнения переключения статуса ачивки
    * @return void
    */
    public function execute() {
        try {

            if (!$this->checkAdminAccess()) {
                \Notification::error('У вас нет прав доступа');
                $this->redirect(ADMIN_URL);
                return;
            }

            $id = $this->params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID ачивки не указан');
            }
            
            $achievement = $this->userModel->getAchievementById($id);
            if (!$achievement) {
                throw new \Exception('Ачивка не найдена');
            }
            
            $newStatus = $achievement['is_active'] ? 0 : 1;
            
            $this->db->query(
                "UPDATE user_achievements SET is_active = ? WHERE id = ?",
                [$newStatus, $id]
            );
            
            $statusText = $newStatus ? 'активирована' : 'деактивирована';
            \Notification::success("Ачивка успешно {$statusText}");
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при изменении статуса ачивки: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/user-achievements');
    }
}