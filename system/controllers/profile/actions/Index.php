<?php

namespace profile\actions;

/**
 * Действие отображения профиля текущего авторизованного пользователя
 * Показывает личный профиль пользователя с его данными
 * 
 * @package profile\actions
 * @extends ProfileAction
 */
class Index extends ProfileAction {
    
    /**
     * Метод выполнения отображения личного профиля
     * Проверяет аутентификацию пользователя, загружает его данные, отображает страницу профиля
     * 
     * @return void
     */
    public function execute() {

        $this->checkAuthentication();
        
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        $this->addBreadcrumb('Главная', BASE_URL);
        $this->addBreadcrumb('Профиль', BASE_URL . '/profile');
        $this->addBreadcrumb($user['display_name'] ?: $user['username']);
        $this->setPageTitle('Профиль: ' . ($user['display_name'] ?: $user['username']));
        
        $userPosts = $this->postModel->getByUserId($user['id']);
        
        $this->render('front/profile/index', [
            'user' => $user,
            'posts' => $userPosts,
            'is_own_profile' => true
        ]);
    }
}