<?php

namespace posts\actions;

/**
* Действие отображения списка всех постов в административной панели
* @package posts\actions
*/
class AdminIndex extends PostAction {
    
    /**
    * Метод выполнения отображения списка постов в админ-панели
    * @return void
    */
    public function execute() {
        $this->pageTitle = 'Управление постами';
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Посты');
        
        try {
            $categoryId = $_GET['category'] ?? null;
            $status = $_GET['status'] ?? null;
            
            $posts = $this->postModel->getAllWithFilters($categoryId, $status);
            
            $categories = $this->categoryModel->getAll();
            
            $this->render('admin/posts/index', [
                'posts' => $posts,
                'categories' => $categories,
                'pageTitle' => 'Управление постами блога'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке списка записей');
            $this->redirect(ADMIN_URL);
        }
    }

}