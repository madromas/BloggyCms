<?php

namespace users\actions\groups;

/**
* Действие отображения списка всех групп пользователей в административной панели
* @package users\actions\groups
*/
class AdminGroupIndex extends AdminGroupAction {
    
    /**
    * Метод выполнения отображения списка групп
    * @return void
    */
    public function execute() {
        try {

            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Группы пользователей', ADMIN_URL . '/user-groups');

            $groups = $this->userModel->getAllGroups();
            
            $this->render('admin/user-groups/index', [
                'groups' => $groups,
                'pageTitle' => 'Управление группами пользователей'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке групп');
            $this->redirect(ADMIN_URL);
        }
    }

}