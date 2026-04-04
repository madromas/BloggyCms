<?php

namespace forms\actions;

/**
* Действие создания новой формы в админ-панели
*/
class AdminCreate extends FormAction {
    
    public function execute() {

        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Формы', ADMIN_URL . '/forms');
        $this->addBreadcrumb('Создание формы');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty(trim($_POST['name']))) {
                    throw new \Exception('Название формы обязательно');
                }
                
                $formStructure = json_decode($_POST['form_structure'] ?? '[]', true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Ошибка при разборе структуры формы: ' . json_last_error_msg());
                }
                
                list($isValid, $validationErrors) = $this->formModel->validateFormStructure($formStructure);
                
                if (!$isValid) {
                    throw new \Exception('Ошибки в структуре формы: ' . implode(', ', $validationErrors));
                }
                
                $fieldNames = [];
                foreach ($formStructure as $field) {
                    if (!empty($field['name']) && $field['type'] !== 'submit') {
                        if (in_array($field['name'], $fieldNames)) {
                            throw new \Exception('Имя поля "' . $field['name'] . '" используется несколько раз');
                        }
                        $fieldNames[] = $field['name'];
                    }
                }
                
                $slug = $this->formModel->createSlug(trim($_POST['name']));
                
                $formData = [
                    'name' => trim($_POST['name']),
                    'slug' => $slug,
                    'description' => trim($_POST['description'] ?? ''),
                    'template' => $_POST['template'] ?? 'default',
                    'success_message' => trim($_POST['success_message'] ?? 'Форма успешно отправлена!'),
                    'error_message' => trim($_POST['error_message'] ?? 'Произошла ошибка при отправке формы.'),
                    'status' => $_POST['status'] ?? 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $processedStructure = $this->processFormStructure($formStructure, $_POST);
                $formData['structure'] = $processedStructure;
                $formData['settings'] = $this->prepareSettings($_POST);
                $formData['notifications'] = $this->prepareNotifications($_POST);
                $formData['actions'] = $this->prepareActions($_POST);
                $formId = $this->formModel->create($formData);
                
                if (!$formId) {
                    throw new \Exception('Не удалось создать форму в базе данных');
                }
                
                \Notification::success('Форма успешно создана');
                
                $this->redirect(ADMIN_URL . '/forms');
                
            } catch (\Exception $e) {
                \Notification::error($e->getMessage());
                
                $form = $_POST;
                $form['template'] = $_POST['template'] ?? 'default';
                $form['structure'] = $formStructure ?? [];
                $templates = $this->controller->getAvailableTemplates();
                $currentTheme = $this->controller->getCurrentTheme();
                
                $this->render('admin/forms/form', [
                    'form' => $form,
                    'formStructure' => $formStructure ?? [],
                    'fieldTypes' => $this->controller->getAvailableFieldTypes(),
                    'validationTypes' => $this->controller->getValidationTypes(),
                    'templates' => $templates,
                    'currentTheme' => $currentTheme,
                    'pageTitle' => 'Создание формы',
                    'isEdit' => false,
                    'settings' => $this->prepareSettings($_POST),
                    'notifications' => $this->prepareNotifications($_POST),
                    'actions' => $this->prepareActions($_POST),
                    'formModel' => $this->formModel
                ]);
                return;
            }
        }
        
        $templates = $this->controller->getAvailableTemplates();
        $currentTheme = $this->controller->getCurrentTheme();
        
        $this->render('admin/forms/form', [
            'form' => [
                'status' => 'active',
                'template' => 'default'
            ],
            'formStructure' => [],
            'fieldTypes' => $this->controller->getAvailableFieldTypes(),
            'validationTypes' => $this->controller->getValidationTypes(),
            'templates' => $templates,
            'currentTheme' => $currentTheme,
            'pageTitle' => 'Создание формы',
            'isEdit' => false,
            'settings' => $this->getFormSettings(),
            'notifications' => $this->getDefaultNotifications(),
            'actions' => $this->getDefaultActions(),
            'formModel' => $this->formModel
        ]);
    }
    
    /**
    * Обработка структуры формы - добавление дополнительных данных
    */
    private function processFormStructure($structure, $postData) {
        $processed = [];
        
        foreach ($structure as $field) {
            $processedField = $field;
            
            if (!empty($postData['field_css_' . ($field['name'] ?? '')])) {
                $processedField['class'] = trim($postData['field_css_' . $field['name']]);
            }
            
            if ($field['type'] === 'submit' && !empty($postData['submit_text'])) {
                $processedField['label'] = trim($postData['submit_text']);
            }
            
            $processed[] = $processedField;
        }
        
        return $processed;
    }
    
    /**
    * Подготовка настроек формы
    */
    private function prepareSettings($postData, $currentSettings = []) {
        $settings = (!empty($currentSettings) && is_array($currentSettings)) 
            ? $currentSettings 
            : $this->getFormSettings();
        
        $checkboxSettings = [
            'ajax_enabled' => 'ajax_enabled',
            'show_labels' => 'show_labels',
            'show_descriptions' => 'show_descriptions',
            'store_submissions' => 'store_submissions',
            'redirect_after_submit' => 'redirect_after_submit',
            'captcha_enabled' => 'captcha_enabled',
            'csrf_protection' => 'csrf_protection',
            'limit_submissions' => 'limit_submissions',
            'spam_protection' => 'spam_protection',
            'email_validation' => 'email_validation'
        ];
        
        foreach ($checkboxSettings as $key => $postKey) {
            if (array_key_exists($postKey, $postData)) {
                $settings[$key] = !empty($postData[$postKey]);
            }
        }
        
        $textSettings = [
            'redirect_url' => 'redirect_url',
            'captcha_type' => 'captcha_type',
            'captcha_question' => 'captcha_question',
            'captcha_secret' => 'captcha_secret',
            'spam_keywords' => 'spam_keywords',
            'success_message' => 'success_message',
            'error_message' => 'error_message'
        ];
        
        foreach ($textSettings as $key => $postKey) {
            if (array_key_exists($postKey, $postData)) {
                $settings[$key] = trim($postData[$postKey]);
            }
        }
        
        $numericSettings = [
            'max_submissions_per_day' => 'max_submissions_per_day',
            'max_submissions_per_ip' => 'max_submissions_per_ip'
        ];
        
        foreach ($numericSettings as $key => $postKey) {
            if (array_key_exists($postKey, $postData)) {
                $settings[$key] = intval($postData[$postKey]);
            }
        }
        
        return $settings;
    }
    
    /**
    * Подготовка уведомлений
    */
    private function prepareNotifications($postData) {
        $notifications = [];
        
        if (!empty($postData['notify_admin_enabled']) || !empty($postData['admin_email'])) {
            $notifications[] = [
                'enabled' => !empty($postData['notify_admin_enabled']),
                'type' => 'admin',
                'to' => trim($postData['admin_email'] ?? ''),
                'from' => trim($postData['admin_from'] ?? ''),
                'subject' => trim($postData['admin_subject'] ?? 'Новая отправка формы'),
                'message' => trim($postData['admin_message'] ?? 'Поступила новая отправка формы.')
            ];
        }
        
        if (!empty($postData['notify_user_enabled']) || !empty($postData['user_email_field'])) {
            $notifications[] = [
                'enabled' => !empty($postData['notify_user_enabled']),
                'type' => 'user',
                'to_field' => trim($postData['user_email_field'] ?? '{email}'),
                'from' => trim($postData['user_from'] ?? ''),
                'subject' => trim($postData['user_subject'] ?? 'Ваша форма отправлена'),
                'message' => trim($postData['user_message'] ?? 'Спасибо за вашу заявку!')
            ];
        }
        
        if (empty($notifications)) {
            return [];
        }
        
        return $notifications;
    }
    
    /**
    * Подготовка действий
    */
    private function prepareActions($postData) {
        $actions = [];
        
        $actions[] = [
            'enabled' => !empty($postData['store_submissions']),
            'type' => 'save_to_db',
            'name' => 'Сохранить в базу данных'
        ];
        
        if (!empty($postData['redirect_enabled']) || !empty($postData['redirect_url'])) {
            $actions[] = [
                'enabled' => !empty($postData['redirect_enabled']),
                'type' => 'redirect',
                'name' => 'Редирект после отправки',
                'url' => trim($postData['redirect_url'] ?? '')
            ];
        }
        
        if (!empty($postData['webhook_enabled']) || !empty($postData['webhook_url'])) {
            $headers = [];
            $headersText = $postData['webhook_headers'] ?? '';
            if (!empty($headersText)) {
                $lines = explode("\n", $headersText);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line) && strpos($line, ':') !== false) {
                        list($key, $value) = explode(':', $line, 2);
                        $headers[trim($key)] = trim($value);
                    }
                }
            }
            
            $actions[] = [
                'enabled' => !empty($postData['webhook_enabled']),
                'type' => 'webhook',
                'name' => 'Отправить на вебхук',
                'url' => trim($postData['webhook_url'] ?? ''),
                'method' => $postData['webhook_method'] ?? 'POST',
                'headers' => $headers
            ];
        }

        if (empty($actions)) {
            return [];
        }
        
        return $actions;
    }
}