<?php

namespace posts\actions;

/**
* Действие добавления/удаления поста в закладки пользователя 
* @package posts\actions
*/
class Bookmark extends PostAction {
    
    /**
    * Метод выполнения переключения закладки
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Требуется авторизация для добавления в закладки');
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
            
            $result = $this->postModel->toggleBookmark($postId, $userId);
            
            if ($result['bookmarked']) {
                try {
                    $achievementTriggers = new \AchievementTriggers($this->db);
                    $achievementTriggers->onPostBookmarked($userId, $postId);
                } catch (\Exception $e) {}
            }
            
            echo json_encode([
                'success' => true,
                'bookmarked' => $result['bookmarked'],
                'message' => $result['message']
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