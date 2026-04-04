<?php

namespace pages\actions;

/**
* Действие отображения списка всех страниц в административной панели
* @package pages\actions
*/
class AdminIndex extends PageAction {
    
    /**
    * Метод выполнения отображения списка страниц
    * @return void
    */
    public function execute() {
        if (!$this->checkAdminAccess()) {
            $this->handleAccessDenied();
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Страницы');
        
        try {
            $pages = $this->loadPages();
            
            $this->renderPageList($pages);
            
        } catch (\Exception $e) {
            $this->handleLoadError($e);
        }
    }
    
    /**
    * Загружает список всех страниц из базы данных 
    * @return array Массив всех страниц
    */
    private function loadPages() {
        return $this->pageModel->getAll();
    }
    
    /**
    * Отображает страницу со списком страниц 
    * @param array $pages Массив страниц для отображения
    * @return void
    */
    private function renderPageList($pages) {
        $this->render('admin/pages/index', [
            'pages' => $pages,
            'pageTitle' => 'Управление страницами'
        ]);
    }
    
    /**
    * Обрабатывает ситуацию с отсутствием прав доступа
    * @return void
    */
    private function handleAccessDenied() {
        \Notification::error('У вас нет прав доступа к этому разделу');
        $this->redirect(ADMIN_URL . '/login');
    }
    
    /**
    * Обрабатывает ошибку при загрузке списка страниц
    * @param \Exception $e Исключение
    * @return void
    */
    private function handleLoadError($e) {
        \Notification::error('Ошибка при загрузке списка страниц');
        
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            \Notification::error('Детали: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL);
    }
}