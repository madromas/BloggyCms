<?php

namespace posts\actions;

/**
* Действие лайка/дизлайка поста
* @package posts\actions
*/
class Like extends PostAction {
    
    /**
    * Метод выполнения переключения лайка 
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Требуется авторизация');
            }
            
            $postId = $this->params['id'] ?? null;
            if (!$postId) {
                throw new \Exception('ID поста не указан');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            $userId = $_SESSION['user_id'];
            
            $post = $this->postModel->getById($postId);
            if (!$post) {
                throw new \Exception('Пост не найден');
            }
            
            $result = $this->postModel->toggleLike($postId, $userId);
            
            if ($result['liked']) {
                try {
                    $achievementTriggers = new \AchievementTriggers($this->db);
                    $achievementTriggers->onPostLiked($userId, $postId);
                } catch (\Exception $e) {}
            }
            
            echo json_encode([
                'success' => true,
                'liked' => $result['liked'],
                'likes_count' => $result['likes_count'],
                'message' => $result['liked'] ? 'Пост добавлен в избранное' : 'Пост удален из избранного'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}