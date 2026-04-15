<?php

/**
* Поле типа "выпадающий список" для системы пользовательских полей
* @package Fields
*/
class SelectField extends BaseField {

    /**
    * Возвращает тип поля
    * @return string 'select'
    */
    public function getType(): string {
        return 'select';
    }
    
    /**
    * Возвращает отображаемое название типа поля 
    * @return string 'Список'
    */
    public function getName(): string {
        return 'Список';
    }
    
    /**
    * Генерирует HTML для редактирования поля в форме 
    * @param mixed $value Текущее значение поля
    * @param string $entityType Тип сущности (post, user, category и т.д.)
    * @param int $entityId ID сущности
    * @return string HTML-код для редактирования
    */
    public function renderInput($value, $entityType, $entityId): string {
        $safeValue = ($value === null) ? '' : $value;
        
        $required = isset($this->config['required']) && $this->config['required'] ? 'required' : '';
        $options = $this->config['options'] ?? [];
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        
        $html = "<select name='{$fieldName}' class='form-select form-select-sm' {$required}>";
        $html .= "<option value=''>-- Выберите --</option>";
        
        foreach ($options as $optionValue => $optionLabel) {
            $selected = ($safeValue == $optionValue) ? 'selected' : '';
            $html .= "<option value='" . html($optionValue, ENT_QUOTES, 'UTF-8') . "' {$selected}>" . 
                     html($optionLabel, ENT_QUOTES, 'UTF-8') . "</option>";
        }
        
        $html .= "</select>";
        return $html;
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
            return '<span class="text-muted">Не выбрано</span>';
        }
        
        $options = $this->config['options'] ?? [];
        $label = $options[$value] ?? $value;
        
        return "<span class='field-select'>" . html($label, ENT_QUOTES, 'UTF-8') . "</span>";
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
        
        $options = $this->config['options'] ?? [];
        $label = $options[$value] ?? $value;
        
        return "<span class='badge bg-secondary'>" . html($label, ENT_QUOTES, 'UTF-8') . "</span>";
    }

    /**
    * Валидирует значение поля
    * @param mixed $value Значение для проверки
    * @return bool true если значение корректно
    */
    public function validate($value): bool {
        if (isset($this->config['required']) && $this->config['required']) {
            if (empty($value) && $value !== '0') {
                return false;
            }
        }
        
        if (!empty($value) && $value !== '0') {
            $options = $this->config['options'] ?? [];
            if (!isset($options[$value])) {
                return false;
            }
        }
        
        return true;
    }

    /**
    * Возвращает шорткод для поля выбора
    * @return string Имя шорткода
    */
    public function getShortcode(): string {
        $systemName = $this->getSystemName();
        $entityType = $this->getEntityType();
        
        $shortcodeName = $entityType . '_' . $systemName;
        
        Shortcodes::add($shortcodeName, function($attrs, $content = null) {
            if ($content !== null) {
                return $this->renderPairedSelectShortcode($attrs, $content);
            }
            return $this->renderSimpleSelectShortcode($attrs);
        });
        
        return $shortcodeName;
    }

    /**
    * Рендерит простой шорткод для select
    * @param array $attrs Атрибуты шорткода
    * @return string Значение или метка выбранной опции
    */
    private function renderSimpleSelectShortcode($attrs): string {
        $entityId = $attrs['id'] ?? $this->entityId;
        $value = $this->getValue($entityId);
        
        if ($value === null || $value === '') {
            return $attrs['default'] ?? '';
        }
        
        $options = $this->config['options'] ?? [];
        $displayValue = $options[$value] ?? $value;
        
        if (isset($attrs['return']) && $attrs['return'] === 'value') {
            return html($value, ENT_QUOTES, 'UTF-8');
        }
        
        return html($displayValue, ENT_QUOTES, 'UTF-8');
    }

    /**
    * Рендерит парный шорткод для select
    * @param array $attrs Атрибуты шорткода
    * @param string $content Содержимое шорткода (шаблон)
    * @return string HTML результат
    */
    private function renderPairedSelectShortcode($attrs, $content): string {
        $entityId = $attrs['id'] ?? $this->entityId;
        $value = $this->getValue($entityId);
        
        if ($value === null || $value === '') {
            return $attrs['default'] ?? '';
        }
        
        $options = $this->config['options'] ?? [];
        $displayValue = $options[$value] ?? $value;
        
        $result = $content;
        $result = str_replace('{value}', html($value, ENT_QUOTES, 'UTF-8'), $result);
        $result = str_replace('{label}', html($displayValue, ENT_QUOTES, 'UTF-8'), $result);
        
        return $result;
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $options = $this->config['options'] ?? [];
        $optionsText = '';
        
        foreach ($options as $value => $label) {
            $optionsText .= html($value, ENT_QUOTES, 'UTF-8') . "|" . 
                           html($label, ENT_QUOTES, 'UTF-8') . "\n";
        }
        
        $defaultValue = html($this->config['default_value'] ?? '', ENT_QUOTES, 'UTF-8');
        
        return "
            <div class='mb-3'>
                <label class='form-label'>Опции списка</label>
                <textarea class='form-control' name='config[options_text]' rows='6' placeholder='значение|Название опции'>" . trim($optionsText) . "</textarea>
                <div class='form-text'>Каждая опция с новой строки в формате: значение|Название<br>Пример:<br>red|Красный<br>green|Зеленый<br>blue|Синий</div>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Значение по умолчанию</label>
                <input type='text' class='form-control' name='config[default_value]' value='{$defaultValue}' placeholder='значение из списка'>
                <div class='form-text'>Введите одно из значений из списка выше</div>
            </div>
        ";
    }
    
    /**
    * Обрабатывает конфигурацию поля после отправки формы
    * @param array $config Исходная конфигурация
    * @return array Обработанная конфигурация
    */
    public function processConfig($config) {
        if (isset($config['options_text'])) {
            $options = [];
            $lines = explode("\n", $config['options_text']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $parts = explode('|', $line, 2);
                    if (count($parts) === 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);
                        if (!empty($key) && !empty($value)) {
                            $options[$key] = $value;
                        }
                    }
                }
            }
            $config['options'] = $options;
            unset($config['options_text']);
        }
        return $config;
    }
    
    /**
    * Подготавливает конфигурацию для отображения в форме 
    * @param array $config Конфигурация поля
    * @return array Подготовленная конфигурация
    */
    public function prepareConfigForForm(array $config): array {
        if (isset($config['options']) && is_array($config['options'])) {
            $optionsText = '';
            foreach ($config['options'] as $value => $label) {
                $optionsText .= html($value, ENT_QUOTES, 'UTF-8') . "|" . 
                               html($label, ENT_QUOTES, 'UTF-8') . "\n";
            }
            $config['options_text'] = trim($optionsText);
        }
        return $config;
    }
}