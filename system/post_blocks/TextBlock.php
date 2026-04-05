<?php
class TextBlock extends BasePostBlock {
    
    public function getName(): string {
        return 'Текст';
    }

    public function getSystemName(): string {
        return 'TextBlock';
    }

    public function getDescription(): string {
        return 'Текстовый блок с поддержкой форматирования (жирный, курсив, ссылки, код и т.д.)';
    }

    public function getIcon(): string {
        return 'bi bi-text-paragraph';
    }

    public function getCategory(): string {
        return 'text';
    }

    public function getTemplateWithShortcodes(): string {
        return '<div class="post-block-text {custom_class}">{content}</div>';
    }

    public function getDefaultContent(): array {
        return [
            'content' => '<p>Ваш текст здесь...</p>'
        ];
    }

    public function getDefaultSettings(): array {
        return [
            'custom_class' => '',
            'text_align' => 'left',
            'font_size' => '',
            'line_height' => ''
        ];
    }

    public function getAdminJs(): array {
        return [
            'templates/default/admin/assets/js/rich-text-editor.js',
        ];
    }

    public function getPreviewHtml($content = [], $settings = []): string {
        $content = $this->validateAndNormalizeContent($content);
        $html = $content['content'] ?? $content['text'] ?? '';
        
        if (empty(trim(strip_tags($html)))) {
            foreach ($content as $value) {
                if (is_string($value) && trim($value) !== '') {
                    $html = $value;
                    break;
                }
            }
        }
        
        $alignment = $settings['text_align'] ?? 'left';
        $charCount = strlen(strip_tags($html));
        
        ob_start();
        ?>
        <div class="post-block-preview post-block-preview-TextBlock full-content-preview">
            <div class="preview-wrapper">
                <div class="preview-header">
                    <div class="preview-header-content">
                        <div class="preview-icon">
                            <i class="bi bi-text-left"></i>
                        </div>
                        <div class="preview-info">
                            <div class="preview-title">
                                <strong>Текст (Rich)</strong>
                                <?php if ($alignment !== 'left'): ?>
                                    <span class="badge bg-secondary badge-sm"><?= html($alignment) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="preview-stats">
                                <?= $charCount ?> симв.
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
                
                <div class="preview-body full-text-content">
                    <?php if (!empty(trim(strip_tags($html)))): ?>
                        <div class="text-content" style="text-align: <?= html($alignment) ?>;">
                            <?= $html ?>
                        </div>
                    <?php else: ?>
                        <div class="preview-empty-state">
                            <i class="bi bi-fonts"></i>
                            <div class="empty-text">Текст не добавлен</div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                    onclick="postBlocksManager.editBlock('{block_id}')">
                                <i class="bi bi-plus-circle"></i> Добавить текст
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    protected function getPreviewStats($content, $settings): string {
        $text = strip_tags($content['content'] ?? '');
        $charCount = strlen($text);
        
        $stats = [];
        $stats[] = $charCount . ' симв.';
        
        if (!empty($settings['text_align']) && $settings['text_align'] !== 'left') {
            $stats[] = 'выравнивание: ' . $settings['text_align'];
        }
        
        return implode(' · ', $stats);
    }

    public function getContentForm($currentContent = []): string {
        $currentContent = $this->validateAndNormalizeContent($currentContent);
        $contentHtml = $currentContent['content'] ?? '';
        $editorId = 'rich-editor-' . uniqid();

        ob_start();
        ?>
        <div class="mb-4 rich-text-wrapper" id="<?= $editorId ?>">
            <label class="form-label">Текст</label>
            
            <div class="rich-text-toolbar mb-2 border rounded-top p-2 bg-light">
                <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-command="bold" title="Жирный (Ctrl+B)">
                    <i class="bi bi-type-bold"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-command="italic" title="Курсив (Ctrl+I)">
                    <i class="bi bi-type-italic"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-command="underline" title="Подчеркнутый (Ctrl+U)">
                    <i class="bi bi-type-underline"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-command="strikeThrough" title="Зачеркнутый">
                    <i class="bi bi-type-strikethrough"></i>
                </button>
                <div class="vr mx-2"></div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-command="createLink" title="Вставить ссылку">
                    <i class="bi bi-link-45deg"></i> Ссылка
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-command="unlink" title="Удалить ссылку">
                    <i class="bi bi-link-45deg"></i>
                </button>
                <div class="vr mx-2"></div>
                <button type="button" class="btn btn-sm btn-outline-dark" data-command="formatCode" title="Выделить как код">
                    <i class="bi bi-code-slash"></i> Код
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" data-command="removeFormat" title="Очистить формат">
                    <i class="bi bi-eraser"></i>
                </button>
            </div>

            <div class="rich-text-editor form-control border-top-0 rounded-top-0" 
                 contenteditable="true" 
                 style="min-height: 150px; font-family: inherit; outline: none;"
                 data-target="content[content]">
                <?= $contentHtml ?>
            </div>

            <textarea name="content[content]" 
                      class="d-none" 
                      id="hidden-content-<?= $editorId ?>"
                      required><?= html($contentHtml) ?></textarea>
            
            <div class="form-text">Поддерживается HTML. Выделите текст и используйте кнопки для форматирования.</div>
        </div>

        <style>
            .rich-text-toolbar button.active {
                background-color: #0d6efd !important;
                color: white !important;
                border-color: #0d6efd !important;
            }
            .rich-text-toolbar button.active i {
                color: white !important;
            }
            .rich-text-editor code {
                background-color: #f4f4f4;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: 'Courier New', Courier, monospace;
                color: #d63384;
            }
        </style>
        <?php
        return ob_get_clean();
    }

    public function getSettingsForm($currentSettings = []): string {
        $currentSettings = $this->validateAndNormalizeSettings($currentSettings);
        
        $customClass = $currentSettings['custom_class'] ?? '';
        $textAlign = $currentSettings['text_align'] ?? 'left';
        $fontSize = $currentSettings['font_size'] ?? '';
        $lineHeight = $currentSettings['line_height'] ?? '';

        ob_start();
        ?>
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
                    <label class="form-label">Дополнительный CSS класс</label>
                    <input type="text" 
                           name="settings[custom_class]" 
                           class="form-control" 
                           value="<?= html($customClass) ?>" 
                           placeholder="my-text-block">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Размер шрифта (опционально)</label>
                    <input type="text" 
                           name="settings[font_size]" 
                           class="form-control" 
                           value="<?= html($fontSize) ?>" 
                           placeholder="16px или 1rem">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Межстрочный интервал (опционально)</label>
                    <input type="text" 
                           name="settings[line_height]" 
                           class="form-control" 
                           value="<?= html($lineHeight) ?>" 
                           placeholder="1.5 или 24px">
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
        
        $text = $content['content'] ?? '';
        $customClass = $settings['custom_class'] ?? '';
        $textAlign = $settings['text_align'] ?? 'left';
        $fontSize = $settings['font_size'] ?? '';
        $lineHeight = $settings['line_height'] ?? '';
        $presetId = $settings['preset_id'] ?? null;
        $presetName = $settings['preset_name'] ?? '';

        if (empty(trim(strip_tags($text)))) {
            return '<!-- TextBlock: пустой текст -->';
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

        $style = '';
        if ($fontSize) {
            $style .= 'font-size: ' . html($fontSize) . '; ';
        }
        if ($lineHeight) {
            $style .= 'line-height: ' . html($lineHeight) . '; ';
        }
        if ($textAlign && $textAlign !== 'left') {
            $style .= 'text-align: ' . html($textAlign) . '; ';
        }

        $result = $template;
        
        $result = str_replace('{custom_class}', trim($customClass . ' ' . $presetClass), $result);
        $safeText = strip_tags($text, '<b><i><u><s><a><br><p><div><span><strong><em><code>');
        
        $result = str_replace('{content}', $safeText, $result);
        
        if (!empty($style)) {
            $result = preg_replace(
                '/class="([^"]*)"/',
                'class="$1" style="' . $style . '"',
                $result
            );
        }
        
        $result = str_replace('{preset_id}', $presetId ? html($presetId) : '', $result);
        $result = str_replace('{preset_name}', $presetName ? html($presetName) : '', $result);
        $result = str_replace('{block_type}', $this->getSystemName(), $result);
        $result = str_replace('{block_name}', $this->getName(), $result);
        $result = str_replace('{text_align}', $textAlign, $result);
        $result = str_replace('{font_size}', html($fontSize), $result);
        $result = str_replace('{line_height}', html($lineHeight), $result);

        return $result;
    }

    public function getShortcodes(): array {
        return array_merge(parent::getShortcodes(), [
            '{content}' => 'Текст содержимого (HTML)',
            '{custom_class}' => 'Дополнительный CSS класс',
            '{text_align}' => 'Выравнивание текста',
            '{font_size}' => 'Размер шрифта',
            '{line_height}' => 'Межстрочный интервал'
        ]);
    }

    public function prepareContent($content): array {
        if (!is_array($content)) {
            $content = [];
        }
        
        if (isset($_POST['content']) && is_array($_POST['content'])) {
            if (isset($_POST['content']['content'])) {
                $content['content'] = trim($_POST['content']['content']);
            }
        }
        
        if (!isset($content['content'])) {
            $content['content'] = '<p>Ваш текст здесь...</p>';
        }

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
        
        if (isset($settings['font_size'])) {
            $settings['font_size'] = trim($settings['font_size']);
        }
        
        if (isset($settings['line_height'])) {
            $settings['line_height'] = trim($settings['line_height']);
        }

        return $settings;
    }

    public function extractFromHtml(string $html): ?array {
        if (!empty(trim($html))) {
            return [
                'content' => $html
            ];
        }
        return null;
    }

    public function validateSettings($settings): array {
        $errors = [];

        if (!empty($settings['custom_class']) && !preg_match('/^[a-zA-Z0-9-_ ]+$/', $settings['custom_class'])) {
            $errors[] = 'CSS класс может содержать только буквы, цифры, дефисы и подчеркивания';
        }

        $allowedAlign = ['left', 'center', 'right', 'justify'];
        if (!empty($settings['text_align']) && !in_array($settings['text_align'], $allowedAlign)) {
            $errors[] = 'Недопустимое значение выравнивания текста';
        }

        return [empty($errors), $errors];
    }

    public function validateAndNormalizeContent($content): array {
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : ['content' => $content];
        }
        
        if (!is_array($content)) {
            return ['content' => (string)$content];
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
        
        $defaults = $this->getDefaultSettings();
        foreach ($defaults as $key => $value) {
            if (!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }
        
        return $settings;
    }
}