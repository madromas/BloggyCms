<?php

namespace users\actions\achievements;

/**
* Действие удаления достижения (ачивки) в административной панели
* @package users\actions\achievements
*/
class AdminAchievementDelete extends AdminAchievementAction {
    
    /**
    * Метод выполнения удаления ачивки
    * @return void
    */
    public function execute() {
        try {

            $id = $this->params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID ачивки не указан');
            }
            
            $achievement = $this->userModel->getAchievementById($id);
            if (!$achievement) {
                throw new \Exception('Ачивка не найдена');
            }
            
            if (!empty($achievement['image'])) {
                $uploadDir = UPLOADS_PATH . '/achievements/';
                $imagePath = $uploadDir . $achievement['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $this->userModel->deleteAchievement($id);
            
            \Notification::success('Ачивка успешно удалена');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении ачивки: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/user-achievements');
    }
}