<?php

namespace auth\actions;

/**
* Действие для восстановления пароля пользователя
*/
class ForgotPassword extends AuthAction {
    
    /**
    * Основной метод выполнения процесса восстановления пароля
    */
    public function execute() {
        try {

            $this->addBreadcrumb(LANG_ACTION_AUTH_FORGOTPASSWORD_BREADCRUMB_HOME, BASE_URL);
            $this->addBreadcrumb(LANG_ACTION_AUTH_FORGOTPASSWORD_BREADCRUMB_LOGIN, BASE_URL . '/login');
            $this->addBreadcrumb(LANG_ACTION_AUTH_FORGOTPASSWORD_BREADCRUMB_FORGOT);
            $this->pageTitle = LANG_ACTION_AUTH_FORGOTPASSWORD_PAGE_TITLE;

            $authSettings = $this->getFrontAuthSettings();
            $disableRestore = $authSettings['disable_restore'] ?? false;
            
            if ($disableRestore) {
                \Notification::error(LANG_ACTION_AUTH_FORGOTPASSWORD_DISABLED);
                $this->redirect(BASE_URL . '/login');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateCsrfToken()) {
                    throw new \Exception(LANG_ACTION_AUTH_FORGOTPASSWORD_INVALID_CSRF);
                }

                if (empty($_POST['email'])) {
                    throw new \Exception(LANG_ACTION_AUTH_FORGOTPASSWORD_EMAIL_REQUIRED);
                }

                $email = $_POST['email'];
                
                $user = $this->userModel->getByEmail($email);
                if (!$user) {
                    throw new \Exception(LANG_ACTION_AUTH_FORGOTPASSWORD_USER_NOT_FOUND);
                }

                $resetToken = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $this->saveResetToken($user['id'], $resetToken, $expiresAt);
                $this->sendResetEmail($user['email'], $resetToken, $user['username']);

                \Notification::success(LANG_ACTION_AUTH_FORGOTPASSWORD_EMAIL_SENT);
                $this->redirect(BASE_URL . '/login');
                return;
            }

            $this->render('front/auth/forgot_password', [
                'csrf_token' => $this->generateCsrfToken()
            ]);

        } catch (\Exception $e) {
            \Notification::error($e->getMessage());
            
            $this->render('front/auth/forgot_password', [
                'csrf_token' => $this->generateCsrfToken(),
                'email' => $_POST['email'] ?? ''
            ]);
        }
    }

    /**
    * Получение настроек авторизации для фронтенда из базы данных
    */
    private function getFrontAuthSettings() {
        return [
            'disable_restore' => \SettingsHelper::get('controller_auth', 'disable_restore', false)
        ];
    }

    /**
    * Сохранение токена восстановления пароля в базе данных
    */
    private function saveResetToken($userId, $token, $expiresAt) {
        $this->db->query(
            "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)",
            [$userId, $token, $expiresAt]
        );
    }

    /**
    * Отправка email с инструкциями по восстановлению пароля
    */
    private function sendResetEmail($email, $token, $username) {
        return \Email::sendPasswordReset($email, $token, $username);
    }
    
}