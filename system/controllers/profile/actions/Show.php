<?php

namespace profile\actions;

/**
 * Действие отображения публичного профиля пользователя
 * Показывает профиль пользователя по его имени пользователя (username)
 * Содержит информацию о пользователе, его постах, достижениях, активности и группах
 * 
 * @package profile\actions
 * @extends ProfileAction
 */
class Show extends ProfileAction {
    
    /** @var string|null Имя пользователя для отображения профиля */
    protected $username;
    
    /**
     * Устанавливает имя пользователя для отображения профиля
     * 
     * @param string|null $username Имя пользователя
     * @return void
     */
    public function setUsername($username) {
        $this->username = $username;
    }
    
    /**
     * Метод выполнения отображения публичного профиля
     * Получает имя пользователя, загружает данные пользователя,
     * его посты, достижения, активность и отображает страницу профиля
     * 
     * @return void
     */
    public function execute() {
        $username = $this->username ?: ($this->params['username'] ?? '');
        
        if (empty($username)) {
            $this->render('front/404', [], 404);
            return;
        }
        
        $user = $this->userModel->getByUsername($username);
        
        if (!$user) {
            $this->render('front/404', [], 404);
            return;
        }
        
        $this->addBreadcrumb('Главная', BASE_URL);
        $this->addBreadcrumb('Пользователи', BASE_URL . '/users');
        $this->addBreadcrumb($user['display_name'] ?: $user['username']);
        $this->setPageTitle('Профиль: ' . ($user['display_name'] ?: $user['username']));
        
        $activityManager = \UserActivityManager::getInstance($this->db);
        
        if ($activityManager) {
            $result = $activityManager->touch($user['id']);
            $checkSql = "SELECT last_activity FROM user_activity WHERE user_id = ?";
            $checkResult = $this->db->fetch($checkSql, [$user['id']]);
            
            $isOnline = $activityManager->isOnline($user['id']);
            
            if ($checkResult && $checkResult['last_activity']) {
                $diffSql = "SELECT TIMESTAMPDIFF(SECOND, last_activity, NOW()) as diff FROM user_activity WHERE user_id = ?";
                $diffResult = $this->db->fetch($diffSql, [$user['id']]);
            }
        }
        
        $isOnline = $activityManager ? $activityManager->isOnline($user['id']) : false;
        $lastActivityInfo = $activityManager ? $activityManager->getLastActivityInfo($user['id']) : ['human' => 'неизвестно', 'days' => 0];
        
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
        $currentUserId = $_SESSION['user_id'] ?? null;
        $isOwnProfile = ($currentUserId && $currentUserId == $user['id']);
        
        $profileUserIsAdmin = ($user['role'] === 'admin' || !empty($user['is_admin']));
        
        if ($profileUserIsAdmin) {
            $userPosts = $this->postModel->getPublishedByUserId($user['id']);
            $bookmarks = [];
            $displayType = 'posts';
        } elseif ($isOwnProfile) {
            $userPosts = [];
            $bookmarksResult = $this->postModel->getUserBookmarks($user['id'], 1, 10);
            $bookmarks = $bookmarksResult['posts'] ?? [];
            $displayType = 'bookmarks';
        } else {
            $userPosts = [];
            $bookmarks = [];
            $displayType = 'restricted';
        }
        
        $commentsCount = $this->userModel->getUserStatValue($user['id'], 'comments_count');
        $daysSinceRegistration = $this->userModel->getUserStatValue($user['id'], 'registration_days');
        $postsCount = $profileUserIsAdmin ? count($userPosts) : 0;
        $customFields = $this->fieldModel->getActiveByEntityType('user');
        $allAchievements = $this->userModel->getUserAchievements($user['id']);
        
        $achievements = array_filter($allAchievements, function($achievement) {
            return $achievement['is_unlocked'];
        });
        
        $allActiveAchievements = $this->userModel->getAllAchievements(['active' => true]);
        $totalAchievementsInSystem = count($allActiveAchievements);
        $unlockedCount = count($achievements);
        $allAchievementsCount = count($allAchievements);
        $groups = $this->getUserGroups($user['id']);
        $roleDisplay = $this->getRoleDisplay($user['role'] ?? 'user');
        
        $this->render('front/profile/show', [
            'user' => $user,
            'posts' => $userPosts,
            'bookmarks' => $bookmarks,
            'displayType' => $displayType,
            'is_own_profile' => $isOwnProfile,
            'profileUserIsAdmin' => $profileUserIsAdmin,
            'customFields' => $customFields,
            'achievements' => $achievements,
            'allAchievementsCount' => $allAchievementsCount,
            'totalAchievementsInSystem' => $totalAchievementsInSystem,
            'unlockedCount' => $unlockedCount,
            'groups' => $groups,
            'commentsCount' => $commentsCount,
            'postsCount' => $postsCount,
            'daysSinceRegistration' => $daysSinceRegistration,
            'is_online' => $isOnline,
            'last_activity_human' => $lastActivityInfo['human'],
            'last_activity_days' => $lastActivityInfo['days'],
            'roleDisplay' => $roleDisplay
        ]);
    }
    
    /**
     * Получает группы пользователя с деталями
     * 
     * @param int $userId ID пользователя
     * @return array Массив групп пользователя
     */
    private function getUserGroups($userId) {
        try {
            if (method_exists($this->userModel, 'getUserGroupsWithDetails')) {
                return $this->userModel->getUserGroupsWithDetails($userId);
            }
            
            return $this->db->fetchAll(
                "SELECT g.* 
                 FROM user_groups g 
                 INNER JOIN users_groups ug ON g.id = ug.group_id 
                 WHERE ug.user_id = ?
                 ORDER BY g.name",
                [$userId]
            );
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Получает отображаемое название роли пользователя
     * 
     * @param string $userRole Код роли пользователя
     * @return string Отображаемое название роли
     */
    private function getRoleDisplay($userRole) {
        if ($userRole === 'user') {
            return '';
        }
        
        $roles = [
            'admin' => 'Администратор', 
            'author' => 'Автор',
            'editor' => 'Редактор',
            'moderator' => 'Модератор'
        ];
        
        return $roles[$userRole] ?? 'Участник';
    }
}