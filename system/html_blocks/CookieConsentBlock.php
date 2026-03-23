<?php

/**
 * Блок "Согласие с cookies"
 * Отображает уведомление о согласии с использованием cookies с возможностью настройки.
 */
class CookieConsentBlock extends BaseHtmlBlock
{
    public function getName(): string
    {
        return "Согласие с cookies";
    }

    public function getSystemName(): string
    {
        return "CookieConsentBlock";
    }

    public function getDescription(): string
    {
        return "Отображает уведомление о согласии с использованием cookies. После согласия скрывается и запоминает выбор пользователя.";
    }

    public function getShortDescription(): string
    {
        return "Уведомление о cookies";
    }

    public function getIcon(): string
    {
        return 'bi bi-cookie';
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

        $fieldsets[] = new \Fieldset('Текст уведомления', [
            'icon' => 'bi bi-pencil',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::textarea('message', [
                    'title' => 'Текст сообщения',
                    'default' => $settings['message'] ?? 'Мы используем cookies для улучшения работы сайта. Продолжая использовать сайт, вы соглашаетесь с нашей политикой обработки данных.',
                    'rows' => 4,
                    'column' => '12',
                ]),
                \FieldFactory::string('accept_button_text', [
                    'title' => 'Текст кнопки согласия',
                    'default' => $settings['accept_button_text'] ?? 'Принять',
                    'column' => '6',
                ]),
                \FieldFactory::string('decline_button_text', [
                    'title' => 'Текст кнопки отказа',
                    'default' => $settings['decline_button_text'] ?? 'Отклонить',
                    'column' => '6',
                ]),
                \FieldFactory::string('policy_link_text', [
                    'title' => 'Текст ссылки на политику',
                    'default' => $settings['policy_link_text'] ?? 'Политика конфиденциальности',
                    'column' => '6',
                ]),
                \FieldFactory::string('policy_url', [
                    'title' => 'Ссылка на политику',
                    'default' => $settings['policy_url'] ?? '/privacy',
                    'placeholder' => '/privacy-policy',
                    'column' => '6',
                ]),
                \FieldFactory::checkbox('show_policy_link', [
                    'title' => 'Показывать ссылку на политику',
                    'default' => $settings['show_policy_link'] ?? 1,
                    'switch' => true,
                    'column' => '12',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Внешний вид', [
            'icon' => 'bi bi-palette',
            'columns' => 'custom',
            'fields' => [
                \FieldFactory::select('position', [
                    'title' => 'Позиция уведомления',
                    'options' => [
                        'bottom' => 'Внизу экрана',
                        'top' => 'Вверху экрана',
                    ],
                    'default' => $settings['position'] ?? 'bottom',
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
                    'column' => '6',
                ]),
                \FieldFactory::color('background_color', [
                    'title' => 'Цвет фона',
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
                    'title' => 'Акцентный цвет (кнопки)',
                    'preset' => 'website',
                    'default' => $settings['accent_color'] ?? '#2563eb',
                    'column' => '12',
                ]),
                \FieldFactory::checkbox('show_shadow', [
                    'title' => 'Показывать тень',
                    'default' => $settings['show_shadow'] ?? 1,
                    'switch' => true,
                    'column' => '12',
                ]),
            ]
        ]);

        $fieldsets[] = new \Fieldset('Дополнительно', [
            'icon' => 'bi bi-gear',
            'columns' => '12',
            'fields' => [
                \FieldFactory::string('cookie_name', [
                    'title' => 'Название cookie',
                    'default' => $settings['cookie_name'] ?? 'cookie_consent',
                    'hint' => 'Имя файла cookie для сохранения согласия',
                    'column' => '6',
                ]),
                \FieldFactory::number('cookie_expiry_days', [
                    'title' => 'Срок хранения (дней)',
                    'default' => $settings['cookie_expiry_days'] ?? 365,
                    'min' => 1,
                    'max' => 730,
                    'step' => 1,
                    'column' => '6',
                ]),
                \FieldFactory::checkbox('auto_show', [
                    'title' => 'Автоматически показывать',
                    'default' => $settings['auto_show'] ?? 1,
                    'switch' => true,
                    'hint' => 'Если отключено, показывать блок можно через JS событие',
                    'column' => '12',
                ]),
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
            <?php foreach ($fieldsets as $fieldset) { ?>
            <div class="col-12"><?= $fieldset->render($settings) ?></div>
            <?php } ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function getDefaultSettings(): array
    {
        return [
            'message' => 'Мы используем cookies для улучшения работы сайта. Продолжая использовать сайт, вы соглашаетесь с нашей политикой обработки данных.',
            'accept_button_text' => 'Принять',
            'decline_button_text' => 'Отклонить',
            'policy_link_text' => 'Политика конфиденциальности',
            'policy_url' => '/privacy',
            'show_policy_link' => 1,
            'position' => 'bottom',
            'theme' => 'light',
            'accent_color' => '#2563eb',
            'show_shadow' => 1,
            'cookie_name' => 'cookie_consent',
            'cookie_expiry_days' => 365,
            'auto_show' => 1,
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

        $textFields = ['message', 'accept_button_text', 'decline_button_text', 'policy_link_text', 'policy_url', 'cookie_name', 'custom_css_class', 'custom_id'];
        foreach ($textFields as $field) {
            if (isset($prepared[$field])) {
                $prepared[$field] = trim($prepared[$field]);
            }
        }

        $prepared['show_policy_link'] = isset($settings['show_policy_link']) ? (int)$settings['show_policy_link'] : 1;
        $prepared['show_shadow'] = isset($settings['show_shadow']) ? (int)$settings['show_shadow'] : 1;
        $prepared['auto_show'] = isset($settings['auto_show']) ? (int)$settings['auto_show'] : 1;
        $prepared['cookie_expiry_days'] = (int)($settings['cookie_expiry_days'] ?? 365);

        return $prepared;
    }

    public function processFrontend($settings = [], $templateName = null): string
    {
        $data = $settings;
        $data['cookie_name'] = $settings['cookie_name'] ?? 'cookie_consent';
        $data['auto_show'] = !empty($settings['auto_show']);
        $data['cookie_expiry_days'] = (int)($settings['cookie_expiry_days'] ?? 365);

        return parent::processFrontend($data, $templateName);
    }
}