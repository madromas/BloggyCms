<?php
class SpoilerBlock extends BasePostBlock {
    
    public function getName(): string {
        return 'Спойлер';
    }

    public function getSystemName(): string {
        return 'SpoilerBlock';
    }

    public function getDescription(): string {
        return 'Блок для скрытия контента под спойлером с настраиваемым заголовком';
    }

    public function getIcon(): string {
        return 'bi bi-eye';
    }

    public function getCategory(): string {
        return 'interactive';
    }

    public function getTemplateWithShortcodes(): string {
        return '
        <div class="post-block-spoiler {custom_class} {show_default} {no_animation_class}" data-spoiler>
            <div class="spoiler-header">
                <button type="button" class="spoiler-toggle {icon_position}" aria-expanded="{aria_expanded}">
                    {icon_before}
                    <span class="spoiler-title">{title}</span>
                    {icon_after}
                </button>
            </div>
            <div class="spoiler-content" aria-hidden="{aria_hidden}">
                <div class="spoiler-body">
                    {content}
                </div>
            </div>
        </div>';
    }

    public function getDefaultContent(): array {
        return [
            'title' => 'Нажмите чтобы раскрыть',
            'content' => 'Скрытый контент...'
        ];
    }

    public function getDefaultSettings(): array {
        return [
            'show_default' => '',
            'icon_before' => 'chevron-down',
            'icon_after' => '',
            'icon_position' => 'icon-after',
            'custom_class' => '',
            'animation' => true
        ];
    }

    public function getContentForm($currentContent = []): string {
        $currentContent = $this->validateAndNormalizeContent($currentContent);
        $title = $currentContent['title'] ?? '';
        $content = $currentContent['content'] ?? '';

        ob_start();
        ?>
        <div class="mb-4">
            <label class="form-label">Заголовок спойлера *</label>
            <input type="text" 
                   name="content[title]" 
                   class="form-control" 
                   value="<?= html($title) ?>" 
                   placeholder="Текст заголовка"
                   required>
            <div class="form-text">Текст, который будет виден когда спойлер закрыт</div>
        </div>

        <div class="mb-4">
            <label class="form-label">Содержимое спойлера *</label>
            <textarea name="content[content]" 
                     class="form-control" 
                     rows="6" 
                     placeholder="Скрытый контент..."
                     required><?= html($content) ?></textarea>
            <div class="form-text">Контент, который будет скрыт под спойлером</div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getSettingsForm($currentSettings = []): string {
        $currentSettings = $this->validateAndNormalizeSettings($currentSettings);
        $showDefault = $currentSettings['show_default'] ?? '';
        $iconBefore = $currentSettings['icon_before'] ?? 'chevron-down';
        $iconAfter = $currentSettings['icon_after'] ?? '';
        $iconPosition = $currentSettings['icon_position'] ?? 'icon-after';
        $customClass = $currentSettings['custom_class'] ?? '';
        $animation = $currentSettings['animation'] ?? true;

        $spoilerIcons = [
            '' => 'Без иконки',
            'chevron-down' => 'Стрелка вниз',
            'chevron-right' => 'Стрелка вправо',
            'plus' => 'Плюс',
            'dash' => 'Минус',
            'caret-down' => 'Уголок вниз',
            'caret-right' => 'Уголок вправо',
            'arrow-down' => 'Стрелка вниз (жирная)',
            'arrow-right' => 'Стрелка вправо (жирная)',
            'eye' => 'Глаз',
            'info-circle' => 'Информация',
            'question-circle' => 'Вопрос',
            'chevron-double-down' => 'Двойная стрелка вниз',
            'chevron-double-right' => 'Двойная стрелка вправо',
            'arrow-down-circle' => 'Стрелка вниз в круге',
            'arrow-right-circle' => 'Стрелка вправо в круге',
            'caret-down-fill' => 'Закрашенный уголок вниз',
            'caret-right-fill' => 'Закрашенный уголок вправо'
        ];

        ob_start();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Иконка перед заголовком</label>
                    <select name="settings[icon_before]" class="form-select">
                        <?php foreach($spoilerIcons as $value => $name) { ?>
                            <option value="<?= $value ?>" <?= $iconBefore === $value ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Иконка после заголовка</label>
                    <select name="settings[icon_after]" class="form-select">
                        <?php foreach($spoilerIcons as $value => $name) { ?>
                            <option value="<?= $value ?>" <?= $iconAfter === $value ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="form-label">Позиция иконки</label>
                    <select name="settings[icon_position]" class="form-select">
                        <option value="icon-before" <?= $iconPosition === 'icon-before' ? 'selected' : '' ?>>Только перед текстом</option>
                        <option value="icon-after" <?= $iconPosition === 'icon-after' ? 'selected' : '' ?>>Только после текста</option>
                        <option value="icon-both" <?= $iconPosition === 'icon-both' ? 'selected' : '' ?>>С обеих сторон</option>
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
                           placeholder="my-spoiler">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="settings[show_default]" 
                           id="show_default"
                           value="show" 
                           <?= $showDefault === 'show' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="show_default">
                        Открыт по умолчанию
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="settings[animation]" 
                           id="animation"
                           value="1" 
                           <?= $animation ? 'checked' : '' ?>>
                    <label class="form-check-label" for="animation">
                        Анимация раскрытия
                    </label>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <?= bloggy_icon('bs', 'info-circle', '16 16', null, 'me-2') ?>
            Для работы спойлера необходим Bootstrap 5. Убедитесь, что он подключен в вашем шаблоне.
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
        
        $title = $content['title'] ?? '';
        $contentText = $content['content'] ?? '';

        if (empty(trim($title))) {
            return '<!-- SpoilerBlock: пустой заголовок -->';
        }

        $showDefault = $settings['show_default'] ?? '';
        $iconBefore = $settings['icon_before'] ?? 'chevron-down';
        $iconAfter = $settings['icon_after'] ?? '';
        $iconPosition = $settings['icon_position'] ?? 'icon-after';
        $customClass = $settings['custom_class'] ?? '';
        $animation = $settings['animation'] ?? true;
        $presetId = $settings['preset_id'] ?? null;
        $presetName = $settings['preset_name'] ?? '';
        $isOpen = $showDefault === 'show';
        $ariaExpanded = $isOpen ? 'true' : 'false';
        $ariaHidden = $isOpen ? 'false' : 'true';
        $noAnimationClass = !$animation ? 'no-animation' : '';
        
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

        $iconBeforeHtml = '';
        $iconAfterHtml = '';

        if ($iconPosition === 'icon-before' || $iconPosition === 'icon-both') {
            if (!empty($iconBefore)) {
                $iconBeforeHtml = bloggy_icon('bs', $iconBefore, '16 16', null, 'spoiler-icon me-2');
            }
        }
        
        if ($iconPosition === 'icon-after' || $iconPosition === 'icon-both') {
            if (!empty($iconAfter)) {
                $iconAfterHtml = bloggy_icon('bs', $iconAfter, '16 16', null, 'spoiler-icon ms-2');
            }
        }

        $result = $template;
        
        $result = str_replace('{custom_class}', trim($customClass . ' ' . $presetClass), $result);
        $result = str_replace('{icon_position}', html($iconPosition), $result);
        $result = str_replace('{icon_before}', $iconBeforeHtml, $result);
        $result = str_replace('{icon_after}', $iconAfterHtml, $result);
        $result = str_replace('{title}', html($title), $result);
        $result = str_replace('{show_default}', $isOpen ? 'show' : '', $result);
        $result = str_replace('{content}', $contentText, $result);
        $result = str_replace('{preset_id}', $presetId ? html($presetId) : '', $result);
        $result = str_replace('{preset_name}', $presetName ? html($presetName) : '', $result);
        $result = str_replace('{block_type}', $this->getSystemName(), $result);
        $result = str_replace('{block_name}', $this->getName(), $result);
        $result = str_replace('{aria_expanded}', $ariaExpanded, $result);
        $result = str_replace('{aria_hidden}', $ariaHidden, $result);
        $result = str_replace('{no_animation_class}', $noAnimationClass, $result);

        return $result;
    }

    public function getShortcodes(): array {
        return array_merge(parent::getShortcodes(), [
            '{title}' => 'Заголовок спойлера',
            '{content}' => 'Содержимое спойлера (может содержать HTML)',
            '{block_id}' => 'Уникальный ID блока',
            '{show_default}' => 'Классы для состояния по умолчанию',
            '{icon_before}' => 'Иконка перед заголовком',
            '{icon_after}' => 'Иконка после заголовком',
            '{icon_position}' => 'Позиция иконки',
            '{custom_class}' => 'Дополнительный CSS класс'
        ]);
    }

    public function getAdminCss(): array {
        return [
            'templates/default/admin/assets/css/blocks/spoiler.css'
        ];
    }

    public function validateSettings($settings): array {
        $errors = [];

        if (!empty($settings['custom_class']) && !preg_match('/^[a-zA-Z0-9-_ ]+$/', $settings['custom_class'])) {
            $errors[] = 'CSS класс может содержать только буквы, цифры, дефисы и подчеркивания';
        }

        $allowedIconPositions = ['icon-before', 'icon-after', 'icon-both'];
        if (!empty($settings['icon_position']) && !in_array($settings['icon_position'], $allowedIconPositions)) {
            $errors[] = 'Недопустимая позиция иконки';
        }

        return [empty($errors), $errors];
    }

    public function extractFromHtml(string $html): ?array {
        if (preg_match('/<div[^>]*class="[^"]*spoiler-header[^"]*"[^>]*>.*?<button[^>]*>.*?<span[^>]*class="[^"]*spoiler-title[^"]*"[^>]*>(.*?)<\/span>.*?<\/button>.*?<\/div>.*?<div[^>]*class="[^"]*spoiler-content[^"]*"[^>]*>.*?<div[^>]*class="[^"]*spoiler-body[^"]*"[^>]*>(.*?)<\/div>.*?<\/div>/is', $html, $matches)) {
            $title = trim(strip_tags($matches[1]));
            $content = trim($matches[2]);
            
            if (!empty($title)) {
                return [
                    'title' => $title,
                    'content' => $content
                ];
            }
        }
        
        if (preg_match('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $html, $titleMatch)) {
            $title = trim(strip_tags($titleMatch[1]));
            $content = trim(strip_tags($html));
            $content = preg_replace('/^.*?<\/h[1-6]>/is', '', $content);
            
            if (!empty($title) && !empty($content)) {
                return [
                    'title' => $title,
                    'content' => $content
                ];
            }
        }
        
        return null;
    }

    public function validateAndNormalizeContent($content): array {
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : ['title' => '', 'content' => ''];
        }
        
        if (!is_array($content)) {
            return ['title' => '', 'content' => ''];
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

    public function prepareContent($content): array {
        if (!is_array($content)) {
            $content = [];
        }
        
        if (isset($_POST['content']) && is_array($_POST['content'])) {
            if (isset($_POST['content']['title'])) {
                $content['title'] = trim($_POST['content']['title']);
            }
            if (isset($_POST['content']['content'])) {
                $content['content'] = trim($_POST['content']['content']);
            }
        }
        
        if (!isset($content['title'])) {
            $content['title'] = 'Нажмите чтобы раскрыть';
        }
        if (!isset($content['content'])) {
            $content['content'] = 'Скрытый контент...';
        }

        return $content;
    }

    public function prepareSettings($settings): array {
        if (!is_array($settings)) {
            $settings = [];
        }
        
        if (isset($_POST['settings']['show_default']) && $_POST['settings']['show_default'] === 'show') {
            $settings['show_default'] = 'show';
        } else {
            $settings['show_default'] = '';
        }
        
        $settings['animation'] = isset($_POST['settings']['animation']) && ($_POST['settings']['animation'] == '1' || $_POST['settings']['animation'] == 'on');
        
        if (isset($_POST['settings']['icon_before'])) {
            $settings['icon_before'] = trim($_POST['settings']['icon_before']);
        }
        
        if (isset($_POST['settings']['icon_after'])) {
            $settings['icon_after'] = trim($_POST['settings']['icon_after']);
        }
        
        if (isset($_POST['settings']['icon_position'])) {
            $settings['icon_position'] = trim($_POST['settings']['icon_position']);
        }
        
        if (isset($_POST['settings']['custom_class'])) {
            $settings['custom_class'] = trim($_POST['settings']['custom_class']);
        }

        return $settings;
    }

    public function getFrontendJs(): array {
        return [
            '/templates/default/front/assets/postblocks/spoilerblock/spoiler.js',
        ];
    }

    public function getFrontendCss(): array {
        return [
            '/templates/default/front/assets/postblocks/spoilerblock/spoiler.css',
        ];
    }

    public function getPreviewHtml($content = [], $settings = []): string {
        $content = $this->validateAndNormalizeContent($content);
        $settings = $this->validateAndNormalizeSettings($settings);
        
        $title = $content['title'] ?? 'Нажмите чтобы раскрыть';
        $contentText = $content['content'] ?? 'Скрытый контент...';
        $showDefault = $settings['show_default'] ?? '';
        $iconBefore = $settings['icon_before'] ?? 'chevron-down';
        $iconAfter = $settings['icon_after'] ?? '';
        $iconPosition = $settings['icon_position'] ?? 'icon-after';
        $customClass = $settings['custom_class'] ?? '';
        $animation = $settings['animation'] ?? true;
        
        $isOpen = $showDefault === 'show';
        $contentLength = strlen($contentText);
        
        $previewIcon = 'chevron-down';
        if (!empty($iconBefore) && $iconBefore !== '' && $iconBefore !== 'null') {
            $previewIcon = $iconBefore;
        } elseif (!empty($iconAfter) && $iconAfter !== '' && $iconAfter !== 'null') {
            $previewIcon = $iconAfter;
        }
        
        $iconPositionText = match($iconPosition) {
            'icon-before' => 'Слева',
            'icon-after' => 'Справа',
            'icon-both' => 'С обеих сторон',
            default => 'Справа'
        };
        
        ob_start();
        ?>
        <div class="post-block-preview post-block-preview-SpoilerBlock full-content-preview">
            <div class="preview-wrapper">
                <div class="preview-header">
                    <div class="preview-header-content">
                        <div class="preview-icon">
                            <?= bloggy_icon('bs', 'eye', '20 20') ?>
                        </div>
                        <div class="preview-info">
                            <div class="preview-title">
                                <strong>Спойлер</strong>
                                <?php if ($isOpen) { ?>
                                    <span class="badge bg-success badge-sm">Открыт</span>
                                <?php } else { ?>
                                    <span class="badge bg-warning badge-sm">Закрыт</span>
                                <?php } ?>
                            </div>
                            <div class="preview-stats">
                                <?= strlen($title) ?> симв. в заголовке
                                · <?= $contentLength ?> симв. в контенте
                            </div>
                        </div>
                    </div>
                    <div class="preview-actions">
                        <button type="button" class="btn btn-xs btn-outline-secondary preview-edit-btn" 
                                onclick="postBlocksManager.editBlock('{block_id}')">
                            <?= bloggy_icon('bs', 'pencil', '14 14') ?>
                        </button>
                    </div>
                </div>
                
                <div class="preview-body">
                    <?php if (!empty(trim($title)) || !empty(trim($contentText))) { ?>
                        <div class="spoiler-preview-container">
                            <div class="spoiler-header-preview border rounded p-3 mb-2 bg-light">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            <?php if (($iconPosition === 'icon-before' || $iconPosition === 'icon-both') && !empty($previewIcon)) { ?>
                                                <?= bloggy_icon('bs', $previewIcon, '16 16', null, 'me-2 text-primary') ?>
                                            <?php } ?>
                                            
                                            <span class="fw-semibold" style="color: #374151;">
                                                <?= html(mb_substr($title, 0, 40)) ?>
                                                <?php if (mb_strlen($title) > 40) { ?>...<?php } ?>
                                            </span>
                                            
                                            <?php if (($iconPosition === 'icon-after' || $iconPosition === 'icon-both') && !empty($previewIcon)) { ?>
                                                <?= bloggy_icon('bs', $previewIcon, '16 16', null, 'ms-2 text-primary') ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <span class="badge <?= $isOpen ? 'bg-success' : 'bg-warning' ?>">
                                            <?= $isOpen ? 'Открыт' : 'Закрыт' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="spoiler-content-preview border rounded p-3 bg-white <?= $isOpen ? '' : 'bg-light' ?>">
                                <div class="small text-muted mb-2 d-flex justify-content-between">
                                    <span><?= bloggy_icon('bs', 'eye-slash', '12 12', null, 'me-1') ?> Скрытый контент</span>
                                    <span><?= $contentLength ?> симв.</span>
                                </div>
                                
                                <?php if (!empty(trim($contentText))) { ?>
                                    <div class="spoiler-text-preview small" style="color: #6b7280; line-height: 1.5;">
                                        <?= html(mb_substr(strip_tags($contentText), 0, 80)) ?>
                                        <?php if (mb_strlen(strip_tags($contentText)) > 80) { ?>...<?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="text-center py-2 text-muted">
                                        <?= bloggy_icon('bs', 'eye-slash', '24 24', null, 'mb-1') ?>
                                        <div class="small mt-1">Контент не добавлен</div>
                                    </div>
                                <?php } ?>
                            </div>
                            
                            <div class="spoiler-preview-info mt-3 small text-muted">
                                <div class="row">
                                    <div class="col-6">
                                        <div><?= bloggy_icon('bs', $isOpen ? 'unlock' : 'lock', '12 12', null, 'me-1') ?>По умолчанию: <strong><?= $isOpen ? 'Открыт' : 'Закрыт' ?></strong></div>
                                        <div><?= bloggy_icon('bs', 'gear', '12 12', null, 'me-1') ?>Иконки: <strong><?= html($iconPositionText) ?></strong></div>
                                    </div>
                                    <div class="col-6">
                                        <?php if ($customClass) { ?>
                                            <div><?= bloggy_icon('bs', 'tag', '12 12', null, 'me-1') ?>Класс: <strong><?= html($customClass) ?></strong></div>
                                        <?php } ?>
                                        <div><?= bloggy_icon('bs', 'play-circle', '12 12', null, 'me-1') ?>Анимация: <strong><?= $animation ? 'Да' : 'Нет' ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="preview-empty-state">
                            <?= bloggy_icon('bs', 'eye', '48 48', '#6C6C6C', 'mb-3') ?>
                            <div class="empty-text">Содержимое не добавлено</div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                    onclick="postBlocksManager.editBlock('{block_id}')">
                                <?= bloggy_icon('bs', 'plus-circle', '14 14', null, 'me-1') ?>
                                Добавить спойлер
                            </button>
                            <div class="mt-3 small text-muted">
                                <?= bloggy_icon('bs', 'info-circle', '14 14', null, 'me-1') ?>
                                Используйте для скрытия контента под заголовком
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