<?php

namespace profile\actions;

/**
* Действие завершения всех сессий пользователя кроме текущей
*/
class TerminateAllSessions extends ProfileAction {
    
    /**
    * Метод выполнения завершения всех сессий
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
        $csrfToken = $input['csrf_token'] ?? '';
        
        if (!$this->validateCsrfToken($csrfToken)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Неверный CSRF токен']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        if ($this->terminateAllUserSessions($userId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не удалось завершить сессии']);
        }
    }
    
    /**
    * Завершает все сессии пользователя кроме текущей 
    * @param int $userId ID пользователя
    * @return bool Результат операции
    */
    private function terminateAllUserSessions($userId) {
        $result = $this->db->query(
            "DELETE FROM user_sessions WHERE user_id = ? AND session_id != ?",
            [$userId, session_id()]
        );
        
        return $result && $result->rowCount() >= 0;
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