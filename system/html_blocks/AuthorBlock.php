<?php

class AuthorBlock extends BaseHtmlBlock {
    
    public function getName(): string {
        return "Автор блога";
    }

    public function getSystemName(): string {
        return "AuthorBlock";
    }

    public function getDescription(): string {
        return "Карточка автора с фото, именем, описанием и ссылками на социальные сети";
    }

    public function getShortDescription(): string {
        return "Карточка автора с фото, описанием и ссылками на соцсети";
    }

    public function getIcon(): string {
        return 'bi bi-person-circle';
    }

    public function getVersion(): string {
        return '1.0.0';
    }

    public function getTemplate(): string {
        return 'default';
    }

    public function getSettingsForm($currentSettings = []): string {
        $settings = array_merge($this->getDefaultSettings(), $currentSettings);
        
        $fieldsets = [];
        
        $fieldsets[] = new \Fieldset('Основная информация', [
            'icon' => 'bi bi-person',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::blockImage('avatar', [
                    'title' => 'Фото автора',
                    'hint' => 'Рекомендуемый размер: 200x200px. Квадратное изображение',
                    'upload_path' => 'uploads/images/html_blocks/' . $this->getSystemName() . '/',
                    'preview_size' => '100px',
                    'column' => '12',
                ]),
                \FieldFactory::string('name', [
                    'title' => 'Имя автора',
                    'default' => $settings['name'] ?? '',
                    'placeholder' => 'Например: Иван Иванов',
                    'column' => '6',
                ]),
                \FieldFactory::string('role', [
                    'title' => 'Должность / Роль',
                    'default' => $settings['role'] ?? '',
                    'placeholder' => 'Например: Основатель проекта, Senior PHP-разработчик',
                    'column' => '6',
                ]),
                \FieldFactory::textarea('description', [
                    'title' => 'Краткое описание',
                    'default' => $settings['description'] ?? '',
                    'rows' => 4,
                    'placeholder' => 'Расскажите о себе, своем опыте, увлечениях...',
                    'column' => '12',
                ]),
            ]
        ]);
        
        $fieldsets[] = new \Fieldset('Социальные сети', [
            'icon' => 'bi bi-share',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::repeater('social_links', [
                    'title' => 'Ссылки на соцсети',
                    'hint' => 'Добавьте ссылки на ваши социальные сети',
                    'column' => '12',
                    'repeater_columns' => 2,
                    'min_items' => 0,
                    'max_items' => 8,
                    'fields' => [
                        [
                            'name' => 'network',
                            'title' => 'Социальная сеть',
                            'type' => 'select',
                            'options' => [
                                'telegram' => 'Telegram',
                                'vk' => 'ВКонтакте',
                                'youtube' => 'YouTube',
                                'github' => 'GitHub',
                                'twitter' => 'Twitter/X',
                                'instagram' => 'Instagram',
                                'facebook' => 'Facebook',
                                'linkedin' => 'LinkedIn',
                                'odnoklassniki' => 'Одноклассники',
                                'behance' => 'Behance',
                                'reddit' => 'Reddit',
                                'discord' => 'Discord',
                                'tiktok' => 'TikTok',
                            ],
                            'default' => 'telegram',
                            'field_column' => '6',
                        ],
                        [
                            'name' => 'url',
                            'title' => 'URL',
                            'type' => 'string',
                            'placeholder' => 'https://...',
                            'field_column' => '6',
                        ],
                    ],
                    'default' => $settings['social_links'] ?? [],
                ]),
            ]
        ]);
        
        $fieldsets[] = new \Fieldset('Дополнительные контакты', [
            'icon' => 'bi bi-envelope',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::string('email', [
                    'title' => 'Email',
                    'default' => $settings['email'] ?? '',
                    'placeholder' => 'ivan@example.com',
                    'column' => '6',
                ]),
                \FieldFactory::string('phone', [
                    'title' => 'Телефон',
                    'default' => $settings['phone'] ?? '',
                    'placeholder' => '+7 (999) 000-00-00',
                    'column' => '6',
                ]),
                \FieldFactory::string('website', [
                    'title' => 'Личный сайт',
                    'default' => $settings['website'] ?? '',
                    'placeholder' => 'https://example.com',
                    'column' => '12',
                ]),
            ]
        ]);
        
        $fieldsets[] = new \Fieldset('Кнопка действия', [
            'icon' => 'bi bi-ui-radios',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::checkbox('show_button', [
                    'title' => 'Показывать кнопку',
                    'default' => $settings['show_button'] ?? 0,
                    'switch' => true,
                    'column' => '12',
                ]),
                \FieldFactory::string('button_text', [
                    'title' => 'Текст кнопки',
                    'default' => $settings['button_text'] ?? 'Связаться',
                    'placeholder' => 'Написать сообщение',
                    'column' => '6',
                    'show' => 'field:show_button',
                ]),
                \FieldFactory::string('button_url', [
                    'title' => 'Ссылка кнопки',
                    'default' => $settings['button_url'] ?? '/contact',
                    'placeholder' => '/contact',
                    'column' => '6',
                    'show' => 'field:show_button',
                ]),
                \FieldFactory::select('button_target', [
                    'title' => 'Открывать в',
                    'options' => [
                        '_self' => 'Текущее окно',
                        '_blank' => 'Новое окно',
                    ],
                    'default' => $settings['button_target'] ?? '_self',
                    'column' => '12',
                    'show' => 'field:show_button',
                ]),
            ]
        ]);
        
        $fieldsets[] = new \Fieldset('Внешний вид', [
            'icon' => 'bi bi-palette',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::select('avatar_style', [
                    'title' => 'Стиль аватара',
                    'options' => [
                        'circle' => 'Круглый',
                        'square' => 'Квадратный',
                        'rounded' => 'Скругленные углы',
                    ],
                    'default' => $settings['avatar_style'] ?? 'circle',
                    'column' => '6',
                ]),
                \FieldFactory::select('align', [
                    'title' => 'Выравнивание текста',
                    'options' => [
                        'left' => 'Слева',
                        'center' => 'По центру',
                    ],
                    'default' => $settings['align'] ?? 'center',
                    'column' => '6',
                ]),
                \FieldFactory::select('theme', [
                    'title' => 'Тема',
                    'options' => [
                        'light' => 'Светлая',
                        'dark' => 'Темная',
                        'custom' => 'Своя',
                    ],
                    'default' => $settings['theme'] ?? 'light',
                    'column' => '12',
                ]),
                \FieldFactory::color('background_color', [
                    'title' => 'Цвет фона карточки',
                    'preset' => 'basic',
                    'column' => '6',
                    'show' => 'field:theme = custom',
                ]),
                \FieldFactory::color('text_color', [
                    'title' => 'Цвет текста',
                    'preset' => 'basic',
                    'column' => '6',
                    'show' => 'field:theme = custom',
                ]),
                \FieldFactory::color('accent_color', [
                    'title' => 'Акцентный цвет',
                    'preset' => 'website',
                    'default' => '#2563eb',
                    'column' => '12',
                ]),
                \FieldFactory::checkbox('show_shadow', [
                    'title' => 'Показывать тень',
                    'default' => $settings['show_shadow'] ?? 1,
                    'switch' => true,
                    'column' => '12',
                ]),
                \FieldFactory::number('padding_top', [
                    'title' => 'Отступ сверху (px)',
                    'default' => $settings['padding_top'] ?? 40,
                    'min' => 0,
                    'max' => 100,
                    'step' => 10,
                    'column' => '6',
                ]),
                \FieldFactory::number('padding_bottom', [
                    'title' => 'Отступ снизу (px)',
                    'default' => $settings['padding_bottom'] ?? 40,
                    'min' => 0,
                    'max' => 100,
                    'step' => 10,
                    'column' => '6',
                ]),
            ]
        ]);
        
        $fieldsets[] = new \Fieldset('Дополнительно', [
            'icon' => 'bi bi-gear',
            'columns' => '12',
            'fields' => [
                \FieldFactory::string('custom_css_class', [
                    'title' => 'CSS класс',
                    'default' => $settings['custom_css_class'] ?? '',
                ]),
                \FieldFactory::string('custom_id', [
                    'title' => 'HTML ID',
                    'default' => $settings['custom_id'] ?? '',
                ]),
            ]
        ]);
        
        ob_start();
        ?>
        <div class="row g-4">
            <?php foreach ($fieldsets as $fieldset): ?>
            <div class="col-12"><?= $fieldset->render($settings) ?></div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function getDefaultSettings(): array {
        return [
            'avatar' => '',
            'name' => '',
            'role' => '',
            'description' => '',
            'social_links' => [],
            'email' => '',
            'phone' => '',
            'website' => '',
            'show_button' => 0,
            'button_text' => 'Связаться',
            'button_url' => '/contact',
            'button_target' => '_self',
            'avatar_style' => 'circle',
            'align' => 'center',
            'theme' => 'light',
            'accent_color' => '#2563eb',
            'show_shadow' => 1,
            'padding_top' => 40,
            'padding_bottom' => 40,
        ];
    }
    
    public function validateSettings($settings): array {
        return [true, []];
    }
    
    public function prepareSettings($settings): array {
        if (!is_array($settings)) {
            return $this->getDefaultSettings();
        }
        
        $prepared = array_merge($this->getDefaultSettings(), $settings);
        
        $uploadResult = BlockImageHelper::handleUpload('avatar', $this->getSystemName(), $prepared['avatar'] ?? '');
        if ($uploadResult['success']) {
            $prepared['avatar'] = $uploadResult['value'];
        }
        $prepared['avatar'] = BlockImageHelper::handleDelete('avatar', $prepared['avatar'] ?? '');
        unset($prepared['avatar_file'], $prepared['remove_avatar']);
        
        $textFields = ['name', 'role', 'description', 'email', 'phone', 'website', 
                       'button_text', 'button_url', 'custom_css_class', 'custom_id'];
        foreach ($textFields as $field) {
            if (isset($prepared[$field])) {
                $prepared[$field] = trim($prepared[$field]);
            }
        }
        
        if (isset($prepared['social_links']) && is_array($prepared['social_links'])) {
            $filteredLinks = [];
            foreach ($prepared['social_links'] as $link) {
                if (!empty(trim($link['url'] ?? ''))) {
                    $filteredLinks[] = [
                        'network' => $link['network'] ?? 'telegram',
                        'url' => trim($link['url']),
                    ];
                }
            }
            $prepared['social_links'] = $filteredLinks;
        }
        
        $prepared['show_button'] = isset($settings['show_button']) ? (int)$settings['show_button'] : 0;
        $prepared['show_shadow'] = isset($settings['show_shadow']) ? (int)$settings['show_shadow'] : 1;
        $prepared['padding_top'] = (int)($settings['padding_top'] ?? 40);
        $prepared['padding_bottom'] = (int)($settings['padding_bottom'] ?? 40);
        
        return $prepared;
    }
    
    public function getAvatarUrl($settings) {
        if (!empty($settings['avatar'])) {
            return BlockImageHelper::getImageUrl($settings['avatar']);
        }
        return '';
    }
    
    public function processFrontend($settings = [], $templateName = null): string {
        $this->avatarUrl = $this->getAvatarUrl($settings);
        $this->socialLinks = $settings['social_links'] ?? [];
        return parent::processFrontend($settings, $templateName);
    }
}