<?php

namespace profile\actions;

/**
 * Действие отображения формы редактирования профиля пользователя
 * Показывает форму с текущими данными пользователя для редактирования
 * 
 * @package profile\actions
 * @extends ProfileAction
 */
class Edit extends ProfileAction {
    
    /**
     * Метод выполнения отображения формы редактирования профиля
     * Проверяет аутентификацию пользователя, загружает его данные
     * и отображает форму редактирования с CSRF-токеном
     * 
     * @return void
     */
    public function execute() {
        $this->checkAuthentication();
        
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        $this->addBreadcrumb('Главная', BASE_URL);
        $this->addBreadcrumb('Профиль', BASE_URL . '/profile');
        $this->addBreadcrumb('Редактирование профиля');
        $this->setPageTitle('Редактирование профиля');
        
        $this->render('front/profile/edit', [
            'user' => $user, 
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }
}