<?php

namespace users\actions;

/**
* Действие отображения публичного списка всех участников сайта
* @package users\actions
*/
class FrontIndex extends UserAction {
    
    /**
    * Метод выполнения отображения списка участников
    * @return void
    */
    public function execute() {
        try {
            $this->addBreadcrumb('Главная', BASE_URL);
            $this->addBreadcrumb('Все участники');
            $this->setPageTitle('Все участники');
            
            $users = $this->userModel->getActiveUsers();
            
            $activityManager = \UserActivityManager::getInstance($this->db);
            
            foreach ($users as &$user) {
                $this->enrichUserData($user, $activityManager);
            }
            
            $customFields = $this->fieldModel->getActiveByEntityType('user');
            
            $this->render('front/users/users', [
                'users' => $users,
                'customFields' => $customFields,
                'total_users' => count($users)
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке списка пользователей: ' . $e->getMessage());
            $this->redirect(BASE_URL);
        }
    }
    
    /**
    * Обогащает данные одного пользователя дополнительной информацией
    * @param array $user Ссылка на данные пользователя
    * @param \UserActivityManager|null $activityManager Менеджер активности
    * @return void
    */
    private function enrichUserData(&$user, $activityManager) {
        $user['posts_count'] = $this->userModel->getUserStatValue($user['id'], 'posts_count');
        $user['comments_count'] = $this->userModel->getUserStatValue($user['id'], 'comments_count');
        $user['registration_days'] = $this->userModel->getUserStatValue($user['id'], 'registration_days');
        $this->enrichUserActivity($user, $activityManager);
        $user['groups'] = $this->userModel->getUserGroupsWithDetails($user['id']);
        $user['achievements'] = $this->userModel->getUserAchievements($user['id']);
        $user['unlocked_achievements_count'] = $this->countUnlockedAchievements($user['achievements']);
        
        if (empty($user['last_activity_human'])) {
            if (!empty($user['last_login'])) {
                $user['last_activity_human'] = $this->formatActivityTime($user['last_login']);
            } else {
                $user['last_activity_human'] = 'никогда';
            }
        }
    }
    
    /**
    * Добавляет информацию об активности пользователя 
    * @param array $user Ссылка на данные пользователя
    * @param \UserActivityManager|null $activityManager Менеджер активности
    * @return void
    */
    private function enrichUserActivity(&$user, $activityManager) {
        if ($activityManager) {
            $user['is_online'] = $activityManager->isOnline($user['id']);
            $lastActivityInfo = $activityManager->getLastActivityInfo($user['id']);
            $user['last_activity_human'] = $lastActivityInfo['human'] ?? null;
            $user['last_activity_days'] = $lastActivityInfo['days'] ?? 0;
        } else {
            $user['is_online'] = false;
            $user['last_activity_human'] = null;
            $user['last_activity_days'] = 0;
        }
    }
    
    /**
    * Форматирует время активности для отображения 
    * @param string $timestamp Временная метка
    * @return string Отформатированное время
    */
    private function formatActivityTime($timestamp) {
        if (empty($timestamp)) {
            return 'никогда';
        }
        
        $lastActivityTimestamp = strtotime($timestamp);
        $currentTimestamp = time();
        $secondsAgo = $currentTimestamp - $lastActivityTimestamp;
        
        if ($secondsAgo < 60) {
            return 'только что';
        } elseif ($secondsAgo < 3600) {
            $minutesAgo = floor($secondsAgo / 60);
            return $minutesAgo . ' мин назад';
        } elseif ($secondsAgo < 86400) {
            $hoursAgo = floor($secondsAgo / 3600);
            return $hoursAgo . ' ч назад';
        } else {
            $daysAgo = floor($secondsAgo / 86400);
            return $daysAgo . ' д назад';
        }
    }
    
    /**
    * Подсчитывает количество разблокированных ачивок у пользователя
    * @param array $achievements Массив достижений пользователя
    * @return int Количество разблокированных достижений
    */
    private function countUnlockedAchievements($achievements) {
        if (empty($achievements)) {
            return 0;
        }
        
        $count = 0;
        foreach ($achievements as $achievement) {
            if (!empty($achievement['is_unlocked'])) {
                $count++;
            }
        }
        
        return $count;
    }
}