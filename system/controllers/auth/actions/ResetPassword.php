<?php

namespace auth\actions;

/**
* Класс действия "Сброс пароля"
*/
class ResetPassword extends AuthAction {
    
    /**
    * Выполнение действия сброса пароля
    * @throws \Exception При недействительном токене или ошибках валидации
    */
    public function execute() {
        try {

            $this->pageTitle = LANG_ACTION_AUTH_RESETPASSWORD_PAGE_TITLE;

            $authSettings = $this->getFrontAuthSettings();
            $disableRestore = $authSettings['disable_restore'] ?? false;
            
            if ($disableRestore) {
                \Notification::error(LANG_ACTION_AUTH_RESETPASSWORD_DISABLED);
                $this->redirect(BASE_URL . '/login');
                return;
            }

            $token = $_GET['token'] ?? '';
            
            if (empty($token)) {
                throw new \Exception(LANG_ACTION_AUTH_RESETPASSWORD_TOKEN_MISSING);
            }

            $tokenData = $this->validateResetToken($token);
            
            if (!$tokenData) {
                throw new \Exception(LANG_ACTION_AUTH_RESETPASSWORD_TOKEN_INVALID);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!$this->validateCsrfToken()) {
                    throw new \Exception(LANG_ACTION_AUTH_RESETPASSWORD_INVALID_CSRF);
                }

                if (empty($_POST['password'])) {
                    throw new \Exception(LANG_ACTION_AUTH_RESETPASSWORD_PASSWORD_REQUIRED);
                }

                if (strlen($_POST['password']) < 6) {
                    throw new \Exception(LANG_ACTION_AUTH_RESETPASSWORD_PASSWORD_MIN_LENGTH);
                }

                if ($_POST['password'] !== $_POST['password_confirm']) {
                    throw new \Exception(LANG_ACTION_AUTH_RESETPASSWORD_PASSWORD_MISMATCH);
                }

                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $this->userModel->update($tokenData['user_id'], [
                    'password' => $hashedPassword
                ]);

                $this->markTokenAsUsed($tokenData['id']);
                $this->sendPasswordChangedEmail($tokenData['email'], $tokenData['username']);

                \Notification::success(LANG_ACTION_AUTH_RESETPASSWORD_SUCCESS);
                $this->redirect(BASE_URL . '/login');
                return;
            }

            $this->render('front/auth/reset_password', [
                'csrf_token' => $this->generateCsrfToken(),
                'token' => $token,
                'error' => false
            ]);

        } catch (\Exception $e) {
            \Notification::error($e->getMessage());
            
            $this->render('front/auth/reset_password', [
                'csrf_token' => $this->generateCsrfToken(),
                'token' => $token ?? '',
                'error' => true
            ]);
        }
    }

    /**
    * Получение настроек авторизации для фронтенда
    */
    private function getFrontAuthSettings() {
        return [
            'disable_restore' => \SettingsHelper::get('controller_auth', 'disable_restore', false)
        ];
    }

    /**
    * Валидация токена восстановления пароля
    * @param string $token Токен восстановления из URL
    * @return array|false Данные токена или false при недействительном токене
    */
    private function validateResetToken($token) {
        $tableExists = $this->db->fetch("
            SELECT COUNT(*) as count 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() 
            AND table_name = 'password_resets'
        ");
        
        if (!$tableExists || $tableExists['count'] == 0) {
            return false;
        }
        
        $result = $this->db->fetch("
            SELECT pr.*, u.email, u.username 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.token = ? 
            AND pr.used = FALSE 
            AND pr.expires_at > NOW()
            ORDER BY pr.created_at DESC 
            LIMIT 1
        ", [$token]);
        
        return $result;
    }

    /**
    * Отметка токена восстановления как использованного
    */
    private function markTokenAsUsed($resetId) {
        $this->db->query(
            "UPDATE password_resets SET used = TRUE WHERE id = ?",
            [$resetId]
        );
    }

    /**
    * Отправка email-уведомления об изменении пароля
    * @param string $email Email адрес пользователя
    * @param string $username Имя пользователя
    * @return bool Результат отправки email
    */
    private function sendPasswordChangedEmail($email, $username) {
        try {
            $siteName = \SettingsHelper::get('general', 'site_name', 'BloggyCMS');
            $siteEmail = \SettingsHelper::get('general', 'contact_email', 'noreply@bloggycms.com');
            
            $subject = sprintf(LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_SUBJECT, $siteName);
            
            $message = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <title>" . LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_TITLE . "</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
                        .button { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
                        .footer { font-size: 12px; color: #666; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; }
                        .warning-box { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #dc3545; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2 style='color: #333; text-align: center;'>" . LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_HEADER . "</h2>
                        <p>" . sprintf(LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_GREETING, $username) . "</p>
                        <p>" . sprintf(LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_MESSAGE, $siteName) . "</p>
                        <div class='warning-box'>
                            <p><strong>" . LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_WARNING_TITLE . "</strong> " . LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_WARNING_TEXT . "</p>
                        </div>
                        <p>" . LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_LOGIN_INSTRUCTION . "</p>
                        <div style='text-align: center; margin: 20px 0;'>
                            <a href='" . BASE_URL . "/login' class='button'>
                                " . LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_BUTTON . "
                            </a>
                        </div>
                        <div class='footer'>
                            <p>" . LANG_ACTION_AUTH_RESETPASSWORD_EMAIL_FOOTER . "</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: " . $siteName . " <" . $siteEmail . ">" . "\r\n";
            $headers .= "Reply-To: " . $siteEmail . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "X-Priority: 1 (Highest)" . "\r\n";
            
            return mail($email, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $headers);
            
        } catch (\Exception $e) {
            return false;
        }
    }
}