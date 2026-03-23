<?php

/**
 * Блок "Кнопка вверх"
 * Добавляет на страницу плавно появляющуюся кнопку для прокрутки страницы вверх.
 */
class ScrollToTopBlock extends BaseHtmlBlock
{
    public function getName(): string
    {
        return "Кнопка вверх";
    }

    public function getSystemName(): string
    {
        return "ScrollToTopBlock";
    }

    public function getDescription(): string
    {
        return "Добавляет кнопку для плавной прокрутки страницы вверх. Появляется после прокрутки.";
    }

    public function getShortDescription(): string
    {
        return "Кнопка прокрутки вверх";
    }

    public function getIcon(): string
    {
        return 'bi bi-arrow-up-circle';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTemplate(): string
    {
        return 'all';
    }

    public function getSettingsForm($currentSettings = []): string
    {
        $settings = array_merge($this->getDefaultSettings(), $currentSettings);

        $fieldsets = [];

        $fieldsets[] = new \Fieldset('Основные настройки', [
            'icon' => 'bi bi-gear',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::number('scroll_threshold', [
                    'title' => 'Порог появления (px)',
                    'hint' => 'Через сколько пикселей прокрутки показывать кнопку',
                    'default' => $settings['scroll_threshold'] ?? 300,
                    'min' => 0,
                    'max' => 1000,
                    'step' => 10,
                    'column' => '6',
                ]),
                \FieldFactory::number('animation_duration', [
                    'title' => 'Длительность анимации (мс)',
                    'hint' => 'Скорость прокрутки страницы вверх',
                    'default' => $settings['animation_duration'] ?? 500,
                    'min' => 100,
                    'max' => 2000,
                    'step' => 50,
                    'column' => '6',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Внешний вид', [
            'icon' => 'bi bi-palette',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::select('position', [
                    'title' => 'Позиция кнопки',
                    'options' => [
                        'bottom-right' => 'Снизу справа',
                        'bottom-left' => 'Снизу слева',
                    ],
                    'default' => $settings['position'] ?? 'bottom-right',
                    'column' => '6',
                ]),
                \FieldFactory::number('offset_bottom', [
                    'title' => 'Отступ снизу (px)',
                    'default' => $settings['offset_bottom'] ?? 20,
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'column' => '6',
                ]),
                \FieldFactory::number('offset_side', [
                    'title' => 'Отступ сбоку (px)',
                    'default' => $settings['offset_side'] ?? 20,
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'column' => '6',
                ]),
                \FieldFactory::select('size', [
                    'title' => 'Размер кнопки',
                    'options' => [
                        'sm' => 'Маленькая (40px)',
                        'md' => 'Средняя (50px)',
                        'lg' => 'Большая (60px)',
                    ],
                    'default' => $settings['size'] ?? 'md',
                    'column' => '6',
                ]),
                \FieldFactory::select('shape', [
                    'title' => 'Форма',
                    'options' => [
                        'circle' => 'Круглая',
                        'rounded' => 'Скругленная',
                    ],
                    'default' => $settings['shape'] ?? 'circle',
                    'column' => '6',
                ]),
                \FieldFactory::color('background_color', [
                    'title' => 'Цвет фона',
                    'preset' => 'website',
                    'default' => $settings['background_color'] ?? '#2563eb',
                    'column' => '6',
                ]),
                \FieldFactory::color('text_color', [
                    'title' => 'Цвет иконки',
                    'preset' => 'basic',
                    'default' => $settings['text_color'] ?? '#ffffff',
                    'column' => '6',
                ]),
                \FieldFactory::checkbox('show_shadow', [
                    'title' => 'Показывать тень',
                    'default' => $settings['show_shadow'] ?? 1,
                    'switch' => true,
                    'column' => '12',
                ]),
                \FieldFactory::icon('custom_icon', [
                    'title' => 'Своя иконка',
                    'hint' => 'Оставьте пустым для использования стандартной иконки',
                    'default' => $settings['custom_icon'] ?? '',
                    'column' => '12',
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

    private function getDefaultSettings(): array
    {
        return [
            'scroll_threshold' => 300,
            'animation_duration' => 500,
            'position' => 'bottom-right',
            'offset_bottom' => 20,
            'offset_side' => 20,
            'size' => 'md',
            'shape' => 'circle',
            'background_color' => '#2563eb',
            'text_color' => '#ffffff',
            'show_shadow' => 1,
            'custom_icon' => '',
        ];
    }

    public function validateSettings($settings): array
    {
        return [true, []];
    }

    public function prepareSettings($settings): array
    {
        if (!is_array($settings)) {
            return $this->getDefaultSettings();
        }

        $prepared = array_merge($this->getDefaultSettings(), $settings);
        $prepared['scroll_threshold'] = (int)($settings['scroll_threshold'] ?? 300);
        $prepared['animation_duration'] = (int)($settings['animation_duration'] ?? 500);
        $prepared['offset_bottom'] = (int)($settings['offset_bottom'] ?? 20);
        $prepared['offset_side'] = (int)($settings['offset_side'] ?? 20);
        $prepared['show_shadow'] = isset($settings['show_shadow']) ? (int)$settings['show_shadow'] : 1;
        $prepared['custom_icon'] = trim($settings['custom_icon'] ?? '');

        return $prepared;
    }

}