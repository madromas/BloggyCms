<?php

namespace users\actions\groups;

/**
* Действие управления членством пользователя в группах в административной панели
* @package users\actions\groups
*/
class AdminManageUserGroups extends AdminGroupAction {
    
    /**
    * Метод выполнения управления группами пользователя
    * @return void
    */
    public function execute() {
        error_log('=== AdminManageUserGroups execute START ===');
    error_log('params: ' . print_r($this->params, true));
        try {

            $userId = $this->params['id'] ?? null;
            if (!$userId) {
                throw new \Exception('ID пользователя не указан');
            }

            $user = $this->userModel->getById($userId);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }

            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Пользователи', ADMIN_URL . '/users');
            $this->addBreadcrumb('Редактирование: ' . ($user['display_name'] ?? $user['username']), ADMIN_URL . '/users/edit/' . $userId);
            $this->addBreadcrumb('Группы');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handlePostRequest($userId);
                return;
            }

            $this->renderGroupsForm($user);

        } catch (\Exception $e) {
            \Notification::error($e->getMessage());
            $this->redirect(ADMIN_URL . '/users');
        }
    }
    
    /**
    * Обрабатывает POST-запрос на обновление групп пользователя 
    * @param int $userId ID пользователя
    * @return void
    */
    private function handlePostRequest($userId) {

        $groupIds = $_POST['groups'] ?? [];
        
        $this->userModel->updateUserGroups($userId, $groupIds);
        
        \Notification::success('Группы пользователя обновлены');
        $this->redirect(ADMIN_URL . '/users');
    }
    
    /**
    * Отображает форму управления группами пользователя
    * @param array $user Данные пользователя
    * @return void
    */
    private function renderGroupsForm($user) {
        $allGroups = $this->userModel->getAllGroups();
        $userGroups = $this->userModel->getUserGroups($user['id']);
        
        $this->render('admin/users/manage-groups', [
            'user' => $user,
            'allGroups' => $allGroups,
            'userGroups' => $userGroups,
            'pageTitle' => 'Управление группами пользователя'
        ]);
    }

}