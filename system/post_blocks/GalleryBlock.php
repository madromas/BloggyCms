<?php
class GalleryBlock extends BasePostBlock {
    
    public function getName(): string {
        return 'Галерея';
    }

    public function getSystemName(): string {
        return 'GalleryBlock';
    }

    public function getDescription(): string {
        return 'Блок для создания галереи изображений с возможностью добавления нескольких фото';
    }

    public function getIcon(): string {
        return 'bi bi-images';
    }

    public function getCategory(): string {
        return 'media';
    }

    public function getAdminJs(): array {
        return [
            'templates/default/admin/assets/js/blocks/gallery.js'
        ];
    }

    public function getAdminCss(): array {
        return [
            'templates/default/admin/assets/css/blocks/gallery.css'
        ];
    }

    public function getFrontendJs(): array {
        return [
            '/templates/default/front/assets/js/zoomi.min.js',
            '/templates/default/front/assets/postblocks/galleryblock/gallery.js'
        ];
    }

    public function getFrontendCss(): array {
        return [
            '/templates/default/front/assets/postblocks/galleryblock/gallery.css'
        ];
    }

    public function getTemplateWithShortcodes(): string {
        return '
        <div class="gallery-row {custom_class}" id="main-gallery">
            {gallery_items}
                <div class="gallery-item">
                    <img src="{image_url}" alt="{alt_text}" class="gallery-img">
                    <div class="caption">{caption}</div>
                </div>
            {/gallery_items}
        </div>';
    }

    public function getDefaultContent(): array {
        return [
            'images' => []
        ];
    }

    public function getDefaultSettings(): array {
        return [
            'custom_class' => ''
        ];
    }

    public function getContentForm($currentContent = []): string {
        $currentContent = $this->validateAndNormalizeContent($currentContent);
        $images = $currentContent['images'] ?? [];
        
        ob_start();
        ?>
        <div class="gallery-block-wrapper">
            <div class="mb-3">
                <label class="form-label fw-semibold">Изображения галереи</label>
                <div class="text-muted small mb-3">Перетащите элементы для изменения порядка</div>
            </div>
            
            <div id="gallery-items-container" class="gallery-items-container">
                <?php if (empty($images)) { ?>
                    <div class="gallery-item card mb-3" data-index="0">
                        <div class="card-body">
                            <div class="row align-items-start">
                                <div class="col-auto pe-0">
                                    <span class="gallery-item-handle text-muted">
                                        <i class="bi bi-grip-vertical fs-5"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="image-upload-area">
                                                <label class="form-label small fw-semibold">Изображение *</label>
                                                <input type="file" 
                                                    name="gallery_image_0" 
                                                    class="form-control form-control-sm gallery-image-input" 
                                                    accept="image/*"
                                                    required>
                                                <div class="form-text small text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Форматы: JPG, PNG, GIF, WebP. Макс. размер: 5MB
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="content[images][0][image_url]" class="gallery-image-url" value="">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Alt текст *</label>
                                            <input type="text" 
                                                name="content[images][0][alt_text]" 
                                                class="form-control form-control-sm" 
                                                value="" 
                                                placeholder="Краткое описание изображения"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Подпись</label>
                                            <input type="text" 
                                                name="content[images][0][caption]" 
                                                class="form-control form-control-sm" 
                                                value="" 
                                                placeholder="Необязательная подпись к изображению">
                                        </div>
                                        <div class="col-12">
                                            <div class="new-image-preview" style="display: none;">
                                                <div class="preview-card p-2 bg-light rounded">
                                                    <img src="" alt="Предпросмотр" class="preview-image" style="max-height: 80px; max-width: 100%; border-radius: 0.5rem;">
                                                    <div class="small text-muted mt-1">Новое изображение</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-light btn-sm remove-gallery-item" disabled>
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <?php foreach ($images as $index => $image) { ?>
                        <div class="gallery-item card mb-3" data-index="<?= $index ?>">
                            <div class="card-body">
                                <div class="row align-items-start">
                                    <div class="col-auto pe-0">
                                        <span class="gallery-item-handle text-muted">
                                            <i class="bi bi-grip-vertical fs-5"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="row g-3">
                                            <?php if (empty($image['image_url'])) { ?>
                                                <div class="col-md-12">
                                                    <div class="image-upload-area">
                                                        <label class="form-label small fw-semibold">Изображение *</label>
                                                        <input type="file" 
                                                            name="gallery_image_<?= $index ?>" 
                                                            class="form-control form-control-sm gallery-image-input" 
                                                            accept="image/*"
                                                            required>
                                                        <div class="form-text small text-muted">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Форматы: JPG, PNG, GIF, WebP. Макс. размер: 5MB
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="content[images][<?= $index ?>][image_url]" class="gallery-image-url" value="">
                                            <?php } else { ?>
                                                <div class="col-md-12">
                                                    <div class="current-image-preview">
                                                        <div class="d-flex align-items-center gap-3 p-2 bg-light rounded">
                                                            <img src="<?= html($image['image_url']) ?>" 
                                                                alt="Текущее изображение" 
                                                                class="rounded"
                                                                style="width: 60px; height: 60px; object-fit: cover;">
                                                            <div class="flex-grow-1">
                                                                <div class="small text-muted mb-1">Текущее изображение</div>
                                                                <code class="small"><?= html(basename($image['image_url'])) ?></code>
                                                                <div class="form-check mt-2">
                                                                    <input class="form-check-input" type="checkbox" 
                                                                        name="remove_gallery_image_<?= $index ?>" 
                                                                        value="1" 
                                                                        id="removeImage<?= $index ?>">
                                                                    <label class="form-check-label small text-danger" for="removeImage<?= $index ?>">
                                                                        <i class="bi bi-trash3 me-1"></i>Удалить
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="content[images][<?= $index ?>][image_url]" class="gallery-image-url" value="<?= html($image['image_url']) ?>">
                                            <?php } ?>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Alt текст *</label>
                                                <input type="text" 
                                                    name="content[images][<?= $index ?>][alt_text]" 
                                                    class="form-control form-control-sm" 
                                                    value="<?= html($image['alt_text'] ?? '') ?>" 
                                                    placeholder="Краткое описание изображения"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Подпись</label>
                                                <input type="text" 
                                                    name="content[images][<?= $index ?>][caption]" 
                                                    class="form-control form-control-sm" 
                                                    value="<?= html($image['caption'] ?? '') ?>" 
                                                    placeholder="Необязательная подпись к изображению">
                                            </div>
                                            <div class="col-12">
                                                <div class="new-image-preview" style="display: none;">
                                                    <div class="preview-card p-2 bg-light rounded">
                                                        <img src="" alt="Предпросмотр" class="preview-image" style="max-height: 80px; max-width: 100%; border-radius: 0.5rem;">
                                                        <div class="small text-muted mt-1">Новое изображение</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-light btn-sm remove-gallery-item" title="Удалить изображение">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            
            <button type="button" class="btn btn-outline-primary mt-3" id="add-gallery-item">
                <i class="bi bi-plus-lg me-2"></i>Добавить изображение
            </button>
        </div>

        <template id="gallery-template">
            <div class="gallery-item card mb-3" data-index="__INDEX__">
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-auto pe-0">
                            <span class="gallery-item-handle text-muted">
                                <i class="bi bi-grip-vertical fs-5"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="image-upload-area">
                                        <label class="form-label small fw-semibold">Изображение *</label>
                                        <input type="file" 
                                            name="gallery_image___INDEX__" 
                                            class="form-control form-control-sm gallery-image-input" 
                                            accept="image/*"
                                            required>
                                        <div class="form-text small text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Форматы: JPG, PNG, GIF, WebP. Макс. размер: 5MB
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="content[images][__INDEX__][image_url]" class="gallery-image-url" value="">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Alt текст *</label>
                                    <input type="text" 
                                        name="content[images][__INDEX__][alt_text]" 
                                        class="form-control form-control-sm" 
                                        value="" 
                                        placeholder="Краткое описание изображения"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Подпись</label>
                                    <input type="text" 
                                        name="content[images][__INDEX__][caption]" 
                                        class="form-control form-control-sm" 
                                        value="" 
                                        placeholder="Необязательная подпись к изображению">
                                </div>
                                <div class="col-12">
                                    <div class="new-image-preview" style="display: none;">
                                        <div class="preview-card p-2 bg-light rounded">
                                            <img src="" alt="Предпросмотр" class="preview-image" style="max-height: 80px; max-width: 100%; border-radius: 0.5rem;">
                                            <div class="small text-muted mt-1">Новое изображение</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-light btn-sm remove-gallery-item" title="Удалить изображение">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <?php
        return ob_get_clean();
    }

    public function getSettingsForm($currentSettings = []): string {
        $currentSettings = $this->validateAndNormalizeSettings($currentSettings);
        $customClass = $currentSettings['custom_class'] ?? '';

        ob_start();
        ?>
        <div class="mb-4">
            <label class="form-label">Дополнительный CSS класс</label>
            <input type="text" 
                   name="settings[custom_class]" 
                   class="form-control" 
                   value="<?= html($customClass) ?>" 
                   placeholder="my-gallery">
            <div class="form-text">Добавьте свой CSS класс для стилизации галереи</div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getEditorHtml($settings = [], $content = []): string {
        $content = $this->validateAndNormalizeContent($content);
        $images = $content['images'] ?? [];
        $imageCount = count($images);

        if ($imageCount === 0) {
            return '
            <div class="post-block-gallery-preview text-center p-4 border rounded bg-light">
                <i class="bi bi-images display-4 text-muted d-block mb-2"></i>
                <span class="text-muted">Галерея пуста</span>
            </div>';
        }

        $previewHtml = '
        <div class="post-block-gallery-preview">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-images text-primary me-2"></i>
                <strong>Галерея</strong>
                <span class="badge bg-secondary ms-2">' . $imageCount . ' изображений</span>
            </div>
            <div class="gallery-preview-grid">';
        
        $previewImages = array_slice($images, 0, 4);
        foreach ($previewImages as $image) {
            if (!empty($image['image_url'])) {
                $previewHtml .= '
                <div class="gallery-preview-item">
                    <img src="' . html($image['image_url']) . '" 
                         alt="' . html($image['alt_text'] ?? '') . '" 
                         class="img-thumbnail">
                </div>';
            }
        }

        $previewHtml .= '
            </div>
        </div>';

        return $previewHtml;
    }

    public function processFrontend($content, $settings = []): string {
        return parent::processFrontend($content, $settings);
    }

    public function prepareContent($content): array {
        if (!is_array($content)) {
            $content = [];
        }
        
        $images = [];
        
        if (isset($_POST['content']['images']) && is_array($_POST['content']['images'])) {
            foreach ($_POST['content']['images'] as $index => $imageData) {
                $image = [
                    'image_url' => trim($imageData['image_url'] ?? ''),
                    'alt_text' => trim($imageData['alt_text'] ?? ''),
                    'caption' => trim($imageData['caption'] ?? '')
                ];
                
                $fileInputName = 'gallery_image_' . $index;
                if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleImageUpload($_FILES[$fileInputName]);
                    if ($uploadResult['success']) {
                        $image['image_url'] = $uploadResult['file_path'];
                    }
                }
                
                $removeInputName = 'remove_gallery_image_' . $index;
                if (isset($_POST[$removeInputName]) && $_POST[$removeInputName] == '1') {
                    if (!empty($image['image_url']) && file_exists($image['image_url'])) {
                        unlink($image['image_url']);
                    }
                    $image['image_url'] = '';
                }
                
                if (!empty($image['image_url'])) {
                    $images[] = $image;
                }
            }
        }
        
        $content['images'] = $images;
        return $content;
    }

    public function prepareSettings($settings): array {
        if (!is_array($settings)) {
            $settings = [];
        }
        
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            $settings = array_merge($settings, $_POST['settings']);
        }
        
        if (isset($settings['custom_class'])) {
            $settings['custom_class'] = trim($settings['custom_class']);
        }

        return $settings;
    }

    protected function renderWithTemplate($content, $settings, $template): string {
        $content = $this->validateAndNormalizeContent($content);
        $settings = $this->validateAndNormalizeSettings($settings);
        
        $images = $content['images'] ?? [];
        $customClass = $settings['custom_class'] ?? '';

        if (empty($images)) {
            return '<!-- GalleryBlock: нет изображений -->';
        }

        $template = $settings['template'] ?? $this->getTemplateWithShortcodes();
        
        $template = str_replace('{custom_class}', html($customClass), $template);
        
        if (strpos($template, '{gallery_items}') !== false && strpos($template, '{/gallery_items}') !== false) {
            preg_match('/\{gallery_items\}(.*?)\{\/gallery_items\}/s', $template, $matches);
            $itemTemplate = $matches[1] ?? '';
            $itemsHtml = '';
            
            foreach ($images as $image) {
                if (empty($image['image_url'])) {
                    continue;
                }

                $imageUrl = $image['image_url'];
                $altText = $image['alt_text'] ?? '';
                $caption = $image['caption'] ?? '';

                if ($imageUrl[0] !== '/') {
                    $imageUrl = '/' . $imageUrl;
                }

                $itemHtml = $itemTemplate;
                
                $itemHtml = str_replace('{image_url}', html($imageUrl), $itemHtml);
                $itemHtml = str_replace('{alt_text}', html($altText), $itemHtml);
                
                if (!empty($caption)) {
                    $captionHtml = '<div class="caption">' . html($caption) . '</div>';
                    $itemHtml = str_replace('{caption}', $captionHtml, $itemHtml);
                } else {
                    $itemHtml = str_replace('{caption}', '', $itemHtml);
                }
                
                $itemsHtml .= $itemHtml;
            }

            $result = preg_replace('/\{gallery_items\}.*?\{\/gallery_items\}/s', $itemsHtml, $template);
        } else {
            $itemsHtml = '';
            foreach ($images as $image) {
                if (empty($image['image_url'])) {
                    continue;
                }

                $imageUrl = $image['image_url'];
                $altText = $image['alt_text'] ?? '';
                $caption = $image['caption'] ?? '';

                if ($imageUrl[0] !== '/') {
                    $imageUrl = '/' . $imageUrl;
                }

                $captionHtml = '';
                if (!empty($caption)) {
                    $captionHtml = '<div class="caption">' . html($caption) . '</div>';
                }

                $itemsHtml .= '
                <div class="gallery-item">
                    <img src="' . html($imageUrl) . '" 
                        alt="' . html($altText) . '" 
                        class="gallery-img">
                    ' . $captionHtml . '
                </div>';
            }

            $result = str_replace('{gallery_items}', $itemsHtml, $template);
            $result = str_replace('{/gallery_items}', '', $result);
        }
        
        $result = str_replace('{block_type}', $this->getSystemName(), $result);
        $result = str_replace('{block_name}', $this->getName(), $result);

        return $result;
    }

    private function handleImageUpload($file) {
        try {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'error' => 'Ошибка загрузки файла'];
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                return ['success' => false, 'error' => 'Недопустимый тип файла'];
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                return ['success' => false, 'error' => 'Файл слишком большой'];
            }

            $uploadDir = 'uploads/images/gallery/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = 'gallery_' . uniqid() . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                return ['success' => false, 'error' => 'Не удалось сохранить файл'];
            }

            return [
                'success' => true, 
                'file_path' => '/' . $filePath,
                'file_name' => $fileName
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Исключение при загрузке: ' . $e->getMessage()];
        }
    }

    public function getShortcodes(): array {
        return array_merge(parent::getShortcodes(), [
            '{custom_class}' => 'Дополнительный CSS класс',
            '{gallery_items}...{/gallery_items}' => 'Цикл по изображениям галереи',
            '{image_url}' => 'URL изображения',
            '{alt_text}' => 'Alt текст изображения',
            '{caption}' => 'Подпись изображения'
        ]);
    }

    public function validateSettings($settings): array {
        $errors = [];

        if (!empty($settings['custom_class']) && !preg_match('/^[a-zA-Z0-9-_ ]+$/', $settings['custom_class'])) {
            $errors[] = 'CSS класс может содержать только буквы, цифры, дефисы и подчеркивания';
        }

        return [empty($errors), $errors];
    }

    public function extractFromHtml(string $html): ?array {
        if (preg_match_all('/<img[^>]+src="([^"]+)"[^>]+alt="([^"]*)"[^>]*>/i', $html, $matches, PREG_SET_ORDER)) {
            $images = [];
            foreach ($matches as $match) {
                $images[] = [
                    'image_url' => $match[1],
                    'alt_text' => $match[2] ?? '',
                    'caption' => ''
                ];
            }
            
            if (!empty($images)) {
                return ['images' => $images];
            }
        }
        
        return null;
    }

    public function validateAndNormalizeContent($content): array {
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : ['images' => []];
        }
        
        if (!is_array($content)) {
            return ['images' => []];
        }
        
        if (!isset($content['images']) || !is_array($content['images'])) {
            $content['images'] = [];
        }
        
        return $content;
    }

    public function validateAndNormalizeSettings($settings): array {
        if (is_string($settings)) {
            $decoded = json_decode($settings, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        if (!is_array($settings)) {
            return [];
        }
        
        return $settings;
    }

    public function getPreviewHtml($content = [], $settings = []): string {
        $content = $this->validateAndNormalizeContent($content);
        $settings = $this->validateAndNormalizeSettings($settings);
        
        $images = $content['images'] ?? [];
        $customClass = $settings['custom_class'] ?? '';
        $validImages = array_filter($images, function($image) {
            return !empty($image['image_url']);
        });
        
        $imageCount = count($validImages);
        
        ob_start();
        ?>
        <div class="post-block-preview post-block-preview-GalleryBlock full-content-preview">
            <div class="preview-wrapper">
                <div class="preview-header">
                    <div class="preview-header-content">
                        <div class="preview-icon">
                            <i class="bi bi-images"></i>
                        </div>
                        <div class="preview-info">
                            <div class="preview-title">
                                <strong>Галерея</strong>
                                <?php if (!empty($customClass)) { ?>
                                    <span class="badge bg-secondary badge-sm"><?= html($customClass) ?></span>
                                <?php } ?>
                            </div>
                            <div class="preview-stats">
                                <?= $imageCount ?> изображени<?= $imageCount == 1 ? 'е' : ($imageCount > 1 && $imageCount < 5 ? 'я' : 'й') ?>
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
                    <?php if ($imageCount > 0) { ?>
                        <div class="gallery-preview-container">
                            <div class="gallery-preview-grid" style="display: grid; grid-template-columns: repeat(<?= min($imageCount, 4) ?>, 1fr); gap: 8px;">
                                <?php 
                                $previewImages = array_slice($validImages, 0, 4);
                                foreach ($previewImages as $index => $image) { 
                                    $imageUrl = $image['image_url'];
                                    $altText = $image['alt_text'] ?? '';
                                ?>
                                    <div class="gallery-preview-item position-relative">
                                        <img src="<?= html($imageUrl) ?>" 
                                            alt="<?= html($altText) ?>"
                                            class="img-fluid rounded"
                                            style="width: 100%; height: 80px; object-fit: cover;">
                                        
                                        <?php if ($index === 3 && $imageCount > 4) { ?>
                                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center rounded">
                                                <span class="text-white fw-bold">+<?= $imageCount - 4 ?></span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="preview-empty-state">
                            <i class="bi bi-images"></i>
                            <div class="empty-text">Изображения не добавлены</div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                    onclick="postBlocksManager.editBlock('{block_id}')">
                                <i class="bi bi-plus-circle"></i> Добавить изображения
                            </button>
                            <div class="mt-3 small text-muted">
                                <i class="bi bi-info-circle"></i>
                                Добавьте несколько изображений для создания галереи
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