<?php

namespace users\actions\achievements;

/**
* Действие назначения достижения (ачивки) пользователю в административной панели
* @package users\actions\achievements
*/
class AdminAchievementAssign extends AdminAchievementAction {
    
    /**
    * Метод выполнения назначения ачивки пользователю
    * @return void
    */
    public function execute() {
        try {

            $userId = $this->params['user_id'] ?? null;
            if (!$userId) {
                throw new \Exception('ID пользователя не указан');
            }
            
            $user = $this->userModel->getById($userId);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handlePostRequest($userId);
                return;
            }
            
            $this->renderAssignForm($user);
            
        } catch (\Exception $e) {
            \Notification::error($e->getMessage());
            $this->redirect(ADMIN_URL . '/users');
        }
    }
    
    /**
    * Обрабатывает POST-запрос на назначение ачивки
    * @param int $userId ID пользователя
    * @return void
    * @throws \Exception При ошибках валидации
    */
    private function handlePostRequest($userId) {
        $achievementId = $_POST['achievement_id'] ?? null;
        if (!$achievementId) {
            throw new \Exception('Ачивка не выбрана');
        }
        
        $achievement = $this->userModel->getAchievementById($achievementId);
        if (!$achievement) {
            throw new \Exception('Ачивка не найдена');
        }
        
        $this->userModel->assignAchievementToUser($userId, $achievementId);
        
        \Notification::success('Ачивка успешно назначена пользователю');
        $this->redirect(ADMIN_URL . '/users/edit/' . $userId);
    }
    
    /**
    * Отображает форму назначения ачивки
    * @param array $user Данные пользователя
    * @return void
    */
    private function renderAssignForm($user) {
        $achievements = $this->userModel->getAllAchievements(['active' => true]);
        
        $this->render('admin/users/assign-achievement', [
            'user' => $user,
            'achievements' => $achievements,
            'pageTitle' => 'Назначение ачивки пользователю'
        ]);
    }
}