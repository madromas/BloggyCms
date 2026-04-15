<?php

/**
* Поле типа "цвет" для системы пользовательских полей
* Использует библиотеку Pickr (https://github.com/simonwep/pickr)
* @package Fields
*/
class ColorField extends BaseField {
    
    private static $assetsLoaded = false;
    
    /**
    * Возвращает тип поля 
    * @return string 'color'
    */
    public function getType(): string {
        return 'color';
    }
    
    /**
    * Возвращает отображаемое название типа поля 
    * @return string 'Выбор цвета'
    */
    public function getName(): string {
        return 'Выбор цвета';
    }
    
    /**
    * Загружает ресурсы Pickr
    */
    private function loadPickrAssets(): void {
        if (self::$assetsLoaded) {
            return;
        }
        
        if (function_exists('admin_css')) {
            admin_css('templates/default/admin/assets/css/pickr/monolith.min.css');
            admin_js('templates/default/admin/assets/js/pickr/pickr.min.js');
            admin_js('templates/default/admin/assets/js/pickr/pickr-init.js');
            
            $config = [
                'iconsPath' => BASE_URL . '/templates/default/admin/icons/'
            ];
            
            if (function_exists('admin_bottom_js')) {
                admin_bottom_js('<script>window.pickrConfig = ' . json_encode($config) . ';</script>');
            }
        }
        
        self::$assetsLoaded = true;
    }
    
    /**
    * Генерирует HTML для редактирования поля в форме
    * @param mixed $value Текущее значение поля
    * @param string $entityType Тип сущности (post, user, category и т.д.)
    * @param int $entityId ID сущности
    * @return string HTML-код для редактирования
    */
    public function renderInput($value, $entityType, $entityId): string {
        $value = $value ?? '#000000';
        $required = isset($this->config['required']) && $this->config['required'] ? 'required' : '';
        
        $this->loadPickrAssets();
        
        $fieldId = 'color-picker-' . $this->systemName . '-' . uniqid();
        $pickrOptions = $this->getPickrOptions();
        $pickrOptionsJson = json_encode($pickrOptions);
        
        $fieldName = 'field_' . $this->systemName;
        
        return '
            <div class="color-field-wrapper">
                <div class="input-group input-group-sm" style="width: auto;">
                    <input type="text" 
                           id="' . $fieldId . '"
                           name="' . $fieldName . '"
                           value="' . html($value) . '"
                           class="form-control pickr-color-picker"
                           placeholder="#000000"
                           style="width: 130px;"
                           data-pickr-options=\'' . htmlspecialchars($pickrOptionsJson) . '\'
                           ' . $required . '>
                </div>
            </div>
        ';
    }
    
    /**
    * Получает настройки для Pickr
    * @return array
    */
    protected function getPickrOptions(): array {
        $defaultOptions = [
            'showInput' => true,
            'showAlpha' => false,
            'allowEmpty' => true,
            'palette' => [
                '#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff',
                '#000000', '#ffffff', '#808080', '#800000', '#808000', '#008000',
                '#800080', '#008080', '#000080', '#ffa500', '#ffc0cb', '#a52a2a',
                '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14',
                '#ffc107', '#198754', '#20c997', '#0dcaf0', '#6c757d', '#343a40'
            ]
        ];
        
        if (isset($this->config['preset'])) {
            switch ($this->config['preset']) {
                case 'basic':
                    return [
                        'showInput' => false,
                        'showAlpha' => false,
                        'allowEmpty' => true,
                        'palette' => []
                    ];
                case 'advanced':
                    return [
                        'showInput' => true,
                        'showAlpha' => true,
                        'allowEmpty' => true,
                        'palette' => $defaultOptions['palette']
                    ];
                case 'minimal':
                    return [
                        'showInput' => false,
                        'showAlpha' => false,
                        'allowEmpty' => false,
                        'palette' => [
                            '#000000', '#666666', '#999999', '#cccccc', '#ffffff',
                            '#ff0000', '#00ff00', '#0000ff'
                        ]
                    ];
                case 'full':
                    return $defaultOptions;
                default:
                    if (isset($this->config['pickr']) && is_array($this->config['pickr'])) {
                        return array_merge($defaultOptions, $this->config['pickr']);
                    }
                    return $defaultOptions;
            }
        }
        
        return $defaultOptions;
    }
    
    /**
    * Генерирует HTML для отображения значения поля в детальном просмотре 
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения
    */
    public function renderDisplay($value, $entityType, $entityId): string {
        if (empty($value) && $value !== '0') {
            return '<span class="text-muted">Не указано</span>';
        }
        
        $escapedValue = html($value, ENT_QUOTES, 'UTF-8');
        
        return "
            <div class='d-flex align-items-center gap-2'>
                <div style='width: 20px; height: 20px; background-color: {$escapedValue}; border: 1px solid #ddd; border-radius: 3px;'></div>
                <code>{$escapedValue}</code>
            </div>
        ";
    }
    
    /**
    * Генерирует HTML для отображения значения поля в списке 
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения в списке
    */
    public function renderList($value, $entityType, $entityId): string {
        if (empty($value) && $value !== '0') {
            return '<span class="text-muted">-</span>';
        }
        
        $escapedValue = html($value, ENT_QUOTES, 'UTF-8');
        
        return "<div style='width: 16px; height: 16px; background-color: {$escapedValue}; border: 1px solid #ddd; border-radius: 2px;' title='{$escapedValue}'></div>";
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели 
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $defaultValue = html($this->config['default_value'] ?? '#000000', ENT_QUOTES, 'UTF-8');
        $preset = $this->config['preset'] ?? 'default';
        
        $presetOptions = [
            'default' => 'Стандартный',
            'basic' => 'Базовый (без палитры)',
            'advanced' => 'Расширенный (с альфа-каналом)',
            'minimal' => 'Минимальный',
            'full' => 'Полный'
        ];
        
        $presetHtml = '';
        foreach ($presetOptions as $value => $label) {
            $selected = $preset === $value ? 'selected' : '';
            $presetHtml .= "<option value='{$value}' {$selected}>" . html($label) . "</option>";
        }
        
        return "
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Пресет оформления</label>
                        <select class='form-select' name='config[preset]'>
                            {$presetHtml}
                        </select>
                        <div class='form-text'>Предустановленный стиль палитры цветов</div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Значение по умолчанию</label>
                        <input type='color' class='form-control form-control-color' name='config[default_value]' value='{$defaultValue}'>
                        <div class='form-text'>Цвет, который будет установлен по умолчанию</div>
                    </div>
                </div>
            </div>
        ";
    }
    
    /**
    * Валидирует значение поля
    * @param mixed $value Значение для проверки
    * @return bool true если значение корректно
    */
    public function validate($value): bool {
        if (isset($this->config['required']) && $this->config['required'] && empty($value)) {
            return false;
        }
        
        if (!empty($value) && !preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value)) {
            return false;
        }
        
        return true;
    }
}