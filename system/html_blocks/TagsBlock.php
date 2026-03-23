<?php

class TagsBlock extends BaseHtmlBlock {
    
    public function getName(): string {
        return "Tags Cloud";
    }

    public function getSystemName(): string {
        return "TagsBlock";
    }

    public function getDescription(): string {
        return "Блок с тегами в виде облака, карточек или списка";
    }

    public function getVersion(): string {
        return '1.0.0';
    }

    public function getTemplate(): string {
        return 'default';
    }

    public $tags = [];

    public function getSettingsForm($currentSettings = []): string {
        
        $settings = array_merge([], $currentSettings);
        
        $fieldsets[] = new \Fieldset('Заголовочная часть', [
            'icon' => 'bi bi-pencil',
            'columns' => '12',
            'fields' => [
                \FieldFactory::string('badge', [
                    'title' => 'Бейдж',
                    'default' => $settings['badge'] ?? 'Теги',
                    'placeholder' => 'Например: Популярные теги',
                ]),
                \FieldFactory::string('title', [
                    'title' => 'Заголовок',
                    'default' => $settings['title'] ?? 'Навигация по <span class="highlight">тегам</span>',
                    'placeholder' => 'Используйте <span class="highlight"> для выделения',
                ]),
                \FieldFactory::textarea('description', [
                    'title' => 'Описание',
                    'default' => $settings['description'] ?? 'Исследуйте статьи по интересующим темам',
                    'rows' => 3,
                ]),
                \FieldFactory::select('align', [
                    'title' => 'Выравнивание заголовка',
                    'options' => [
                        'left' => 'Слева',
                        'center' => 'По центру',
                    ],
                    'default' => 'center',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Настройки отображения', [
            'icon' => 'bi bi-grid-3x3',
            'columns' => '12',
            'fields' => [
                \FieldFactory::select('display_style', [
                    'title' => 'Стиль отображения',
                    'options' => [
                        'cloud' => 'Облако тегов (разный размер)',
                        'cards' => 'Карточки',
                        'list' => 'Список',
                        'compact' => 'Компактный',
                        'grid' => 'Сетка',
                    ],
                    'default' => 'cloud',
                ]),
                \FieldFactory::select('columns', [
                    'title' => 'Количество колонок',
                    'options' => [
                        '2' => '2 колонки',
                        '3' => '3 колонки',
                        '4' => '4 колонки',
                    ],
                    'default' => '3',
                    'show' => 'field:display_style in cards,grid',
                ]),
                \FieldFactory::number('limit', [
                    'title' => 'Количество тегов',
                    'default' => 20,
                    'min' => 1,
                    'max' => 100,
                    'hint' => '0 = все теги',
                ]),
                \FieldFactory::number('min_posts', [
                    'title' => 'Минимальное количество постов',
                    'default' => 1,
                    'min' => 0,
                    'max' => 100,
                    'hint' => 'Показывать только теги с указанным минимумом постов',
                ]),
                \FieldFactory::checkbox('show_post_count', [
                    'title' => 'Показывать количество постов',
                    'default' => 1,
                    'switch' => true,
                ]),
                \FieldFactory::checkbox('show_icon', [
                    'title' => 'Показывать иконки',
                    'default' => 1,
                    'switch' => true,
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Изображения тегов', [
            'icon' => 'bi bi-image',
            'columns' => '12',
            'fields' => [
                \FieldFactory::select('image_style', [
                    'title' => 'Стиль отображения изображений',
                    'options' => [
                        'none' => 'Не показывать',
                        'icon' => 'Только иконки',
                        'thumbnail' => 'Миниатюра (маленькая)',
                        'cover' => 'Обложка (на всю карточку)',
                        'background' => 'Фон карточки',
                        'side' => 'Сбоку (как в карточках постов)',
                    ],
                    'default' => 'icon',
                ]),
                \FieldFactory::select('image_size', [
                    'title' => 'Размер изображения',
                    'options' => [
                        'sm' => 'Маленький',
                        'md' => 'Средний',
                        'lg' => 'Большой',
                    ],
                    'default' => 'md',
                    'show' => 'field:image_style != none && field:image_style != icon',
                ]),
                \FieldFactory::checkbox('image_rounded', [
                    'title' => 'Скругленные углы',
                    'default' => 1,
                    'switch' => true,
                    'show' => 'field:image_style != none',
                ]),
                \FieldFactory::checkbox('image_shadow', [
                    'title' => 'Тень',
                    'default' => 0,
                    'switch' => true,
                    'show' => 'field:image_style != none',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Сортировка', [
            'icon' => 'bi bi-arrow-up-short',
            'columns' => '12',
            'fields' => [
                \FieldFactory::select('order_by', [
                    'title' => 'Сортировка',
                    'options' => [
                        'name ASC' => 'По названию (А-Я)',
                        'name DESC' => 'По названию (Я-А)',
                        'posts_count DESC' => 'По популярности (сначала популярные)',
                        'posts_count ASC' => 'По популярности (сначала редкие)',
                        'id DESC' => 'По ID (сначала новые)',
                        'id ASC' => 'По ID (сначала старые)',
                    ],
                    'default' => 'posts_count DESC',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Фильтрация', [
            'icon' => 'bi bi-funnel',
            'columns' => '12',
            'fields' => [
                \FieldFactory::textarea('exclude_ids', [
                    'title' => 'Исключить теги (ID через запятую)',
                    'default' => '',
                    'placeholder' => '5, 12, 8',
                    'rows' => 2,
                    'hint' => 'ID тегов, которые не нужно показывать',
                ]),
                \FieldFactory::textarea('include_ids', [
                    'title' => 'Только указанные теги (ID через запятую)',
                    'default' => '',
                    'placeholder' => '3, 7, 15',
                    'rows' => 2,
                    'hint' => 'Показывать только эти теги (переопределяет остальные фильтры)',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Цвета и фон', [
            'icon' => 'bi bi-palette',
            'columns' => '12',
            'fields' => [
                \FieldFactory::select('theme', [
                    'title' => 'Тема',
                    'options' => [
                        'light' => 'Светлая',
                        'dark' => 'Темная',
                        'custom' => 'Своя',
                    ],
                    'default' => 'light',
                ]),
                \FieldFactory::color('background_color', [
                    'title' => 'Цвет фона',
                    'preset' => 'basic',
                    'show' => 'field:theme = custom',
                ]),
                \FieldFactory::color('text_color', [
                    'title' => 'Цвет текста',
                    'preset' => 'basic',
                    'show' => 'field:theme = custom',
                ]),
                \FieldFactory::color('accent_color', [
                    'title' => 'Акцентный цвет',
                    'preset' => 'website',
                    'default' => '#2563eb',
                ]),
                \FieldFactory::color('card_background', [
                    'title' => 'Цвет карточек',
                    'preset' => 'basic',
                    'default' => $settings['card_background'] ?? '',
                    'hint' => 'Оставьте пустым для автоматического',
                ]),
                \FieldFactory::checkbox('gradient_cards', [
                    'title' => 'Градиентные карточки',
                    'default' => 0,
                    'switch' => true,
                    'hint' => 'Каждая карточка с легким градиентом',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Отступы', [
            'icon' => 'bi bi-arrows-expand',
            'columns' => '12',
            'fields' => [
                \FieldFactory::number('padding_top', [
                    'title' => 'Отступ сверху (px)',
                    'default' => 80,
                    'min' => 0,
                    'max' => 200,
                    'step' => 10,
                ]),
                \FieldFactory::number('padding_bottom', [
                    'title' => 'Отступ снизу (px)',
                    'default' => 80,
                    'min' => 0,
                    'max' => 200,
                    'step' => 10,
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

    private function getTags($settings) {
        try {
            if (!API::hasModel('tags')) {
                return [];
            }

            $limit = (int)($settings['limit'] ?? 20);
            $minPosts = (int)($settings['min_posts'] ?? 1);
            $orderBy = $settings['order_by'] ?? 'posts_count DESC';
            
            $excludeIds = [];
            if (!empty($settings['exclude_ids'])) {
                $ids = explode(',', $settings['exclude_ids']);
                foreach ($ids as $id) {
                    $id = trim($id);
                    if (is_numeric($id)) {
                        $excludeIds[] = (int)$id;
                    }
                }
            }

            $includeIds = [];
            if (!empty($settings['include_ids'])) {
                $ids = explode(',', $settings['include_ids']);
                foreach ($ids as $id) {
                    $id = trim($id);
                    if (is_numeric($id)) {
                        $includeIds[] = (int)$id;
                    }
                }
            }

            if (!empty($includeIds)) {
                $tags = [];
                foreach ($includeIds as $id) {
                    $tag = API::tags()->getById($id);
                    if ($tag) {
                        $tags[] = $tag;
                    }
                }
            } else {
                $tags = API::tags()->getAll();
            }

            if (empty($tags)) {
                return [];
            }

            $filteredTags = [];
            foreach ($tags as $tag) {
                $tag['posts_count'] = $this->getTagPostsCount($tag['id']);
                
                if ($tag['posts_count'] >= $minPosts) {
                    if (!empty($excludeIds) && in_array($tag['id'], $excludeIds)) {
                        continue;
                    }
                    $filteredTags[] = $tag;
                }
            }

            list($field, $direction) = explode(' ', $orderBy);

            usort($filteredTags, function($a, $b) use ($field, $direction) {
                if ($field === 'posts_count') {
                    $aVal = (int)($a[$field] ?? 0);
                    $bVal = (int)($b[$field] ?? 0);
                    $result = $aVal - $bVal;
                } else {
                    $aVal = (string)($a[$field] ?? '');
                    $bVal = (string)($b[$field] ?? '');
                    $result = strcmp($aVal, $bVal);
                }

                return $direction === 'DESC' ? -$result : $result;
            });

            if ($limit > 0) {
                $filteredTags = array_slice($filteredTags, 0, $limit);
            }

            if (($settings['display_style'] ?? 'cloud') === 'cloud') {
                $filteredTags = $this->calculateTagWeights($filteredTags);
            }

            return $filteredTags;

        } catch (Exception $e) {
            return [];
        }
    }

    private function getTagPostsCount($tagId) {
        try {
            if (!API::hasModel('posts')) {
                return 0;
            }
            
            $allPosts = API::posts()->getAll();
            $count = 0;
            
            foreach ($allPosts as $post) {
                if ($post['status'] !== 'published') {
                    continue;
                }
                
                $postTags = API::posts()->getPostTags($post['id']);
                foreach ($postTags as $tag) {
                    if ($tag['id'] == $tagId) {
                        $count++;
                        break;
                    }
                }
            }
            
            return $count;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function calculateTagWeights($tags) {
        if (empty($tags)) {
            return [];
        }

        $minCount = min(array_column($tags, 'posts_count'));
        $maxCount = max(array_column($tags, 'posts_count'));
        $range = $maxCount - $minCount;

        if ($range == 0) {
            $range = 1;
        }

        foreach ($tags as &$tag) {
            $count = $tag['posts_count'] ?? 1;
            $weight = 1 + floor(($count - $minCount) / $range * 4);
            $tag['weight'] = (int)$weight;
            $tag['font_size'] = 0.8 + ($weight * 0.2);
        }

        return $tags;
    }

    public function getTagImageUrl($tag) {
        if (!empty($tag['image'])) {
            if (strpos($tag['image'], 'http') === 0 || strpos($tag['image'], '/') === 0) {
                return $tag['image'];
            }
            return '/uploads/tags/' . $tag['image'];
        }
        
        $defaultImage = \SettingsHelper::get('controller_tags', 'default_tag_image');
        if (!empty($defaultImage)) {
            return '/uploads/settings/tags/' . $defaultImage;
        }
        
        return '/uploads/default/default-tag.jpg';
    }

    public function processFrontend($settings = [], $templateName = null): string {

        $tags = $this->getTags($settings);

        $this->tags = $tags;

        return parent::processFrontend($settings, $templateName);
    }
}