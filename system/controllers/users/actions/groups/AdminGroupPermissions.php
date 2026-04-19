<?php

namespace users\actions\groups;

/**
* Действие управления правами доступа для группы пользователей в административной панели
* @package users\actions\groups
*/
class AdminGroupPermissions extends AdminGroupAction {
    
    /**
    * Метод выполнения управления правами группы
    * @return void
    */
    public function execute() {
        try {

            $id = $this->params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID группы не указан');
            }

            $group = $this->userModel->getGroupById($id);
            if (!$group) {
                throw new \Exception('Группа не найдена');
            }

            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Группы пользователей', ADMIN_URL . '/user-groups');
            $this->addBreadcrumb('Редактирование: ' . $group['name'], ADMIN_URL . '/user-groups/edit/' . $id);
            $this->addBreadcrumb('Права доступа');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handlePostRequest($id);
                return;
            }

            $this->renderPermissionsForm($group);

        } catch (\Exception $e) {
            \Notification::error($e->getMessage());
            $this->redirect(ADMIN_URL . '/user-groups');
        }
    }
    
    /**
    * Обрабатывает POST-запрос на сохранение прав группы
    * @param int $id ID группы
    * @return void
    */
    private function handlePostRequest($id) {

        $permissions = $_POST['permissions'] ?? [];
        
        $this->userModel->updateGroupPermissions($id, $permissions);
        
        \Notification::success('Права группы обновлены');
        $this->redirect(ADMIN_URL . '/user-groups');
    }
    
    /**
    * Отображает форму управления правами группы
    * @param array $group Данные группы
    * @return void
    */
    private function renderPermissionsForm($group) {
        $allPermissions = $this->loadAllPermissions();
        $groupPermissions = $this->userModel->getGroupPermissions($group['id']);
        
        $this->render('admin/user-groups/permissions', [
            'group' => $group,
            'allPermissions' => $allPermissions,
            'groupPermissions' => $groupPermissions,
            'pageTitle' => 'Управление правами группы'
        ]);
    }

    /**
    * Загружает все доступные права из файлов permissions.php контроллеров
    * @return array Массив прав, сгруппированных по контроллерам
    */
    private function loadAllPermissions() {
        $permissions = [];
        
        $controllersPath = ROOT_PATH . '/system/controllers';
        
        $permissionFiles = glob($controllersPath . '/*/permissions.php');
        
        foreach ($permissionFiles as $file) {
            if (file_exists($file) && is_readable($file)) {

                $controllerPermissions = include $file;
                
                $controllerName = basename(dirname($file));
                
                $permissions[$controllerName] = $controllerPermissions;
            }
        }
        
        return $permissions;
    }
}