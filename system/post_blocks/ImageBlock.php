<?php
class ImageBlock extends BasePostBlock {
    
    public function getName(): string {
        return 'Изображение';
    }

    public function getSystemName(): string {
        return 'ImageBlock';
    }

    public function getDescription(): string {
        return 'Блок для вставки изображений с загрузкой файлов';
    }

    public function getIcon(): string {
        return 'bi bi-image';
    }

    public function getCategory(): string {
        return 'media';
    }

    public function getPreviewHtml($content = [], $settings = []): string {
        $content = $this->validateAndNormalizeContent($content);
        $settings = $this->validateAndNormalizeSettings($settings);
        
        $url = $content['image_url'] ?? '';
        $alt = $content['alt_text'] ?? '';
        $caption = $content['caption'] ?? '';
        $alignment = $settings['alignment'] ?? 'center';
        
        ob_start();
        ?>
        <div class="post-block-preview post-block-preview-ImageBlock full-content-preview">
            <div class="preview-wrapper">
                <div class="preview-header">
                    <div class="preview-header-content">
                        <div class="preview-icon">
                            <i class="bi bi-image"></i>
                        </div>
                        <div class="preview-info">
                            <div class="preview-title">
                                <strong>Изображение</strong>
                            </div>
                            <div class="preview-stats">
                                <?php if (!empty($url)) { ?>
                                    Изображение загружено
                                    <?php if (!empty($alt)) { ?>
                                        · есть описание
                                    <?php } ?>
                                <?php } else { ?>
                                    Не загружено
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="preview-actions">
                        <button type="button" class="btn btn-xs btn-outline-secondary preview-edit-btn" 
                                onclick="postBlocksManager.editBlock('{block_id}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
                
                <div class="preview-body">
                    <?php if (!empty($url)) { ?>
                        <div class="image-content-container text-<?= html($alignment) ?>">
                            <div class="image-wrapper position-relative d-inline-block">
                                <img src="<?= html($url) ?>" 
                                     alt="<?= html($alt) ?>"
                                     class="img-fluid rounded shadow-sm"
                                     style="max-width: 100%; max-height: 300px;">
                            </div>
                            
                            <?php if (!empty($alt)) { ?>
                                <div class="image-alt mt-2">
                                    <div class="small fw-semibold">Описание:</div>
                                    <div class="small text-muted"><?= html($alt) ?></div>
                                </div>
                            <?php } ?>
                            
                            <?php if (!empty($caption)) { ?>
                                <div class="image-caption mt-2">
                                    <div class="small fw-semibold">Подпись:</div>
                                    <div class="small text-muted"><?= html($caption) ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="preview-empty-state">
                            <i class="bi bi-image"></i>
                            <div class="empty-text">Изображение не загружено</div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                    onclick="postBlocksManager.editBlock('{block_id}')">
                                <i class="bi bi-plus-circle"></i> Добавить изображение
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getTemplateWithShortcodes(): string {
        return '
        <div class="post-block-image {alignment} {custom_class}">
            <figure class="image-figure">
                <img src="{image_url}" 
                     alt="{alt_text}" 
                     class="img-fluid {image_class}"
                     {width_attr}
                     {height_attr}
                     {loading_attr}>
                {caption_html}
            </figure>
        </div>';
    }

    public function getDefaultContent(): array {
        return [
            'image_url' => '',
            'alt_text' => '',
            'caption' => ''
        ];
    }

    public function getDefaultSettings(): array {
        return [
            'alignment' => 'center',
            'width' => '',
            'height' => '',
            'image_class' => '',
            'custom_class' => '',
            'lazy_loading' => true
        ];
    }

    public function getContentForm($currentContent = []): string {
        $currentContent = $this->validateAndNormalizeContent($currentContent);
        $imageUrl = $currentContent['image_url'] ?? '';
        $altText = $currentContent['alt_text'] ?? '';
        $caption = $currentContent['caption'] ?? '';

        ob_start();
        ?>
        <div class="mb-4">
            <label class="form-label">Загрузить изображение *</label>
            <input type="file" 
                   name="image_file" 
                   class="form-control image-file-input" 
                   accept="image/*"
                   <?= empty($imageUrl) ? 'required' : '' ?>>
            <div class="form-text">
                Поддерживаемые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5MB
            </div>
        </div>

        <input type="hidden" 
               name="content[image_url]" 
               class="image-url-input" 
               value="<?= html($imageUrl) ?>">
        <?php if ($imageUrl) { ?>
            <div class="mb-4">
                <label class="form-label">Текущее изображение</label>
                <div class="current-image-preview border rounded p-3 text-center bg-light">
                    <img src="<?= html($imageUrl) ?>" 
                        alt="Текущее изображение" 
                        class="img-thumbnail"
                        style="max-height: 200px; max-width: 100%;">
                    <div class="mt-2">
                        <small class="text-muted"><?= html($imageUrl) ?></small>
                    </div>
                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage">
                            <label class="form-check-label" for="removeImage">
                                Удалить текущее изображение
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="mb-4">
            <label class="form-label">Alt текст *</label>
            <input type="text" 
                   name="content[alt_text]" 
                   class="form-control" 
                   value="<?= html($altText) ?>" 
                   placeholder="Описание изображения для SEO"
                   required>
            <div class="form-text">
                Важно для доступности и поисковых систем
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Подпись</label>
            <textarea name="content[caption]" 
                      class="form-control" 
                      rows="2"
                      placeholder="Необязательная подпись под изображением"><?= html($caption) ?></textarea>
        </div>
        <div class="new-image-preview mb-4" style="display: none;">
            <label class="form-label">Предпросмотр нового изображения</label>
            <div class="border rounded p-3 text-center">
                <img src="" alt="Предпросмотр" class="img-thumbnail preview-image" style="max-height: 200px; max-width: 100%;">
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getSettingsForm($currentSettings = []): string {
        $currentSettings = $this->validateAndNormalizeSettings($currentSettings);
        $alignment = $currentSettings['alignment'] ?? 'center';
        $width = $currentSettings['width'] ?? '';
        $height = $currentSettings['height'] ?? '';
        $imageClass = $currentSettings['image_class'] ?? '';
        $customClass = $currentSettings['custom_class'] ?? '';
        $lazyLoading = $currentSettings['lazy_loading'] ?? true;

        ob_start();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Выравнивание</label>
                    <select name="settings[alignment]" class="form-select">
                        <option value="left" <?= $alignment === 'left' ? 'selected' : '' ?>>По левому краю</option>
                        <option value="center" <?= $alignment === 'center' ? 'selected' : '' ?>>По центру</option>
                        <option value="right" <?= $alignment === 'right' ? 'selected' : '' ?>>По правому краю</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Дополнительный CSS класс</label>
                    <input type="text" 
                           name="settings[custom_class]" 
                           class="form-control" 
                           value="<?= html($customClass) ?>" 
                           placeholder="my-image-block">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="mb-4">
                    <label class="form-label">Ширина</label>
                    <input type="text" 
                           name="settings[width]" 
                           class="form-control" 
                           value="<?= html($width) ?>" 
                           placeholder="800px или 50%">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <label class="form-label">Высота</label>
                    <input type="text" 
                           name="settings[height]" 
                           class="form-control" 
                           value="<?= html($height) ?>" 
                           placeholder="600px">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <label class="form-label">CSS класс изображения</label>
                    <input type="text" 
                           name="settings[image_class]" 
                           class="form-control" 
                           value="<?= html($imageClass) ?>" 
                           placeholder="rounded shadow">
                </div>
            </div>
        </div>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" 
                   type="checkbox" 
                   name="settings[lazy_loading]" 
                   id="lazy_loading"
                   value="1" 
                   <?= $lazyLoading ? 'checked' : '' ?>>
            <label class="form-check-label" for="lazy_loading">
                Отложенная загрузка
            </label>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getEditorHtml($settings = [], $content = []): string {
        return parent::getEditorHtml($settings, $content);
    }

    public function processFrontend($content, $settings = []): string {
        return parent::processFrontend($content, $settings);
    }

    protected function renderWithTemplate($content, $settings, $template): string {
        $content = $this->validateAndNormalizeContent($content);
        $settings = $this->validateAndNormalizeSettings($settings);
        
        $imageUrl = $content['image_url'] ?? '';
        $altText = $content['alt_text'] ?? '';
        $caption = $content['caption'] ?? '';
        $alignment = $settings['alignment'] ?? 'center';
        $width = $settings['width'] ?? '';
        $height = $settings['height'] ?? '';
        $imageClass = $settings['image_class'] ?? '';
        $customClass = $settings['custom_class'] ?? '';
        $lazyLoading = $settings['lazy_loading'] ?? true;
        $presetId = $settings['preset_id'] ?? null;
        $presetName = $settings['preset_name'] ?? '';

        if (empty($imageUrl)) {
            return '<!-- ImageBlock: нет изображения -->';
        }

        if ($imageUrl[0] !== '/') {
            $imageUrl = '/' . $imageUrl;
        }

        $widthAttr = '';
        $heightAttr = '';
        if (!empty($width)) {
            $widthAttr = 'width="' . htmlspecialchars($width) . '"';
        }
        if (!empty($height)) {
            $heightAttr = 'height="' . htmlspecialchars($height) . '"';
        }
        
        $loadingAttr = $lazyLoading ? 'loading="lazy"' : '';
        
        $captionHtml = '';
        if (!empty($caption)) {
            $captionHtml = '<figcaption class="image-caption">' . nl2br(htmlspecialchars($caption)) . '</figcaption>';
        }
        
        $presetClass = '';
        if ($presetId) {
            $presetClass = 'preset-' . (int)$presetId;
            if ($presetName) {
                $presetClass .= ' preset-' . preg_replace('/[^a-z0-9_-]/i', '-', strtolower($presetName));
            }
        }
        
        $finalCustomClass = trim($customClass . ' ' . $presetClass);
        
        $result = $template;
        
        $replacements = [
            '{image_url}' => htmlspecialchars($imageUrl),
            '{alt_text}' => htmlspecialchars($altText),
            '{caption}' => htmlspecialchars($caption),
            '{caption_html}' => $captionHtml,
            '{alignment}' => $alignment,
            '{image_class}' => $imageClass,
            '{custom_class}' => $finalCustomClass,
            '{width_attr}' => $widthAttr,
            '{height_attr}' => $heightAttr,
            '{loading_attr}' => $loadingAttr,
            '{preset_id}' => $presetId ? htmlspecialchars($presetId) : '',
            '{preset_name}' => $presetName ? htmlspecialchars($presetName) : '',
            '{block_type}' => $this->getSystemName(),
            '{block_name}' => $this->getName()
        ];
        
        foreach ($replacements as $placeholder => $value) {
            $result = str_replace($placeholder, $value, $result);
        }
        
        $result = preg_replace('/\s+(width|height|loading)=""/', '', $result);
        
        return $result;
    }

    public function getShortcodes(): array {
        return array_merge(parent::getShortcodes(), [
            '{image_url}' => 'URL изображения',
            '{alt_text}' => 'Alt текст',
            '{caption}' => 'Подпись изображения',
            '{caption_html}' => 'HTML подписи с тегом figcaption',
            '{alignment}' => 'Выравнивание',
            '{image_class}' => 'CSS класс изображения',
            '{custom_class}' => 'Дополнительный CSS класс',
            '{width_attr}' => 'Атрибут ширины',
            '{height_attr}' => 'Атрибут высоты',
            '{loading_attr}' => 'Атрибут loading (lazy/eager)'
        ]);
    }

    public function prepareSettings($settings): array {
        if (!is_array($settings)) {
            $settings = [];
        }
        
        $settings['lazy_loading'] = isset($_POST['settings']['lazy_loading']) && ($_POST['settings']['lazy_loading'] == '1' || $_POST['settings']['lazy_loading'] == 'on');
        
        if (isset($_POST['settings']['alignment'])) {
            $settings['alignment'] = trim($_POST['settings']['alignment']);
        }
        
        if (isset($_POST['settings']['width'])) {
            $settings['width'] = trim($_POST['settings']['width']);
        }
        
        if (isset($_POST['settings']['height'])) {
            $settings['height'] = trim($_POST['settings']['height']);
        }
        
        if (isset($_POST['settings']['image_class'])) {
            $settings['image_class'] = trim($_POST['settings']['image_class']);
        }
        
        if (isset($_POST['settings']['custom_class'])) {
            $settings['custom_class'] = trim($_POST['settings']['custom_class']);
        }
        
        return $settings;
    }

    public function validateAndNormalizeSettings($settings): array {
        if (is_string($settings)) {
            $decoded = json_decode($settings, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        if (!is_array($settings)) {
            return [];
        }
        
        $defaults = $this->getDefaultSettings();
        
        $settings['lazy_loading'] = isset($settings['lazy_loading']) 
            ? filter_var($settings['lazy_loading'], FILTER_VALIDATE_BOOLEAN) 
            : ($defaults['lazy_loading'] ?? true);
        
        if (!isset($settings['alignment']) || !in_array($settings['alignment'], ['left', 'center', 'right'])) {
            $settings['alignment'] = $defaults['alignment'] ?? 'center';
        }
        
        if (!isset($settings['width'])) {
            $settings['width'] = $defaults['width'] ?? '';
        }
        
        if (!isset($settings['height'])) {
            $settings['height'] = $defaults['height'] ?? '';
        }
        
        if (!isset($settings['image_class'])) {
            $settings['image_class'] = $defaults['image_class'] ?? '';
        }
        
        if (!isset($settings['custom_class'])) {
            $settings['custom_class'] = $defaults['custom_class'] ?? '';
        }
        
        return $settings;
    }

    public function prepareContent($content): array {
        if (!is_array($content)) {
            $content = [];
        }
        
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadResult = $this->handleImageUpload($_FILES['image_file']);
                if ($uploadResult['success']) {
                    $content['image_url'] = $uploadResult['file_path'];
                } else {
                    throw new Exception($uploadResult['error'] ?? 'Ошибка загрузки изображения');
                }
            } catch (Exception $e) {
                throw $e;
            }
        } elseif (isset($_POST['content']['image_url'])) {
            $existingUrl = $_POST['content']['image_url'];
            if (!empty($existingUrl) && $existingUrl[0] !== '/') {
                $content['image_url'] = '/' . $existingUrl;
            } else {
                $content['image_url'] = $existingUrl;
            }
        }
        
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            if (!empty($content['image_url'])) {
                $filePath = ltrim($content['image_url'], '/');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $content['image_url'] = '';
        }

        if (isset($_POST['content']['alt_text'])) {
            $content['alt_text'] = $_POST['content']['alt_text'];
        }
        
        if (isset($_POST['content']['caption'])) {
            $content['caption'] = $_POST['content']['caption'];
        }

        if (empty($content['alt_text'])) {
            $content['alt_text'] = 'Изображение';
        }

        return $content;
    }

    public function validateAndNormalizeContent($content): array {
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : ['image_url' => '', 'alt_text' => '', 'caption' => ''];
        }
        
        if (!is_array($content)) {
            return ['image_url' => '', 'alt_text' => '', 'caption' => ''];
        }
        
        if (!isset($content['image_url'])) {
            $content['image_url'] = '';
        }
        if (!isset($content['alt_text'])) {
            $content['alt_text'] = '';
        }
        if (!isset($content['caption'])) {
            $content['caption'] = '';
        }
        
        return $content;
    }

    public function handleImageUpload($file) {
        try {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'error' => 'Ошибка загрузки файла: ' . $this->getUploadError($file['error'])];
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                return ['success' => false, 'error' => 'Недопустимый тип файла. Разрешены: JPG, PNG, GIF, WebP'];
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                return ['success' => false, 'error' => 'Файл слишком большой. Максимальный размер: 5MB'];
            }

            $uploadDir = 'uploads/images/post_blocks/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    return ['success' => false, 'error' => 'Не удалось создать директорию для загрузки'];
                }
            }

            if (!is_writable($uploadDir)) {
                return ['success' => false, 'error' => 'Директория для загрузки недоступна для записи'];
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = 'post_block_' . uniqid() . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                return ['success' => false, 'error' => 'Не удалось сохранить файл на сервер'];
            }

            if (!file_exists($filePath)) {
                return ['success' => false, 'error' => 'Файл не был создан после загрузки'];
            }

            return [
                'success' => true, 
                'file_path' => '/' . $filePath,
                'file_name' => $fileName,
                'file_size' => $file['size'],
                'file_type' => $fileType
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Исключение при загрузке: ' . $e->getMessage()];
        }
    }

    private function getUploadError($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Файл превышает максимальный размер',
            UPLOAD_ERR_FORM_SIZE => 'Файл превышает максимальный размер формы',
            UPLOAD_ERR_PARTIAL => 'Файл был загружен только частично',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная директория',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
            UPLOAD_ERR_EXTENSION => 'Расширение PHP остановило загрузку файла'
        ];
        
        return $errors[$errorCode] ?? 'Неизвестная ошибка';
    }

    public function validateSettings($settings): array {
        $errors = [];

        if (!empty($settings['custom_class']) && !preg_match('/^[a-zA-Z0-9-_ ]+$/', $settings['custom_class'])) {
            $errors[] = 'CSS класс может содержать только буквы, цифры, дефисы и подчеркивания';
        }

        if (!empty($settings['image_class']) && !preg_match('/^[a-zA-Z0-9-_ ]+$/', $settings['image_class'])) {
            $errors[] = 'CSS класс изображения может содержать только буквы, цифры, дефисы и подчеркивания';
        }

        return [empty($errors), $errors];
    }
}