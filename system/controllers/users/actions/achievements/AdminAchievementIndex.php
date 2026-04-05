<?php

namespace users\actions\achievements;

/**
* Действие отображения списка всех достижений (ачивок) в административной панели
* @package users\actions\achievements
*/
class AdminAchievementIndex extends AdminAchievementAction {
    
    /**
    * Метод выполнения отображения списка ачивок
    * @return void
    */
    public function execute() {
        try {

            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Ачивки', ADMIN_URL . '/user-achievements');
            
            $type = $_GET['type'] ?? null;
            $search = $_GET['search'] ?? null;
            
            $filters = [];
            if ($type) {
                $filters['type'] = $type;
            }
            if ($search) {
                $filters['search'] = $search;
            }
            
            $achievements = $this->userModel->getAllAchievements($filters);
            
            $this->render('admin/user-achievements/index', [
                'achievements' => $achievements,
                'pageTitle' => 'Управление ачивками'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке ачивок: ' . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }

}