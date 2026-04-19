<?php
class ImageWithTextBlock extends BasePostBlock {
    
    public function getName(): string {
        return 'Изображение с текстом';
    }

    public function getSystemName(): string {
        return 'ImageWithTextBlock';
    }

    public function getDescription(): string {
        return 'Блок с изображением, заголовком и текстовым описанием с выбором позиции изображения';
    }

    public function getIcon(): string {
        return 'bi bi-file-earmark-richtext';
    }

    public function getCategory(): string {
        return 'media';
    }

    public function getTemplateWithShortcodes(): string {
        return '
        <div class="image-text-block {layout} {custom_class} align-{vertical_align}" style="gap: {gap};">
            <div class="image-text-container">
                <div class="image-wrapper {image_size}">
                    <img class="img-fluid {image_class}" 
                         src="{image_url}" 
                         alt="{alt_text}"
                         {width_attr}
                         {height_attr}
                         loading="lazy">
                </div>
                <div class="text-wrapper text-{text_align}">
                    {title_html}
                    <div class="text-content">{text_content}</div>
                </div>
            </div>
        </div>';
    }

    public function getDefaultContent(): array {
        return [
            'image_url' => '',
            'alt_text' => '',
            'title' => '',
            'text_content' => 'Текст описания...'
        ];
    }

    public function getDefaultSettings(): array {
        return [
            'layout' => 'image-left',
            'width' => '',
            'height' => '',
            'image_class' => '',
            'custom_class' => '',
            'image_size' => 'medium',
            'text_align' => 'left',
            'title_align' => 'left',
            'title_tag' => 'h2',
            'gap' => '30px',
            'vertical_align' => 'top'
        ];
    }

    public function getContentForm($currentContent = []): string {
        $currentContent = $this->validateAndNormalizeContent($currentContent);
        
        $imageUrl = $currentContent['image_url'] ?? '';
        $altText = $currentContent['alt_text'] ?? '';
        $title = $currentContent['title'] ?? '';
        $textContent = $currentContent['text_content'] ?? '';

        ob_start();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Загрузить изображение *</label>
                    <input type="file" 
                           name="image_file" 
                           class="form-control image-file-input" 
                           accept="image/*"
                           <?= empty($imageUrl) ? 'required' : '' ?>>
                    <div class="form-text small">
                        Форматы: JPG, PNG, GIF, WebP. Макс. размер: 5MB
                    </div>
                </div>

                <input type="hidden" 
                       name="content[image_url]" 
                       class="image-url-input" 
                       value="<?= html($imageUrl) ?>">

                <div class="mb-4">
                    <label class="form-label">Alt текст *</label>
                    <input type="text" 
                           name="content[alt_text]" 
                           class="form-control" 
                           value="<?= html($altText) ?>" 
                           placeholder="Описание изображения для SEO"
                           required>
                </div>

                <div class="new-image-preview mb-4" style="display: none;">
                    <label class="form-label">Предпросмотр нового изображения</label>
                    <div class="border rounded p-3 text-center">
                        <img src="" alt="Предпросмотр" class="img-thumbnail preview-image" style="max-height: 200px;">
                    </div>
                </div>

                <?php if ($imageUrl) { ?>
                    <div class="mb-4">
                        <label class="form-label">Текущее изображение</label>
                        <div class="current-image-preview border rounded p-3 text-center bg-light">
                            <img src="<?= html($imageUrl) ?>" 
                                alt="Текущее изображение" 
                                class="img-thumbnail"
                                style="max-height: 200px;">
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
            </div>

            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Заголовок</label>
                    <input type="text" 
                           name="content[title]" 
                           class="form-control" 
                           value="<?= html($title) ?>" 
                           placeholder="Введите заголовок">
                    <div class="form-text small">
                        Опционально. Отображается над текстовым описанием
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Текстовое описание *</label>
                    <textarea name="content[text_content]" 
                              class="form-control" 
                              rows="10"
                              placeholder="Текст описания рядом с изображением..."
                              required><?= html($textContent) ?></textarea>
                    <div class="form-text small">
                        Поддерживает HTML разметку
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getSettingsForm($currentSettings = []): string {
        $currentSettings = $this->validateAndNormalizeSettings($currentSettings);
        
        $layout = $currentSettings['layout'] ?? 'image-left';
        $width = $currentSettings['width'] ?? '';
        $height = $currentSettings['height'] ?? '';
        $imageClass = $currentSettings['image_class'] ?? '';
        $customClass = $currentSettings['custom_class'] ?? '';
        $imageSize = $currentSettings['image_size'] ?? 'medium';
        $textAlign = $currentSettings['text_align'] ?? 'left';
        $titleAlign = $currentSettings['title_align'] ?? 'left';
        $titleTag = $currentSettings['title_tag'] ?? 'h2';
        $gap = $currentSettings['gap'] ?? '30px';
        $verticalAlign = $currentSettings['vertical_align'] ?? 'top';

        ob_start();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Расположение изображения</label>
                    <select name="settings[layout]" class="form-select">
                        <option value="image-left" <?= $layout === 'image-left' ? 'selected' : '' ?>>Изображение слева, текст справа</option>
                        <option value="image-right" <?= $layout === 'image-right' ? 'selected' : '' ?>>Изображение справа, текст слева</option>
                        <option value="image-top" <?= $layout === 'image-top' ? 'selected' : '' ?>>Изображение сверху, текст снизу</option>
                        <option value="image-bottom" <?= $layout === 'image-bottom' ? 'selected' : '' ?>>Изображение снизу, текст сверху</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Размер изображения</label>
                    <select name="settings[image_size]" class="form-select">
                        <option value="small" <?= $imageSize === 'small' ? 'selected' : '' ?>>Маленький (25%)</option>
                        <option value="medium" <?= $imageSize === 'medium' ? 'selected' : '' ?>>Средний (40%)</option>
                        <option value="large" <?= $imageSize === 'large' ? 'selected' : '' ?>>Большой (50%)</option>
                        <option value="custom" <?= $imageSize === 'custom' ? 'selected' : '' ?>>Произвольный</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Выравнивание заголовка</label>
                    <select name="settings[title_align]" class="form-select">
                        <option value="left" <?= $titleAlign === 'left' ? 'selected' : '' ?>>По левому краю</option>
                        <option value="center" <?= $titleAlign === 'center' ? 'selected' : '' ?>>По центру</option>
                        <option value="right" <?= $titleAlign === 'right' ? 'selected' : '' ?>>По правому краю</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">HTML тег заголовка</label>
                    <select name="settings[title_tag]" class="form-select">
                        <option value="h1" <?= $titleTag === 'h1' ? 'selected' : '' ?>>H1</option>
                        <option value="h2" <?= $titleTag === 'h2' ? 'selected' : '' ?>>H2</option>
                        <option value="h3" <?= $titleTag === 'h3' ? 'selected' : '' ?>>H3</option>
                        <option value="h4" <?= $titleTag === 'h4' ? 'selected' : '' ?>>H4</option>
                        <option value="h5" <?= $titleTag === 'h5' ? 'selected' : '' ?>>H5</option>
                        <option value="h6" <?= $titleTag === 'h6' ? 'selected' : '' ?>>H6</option>
                        <option value="div" <?= $titleTag === 'div' ? 'selected' : '' ?>>DIV</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Выравнивание текста</label>
                    <select name="settings[text_align]" class="form-select">
                        <option value="left" <?= $textAlign === 'left' ? 'selected' : '' ?>>По левому краю</option>
                        <option value="center" <?= $textAlign === 'center' ? 'selected' : '' ?>>По центру</option>
                        <option value="right" <?= $textAlign === 'right' ? 'selected' : '' ?>>По правому краю</option>
                        <option value="justify" <?= $textAlign === 'justify' ? 'selected' : '' ?>>По ширине</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Вертикальное выравнивание</label>
                    <select name="settings[vertical_align]" class="form-select">
                        <option value="top" <?= $verticalAlign === 'top' ? 'selected' : '' ?>>По верху</option>
                        <option value="middle" <?= $verticalAlign === 'middle' ? 'selected' : '' ?>>По середине</option>
                        <option value="bottom" <?= $verticalAlign === 'bottom' ? 'selected' : '' ?>>По низу</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="mb-4">
                    <label class="form-label">Ширина изображения (для произвольного)</label>
                    <input type="text" 
                           name="settings[width]" 
                           class="form-control" 
                           value="<?= html($width) ?>" 
                           placeholder="400px или 50%">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <label class="form-label">Высота изображения (опционально)</label>
                    <input type="text" 
                           name="settings[height]" 
                           class="form-control" 
                           value="<?= html($height) ?>" 
                           placeholder="300px">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-4">
                    <label class="form-label">Расстояние между изображением и текстом</label>
                    <input type="text" 
                           name="settings[gap]" 
                           class="form-control" 
                           value="<?= html($gap) ?>" 
                           placeholder="30px">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">CSS класс изображения</label>
                    <input type="text" 
                           name="settings[image_class]" 
                           class="form-control" 
                           value="<?= html($imageClass) ?>" 
                           placeholder="rounded shadow">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Дополнительный CSS класс блока</label>
                    <input type="text" 
                           name="settings[custom_class]" 
                           class="form-control" 
                           value="<?= html($customClass) ?>" 
                           placeholder="my-image-text-block">
                </div>
            </div>
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
        $title = $content['title'] ?? '';
        $textContent = $content['text_content'] ?? '';
        
        $layout = $settings['layout'] ?? 'image-left';
        $width = $settings['width'] ?? '';
        $height = $settings['height'] ?? '';
        $imageClass = $settings['image_class'] ?? '';
        $customClass = $settings['custom_class'] ?? '';
        $imageSize = $settings['image_size'] ?? 'medium';
        $textAlign = $settings['text_align'] ?? 'left';
        $titleAlign = $settings['title_align'] ?? 'left';
        $titleTag = $settings['title_tag'] ?? 'h2';
        $gap = $settings['gap'] ?? '30px';
        $verticalAlign = $settings['vertical_align'] ?? 'top';
        $presetId = $settings['preset_id'] ?? null;
        $presetName = $settings['preset_name'] ?? '';

        if (empty($imageUrl)) {
            return '<!-- ImageWithTextBlock: пустой URL изображения -->';
        }

        if (empty(trim($textContent))) {
            $textContent = '<!-- Текст не указан -->';
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

        $sizeClass = '';
        switch ($imageSize) {
            case 'small':
                $sizeClass = 'image-size-small';
                break;
            case 'medium':
                $sizeClass = 'image-size-medium';
                break;
            case 'large':
                $sizeClass = 'image-size-large';
                break;
            case 'custom':
                $sizeClass = 'image-size-custom';
                break;
        }
        
        $presetClass = '';
        if ($presetId) {
            $presetClass = 'preset-' . (int)$presetId;
            if (!empty($presetName)) {
                $cleanPresetName = preg_replace('/[^a-z0-9_-]+/i', '-', strtolower($presetName));
                $cleanPresetName = preg_replace('/-+/', '-', $cleanPresetName);
                $cleanPresetName = trim($cleanPresetName, '-');
                if (!empty($cleanPresetName)) {
                    $presetClass .= ' preset-' . $cleanPresetName;
                }
            }
        }

        $titleHtml = '';
        if (!empty($title)) {
            $titleClass = 'title-align-' . $titleAlign;
            $titleHtml = sprintf(
                '<%1$s class="%2$s">%3$s</%1$s>',
                htmlspecialchars($titleTag),
                $titleClass,
                htmlspecialchars($title)
            );
        }
        
        $result = $template;
        
        $replacements = [
            '{layout}' => htmlspecialchars($layout),
            '{custom_class}' => trim($customClass . ' ' . $presetClass),
            '{image_url}' => htmlspecialchars($imageUrl),
            '{alt_text}' => htmlspecialchars($altText),
            '{image_class}' => trim($imageClass . ' ' . $sizeClass),
            '{width_attr}' => $widthAttr,
            '{height_attr}' => $heightAttr,
            '{title_html}' => $titleHtml,
            '{text_content}' => $textContent,
            '{text_align}' => htmlspecialchars($textAlign),
            '{title_align}' => htmlspecialchars($titleAlign),
            '{title_tag}' => htmlspecialchars($titleTag),
            '{vertical_align}' => htmlspecialchars($verticalAlign),
            '{gap}' => htmlspecialchars($gap),
            '{preset_id}' => $presetId ? htmlspecialchars($presetId) : '',
            '{preset_name}' => $presetName ? htmlspecialchars($presetName) : '',
            '{block_type}' => $this->getSystemName(),
            '{block_name}' => $this->getName()
        ];
        
        foreach ($replacements as $placeholder => $value) {
            $result = str_replace($placeholder, $value, $result);
        }
        
        $result = preg_replace('/\s+(width|height)=""/', '', $result);
        
        return $result;
    }

    public function getShortcodes(): array {
        return array_merge(parent::getShortcodes(), [
            '{image_url}' => 'URL изображения',
            '{alt_text}' => 'Alt текст изображения',
            '{title}' => 'Заголовок',
            '{title_html}' => 'HTML заголовка с тегом',
            '{text_content}' => 'Текстовое содержание',
            '{layout}' => 'Расположение (image-left, image-right, image-top, image-bottom)',
            '{image_class}' => 'CSS класс изображения',
            '{custom_class}' => 'Дополнительный CSS класс',
            '{width_attr}' => 'Атрибут ширины',
            '{height_attr}' => 'Атрибут высоты',
            '{text_align}' => 'Выравнивание текста',
            '{title_align}' => 'Выравнивание заголовка',
            '{title_tag}' => 'HTML тег заголовка',
            '{vertical_align}' => 'Вертикальное выравнивание',
            '{gap}' => 'Расстояние между изображением и текстом'
        ]);
    }

    public function getFrontendCss(): array {
        return [
            '/templates/default/front/assets/postblocks/image-with-text/image-with-text.css'
        ];
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
        
        if (isset($_POST['content']['title'])) {
            $content['title'] = $_POST['content']['title'];
        }
        
        if (isset($_POST['content']['text_content'])) {
            $content['text_content'] = $_POST['content']['text_content'];
        }

        if (empty($content['alt_text'])) {
            $content['alt_text'] = 'Изображение';
        }
        
        if (!isset($content['title'])) {
            $content['title'] = '';
        }
        
        if (!isset($content['text_content'])) {
            $content['text_content'] = 'Текст описания...';
        }

        return $content;
    }

    public function prepareSettings($settings): array {
        if (!is_array($settings)) {
            $settings = [];
        }
        
        if (isset($_POST['settings']['layout'])) {
            $settings['layout'] = trim($_POST['settings']['layout']);
        }
        
        if (isset($_POST['settings']['image_size'])) {
            $settings['image_size'] = trim($_POST['settings']['image_size']);
        }
        
        if (isset($_POST['settings']['text_align'])) {
            $settings['text_align'] = trim($_POST['settings']['text_align']);
        }
        
        if (isset($_POST['settings']['title_align'])) {
            $settings['title_align'] = trim($_POST['settings']['title_align']);
        }
        
        if (isset($_POST['settings']['title_tag'])) {
            $settings['title_tag'] = trim($_POST['settings']['title_tag']);
        }
        
        if (isset($_POST['settings']['vertical_align'])) {
            $settings['vertical_align'] = trim($_POST['settings']['vertical_align']);
        }
        
        if (isset($_POST['settings']['width'])) {
            $settings['width'] = trim($_POST['settings']['width']);
        }
        
        if (isset($_POST['settings']['height'])) {
            $settings['height'] = trim($_POST['settings']['height']);
        }
        
        if (isset($_POST['settings']['gap'])) {
            $settings['gap'] = trim($_POST['settings']['gap']);
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
        
        $allowedLayouts = ['image-left', 'image-right', 'image-top', 'image-bottom'];
        if (!isset($settings['layout']) || !in_array($settings['layout'], $allowedLayouts)) {
            $settings['layout'] = $defaults['layout'] ?? 'image-left';
        }
        
        $allowedSizes = ['small', 'medium', 'large', 'custom'];
        if (!isset($settings['image_size']) || !in_array($settings['image_size'], $allowedSizes)) {
            $settings['image_size'] = $defaults['image_size'] ?? 'medium';
        }
        
        $allowedAlign = ['left', 'center', 'right', 'justify'];
        if (!isset($settings['text_align']) || !in_array($settings['text_align'], $allowedAlign)) {
            $settings['text_align'] = $defaults['text_align'] ?? 'left';
        }
        
        $allowedTitleAlign = ['left', 'center', 'right'];
        if (!isset($settings['title_align']) || !in_array($settings['title_align'], $allowedTitleAlign)) {
            $settings['title_align'] = $defaults['title_align'] ?? 'left';
        }
        
        $allowedTitleTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'];
        if (!isset($settings['title_tag']) || !in_array($settings['title_tag'], $allowedTitleTags)) {
            $settings['title_tag'] = $defaults['title_tag'] ?? 'h2';
        }
        
        $allowedVerticalAlign = ['top', 'middle', 'bottom'];
        if (!isset($settings['vertical_align']) || !in_array($settings['vertical_align'], $allowedVerticalAlign)) {
            $settings['vertical_align'] = $defaults['vertical_align'] ?? 'top';
        }
        
        if (!isset($settings['width'])) {
            $settings['width'] = $defaults['width'] ?? '';
        }
        if (!isset($settings['height'])) {
            $settings['height'] = $defaults['height'] ?? '';
        }
        if (!isset($settings['gap'])) {
            $settings['gap'] = $defaults['gap'] ?? '30px';
        }
        if (!isset($settings['image_class'])) {
            $settings['image_class'] = $defaults['image_class'] ?? '';
        }
        if (!isset($settings['custom_class'])) {
            $settings['custom_class'] = $defaults['custom_class'] ?? '';
        }
        
        return $settings;
    }

    public function validateAndNormalizeContent($content): array {
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : ['image_url' => '', 'alt_text' => '', 'title' => '', 'text_content' => ''];
        }
        
        if (!is_array($content)) {
            return ['image_url' => '', 'alt_text' => '', 'title' => '', 'text_content' => ''];
        }
        
        if (!isset($content['image_url'])) {
            $content['image_url'] = '';
        }
        if (!isset($content['alt_text'])) {
            $content['alt_text'] = '';
        }
        if (!isset($content['title'])) {
            $content['title'] = '';
        }
        if (!isset($content['text_content'])) {
            $content['text_content'] = '';
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

            $uploadDir = 'uploads/images/image_with_text/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    return ['success' => false, 'error' => 'Не удалось создать директорию для загрузки'];
                }
            }

            if (!is_writable($uploadDir)) {
                return ['success' => false, 'error' => 'Директория для загрузки недоступна для записи'];
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = 'image_text_' . uniqid() . '_' . time() . '.' . $fileExtension;
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

        $allowedLayouts = ['image-left', 'image-right', 'image-top', 'image-bottom'];
        if (!empty($settings['layout']) && !in_array($settings['layout'], $allowedLayouts)) {
            $errors[] = 'Недопустимое расположение элементов';
        }

        $allowedAlign = ['left', 'center', 'right', 'justify'];
        if (!empty($settings['text_align']) && !in_array($settings['text_align'], $allowedAlign)) {
            $errors[] = 'Недопустимое выравнивание текста';
        }

        $allowedTitleAlign = ['left', 'center', 'right'];
        if (!empty($settings['title_align']) && !in_array($settings['title_align'], $allowedTitleAlign)) {
            $errors[] = 'Недопустимое выравнивание заголовка';
        }

        $allowedTitleTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'];
        if (!empty($settings['title_tag']) && !in_array($settings['title_tag'], $allowedTitleTags)) {
            $errors[] = 'Недопустимый тег заголовка';
        }

        $allowedVerticalAlign = ['top', 'middle', 'bottom'];
        if (!empty($settings['vertical_align']) && !in_array($settings['vertical_align'], $allowedVerticalAlign)) {
            $errors[] = 'Недопустимое вертикальное выравнивание';
        }

        $allowedSizes = ['small', 'medium', 'large', 'custom'];
        if (!empty($settings['image_size']) && !in_array($settings['image_size'], $allowedSizes)) {
            $errors[] = 'Недопустимый размер изображения';
        }

        return [empty($errors), $errors];
    }

    public function extractFromHtml(string $html): ?array {
        if (preg_match('/<img[^>]+src="([^"]+)"[^>]+alt="([^"]*)"[^>]*>/i', $html, $imageMatches)) {
            $content = [
                'image_url' => $imageMatches[1],
                'alt_text' => $imageMatches[2] ?? ''
            ];

            $textHtml = preg_replace('/<img[^>]*>/i', '', $html);
            
            if (preg_match('/<(h[1-6])[^>]*>(.*?)<\/\1>/i', $textHtml, $titleMatches)) {
                $content['title'] = trim(strip_tags($titleMatches[2]));
                $textHtml = preg_replace('/<(h[1-6])[^>]*>.*?<\/\1>/i', '', $textHtml, 1);
            } elseif (preg_match('/<(strong|b)[^>]*>(.*?)<\/(strong|b)>/i', $textHtml, $strongMatches)) {
                $content['title'] = trim(strip_tags($strongMatches[2]));
                $textHtml = preg_replace('/<(strong|b)[^>]*>.*?<\/(strong|b)>/i', '', $textHtml, 1);
            } else {
                $content['title'] = '';
            }
            
            $text = trim(strip_tags($textHtml, '<p><br><strong><em><a><ul><ol><li>'));
            
            if (!empty($text)) {
                $content['text_content'] = $text;
            }
            
            return $content;
        }
        
        return null;
    }

    public function getPreviewHtml($content = [], $settings = []): string {
        $content = $this->validateAndNormalizeContent($content);
        $settings = $this->validateAndNormalizeSettings($settings);
        
        $imageUrl = $content['image_url'] ?? '';
        $altText = $content['alt_text'] ?? '';
        $title = $content['title'] ?? '';
        $textContent = $content['text_content'] ?? '';
        
        $layout = $settings['layout'] ?? 'image-left';
        $imageSize = $settings['image_size'] ?? 'medium';
        $textAlign = $settings['text_align'] ?? 'left';
        $titleAlign = $settings['title_align'] ?? 'left';
        $titleTag = $settings['title_tag'] ?? 'h2';
        $verticalAlign = $settings['vertical_align'] ?? 'top';
        
        $layoutIcon = match($layout) {
            'image-left' => 'bi bi-image-fill',
            'image-right' => 'bi bi-image-fill',
            'image-top' => 'bi bi-image',
            'image-bottom' => 'bi bi-image',
            default => 'bi bi-image'
        };
        
        $layoutText = match($layout) {
            'image-left' => 'Слева',
            'image-right' => 'Справа',
            'image-top' => 'Сверху',
            'image-bottom' => 'Снизу',
            default => 'Слева'
        };
        
        $sizeText = match($imageSize) {
            'small' => 'Маленький',
            'medium' => 'Средний',
            'large' => 'Большой',
            'custom' => 'Произвольный',
            default => 'Средний'
        };
        
        ob_start();
        ?>
        <div class="post-block-preview post-block-preview-ImageWithTextBlock full-content-preview">
            <div class="preview-wrapper">
                <div class="preview-header">
                    <div class="preview-header-content">
                        <div class="preview-icon">
                            <i class="bi bi-file-earmark-richtext"></i>
                        </div>
                        <div class="preview-info">
                            <div class="preview-title">
                                <strong>Изображение с текстом</strong>
                                <span class="badge bg-info badge-sm"><?= html($layoutText) ?></span>
                            </div>
                            <div class="preview-stats">
                                <?php if ($imageUrl) { ?>
                                    Изображение загружено
                                <?php } else { ?>
                                    Без изображения
                                <?php } ?>
                                <?php if (!empty(trim($textContent))) { ?>
                                    · <?= strlen($textContent) ?> симв.
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
                    <?php if ($imageUrl || !empty(trim($title)) || !empty(trim($textContent))) { ?>
                        <div class="image-with-text-preview-container">
                            <div class="image-text-preview-mockup border rounded p-3 bg-light">
                                <div class="row g-3 <?= $layout === 'image-left' || $layout === 'image-right' ? 'flex-nowrap' : '' ?>">
                                    <div class="col-<?= $layout === 'image-top' || $layout === 'image-bottom' ? '12' : '5' ?>">
                                        <div class="image-preview h-100 d-flex align-items-center justify-content-center bg-white border rounded p-2">
                                            <?php if ($imageUrl) { ?>
                                                <img src="<?= html($imageUrl) ?>" 
                                                    alt="<?= html($altText) ?>"
                                                    class="img-fluid"
                                                    style="max-height: 80px; object-fit: contain;">
                                            <?php } ?>
                                            <div class="text-center <?= $imageUrl ? 'd-none' : '' ?>">
                                                <i class="bi bi-image text-muted display-6"></i>
                                                <div class="small text-muted mt-1">Изображение</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-<?= $layout === 'image-top' || $layout === 'image-bottom' ? '12' : '7' ?>">
                                        <div class="text-preview h-100">
                                            <?php if (!empty(trim($title))) { ?>
                                                <div class="title-preview mb-2">
                                                    <div class="h6 mb-1" style="color: #3b82f6;"><?= html(mb_substr($title, 0, 50)) ?></div>
                                                    <div class="small text-muted">Заголовок (<?= html($titleTag) ?>)</div>
                                                </div>
                                            <?php } ?>
                                            
                                            <?php if (!empty(trim($textContent))) { ?>
                                                <div class="content-preview">
                                                    <div class="small" style="color: #374151; line-height: 1.4;">
                                                        <?= html(mb_substr(strip_tags($textContent), 0, 100)) ?>
                                                        <?php if (mb_strlen(strip_tags($textContent)) > 100) { ?>...<?php } ?>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="content-placeholder text-center py-3">
                                                    <i class="bi bi-text-left text-muted"></i>
                                                    <div class="small text-muted mt-1">Текст не добавлен</div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="image-text-preview-info mt-3">
                                <div class="row small text-muted">
                                    <div class="col-6">
                                        <div><i class="bi bi-layout-split <?= $layoutIcon ?> me-1"></i>Макет: <strong><?= html($layoutText) ?></strong></div>
                                        <div><i class="bi bi-arrows-angle-expand me-1"></i>Размер: <strong><?= html($sizeText) ?></strong></div>
                                    </div>
                                    <div class="col-6">
                                        <div><i class="bi bi-text-left me-1"></i>Выравнивание: <strong><?= html($textAlign) ?></strong></div>
                                        <div><i class="bi bi-arrows-vertical me-1"></i>Вертикально: <strong><?= html($verticalAlign) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="preview-empty-state">
                            <i class="bi bi-file-earmark-richtext"></i>
                            <div class="empty-text">Контент не добавлен</div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                    onclick="postBlocksManager.editBlock('{block_id}')">
                                <i class="bi bi-plus-circle"></i> Добавить контент
                            </button>
                            <div class="mt-3 small text-muted">
                                <i class="bi bi-info-circle"></i>
                                Этот блок объединяет изображение с текстовым описанием
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}