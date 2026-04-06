<?php

/**
* Класс для рендеринга и обработки HTML-форм
* @package Forms
*/
class FormRenderer {
    
    /**
    * Проверяет, существует ли класс Database, и возвращает подключение 
    * @return object|null Подключение к базе данных
    */
    private static function getDatabase() {
        if (class_exists('Database') && method_exists('Database', 'getInstance')) {
            return Database::getInstance();
        }

        global $db;
        return $db;
    }
    
    /**
    * Рендерит форму по её ID 
    * @param int $formId ID формы
    * @param array $options Опции рендеринга
    * @return string HTML-код формы
    */
    public static function renderById($formId, $options = []) {
        $db = self::getDatabase();
        $formModel = new FormModel($db);
        
        $form = $formModel->getById($formId);
        if (!$form || $form['status'] !== 'active') {
            return '<!-- Form not found or inactive -->';
        }
        
        return self::renderForm($form, $options);
    }
    
    /**
    * Рендерит форму по её слагу с учетом шаблона 
    * @param string $formSlug Слаг формы
    * @param array $options Опции рендеринга
    * @return string HTML-код формы
    */
    public static function render($formSlug, $options = []) {
        $db = self::getDatabase();
        $formModel = new FormModel($db);
        
        $form = $formModel->getBySlug($formSlug);
        if (!$form) {
            return '<!-- Form not found: ' . $formSlug . ' -->';
        }
        
        $template = $form['template'] ?? 'default';
        $currentTheme = defined('DEFAULT_TEMPLATE') ? DEFAULT_TEMPLATE : 'default';
        $templateFile = ROOT_PATH . '/templates/' . $currentTheme . '/front/assets/forms/' . $template . '.php';
        
        if ($template !== 'default' && file_exists($templateFile)) {
            return self::renderCustomTemplate($form, $templateFile, $options);
        }
        
        return self::renderForm($form, $options);
    }
    
    /**
    * Рендерит форму с использованием кастомного шаблона 
    * @param array $form Данные формы
    * @param string $templateFile Путь к файлу шаблона
    * @param array $options Опции рендеринга
    * @return string HTML-код формы
    */
    private static function renderCustomTemplate($form, $templateFile, $options = []) {
        $structure = $form['structure'] ?? [];
        $settings = $form['settings'] ?? [];
        $formId = $form['id'];
        $formSlug = $form['slug'];
        $formName = $form['name'];
        $formDescription = $form['description'] ?? '';
        $formTemplate = $form['template'] ?? 'default';
        $actionUrl = BASE_URL . '/form/' . $formSlug . '/submit';
        
        $options = array_merge([
            'ajax' => $settings['ajax_enabled'] ?? true,
            'show_labels' => $settings['show_labels'] ?? true,
            'show_descriptions' => $settings['show_descriptions'] ?? true,
            'captcha' => $settings['captcha_enabled'] ?? false,
            'csrf_protection' => $settings['csrf_protection'] ?? true,
            'class' => '',
            'style' => '',
            'submit_text' => 'Отправить',
            'submit_class' => 'btn btn-primary'
        ], $options);
        
        $csrfToken = '';
        if ($options['csrf_protection']) {
            $csrfToken = self::generateCsrfToken($formSlug);
        }
        
        $captchaHtml = '';
        if ($options['captcha'] && !empty($settings['captcha_enabled'])) {
            $captchaHtml = self::renderCaptcha($settings);
        }
        
        ob_start();
        include $templateFile;
        $output = ob_get_clean();
        return $output;
    }
    
    /**
    * Основной метод рендеринга формы 
    * @param array $form Данные формы
    * @param array $options Опции рендеринга
    * @return string HTML-код формы
    */
    private static function renderForm($form, $options = []) {
        $structure = $form['structure'] ?? [];
        $settings = $form['settings'] ?? [];
        $formId = $form['id'];
        $formSlug = $form['slug'];
        
        $options = array_merge([
            'class' => '',
            'style' => '',
            'show_labels' => true,
            'show_descriptions' => true,
            'submit_text' => $form['submit_text'] ?? 'Отправить',
            'submit_class' => 'btn btn-primary',
            'ajax' => false,
            'captcha' => false,
            'captcha_site_key' => '',
            'csrf_protection' => true
        ], $options);
        
        $actionUrl = BASE_URL . '/form/' . $formSlug . '/submit';
        
        $csrfToken = '';
        if ($options['csrf_protection']) {
            $csrfToken = self::generateCsrfToken($formSlug);
        }
        
        $captchaHtml = '';
        if ($options['captcha'] && !empty($settings['captcha_enabled'])) {
            $captchaHtml = self::renderCaptcha($settings);
        }
        
        ob_start();
        ?>
        <form id="form-<?= $formSlug ?>" 
              class="form-builder-form <?= $options['class'] ?>" 
              style="<?= $options['style'] ?>" 
              method="POST" 
              action="<?= $actionUrl ?>"
              enctype="multipart/form-data"
              <?= $options['ajax'] ? 'data-ajax="true"' : '' ?>>
            
            <input type="hidden" name="form_id" value="<?= $formId ?>">
            <input type="hidden" name="form_slug" value="<?= $formSlug ?>">
            
            <?php if ($csrfToken) { ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <?php } ?>
            
            <div class="form-fields">
                <?php foreach ($structure as $field) { ?>
                    <?= self::renderField($field, $options) ?>
                <?php } ?>
            </div>
            
            <?php if ($captchaHtml) { ?>
                <?= $captchaHtml ?>
            <?php } ?>
            
            <?php if (!empty($form['description'])) { ?>
                <div class="form-description mb-3">
                    <small class="text-muted"><?= htmlspecialchars($form['description']) ?></small>
                </div>
            <?php } ?>
        </form>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('form-<?= $formSlug ?>');
                if (!form) return;
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn ? submitBtn.innerHTML : 'Отправить';
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Отправка...';
                    }
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const successHtml = `
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    ${data.message}
                                </div>
                            `;
                            form.innerHTML = successHtml;
                            
                            if (data.redirect) {
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 2000);
                            }
                        } else {
                            const formContent = form.innerHTML;
                            const errorHtml = `
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    ${data.message}
                                </div>
                            `;
                            form.innerHTML = errorHtml + formContent;
                            
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Произошла ошибка при отправке формы');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    });
                });
            });
        </script>
        
        <?php
        return ob_get_clean();
    }
    
    /**
    * Рендерит капчу для защиты от спама
    */
    private static function renderCaptcha($settings) {
        $type = $settings['captcha_type'] ?? 'math';
        $secret = $settings['captcha_secret'] ?? 'bloggy_cms_captcha';
        
        $captchaData = self::generateCaptchaData($type, $settings);
        
        $encryptedAnswer = openssl_encrypt(
            $captchaData['answer'],
            'AES-128-ECB',
            $secret,
            0
        );
        
        ob_start();
        ?>
        <div class="form-group mb-3">
            <label class="form-label">
                <i class="bi bi-shield-check me-1"></i>Защита от спама
                <span class="text-danger">*</span>
            </label>
            
            <div class="card bg-light">
                <div class="card-body">
                    <?php if ($type === 'image' && !empty($captchaData['image'])) { ?>
                        <div class="mb-3">
                            <img src="<?= $captchaData['image'] ?>" 
                                alt="Captcha" 
                                style="max-width: 200px; border: 1px solid #ddd; border-radius: 4px;">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick="location.reload()">
                                    <i class="bi bi-arrow-clockwise"></i> Обновить
                                </button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <h6 class="card-title"><?= htmlspecialchars($captchaData['question']) ?></h6>
                    <?php } ?>
                    
                    <input type="hidden" name="captcha_hash" value="<?= htmlspecialchars($encryptedAnswer) ?>">
                    <input type="text" 
                        class="form-control" 
                        name="captcha_answer" 
                        placeholder="<?= $type === 'image' ? 'Введите символы' : 'Введите ответ' ?>"
                        autocomplete="off"
                        required>
                    <div class="form-text small">Пожалуйста, подтвердите, что вы не робот</div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
    * Генерирует данные капчи
    */
    private static function generateCaptchaData($type, $settings) {
        switch ($type) {
            case 'math':
                $operations = ['+', '-', '*'];
                $op = $operations[array_rand($operations)];
                $a = rand(10, 50);
                $b = rand(10, 50);
                
                if ($op === '-') {
                    $a = max($a, $b) + rand(5, 20);
                }
                
                $question = "Сколько будет {$a} {$op} {$b}?";
                
                switch ($op) {
                    case '+': $answer = $a + $b; break;
                    case '-': $answer = $a - $b; break;
                    case '*': $answer = $a * $b; break;
                    default: $answer = 0;
                }
                
                return [
                    'question' => $question,
                    'answer' => (string)$answer
                ];
                
            case 'text':
                $questions = [
                    'Столица России?' => 'Москва',
                    'Сколько дней в неделе?' => '7',
                    'Какого цвета трава?' => 'Зеленый',
                    'Что пьют коровы?' => 'Воду',
                    'Сколько месяцев в году?' => '12'
                ];
                $question = array_rand($questions);
                return [
                    'question' => $question,
                    'answer' => $questions[$question]
                ];
                
            case 'logic':
                $questions = [
                    'Что тяжелее: 1 кг пуха или 1 кг железа?' => 'одинаково',
                    'Что идет не двигаясь с места?' => 'время',
                    'Что можно увидеть с закрытыми глазами?' => 'сон'
                ];
                $question = array_rand($questions);
                return [
                    'question' => $question,
                    'answer' => $questions[$question]
                ];
                
            case 'image':
                if (!extension_loaded('gd')) {
                    return self::generateCaptchaData('math', $settings);
                }
                
                $length = rand(4, 6);
                $chars = 'ABCDEFGHKMNPRSTUVWXYZ23456789';
                $code = '';
                for ($i = 0; $i < $length; $i++) {
                    $code .= $chars[rand(0, strlen($chars) - 1)];
                }
                
                $width = 200;
                $height = 60;
                $image = imagecreatetruecolor($width, $height);
                
                $bgColor = imagecolorallocate($image, rand(240, 255), rand(240, 255), rand(240, 255));
                imagefill($image, 0, 0, $bgColor);
                
                for ($i = 0; $i < 100; $i++) {
                    $noiseColor = imagecolorallocate($image, rand(200, 230), rand(200, 230), rand(200, 230));
                    imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
                }
                
                $font = 5;
                $textColor = imagecolorallocate($image, rand(50, 150), rand(50, 150), rand(50, 150));
                $x = ($width - strlen($code) * imagefontwidth($font)) / 2;
                $y = ($height - imagefontheight($font)) / 2;
                imagestring($image, $font, $x, $y, $code, $textColor);
                
                for ($i = 0; $i < 5; $i++) {
                    $lineColor = imagecolorallocate($image, rand(150, 200), rand(150, 200), rand(150, 200));
                    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
                }
                
                ob_start();
                imagepng($image);
                $imageData = ob_get_clean();
                imagedestroy($image);
                
                return [
                    'question' => 'Введите символы с изображения',
                    'answer' => strtolower($code),
                    'image' => 'data:image/png;base64,' . base64_encode($imageData)
                ];
                
            default:
                return [
                    'question' => 'Сколько будет 2 + 2?',
                    'answer' => '4'
                ];
        }
    }
    
    /**
    * Генерирует CSRF токен для формы 
    * @param string $formSlug Слаг формы
    * @return string Сгенерированный токен
    */
    public static function generateCsrfToken($formSlug) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $formName = 'form_' . $formSlug;
        
        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }
        
        $token = bin2hex(random_bytes(32));
        
        $_SESSION['csrf_tokens'][$formName] = [
            'token' => $token,
            'created_at' => time()
        ];
        
        foreach ($_SESSION['csrf_tokens'] as $name => $tokenData) {
            if (time() - $tokenData['created_at'] > 3600) {
                unset($_SESSION['csrf_tokens'][$name]);
            }
        }
        
        return $token;
    }
    
    /**
    * Проверяет CSRF токен 
    * @param string $token Токен для проверки
    * @param string $formSlug Слаг формы
    * @return bool true если токен валидный
    */
    public static function verifyCsrfToken($token, $formSlug) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $formName = 'form_' . $formSlug;
        
        if (!isset($_SESSION['csrf_tokens'][$formName])) {
            return false;
        }
        
        $storedToken = $_SESSION['csrf_tokens'][$formName];
        
        if (time() - $storedToken['created_at'] > 3600) {
            unset($_SESSION['csrf_tokens'][$formName]);
            return false;
        }
        
        if (!hash_equals($storedToken['token'], $token)) {
            return false;
        }
        
        return true;
    }
    
    /**
    * Рендерит одно поле формы 
    * @param array $field Данные поля
    * @param array $options Опции рендеринга
    * @return string HTML-код поля
    */
    private static function renderField($field, $options = []) {
        $type = $field['type'] ?? 'text';
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $value = $field['default_value'] ?? '';
        $placeholder = $field['placeholder'] ?? '';
        $required = !empty($field['required']);
        $description = $field['description'] ?? '';
        $validation = $field['validation'] ?? [];
        $fieldOptions = $field['options'] ?? [];
        $cssClass = $field['class'] ?? '';
        $validationAttrs = self::getValidationAttributes($validation);
        
        if ($type === 'hidden') {
            return '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '">';
        }
        
        if ($type === 'submit') {
            $label = $label ?: ($field['submit_text'] ?? 'Отправить');
            return '<button type="submit" class="' . ($field['class'] ?? $options['submit_class']) . '">' . htmlspecialchars($label) . '</button>';
        }
        
        ob_start();
        ?>
        <div class="form-group mb-3 field-<?= $type ?> <?= $field['class'] ?? '' ?>">
            <?php if ($options['show_labels'] && $label && $type !== 'checkbox' && $type !== 'radio') { ?>
                <label for="field-<?= $name ?>" class="form-label">
                    <?= htmlspecialchars($label) ?>
                    <?php if ($required) { ?>
                        <span class="text-danger">*</span>
                    <?php } ?>
                </label>
            <?php } ?>
            
            <?php switch ($type):
                case 'text':
                case 'email':
                case 'tel':
                case 'number':
                case 'date':
                case 'password': ?>
                    <input type="<?= $type ?>" 
                        id="field-<?= $name ?>" 
                        name="<?= htmlspecialchars($name) ?>" 
                        class="form-control <?= $cssClass ?> <?= $required ? 'required' : '' ?>" 
                        value="<?= htmlspecialchars($value) ?>"
                        placeholder="<?= htmlspecialchars($placeholder) ?>"
                        <?= $required ? 'required' : '' ?>
                        <?= $validationAttrs ?>>
                    <?php break;
                
                case 'textarea': ?>
                    <textarea id="field-<?= $name ?>" 
                              name="<?= htmlspecialchars($name) ?>" 
                              class="form-control <?= $cssClass ?> <?= $required ? 'required' : '' ?>" 
                              rows="<?= $field['rows'] ?? 3 ?>"
                              placeholder="<?= htmlspecialchars($placeholder) ?>"
                              <?= $required ? 'required' : '' ?>
                              <?= $validationAttrs ?>><?= htmlspecialchars($value) ?></textarea>
                    <?php break;
                
                case 'select': ?>
                    <select id="field-<?= $name ?>" 
                            name="<?= htmlspecialchars($name) . (!empty($field['multiple']) ? '[]' : '') ?>" 
                            class="form-select <?= $cssClass ?> <?= $required ? 'required' : '' ?>"
                            <?= $required ? 'required' : '' ?>
                            <?= !empty($field['multiple']) ? 'multiple' : '' ?>
                            <?= $validationAttrs ?>>
                        <?php if (!empty($placeholder)) { ?>
                            <option value=""><?= htmlspecialchars($placeholder) ?></option>
                        <?php } ?>
                        <?php foreach ($fieldOptions as $option) { ?>
                            <option value="<?= htmlspecialchars($option['value'] ?? '') ?>"
                                    <?= self::isOptionSelected($option['value'] ?? '', $value) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($option['label'] ?? '') ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php break;
                
                case 'checkbox': ?>
                    <div class="form-check">
                        <input type="checkbox" 
                               id="field-<?= $name ?>" 
                               name="<?= htmlspecialchars($name) ?>" 
                               class="form-check-input <?= $cssClass ?> <?= $required ? 'required' : '' ?>" 
                               value="<?= htmlspecialchars($field['checkbox_value'] ?? '1') ?>"
                               <?= !empty($value) ? 'checked' : '' ?>
                               <?= $required ? 'required' : '' ?>
                               <?= $validationAttrs ?>>
                        <label for="field-<?= $name ?>" class="form-check-label">
                            <?= htmlspecialchars($label) ?>
                            <?php if ($required) { ?>
                                <span class="text-danger">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <?php break;
                
                case 'radio': ?>
                    <div class="radio-group">
                        <?php foreach ($fieldOptions as $index => $option) { ?>
                            <div class="form-check">
                                <input type="radio" 
                                       id="field-<?= $name ?>-<?= $index ?>" 
                                       name="<?= htmlspecialchars($name) ?>" 
                                       class="form-check-input <?= $cssClass ?> <?= $required ? 'required' : '' ?>" 
                                       value="<?= htmlspecialchars($option['value'] ?? '') ?>"
                                       <?= self::isOptionSelected($option['value'] ?? '', $value) ? 'checked' : '' ?>
                                       <?= $required ? 'required' : '' ?>
                                       <?= $validationAttrs ?>>
                                <label for="field-<?= $name ?>-<?= $index ?>" class="form-check-label">
                                    <?= htmlspecialchars($option['label'] ?? '') ?>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                    <?php break;
                
                case 'file': ?>
                    <input type="file" 
                           id="field-<?= $name ?>" 
                           name="<?= htmlspecialchars($name) . (!empty($field['multiple']) ? '[]' : '') ?>" 
                           class="form-control <?= $cssClass ?> <?= $required ? 'required' : '' ?>" 
                           <?= $required ? 'required' : '' ?>
                           <?= !empty($field['multiple']) ? 'multiple' : '' ?>
                           <?= !empty($field['accept']) ? 'accept="' . htmlspecialchars($field['accept']) . '"' : '' ?>
                           <?= $validationAttrs ?>>
                    <?php break;
                
                default: ?>
                    <div class="alert alert-warning">
                        Неизвестный тип поля: <?= htmlspecialchars($type) ?>
                    </div>
            <?php endswitch; ?>
            
            <?php if ($options['show_descriptions'] && $description) { ?>
                <div class="form-text"><?= htmlspecialchars($description) ?></div>
            <?php } ?>
            
            <?php if (!empty($validation)) { ?>
                <div class="invalid-feedback d-none"></div>
            <?php } ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
    * Проверяет, выбрана ли опция
    * @param mixed $optionValue Значение опции
    * @param mixed $fieldValue Значение поля
    * @return bool true если выбрана
    */
    private static function isOptionSelected($optionValue, $fieldValue) {
        if (is_array($fieldValue)) {
            return in_array($optionValue, $fieldValue);
        }
        return $optionValue == $fieldValue;
    }
    
    /**
    * Получает атрибуты валидации для поля 
    * @param array $validation Массив правил валидации
    * @return string Строка с HTML-атрибутами
    */
    private static function getValidationAttributes($validation) {
        $attrs = [];
        
        foreach ($validation as $rule => $params) {
            switch ($rule) {
                case 'required':
                    $attrs[] = 'required';
                    break;
                case 'email':
                    $attrs[] = 'pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"';
                    break;
                case 'url':
                    $attrs[] = 'pattern="https?://.+"';
                    break;
                case 'numeric':
                    $attrs[] = 'pattern="\d*"';
                    break;
                case 'min':
                    if (is_numeric($params)) {
                        $attrs[] = 'min="' . $params . '"';
                    } else {
                        $attrs[] = 'minlength="' . $params . '"';
                    }
                    break;
                case 'max':
                    if (is_numeric($params)) {
                        $attrs[] = 'max="' . $params . '"';
                    } else {
                        $attrs[] = 'maxlength="' . $params . '"';
                    }
                    break;
                case 'regex':
                    $attrs[] = 'pattern="' . htmlspecialchars($params) . '"';
                    break;
            }
        }
        
        return implode(' ', $attrs);
    }
    
    /**
    * Валидирует данные формы на стороне сервера
    * @param array $form Данные формы
    * @param array $data POST-данные
    * @param array $files FILES-данные
    * @return array Массив ошибок [поле => сообщение]
    */
    public static function validateSubmission($form, $data, $files = []) {
        $errors = [];
        $structure = $form['structure'] ?? [];
        
        foreach ($structure as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldType = $field['type'] ?? '';
            $fieldLabel = $field['label'] ?? $fieldName;
            $required = !empty($field['required']);
            $validation = $field['validation'] ?? [];
            
            if ($fieldType === 'submit' || $fieldType === 'hidden') {
                continue;
            }
            
            $value = $data[$fieldName] ?? '';
            $file = $files[$fieldName] ?? null;
            
            if ($required) {
                if ($fieldType === 'file') {
                    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
                        $errors[$fieldName] = "Поле '{$fieldLabel}' обязательно для заполнения";
                        continue;
                    }
                } elseif (empty($value) && $value !== '0') {
                    $errors[$fieldName] = "Поле '{$fieldLabel}' обязательно для заполнения";
                    continue;
                }
            }
            
            if (!$required && empty($value) && $value !== '0' && (!$file || $file['error'] === UPLOAD_ERR_NO_FILE)) {
                continue;
            }
            
            foreach ($validation as $rule => $params) {
                $error = self::validateRule($rule, $params, $value, $fieldLabel, $fieldType);
                if ($error) {
                    $errors[$fieldName] = $error;
                    break;
                }
            }
            
            $typeError = self::validateFieldType($fieldType, $value, $fieldLabel);
            if ($typeError) {
                $errors[$fieldName] = $typeError;
            }
            
            if ($fieldType === 'file' && $file && $file['error'] === UPLOAD_ERR_OK) {
                $fileError = self::validateFile($file, $field);
                if ($fileError) {
                    $errors[$fieldName] = $fileError;
                }
            }
        }
        
        return $errors;
    }
    
    /**
    * Проверяет правило валидации
    * @param string $rule Правило
    * @param mixed $params Параметры
    * @param mixed $value Значение
    * @param string $label Название поля
    * @param string $type Тип поля
    * @return string|null Сообщение об ошибке или null
    */
    private static function validateRule($rule, $params, $value, $label, $type) {
        switch ($rule) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "Поле '{$label}' должно содержать корректный email адрес";
                }
                break;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return "Поле '{$label}' должно содержать корректный URL";
                }
                break;
            case 'numeric':
                if (!is_numeric($value)) {
                    return "Поле '{$label}' должно содержать только цифры";
                }
                break;
            case 'min':
                if ($type === 'number' || $type === 'date') {
                    if ($value < $params) {
                        return "Поле '{$label}' должно быть не меньше {$params}";
                    }
                } else {
                    if (strlen($value) < $params) {
                        return "Поле '{$label}' должно содержать не менее {$params} символов";
                    }
                }
                break;
            case 'max':
                if ($type === 'number' || $type === 'date') {
                    if ($value > $params) {
                        return "Поле '{$label}' должно быть не больше {$params}";
                    }
                } else {
                    if (strlen($value) > $params) {
                        return "Поле '{$label}' должно содержать не более {$params} символов";
                    }
                }
                break;
            case 'regex':
                if (!preg_match($params, $value)) {
                    return "Поле '{$label}' не соответствует требуемому формату";
                }
                break;
        }
        
        return null;
    }
    
    /**
    * Проверяет тип поля 
    * @param string $type Тип поля
    * @param mixed $value Значение
    * @param string $label Название поля
    * @return string|null Сообщение об ошибке или null
    */
    private static function validateFieldType($type, $value, $label) {
        if (empty($value)) return null;
        
        switch ($type) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "Поле '{$label}' должно содержать корректный email адрес";
                }
                break;
            case 'number':
                if (!is_numeric($value)) {
                    return "Поле '{$label}' должно содержать число";
                }
                break;
            case 'tel':
                if (!preg_match('/^[\d\s\-\+\(\)]+$/', $value)) {
                    return "Поле '{$label}' должно содержать корректный номер телефона";
                }
                break;
            case 'date':
                if (!strtotime($value)) {
                    return "Поле '{$label}' должно содержать корректную дату";
                }
                break;
        }
        
        return null;
    }
    
    /**
    * Проверяет файл
    * @param array $file Данные файла
    * @param array $field Данные поля
    * @return string|null Сообщение об ошибке или null
    */
    private static function validateFile($file, $field) {
        $maxSize = $field['max_size'] ?? 5242880;
        $allowedTypes = $field['allowed_types'] ?? [];
        
        if ($file['size'] > $maxSize) {
            $maxSizeMB = round($maxSize / 1024 / 1024, 1);
            return "Размер файла не должен превышать {$maxSizeMB}MB";
        }
        
        if (!empty($allowedTypes)) {
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileMime = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileExtension, $allowedTypes) && !in_array($fileMime, $allowedTypes)) {
                return "Разрешены только файлы типов: " . implode(', ', $allowedTypes);
            }
        }
        
        return null;
    }
    
    /**
    * Отправляет уведомления по email 
    * @param array $form Данные формы
    * @param array $data Данные отправки
    * @param int $submissionId ID отправки
    * @return int Количество отправленных уведомлений
    */
    public static function sendNotifications($form, $data, $submissionId) {
        $notifications = $form['notifications'] ?? [];
        $sent = 0;
        
        foreach ($notifications as $notification) {
            if (empty($notification['enabled'])) continue;
            
            $to = self::parseEmailTemplate($notification['to'], $data);
            $subject = self::parseEmailTemplate($notification['subject'], $data);
            $message = self::parseEmailTemplate($notification['message'], $data);
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . ($notification['from'] ?? 'noreply@' . $_SERVER['HTTP_HOST']) . "\r\n";
            
            if (mail($to, $subject, $message, $headers)) {
                $sent++;
            }
        }
        
        return $sent;
    }
    
    /**
    * Парсит шаблон email с подстановкой данных 
    * @param string $template Шаблон
    * @param array $data Данные
    * @return string Обработанный шаблон
    */
    private static function parseEmailTemplate($template, $data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        $template = str_replace('{date}', date('d.m.Y H:i'), $template);
        $template = str_replace('{ip}', $_SERVER['REMOTE_ADDR'] ?? '', $template);
        
        return $template;
    }
    
    /**
    * Выполняет действия после отправки формы
    * @param array $form Данные формы
    * @param array $data Данные отправки
    * @param int $submissionId ID отправки
    * @return int Количество выполненных действий
    */
    public static function executeActions($form, $data, $submissionId) {
        $actions = $form['actions'] ?? [];
        $executed = 0;
        
        foreach ($actions as $action) {
            if (empty($action['enabled'])) continue;
            
            switch ($action['type']) {
                case 'redirect':
                    $_SESSION['form_redirect'] = $action['url'];
                    $executed++;
                    break;
                    
                case 'save_to_db':
                    $executed++;
                    break;
                    
                case 'webhook':
                    $webhookData = [
                        'form_id' => $form['id'],
                        'submission_id' => $submissionId,
                        'data' => $data,
                        'timestamp' => time()
                    ];
                    
                    $ch = curl_init($action['url']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json'
                    ]);
                    curl_exec($ch);
                    curl_close($ch);
                    
                    $executed++;
                    break;
            }
        }
        
        return $executed;
    }
}