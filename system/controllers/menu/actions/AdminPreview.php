<?php

namespace menu\actions;

/**
* Действие предпросмотра меню в админ-панели
* @package menu\actions
*/
class AdminPreview extends MenuAction {
    
    /**
    * Метод выполнения предпросмотра меню
    * @return void
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID меню не указан');
            $this->redirect(ADMIN_URL . '/menu');
            return;
        }
        
        $menu = $this->menuModel->getById($id);

        if (!$menu) {
            \Notification::error('Меню не найдено');
            $this->redirect(ADMIN_URL . '/menu');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Меню', ADMIN_URL . '/menu');
        $this->addBreadcrumb('Редактирование: ' . html($menu['name']), ADMIN_URL . '/menu/edit/' . $id);
        $this->addBreadcrumb('Предпросмотр');
        
        $currentTheme = $this->menuModel->getCurrentTheme();
        
        $structure = json_decode($menu['structure'], true) ?: [];
        
        $templateFile = TEMPLATES_PATH . '/' . $currentTheme . '/front/assets/menu/' . $menu['template'] . '.php';
        
        $this->render('admin/menu/preview', [
            'menu' => $menu,
            'structure' => $structure,
            'templateFile' => $templateFile,
            'currentTheme' => $currentTheme,
            'pageTitle' => 'Предпросмотр меню: ' . $menu['name']
        ]);
    }

}