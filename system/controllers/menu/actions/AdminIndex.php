<?php

namespace menu\actions;

/**
* Действие отображения списка всех меню в админ-панели
* @package menu\actions
* @extends MenuAction
*/
class AdminIndex extends MenuAction {
    
    /**
    * Метод выполнения отображения списка меню 
    * @return void
    */
    public function execute() {

        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Меню');
        
        $menus = $this->menuModel->getAll();
        
        $this->render('admin/menu/index', [
            'menus' => $menus,
            'pageTitle' => 'Управление меню'
        ]);
    }
    
}