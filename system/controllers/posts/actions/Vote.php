<?php

namespace posts\actions;

/**
* Действие голосования за пост (устаревшее, использует систему лайков)
* @package posts\actions
*/
class Vote extends PostAction {
    
    /**
    * Метод выполнения голосования за пост 
    * @return void
    */
    public function execute() {

        $postId = $this->params['id'] ?? null;
        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Post ID not provided']);
            return;
        }

        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
    
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Требуется авторизация',
                    'redirect' => BASE_URL . '/login'
                ]);
                return;
            }
            
            $userId = $_SESSION['user_id'];
    
            $post = $this->postModel->getById($postId);
            if (!$post) {
                echo json_encode(['success' => false, 'message' => 'Пост не найден']);
                return;
            }
    
            $result = $this->postModel->toggleLike($postId, $userId);
            
            echo json_encode([
                'success' => true,
                'liked' => $result['liked'],
                'likes_count' => $result['likes_count'],
                'message' => $result['liked'] ? 'Посту понравилось' : 'Лайк удален'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка сервера: ' . $e->getMessage()]);
        }
        exit;
    }
}