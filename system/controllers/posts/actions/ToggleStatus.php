<?php

namespace posts\actions;

/**
* Действие переключения статуса поста в административной панели 
* @package posts\actions
*/
class ToggleStatus extends PostAction {
    
    /**
    * Метод выполнения переключения статуса поста
    * @return void
    * @throws \Exception Если ID не указан
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            throw new \Exception('ID поста не указан');
        }

        try {
            $post = $this->postModel->getById($id);
            if (!$post) {
                throw new \Exception('Пост не найден');
            }
            
            $newStatus = $post['status'] === 'published' ? 'draft' : 'published';
            
            $this->postModel->update($id, ['status' => $newStatus]);
            
            $statusText = $newStatus === 'published' ? 'опубликован' : 'перемещен в черновики';
            \Notification::success("Пост {$statusText}");
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при изменении статуса поста: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/posts');
    }
}