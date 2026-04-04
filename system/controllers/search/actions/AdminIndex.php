<?php

namespace search\actions;

/**
* Действие отображения списка всех поисковых запросов в административной панели
* @package search\actions
*/
class AdminIndex extends SearchAction {
    
    /**
    * Метод выполнения отображения списка поисковых запросов
    * @return void
    */
    public function execute() {
        
        if (!$this->checkAuth()) {
            \Notification::error('Пожалуйста, авторизуйтесь для доступа к истории поиска');
            $this->redirect(ADMIN_URL . '/login');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('История поиска');
        
        try {
            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $page = max(1, $page);
            
            $result = $this->searchModel->getAllSearchQueries($page);
            
            $this->render('admin/search/index', [
                'queries' => $result['queries'],
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'pageTitle' => 'История поисковых запросов'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке истории поисковых запросов');
            $this->redirect(ADMIN_URL);
        }
    }

}