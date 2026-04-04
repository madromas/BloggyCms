<?php

namespace comments\actions;

/**
* Действие отображения списка комментариев в админ-панели
* @package comments\actions
*/
class AdminIndex extends CommentAction {
    
    /**
    * Метод выполнения отображения списка комментариев
    * @return void
    */
    public function execute() {
        try {
            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Комментарии');

            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 20;
            
            $result = $this->commentModel->getAllComments($page, $perPage);
            
            $this->render('admin/comments/index', [
                'comments' => $result['comments'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'pageTitle' => 'Управление комментариями'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке списка комментариев');
            $this->redirect(ADMIN_URL);
        }
    }
}