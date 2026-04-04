<?php

namespace comments\actions;

/**
* Действие одобрения комментария в админ-панели
* @package comments\actions
*/
class AdminApprove extends CommentAction {
    
    /**
    * Метод выполнения одобрения комментария 
    * @return void
    */
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        $isAjax = $this->isAjaxRequest();
        
        if (!$id) {
            if ($isAjax) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID комментария не указан'
                ]);
                return;
            } else {
                \Notification::error('ID комментария не указан');
                $this->redirect(ADMIN_URL . '/comments');
                return;
            }
        }
        
        try {
            $comment = $this->commentModel->getCommentById($id);
            if (!$comment) {
                if ($isAjax) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Комментарий не найден'
                    ]);
                    return;
                } else {
                    \Notification::error('Комментарий не найден');
                    $this->redirect(ADMIN_URL . '/comments');
                    return;
                }
            }
            
            if ($comment['status'] === 'approved') {
                if ($isAjax) {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Комментарий уже одобрен'
                    ]);
                    return;
                } else {
                    \Notification::warning('Комментарий уже одобрен');
                    $this->redirect(ADMIN_URL . '/comments');
                    return;
                }
            }
            
            $this->commentModel->approveComment($id);
            
            if ($isAjax) {
                $updatedComment = $this->commentModel->getCommentById($id);
                
                $isAdminPage = strpos($_SERVER['HTTP_REFERER'] ?? '', '/admin/') !== false;
                
                if ($isAdminPage) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Комментарий успешно одобрен',
                        'comment_id' => $id,
                        'new_status' => 'approved'
                    ]);
                } else {
                    if ($this->controller) {
                        $commentData = $this->controller->getCommentWithUserData($updatedComment);
                        
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'comment' => $commentData,
                            'message' => 'Комментарий одобрен',
                            'comment_id' => $id
                        ]);
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message' => 'Комментарий успешно одобрен',
                            'comment_id' => $id
                        ]);
                    }
                }
                return;
            } 
            else {
                \Notification::success('Комментарий успешно одобрен');
            }
            
        } catch (\Exception $e) {
            $errorMessage = 'Ошибка при одобрении комментария: ' . $e->getMessage();
            
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $errorMessage
                ]);
                return;
            } else {
                \Notification::error($errorMessage);
            }
        }
        
        if (!$isAjax) {
            $this->redirect($_SERVER['HTTP_REFERER'] ?? ADMIN_URL . '/comments');
        }
    }
}