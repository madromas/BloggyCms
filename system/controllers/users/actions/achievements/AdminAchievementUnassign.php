<?php

namespace users\actions\achievements;

/**
* Действие отзыва (удаления) достижения у пользователя в административной панели
* @package users\actions\achievements
*/
class AdminAchievementUnassign extends AdminAchievementAction {
    
    /**
    * Метод выполнения отзыва ачивки у пользователя
    * @return void
    */
    public function execute() {
        try {
            if (!$this->checkAdminAccess()) {
                if ($this->isAjaxRequest()) {
                    $this->sendJsonResponse(false, 'У вас нет прав доступа');
                    return;
                }
                \Notification::error('У вас нет прав доступа');
                $this->redirect(ADMIN_URL);
                return;
            }

            $userId = $this->params['user_id'] ?? null;
            $achievementId = $this->params['achievement_id'] ?? null;
            
            if (!$userId || !$achievementId) {
                throw new \Exception('Не указаны ID пользователя или ачивки');
            }
            
            $user = $this->userModel->getById($userId);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }
            
            $achievement = $this->userModel->getAchievementById($achievementId);
            if (!$achievement) {
                throw new \Exception('Ачивка не найдена');
            }
            
            $this->userModel->removeAchievementFromUser($userId, $achievementId);
            
            if ($this->isAjaxRequest()) {
                $this->sendJsonResponse(true, 'Ачивка успешно отозвана у пользователя');
                return;
            }
            
            \Notification::success('Ачивка успешно отозвана у пользователя');
            
        } catch (\Exception $e) {
            if ($this->isAjaxRequest()) {
                $this->sendJsonResponse(false, $e->getMessage());
                return;
            }
            \Notification::error($e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/users/edit/' . $userId);
    }

    /**
    * Отправляет JSON-ответ для AJAX-запросов
    * @param bool $success Флаг успеха
    * @param string $message Сообщение
    * @return void
    */
    private function sendJsonResponse($success, $message) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }

    /**
    * Проверяет, является ли текущий запрос AJAX-запросом
    * @return bool
    */
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

}