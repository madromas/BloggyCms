<?php

namespace categories;

class CategoriesSettings {
    public static function getForm($currentSettings) {
        $fieldsets = [
            new \Fieldset('Общие настройки', [
                'icon' => 'bi bi-palette',
                'columns' => '6',
                'fields' => [
                    \FieldFactory::checkbox('show_stat', [
                        'title' => 'Отображать блок со статистикой над списком категорий',
                        'default' => true,
                        'switch' => true
                    ]),
                    \FieldFactory::checkbox('show_search', [
                        'title' => 'Отображать блок поиска над списком категорий',
                        'default' => true,
                        'switch' => true
                    ]),
                    \FieldFactory::checkbox('show_info', [
                        'title' => 'Отображать блок подсказок над списком категорий',
                        'hint' => 'В случайном порядке будут показываться полезные советы по работе с категориями.',
                        'default' => false,
                        'switch' => true
                    ]),
                    \FieldFactory::checkbox('show_stat_list', [
                        'title' => 'Отображать мини-статистику в списке категорий',
                        'hint' => 'Будут показаны количество постов категории и порядок отображения',
                        'default' => false,
                        'switch' => true
                    ]),
                ]
            ]),
            new \Fieldset('Настройки фронтенда', [
                'icon' => 'bi bi-display',
                'columns' => '6',
                'fields' => [
                    \FieldFactory::select('category_layout', [
                        'title' => 'Раскладка категорий на фронте',
                        'default' => 'grid',
                        'options' => [
                            'grid' => 'Сетка (плитка)',
                            'list' => 'Список',
                            'cards' => 'Карточки'
                        ]
                    ]),
                    \FieldFactory::number('categories_per_page', [
                        'title' => 'Количество категорий на странице',
                        'default' => 12,
                        'min' => 1,
                        'max' => 100
                    ]),
                    \FieldFactory::checkbox('show_category_images', [
                        'title' => 'Показывать изображения категорий',
                        'default' => true,
                        'switch' => true
                    ]),
                    \FieldFactory::checkbox('show_category_descriptions', [
                        'title' => 'Показывать описания категорий',
                        'default' => true,
                        'switch' => true
                    ]),
                    \FieldFactory::checkbox('show_post_counts', [
                        'title' => 'Показывать количество постов',
                        'default' => true,
                        'switch' => true
                    ]),
                    \FieldFactory::select('categories_order', [
                        'title' => 'Порядок сортировки категорий',
                        'default' => 'name',
                        'options' => [
                            'name' => 'По названию',
                            'posts_count' => 'По количеству постов',
                            'created_at' => 'По дате создания',
                            'sort_order' => 'По порядку сортировки'
                        ]
                    ]),
                ]
            ]),
        ];
        ob_start();
        ?>
        <div class="row">
        <?php foreach ($fieldsets as $fieldset) { ?>
        <div class="col-md-12">
        <?= $fieldset->render($currentSettings) ?>
        </div>
        <?php } ?>
        </div>
        <?php
        return ob_get_clean();
    }
}