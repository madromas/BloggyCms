<?php

namespace profile\actions;

class Edit extends ProfileAction {
    
    public function execute() {
        $this->checkAuthentication();
        
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        if (!$user) {
            $this->redirectWithError('Пользователь не найден', '/');
            return;
        }

        $customFieldValues = $this->fieldModel->getFieldValues($user['id'], 'user');
        $customFields = $this->fieldModel->getActiveByEntityType('user');
        
        $fieldsWithValues = [];
        foreach ($customFields as $field) {
            $field['value'] = $customFieldValues[$field['system_name']] ?? null;
            $fieldsWithValues[] = $field;
        }
        
        $this->addBreadcrumb('Главная', BASE_URL);
        $this->addBreadcrumb('Профиль', BASE_URL . '/profile/' . $user['username']);
        $this->addBreadcrumb('Редактирование профиля');
        $this->setPageTitle('Редактирование профиля');
        
        $this->render('front/profile/edit', [
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken(),
            'customFields' => $fieldsWithValues,
            'fieldManager' => new \FieldManager($this->db)
        ]);
    }
}