<?php

namespace profile\actions;

/**
* Действие обновления данных профиля пользователя 
* @package profile\actions
*/
class Update extends ProfileAction {
    
    /**
    * Метод выполнения обновления профиля
    * @return void
    */
    public function execute() {

        $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('Неправильный метод запроса', '/profile/edit');
            return;
        }
        
        if (!$this->validateCsrfToken()) {
            $this->redirectWithError('Неверный CSRF-токен', '/profile/edit');
            return;
        }
        
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        if (!$user) {
            $this->redirectWithError('Пользователь не найден', '/profile/edit');
            return;
        }

        $updateData = $this->prepareUpdateData();
        
        if ($this->processUpdate($user, $updateData)) {
            $this->redirectWithSuccess('/profile/' . $user['username']);
        }
    }
    
    /**
    * Подготавливает данные из POST-запроса для обновления
    * @return array Массив данных для обновления (пустые поля отфильтрованы)
    */
    private function prepareUpdateData() {
        $data = [
            'display_name' => trim($_POST['display_name'] ?? ''),
            'email' => $this->validateEmail($_POST['email'] ?? ''),
            'website' => $this->validateWebsite($_POST['website'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($_FILES['avatar']['tmp_name'])) {
            $avatarResult = $this->handleAvatarUpload();
            if ($avatarResult) {
                $data['avatar'] = $avatarResult;
            }
        }

        return array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });
    }
    
    /**
    * Выполняет обновление данных пользователя
    * @param array $user Текущие данные пользователя
    * @param array $updateData Данные для обновления
    * @return bool true при успешном обновлении
    */
    private function processUpdate($user, $updateData) {
        if (!empty($_POST['new_password'])) {
            if (!$this->userModel->updatePassword(
                $user['id'],
                $_POST['current_password'] ?? '',
                $_POST['new_password']
            )) {
                $this->redirectWithError('Неверный текущий пароль', '/profile/edit');
                return false;
            }
        }

        if (!empty($updateData) && !$this->userModel->update($user['id'], $updateData)) {
            $this->redirectWithError('Ошибка при сохранении данных', '/profile/edit');
            return false;
        }

        if (!$this->saveCustomFields($user['id'])) {
            return false;
        }

        $this->updateSession($updateData);
        
        return true;
    }
    

    /**
    * Сохраняет значения пользовательских полей
    * @param int $userId ID пользователя
    * @return bool Результат сохранения
    */
    private function saveCustomFields($userId) {
        try {
            $customFields = $this->fieldModel->getActiveByEntityType('user');
            $currentValues = $this->fieldModel->getFieldValues($userId, 'user');
            $fieldManager = new \FieldManager($this->db);
            
            foreach ($customFields as $field) {

                $isRequired = (bool)$field['is_required'];
                
                if ($isRequired) {
                    $postKey = 'field_' . $field['system_name'];
                    $value = $_POST[$postKey] ?? null;
                    
                    if (empty($value) && $value !== '0') {
                        $this->redirectWithError('Поле "' . $field['name'] . '" обязательно для заполнения', '/profile/edit');
                        return false;
                    }
                }
            }
            
            foreach ($customFields as $field) {
                $processedValue = $fieldManager->processFieldValue(
                    $field,
                    $_POST,
                    $_FILES,
                    $currentValues
                );
                
                if ($processedValue !== null) {
                    $result = $this->fieldModel->saveFieldValue(
                        'user',
                        $userId,
                        $field['system_name'],
                        $processedValue
                    );
                    
                    if (!$result) {
                        throw new Exception('Не удалось сохранить поле: ' . $field['name']);
                    }
                } elseif ($field['type'] === 'flag') {
                    $this->fieldModel->saveFieldValue(
                        'user',
                        $userId,
                        $field['system_name'],
                        '0'
                    );
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->redirectWithError('Ошибка при сохранении дополнительных полей: ' . $e->getMessage(), '/profile/edit');
            return false;
        }
    }
    
    /**
    * Обрабатывает загрузку нового аватара пользователя
    * @return string|null Имя загруженного файла или null при ошибке
    */
    private function handleAvatarUpload() {
        $file = $_FILES['avatar'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'Файл превышает максимальный размер',
                UPLOAD_ERR_FORM_SIZE => 'Файл превышает максимальный размер формы',
                UPLOAD_ERR_PARTIAL => 'Файл загружен частично',
                UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
                UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
                UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла',
                UPLOAD_ERR_EXTENSION => 'Загрузка файла остановлена расширением'
            ];
            $errorMsg = $errorMessages[$file['error']] ?? 'Неизвестная ошибка загрузки';
            $this->redirectWithError('Ошибка загрузки аватара: ' . $errorMsg, '/profile/edit');
            return null;
        }

        $uploadDir = UPLOADS_PATH . '/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            $this->redirectWithError('Допустимы только JPG, PNG, GIF или WebP', '/profile/edit');
            return null;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $this->redirectWithError('Максимальный размер файла - 5MB', '/profile/edit');
            return null;
        }

        $currentUser = $this->userModel->getById($_SESSION['user_id']);
        if (!empty($currentUser['avatar']) && $currentUser['avatar'] !== 'default.jpg') {
            $oldAvatarPath = $uploadDir . $currentUser['avatar'];
            if (file_exists($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->redirectWithError('Ошибка загрузки аватара', '/profile/edit');
            return null;
        }

        return $filename;
    }
    
    /**
    * Обновляет данные пользователя в сессии 
    * @param array $data Обновленные данные
    * @return void
    */
    private function updateSession($data) {
        if (isset($data['display_name'])) {
            $_SESSION['display_name'] = $data['display_name'];
        }
        if (isset($data['avatar'])) {
            $_SESSION['avatar'] = $data['avatar'];
        }
    }
    
    /**
    * Валидирует email-адрес
    * @param string $email Email для проверки
    * @return string|null Валидный email или null
    */
    private function validateEmail($email) {
        $email = trim($email);
        
        if (empty($email)) {
            $this->redirectWithError('Email не может быть пустым', '/profile/edit');
            return null;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError('Некорректный email', '/profile/edit');
            return null;
        }
        
        $existingUser = $this->userModel->getByEmail($email);
        if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
            $this->redirectWithError('Этот email уже используется другим пользователем', '/profile/edit');
            return null;
        }
        
        return $email;
    }
    
    /**
    * Валидирует URL веб-сайта
    * @param string $website URL для проверки
    * @return string|null Валидный URL или null
    */
    private function validateWebsite($website) {
        $website = trim($website);
        
        if (empty($website)) {
            return null;
        }
        
        if (!preg_match('/^https?:\/\//', $website)) {
            $website = 'http://' . $website;
        }
        
        if (!filter_var($website, FILTER_VALIDATE_URL)) {
            $this->redirectWithError('Некорректный URL сайта', '/profile/edit');
            return null;
        }
        
        return $website;
    }
}