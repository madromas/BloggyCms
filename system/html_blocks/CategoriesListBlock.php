<?php

class CategoriesListBlock extends BaseHtmlBlock {
    
    public function getName(): string {
        return "Categories List";
    }

    public function getSystemName(): string {
        return "CategoriesListBlock";
    }

    public function getDescription(): string {
        return "Блок со списком категорий в стиле карточек";
    }

    public function getVersion(): string {
        return '1.0.0';
    }

    public function getTemplate(): string {
        return 'default';
    }

    public $categories = [];

    public function getSettingsForm($currentSettings = []): string {
        
        $settings = array_merge([], $currentSettings);
        
        $fieldsets[] = new \Fieldset('Заголовочная часть', [
            'icon' => 'bi bi-pencil',
            'columns' => '12',
            'fields' => [
                \FieldFactory::string('badge', [
                    'title' => 'Бейдж',
                    'default' => $settings['badge'] ?? 'Рубрики',
                    'placeholder' => 'Например: Категории',
                ]),
                \FieldFactory::string('title', [
                    'title' => 'Заголовок',
                    'default' => $settings['title'] ?? 'Исследуйте <span class="highlight">по рубрикам</span>',
                    'placeholder' => 'Используйте <span class="highlight"> для выделения',
                ]),
                \FieldFactory::textarea('description', [
                    'title' => 'Описание',
                    'default' => $settings['description'] ?? 'Выбирайте интересующие вас темы и погружайтесь в мир знаний',
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
                        'cards' => 'Карточки',
                        'list' => 'Список',
                        'grid' => 'Сетка',
                        'compact' => 'Компактный',
                    ],
                    'default' => 'cards',
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
                    'title' => 'Количество категорий',
                    'default' => 6,
                    'min' => 1,
                    'max' => 50,
                    'hint' => '0 = все категории',
                ]),
                \FieldFactory::checkbox('show_hierarchy', [
                    'title' => 'Показывать иерархию',
                    'default' => 0,
                    'switch' => true,
                    'hint' => 'Отображать вложенные категории',
                ]),
                \FieldFactory::checkbox('show_post_count', [
                    'title' => 'Показывать количество постов',
                    'default' => 1,
                    'switch' => true,
                ]),
                \FieldFactory::checkbox('show_empty', [
                    'title' => 'Показывать пустые категории',
                    'default' => 0,
                    'switch' => true,
                    'hint' => 'Категории без постов',
                ]),
                \FieldFactory::checkbox('show_icon', [
                    'title' => 'Показывать иконки',
                    'default' => 1,
                    'switch' => true,
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Изображения категорий', [
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
                        'posts_count DESC' => 'По популярности (сначала с большим кол-вом постов)',
                        'posts_count ASC' => 'По популярности (сначала с малым кол-вом постов)',
                        'id ASC' => 'По ID (сначала старые)',
                        'id DESC' => 'По ID (сначала новые)',
                    ],
                    'default' => 'name ASC',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Фильтрация', [
            'icon' => 'bi bi-funnel',
            'columns' => '12',
            'fields' => [
                \FieldFactory::checkbox('filter_by_parent', [
                    'title' => 'Только родительские категории',
                    'default' => 0,
                    'switch' => true,
                    'show' => 'field:show_hierarchy = 0',
                ]),
                \FieldFactory::checkbox('exclude_current', [
                    'title' => 'Исключить текущую категорию',
                    'default' => 0,
                    'switch' => true,
                    'hint' => 'На странице категории не показывать её же в списке',
                ]),
                \FieldFactory::textarea('exclude_ids', [
                    'title' => 'Исключить категории (ID через запятую)',
                    'default' => '',
                    'placeholder' => '5, 12, 8',
                    'rows' => 2,
                    'hint' => 'ID категорий, которые не нужно показывать',
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

    private function getCategories($settings) {
        try {
            if (!API::hasModel('categories')) {
                return [];
            }

            $limit = (int)($settings['limit'] ?? 6);
            $showEmpty = !empty($settings['show_empty']);
            $orderBy = $settings['order_by'] ?? 'name ASC';
            $filterParent = !empty($settings['filter_by_parent']);
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

            if (!empty($settings['exclude_current']) && isset($_GET['id'])) {
                $excludeIds[] = (int)$_GET['id'];
            } elseif (!empty($settings['exclude_current']) && isset($_GET['slug'])) {

            }

            $categories = API::categories()->getAll();

            if (empty($categories)) {
                return [];
            }

            $filteredCategories = [];

            foreach ($categories as $category) {
                if ($filterParent && !empty($category['parent_id'])) {
                    continue;
                }

                if (in_array($category['id'], $excludeIds)) {
                    continue;
                }

                if (!$showEmpty && ($category['posts_count'] ?? 0) == 0) {
                    continue;
                }

                $filteredCategories[] = $category;
            }

            list($field, $direction) = explode(' ', $orderBy);

            usort($filteredCategories, function($a, $b) use ($field, $direction) {
                $aVal = $a[$field] ?? '';
                $bVal = $b[$field] ?? '';

                if ($field === 'posts_count') {
                    $aVal = (int)$aVal;
                    $bVal = (int)$bVal;
                    $result = $aVal - $bVal;
                } else {
                    $result = strcmp((string)$aVal, (string)$bVal);
                }

                return $direction === 'DESC' ? -$result : $result;
            });

            if ($limit > 0) {
                $filteredCategories = array_slice($filteredCategories, 0, $limit);
            }

            if (!empty($settings['show_hierarchy'])) {
                $filteredCategories = $this->buildHierarchy($filteredCategories);
            }

            return $filteredCategories;

        } catch (Exception $e) {
            return [];
        }
    }

    private function buildHierarchy($categories, $parentId = 0) {
        $result = [];

        foreach ($categories as $category) {
            if (($category['parent_id'] ?? 0) == $parentId) {
                $children = $this->buildHierarchy($categories, $category['id']);
                if (!empty($children)) {
                    $category['children'] = $children;
                }
                $result[] = $category;
            }
        }

        return $result;
    }

    public function getCategoryImageUrl($category) {
        if (!empty($category['image'])) {
            if (strpos($category['image'], 'http') === 0 || strpos($category['image'], '/') === 0) {
                return $category['image'];
            }
            return '/uploads/images/' . $category['image'];
        }
        
        return '/templates/' . DEFAULT_TEMPLATE . '/front/assets/img/default-category.jpg';
    }

    public function processFrontend($settings = [], $templateName = null): string {

        $categories = $this->getCategories($settings);

        $this->categories = $categories;

        return parent::processFrontend($settings, $templateName);
    }
}