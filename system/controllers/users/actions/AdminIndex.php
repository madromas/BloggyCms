<?php

namespace users\actions;

/**
* Действие отображения списка всех пользователей в административной панели 
* @package users\actions
*/
class AdminIndex extends UserAction {
    
    /**
    * Метод выполнения отображения списка пользователей
    * @return void
    */
    public function execute() {

        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Пользователи');
        
        try {

            $hints = [
                "Создайте группы пользователей для гибкого разграничения прав",
                "Добавьте дополнительные поля для профилей - создавайте целые анкеты для сбора важной информации о пользователях",
                "Вы можете заблокировать (забанить) пользователя прямо в общем списке ниже",
            ];
            
            $randomHint = $hints[array_rand($hints)];

            $role = $_GET['role'] ?? null;
            $status = $_GET['status'] ?? null;
            $group = $_GET['group'] ?? null;
            $search = $_GET['search'] ?? null;
            
            $users = $this->userModel->getUsersWithGroups([
                'role' => $role,
                'status' => $status,
                'group' => $group,
                'search' => $search
            ]);
            
            if (\SettingsHelper::get('controller_users', 'admin_top', true)) {
                $users = $this->sortUsersWithAdminsFirst($users);
            }
            
            $allGroups = $this->userModel->getAllGroups();
            
            $this->render('admin/users/index', [
                'users' => $users,
                'allGroups' => $allGroups,
                'randomHint' => $randomHint,
                'pageTitle' => 'Управление пользователями'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке списка пользователей');
            $this->redirect(ADMIN_URL);
        }
    }
    
    /**
    * Сортирует пользователей: администраторы сверху, затем по алфавиту
    * @param array $users Массив пользователей для сортировки
    * @return array Отсортированный массив пользователей
    */
    private function sortUsersWithAdminsFirst($users) {
        usort($users, function($a, $b) {

            $aIsAdmin = $this->isUserAdmin($a);
            $bIsAdmin = $this->isUserAdmin($b);
            
            if ($aIsAdmin && !$bIsAdmin) {
                return -1;
            } elseif (!$aIsAdmin && $bIsAdmin) {
                return 1;
            } else {
                return strcmp($a['username'], $b['username']);
            }
        });
        
        return $users;
    }
    
    /**
    * Проверяет, является ли пользователь администратором
    * @param array $user Данные пользователя
    * @return bool true если пользователь администратор
    */
    private function isUserAdmin($user) {
        if (!empty($user['groups'])) {
            foreach ($user['groups'] as $group) {
                if (isset($group['name']) && $group['name'] === 'Администраторы') {
                    return true;
                }

                if (isset($group['system_name']) && $group['system_name'] === 'administrators') {
                    return true;
                }
            }
        }
        
        if (isset($user['is_admin']) && $user['is_admin']) {
            return true;
        }
        
        return false;
    }
}