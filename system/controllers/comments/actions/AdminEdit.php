<?php

namespace comments\actions;

/**
* Действие редактирования комментария в админ-панели
* @package comments\actions
*/
class AdminEdit extends CommentAction {
    
    /**
    * Метод выполнения редактирования комментария
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
            $comment = $this->commentModel->getCommentById($id);
            
            if (!$comment) {
                \Notification::error('Комментарий не найден');
                $this->redirect(ADMIN_URL . '/comments');
                return;
            }
            
            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Комментарии', ADMIN_URL . '/comments');
            $this->addBreadcrumb('Редактирование комментария #' . $id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'content' => $_POST['content'] ?? '',
                    'status' => $_POST['status'] ?? 'pending'
                ];
                
                $this->commentModel->updateComment($id, $data);

                \Notification::success('Комментарий успешно обновлен');
                
                $this->redirect(ADMIN_URL . '/comments');
                return;
            }

            $this->render('admin/comments/edit', [
                'comment' => $comment,
                'pageTitle' => 'Редактирование комментария'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при редактировании комментария');
            $this->redirect(ADMIN_URL . '/comments');
        }
    }

}