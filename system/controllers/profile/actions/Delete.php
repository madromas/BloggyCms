<?php

namespace profile\actions;

/**
* Действие удаления аккаунта пользователя
*/
class Delete extends ProfileAction {
    
    /**
    * Метод выполнения удаления аккаунта
    * @return void
    */
    public function execute() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $password = $input['password'] ?? '';
        $csrfToken = $input['csrf_token'] ?? '';
        
        if (!$this->validateCsrfToken($csrfToken)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Неверный CSRF токен']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        $user = $this->userModel->getById($userId);
        if (!$user || !password_verify($password, $user['password'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
            return;
        }
        
        if ($user['role'] === 'admin') {
            $adminsCount = $this->db->fetchValue(
                "SELECT COUNT(*) FROM users WHERE role = 'admin'"
            );
            if ($adminsCount <= 1) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Нельзя удалить последнего администратора']);
                return;
            }
        }
        
        $this->deleteUserAvatar($user);
        
        if ($this->userModel->delete($userId)) {
            session_destroy();
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ошибка при удалении аккаунта']);
        }
    }
    
    /**
    * Удаляет аватар пользователя
    * @param array $user Данные пользователя
    * @return void
    */
    private function deleteUserAvatar($user) {
        if (!empty($user['avatar']) && $user['avatar'] !== 'default.jpg') {
            $avatarPath = UPLOADS_PATH . '/avatars/' . $user['avatar'];
            if (file_exists($avatarPath)) {
                @unlink($avatarPath);
            }
        }
    }
    
    /**
    * Проверяет CSRF токен
    * @param string $token Токен для проверки
    * @return bool Результат проверки
    */
    private function validateCsrfToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}