<?php
class DefaultFooterBlock extends BaseHtmlBlock {
    
    public function getName(): string {
        return "Footer";
    }

    public function getSystemName(): string {
        return "DefaultFooterBlock";
    }

    public function getDescription(): string {
        return "Подвал сайта с меню, виджетами, контактами и навигацией";
    }

    public function getVersion(): string {
        return '2.0.0';
    }

    public function getTemplate(): string {
        return 'default';
    }

    public $categories = [];
    public $recentPosts = [];
    public $recentTags = [];

    public function getSettingsForm($currentSettings = []): string {
        $allMenus = MenuRenderer::getAllMenusForSelect();
        $settings = array_merge([], $currentSettings);
        
        $fieldsets = [
            new \Fieldset('Брендинг и логотип', [
                'icon' => 'bi bi-brush',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::blockImage('logo_path', [
                        'title' => 'Логотип',
                        'hint' => 'Загрузите логотип для футера',
                        'default' => $settings['logo_path'] ?? '',
                        'upload_path' => 'uploads/images/html_blocks/' . $this->getSystemName() . '/',
                        'preview_size' => '80px',
                        'column' => '12'
                    ]),
                    \FieldFactory::string('logo_alt', [
                        'title' => 'Alt текст логотипа',
                        'default' => $settings['logo_alt'] ?? 'Логотип сайта',
                        'column' => '6'
                    ]),
                    \FieldFactory::string('site_name', [
                        'title' => 'Название сайта',
                        'default' => $settings['site_name'] ?? SettingsHelper::get('general', 'site_name', 'BloggyCMS'),
                        'column' => '6'
                    ]),
                    \FieldFactory::textarea('site_description', [
                        'title' => 'Описание сайта',
                        'default' => $settings['site_description'] ?? 'Современная CMS для блогов и публикаций',
                        'rows' => 2,
                        'hint' => 'Краткое описание под логотипом',
                        'column' => '12'
                    ]),
                ]
            ]),
            
            new \Fieldset('Меню навигации', [
                'icon' => 'bi bi-menu-button-wide',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::select('footer_menu_1', [
                        'title' => 'Основное меню',
                        'options' => ['' => '-- Не показывать --'] + $allMenus,
                        'hint' => 'Первая колонка меню',
                        'column' => '6'
                    ]),
                    \FieldFactory::select('footer_menu_2', [
                        'title' => 'Дополнительное меню',
                        'options' => ['' => '-- Не показывать --'] + $allMenus,
                        'hint' => 'Вторая колонка меню',
                        'column' => '6'
                    ]),
                    \FieldFactory::string('menu_1_title', [
                        'title' => 'Заголовок меню 1',
                        'default' => $settings['menu_1_title'] ?? 'Навигация',
                        'column' => '6',
                        'placeholder' => 'Например: Меню'
                    ]),
                    \FieldFactory::string('menu_2_title', [
                        'title' => 'Заголовок меню 2',
                        'default' => $settings['menu_2_title'] ?? 'Информация',
                        'column' => '6',
                        'placeholder' => 'Например: О сайте'
                    ]),
                ]
            ]),
            
            new \Fieldset('Виджеты', [
                'icon' => 'bi bi-grid-3x3-gap',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::checkbox('show_recent_posts', [
                        'title' => 'Последние посты',
                        'default' => $settings['show_recent_posts'] ?? 0,
                        'switch' => true,
                        'column' => '12'
                    ]),
                    \FieldFactory::string('recent_posts_title', [
                        'title' => 'Заголовок виджета постов',
                        'default' => $settings['recent_posts_title'] ?? 'Последние посты',
                        'column' => '6',
                        'show' => 'field:show_recent_posts'
                    ]),
                    \FieldFactory::number('recent_posts_count', [
                        'title' => 'Количество постов',
                        'default' => $settings['recent_posts_count'] ?? 3,
                        'min' => 1,
                        'max' => 10,
                        'column' => '6',
                        'show' => 'field:show_recent_posts'
                    ]),
                    \FieldFactory::checkbox('show_recent_tags', [
                        'title' => 'Популярные теги',
                        'default' => $settings['show_recent_tags'] ?? 0,
                        'switch' => true,
                        'column' => '12'
                    ]),
                    \FieldFactory::string('recent_tags_title', [
                        'title' => 'Заголовок виджета тегов',
                        'default' => $settings['recent_tags_title'] ?? 'Популярные теги',
                        'column' => '6',
                        'show' => 'field:show_recent_tags'
                    ]),
                    \FieldFactory::number('recent_tags_count', [
                        'title' => 'Количество тегов',
                        'default' => $settings['recent_tags_count'] ?? 5,
                        'min' => 1,
                        'max' => 20,
                        'column' => '6',
                        'show' => 'field:show_recent_tags'
                    ]),
                ]
            ]),
            
            new \Fieldset('Категории', [
                'icon' => 'bi bi-folder',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::checkbox('show_categories', [
                        'title' => 'Показывать категории',
                        'default' => $settings['show_categories'] ?? 0,
                        'switch' => true,
                        'column' => '12'
                    ]),
                    \FieldFactory::string('categories_title', [
                        'title' => 'Заголовок секции категорий',
                        'default' => $settings['categories_title'] ?? 'Категории',
                        'column' => '6',
                        'show' => 'field:show_categories'
                    ]),
                    \FieldFactory::number('categories_count', [
                        'title' => 'Количество категорий',
                        'default' => $settings['categories_count'] ?? 8,
                        'min' => 1,
                        'max' => 20,
                        'column' => '6',
                        'show' => 'field:show_categories'
                    ]),
                    \FieldFactory::select('categories_style', [
                        'title' => 'Стиль отображения',
                        'options' => [
                            'pills' => 'Таблетки (с фоном)',
                            'links' => 'Простые ссылки',
                            'chips' => 'Чипсы (компактные)'
                        ],
                        'default' => $settings['categories_style'] ?? 'pills',
                        'column' => '6',
                        'show' => 'field:show_categories'
                    ]),
                    \FieldFactory::checkbox('categories_show_count', [
                        'title' => 'Показывать количество постов',
                        'default' => $settings['categories_show_count'] ?? 1,
                        'switch' => true,
                        'column' => '6',
                        'show' => 'field:show_categories'
                    ])
                ]
            ]),
            
            new \Fieldset('Контакты (внизу)', [
                'icon' => 'bi bi-telephone',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::checkbox('show_contacts', [
                        'title' => 'Показывать блок контактов',
                        'default' => $settings['show_contacts'] ?? 0,
                        'switch' => true,
                        'column' => '12'
                    ]),
                    \FieldFactory::string('contacts_title', [
                        'title' => 'Заголовок контактов',
                        'default' => $settings['contacts_title'] ?? 'Свяжитесь с нами',
                        'column' => '3',
                        'show' => 'field:show_contacts'
                    ]),
                    \FieldFactory::string('contact_email', [
                        'title' => 'Email',
                        'default' => $settings['contact_email'] ?? '',
                        'column' => '3',
                        'placeholder' => 'info@example.com',
                        'show' => 'field:show_contacts'
                    ]),
                    \FieldFactory::string('contact_phone', [
                        'title' => 'Телефон',
                        'default' => $settings['contact_phone'] ?? '',
                        'column' => '3',
                        'placeholder' => '+7 (999) 000-00-00',
                        'show' => 'field:show_contacts'
                    ]),
                    \FieldFactory::string('contact_address', [
                        'title' => 'Адрес',
                        'default' => $settings['contact_address'] ?? '',
                        'column' => '3',
                        'placeholder' => 'г. Москва, ул. Примерная, 1',
                        'show' => 'field:show_contacts'
                    ]),
                ]
            ]),
            
            new \Fieldset('Социальные сети', [
                'icon' => 'bi bi-share',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::repeater('social_links', [
                        'title' => 'Ссылки на соцсети',
                        'hint' => 'Добавьте ссылки на ваши социальные сети',
                        'column' => '12',
                        'repeater_columns' => 4,
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
                                    'reddit'  => 'Reddit'
                                ],
                                'default' => 'telegram',
                            ],
                            [
                                'name' => 'url',
                                'title' => 'URL',
                                'type' => 'string',
                                'placeholder' => 'https://...',
                                'attributes' => ['pattern' => 'https?://.+']
                            ],
                        ],
                        'default' => $settings['social_links'] ?? [],
                    ]),
                ]
            ]),
            
            new \Fieldset('Нижняя часть (права и ссылки)', [
                'icon' => 'bi bi-file-text',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::string('copyright_text', [
                        'title' => 'Текст копирайта',
                        'default' => $settings['copyright_text'] ?? '© ' . date('Y') . ' ' . SettingsHelper::get('general', 'site_name', 'BloggyCMS'),
                        'hint' => 'Поддерживает HTML',
                        'column' => '12'
                    ]),
                    \FieldFactory::repeater('footer_links', [
                        'title' => 'Дополнительные ссылки',
                        'hint' => 'Ссылки, которые будут отображаться в нижней части футера',
                        'column' => '12',
                        'repeater_columns' => 2,
                        'fields' => [
                            [
                                'name' => 'title',
                                'title' => 'Текст ссылки',
                                'type' => 'string',
                                'placeholder' => 'Например: Политика конфиденциальности'
                            ],
                            [
                                'name' => 'url',
                                'title' => 'URL',
                                'type' => 'string',
                                'placeholder' => '/privacy'
                            ],
                            [
                                'name' => 'target',
                                'title' => 'Открывать в',
                                'type' => 'select',
                                'options' => [
                                    '_self' => 'Текущее окно',
                                    '_blank' => 'Новое окно',
                                ],
                                'default' => '_self'
                            ],
                        ],
                        'default' => $settings['footer_links'] ?? [
                            ['title' => 'Политика конфиденциальности', 'url' => '/privacy', 'target' => '_self']
                        ],
                    ]),
                ]
            ]),
            
            new \Fieldset('Внешний вид', [
                'icon' => 'bi bi-palette',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::color('background_color', [
                        'title' => 'Цвет фона',
                        'preset' => 'basic',
                        'column' => '3',
                        'default' => $settings['background_color'] ?? '#111827'
                    ]),
                    \FieldFactory::color('text_color', [
                        'title' => 'Цвет текста',
                        'preset' => 'basic',
                        'column' => '3',
                        'default' => $settings['text_color'] ?? '#9ca3af'
                    ]),
                    \FieldFactory::color('accent_color', [
                        'title' => 'Акцентный цвет',
                        'preset' => 'website',
                        'column' => '3',
                        'default' => $settings['accent_color'] ?? '#2563eb'
                    ]),
                    \FieldFactory::color('heading_color', [
                        'title' => 'Цвет заголовков',
                        'preset' => 'basic',
                        'column' => '3',
                        'default' => $settings['heading_color'] ?? '#f9fafb'
                    ]),
                    \FieldFactory::number('padding_top', [
                        'title' => 'Отступ сверху (px)',
                        'default' => $settings['padding_top'] ?? 80,
                        'min' => 40,
                        'max' => 160,
                        'step' => 10,
                        'column' => '6'
                    ]),
                    \FieldFactory::number('padding_bottom', [
                        'title' => 'Отступ снизу (px)',
                        'default' => $settings['padding_bottom'] ?? 40,
                        'min' => 20,
                        'max' => 100,
                        'step' => 10,
                        'column' => '6'
                    ]),
                ]
            ]),
        ];
        
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

    public function validateSettings($settings): array {
        if (!is_array($settings)) {
            return [false, ['Настройки должны быть массивом']];
        }
        return [true, []];
    }

    public function prepareSettings($settings): array {
        if (!is_array($settings)) {
            return [];
        }
        

        $uploadResult = BlockImageHelper::handleUpload('logo_path', $this->getSystemName(), $settings['logo_path'] ?? '');
        if ($uploadResult['success']) {
            $settings['logo_path'] = $uploadResult['value'];
        }
        $settings['logo_path'] = BlockImageHelper::handleDelete('logo_path', $settings['logo_path'] ?? '');
        unset($settings['logo_path_file'], $settings['remove_logo_path']);
        
        $settings['logo_alt'] = trim($settings['logo_alt'] ?? 'Логотип сайта');
        $settings['site_name'] = trim($settings['site_name'] ?? SettingsHelper::get('general', 'site_name', 'BloggyCMS'));
        $settings['site_description'] = trim($settings['site_description'] ?? '');
        $settings['footer_menu_1'] = $settings['footer_menu_1'] ?? '';
        $settings['footer_menu_2'] = $settings['footer_menu_2'] ?? '';
        $settings['menu_1_title'] = trim($settings['menu_1_title'] ?? 'Навигация');
        $settings['menu_2_title'] = trim($settings['menu_2_title'] ?? 'Информация');
        $settings['show_recent_posts'] = isset($settings['show_recent_posts']) ? (int)$settings['show_recent_posts'] : 0;
        $settings['recent_posts_title'] = trim($settings['recent_posts_title'] ?? 'Последние посты');
        $settings['recent_posts_count'] = (int)($settings['recent_posts_count'] ?? 3);
        $settings['show_recent_tags'] = isset($settings['show_recent_tags']) ? (int)$settings['show_recent_tags'] : 0;
        $settings['recent_tags_title'] = trim($settings['recent_tags_title'] ?? 'Популярные теги');
        $settings['recent_tags_count'] = (int)($settings['recent_tags_count'] ?? 5);
        $settings['show_categories'] = isset($settings['show_categories']) ? (int)$settings['show_categories'] : 0;
        $settings['categories_title'] = trim($settings['categories_title'] ?? 'Категории');
        $settings['categories_count'] = (int)($settings['categories_count'] ?? 8);
        $settings['categories_show_count'] = isset($settings['categories_show_count']) ? (int)$settings['categories_show_count'] : 1;
        $settings['categories_style'] = $settings['categories_style'] ?? 'pills';
        $settings['show_contacts'] = isset($settings['show_contacts']) ? (int)$settings['show_contacts'] : 0;
        $settings['contacts_title'] = trim($settings['contacts_title'] ?? 'Свяжитесь с нами');
        $settings['contact_email'] = trim($settings['contact_email'] ?? '');
        $settings['contact_phone'] = trim($settings['contact_phone'] ?? '');
        $settings['contact_address'] = trim($settings['contact_address'] ?? '');
        $settings['social_links'] = $settings['social_links'] ?? [];
        if (!is_array($settings['social_links'])) {
            $settings['social_links'] = [];
        }
        
        $settings['copyright_text'] = $settings['copyright_text'] ?? '© ' . date('Y') . ' ' . SettingsHelper::get('general', 'site_name', 'BloggyCMS');
        $settings['footer_links'] = $settings['footer_links'] ?? [];
        if (!is_array($settings['footer_links'])) {
            $settings['footer_links'] = [];
        }
        
        $settings['background_color'] = $settings['background_color'] ?? '#111827';
        $settings['text_color'] = $settings['text_color'] ?? '#9ca3af';
        $settings['accent_color'] = $settings['accent_color'] ?? '#2563eb';
        $settings['heading_color'] = $settings['heading_color'] ?? '#f9fafb';
        $settings['padding_top'] = (int)($settings['padding_top'] ?? 80);
        $settings['padding_bottom'] = (int)($settings['padding_bottom'] ?? 40);
        
        return $settings;
    }

    private function getRecentPosts($limit = 3) {
        try {
            if (!API::hasModel('posts')) return [];
            $posts = API::posts()->getAll($limit);
            $posts = array_filter($posts, fn($p) => ($p['status'] ?? '') === 'published');
            return array_slice($posts, 0, $limit);
        } catch (Exception $e) { return []; }
    }

    private function getRecentTags($limit = 5) {
        try {
            if (!API::hasModel('tags')) return [];
            $tags = API::tags()->getAll();
            usort($tags, fn($a, $b) => ($b['posts_count'] ?? 0) - ($a['posts_count'] ?? 0));
            return array_slice($tags, 0, $limit);
        } catch (Exception $e) { return []; }
    }

    private function getCategories($limit = 8) {
        try {
            if (!API::hasModel('categories')) return [];
            $categories = API::categories()->getAll();
            return array_slice($categories, 0, $limit);
        } catch (Exception $e) { return []; }
    }

    public function getLogoUrl($settings) {
        if (!empty($settings['logo_path'])) {
            return BlockImageHelper::getImageUrl($settings['logo_path']);
        }
        return '';
    }

    public function processFrontend($settings = [], $templateName = null): string {
        $this->recentPosts = $settings['show_recent_posts'] ? $this->getRecentPosts($settings['recent_posts_count'] ?? 3) : [];
        $this->recentTags = $settings['show_recent_tags'] ? $this->getRecentTags($settings['recent_tags_count'] ?? 5) : [];
        $this->categories = $settings['show_categories'] ? $this->getCategories($settings['categories_count'] ?? 8) : [];
        return parent::processFrontend($settings, $templateName);
    }
}