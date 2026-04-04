<?php

namespace comments\actions;

/**
* Действие удаления комментария в админ-панели
* @package comments\actions
*/
class AdminDelete extends CommentAction {
    
    /**
    * Метод выполнения удаления комментария
    * @return void
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID комментария не указан');
            $this->redirect(ADMIN_URL . '/comments');
            return;
        }
        
        try {
            $this->commentModel->deleteComment($id);
            \Notification::success('Комментарий успешно удален');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении комментария');
        }
        
        $this->redirect(ADMIN_URL . '/comments');
    }
}