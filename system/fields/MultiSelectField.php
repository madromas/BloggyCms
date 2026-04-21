<?php

/**
* Поле типа "мультивыбор" для системы пользовательских полей
* @package Fields
*/
class MultiSelectField extends BaseField {
    
    /**
    * Возвращает тип поля
    * @return string 'multiselect'
    */
    public function getType(): string {
        return 'multiselect';
    }
    
    /**
    * Возвращает отображаемое название типа поля 
    * @return string 'Список: мультивыбор'
    */
    public function getName(): string {
        return LANG_FIELD_MULTISELECT_TITLE;
    }
    
    /**
    * Генерирует HTML для редактирования поля в форме
    * @param mixed $value Текущее значение поля (JSON строка)
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для редактирования
    */
    public function renderInput($value, $entityType, $entityId): string {
        $safeValue = ($value === null) ? '' : $value;
        
        $required = isset($this->config['required']) && $this->config['required'] ? 'required' : '';
        $options = $this->config['options'] ?? [];
        
        $selectedValues = [];
        if (!empty($safeValue)) {
            if (is_string($safeValue)) {
                $selectedValues = json_decode($safeValue, true) ?: [];
            } else {
                $selectedValues = (array)$safeValue;
            }
        }
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        
        $html = "<select name='{$fieldName}[]' class='form-select form-select-sm' multiple {$required} style='height: 120px;'>";
        
        foreach ($options as $optionValue => $optionLabel) {
            $selected = in_array($optionValue, $selectedValues) ? 'selected' : '';
            $html .= "<option value='" . html($optionValue, ENT_QUOTES, 'UTF-8') . "' {$selected}>" . 
                     html($optionLabel, ENT_QUOTES, 'UTF-8') . "</option>";
        }
        
        $html .= "</select>";
        $html .= "<div class='form-text'>" . LANG_FIELD_MULTISELECT_HINT . "</div>";
        
        return $html;
    }
    
    /**
    * Генерирует HTML для отображения значения поля в детальном просмотре
    * @param mixed $value Значение поля (JSON строка)
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения
    */
    public function renderDisplay($value, $entityType, $entityId): string {
        $items = $this->getIterableData($value, $entityType, $entityId);
        
        if (empty($items)) {
            return '<span class="text-muted">' . LANG_FIELD_MULTISELECT_NOT_SELECTED . '</span>';
        }
        
        $html = '<div class="field-multiselect d-flex flex-wrap gap-1">';
        foreach ($items as $item) {
            $html .= '<span class="badge bg-secondary">' . 
                    html($item['label'], ENT_QUOTES, 'UTF-8') . 
                    '</span>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    /**
    * Генерирует HTML для отображения значения поля в списке 
    * @param mixed $value Значение поля (JSON строка)
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения в списке
    */
    public function renderList($value, $entityType, $entityId): string {
        $items = $this->getIterableData($value, $entityType, $entityId);
        $count = count($items);
        
        if ($count === 0) {
            return '<span class="text-muted">-</span>';
        }
        
        $firstItem = $items[0];
        $display = html($firstItem['label'], ENT_QUOTES, 'UTF-8');
        if ($count > 1) {
            $display .= " +" . ($count - 1);
        }
        
        $allValues = array_column($items, 'value');
        return "<span class='badge bg-secondary' title='" . 
               html(implode(', ', $allValues), ENT_QUOTES, 'UTF-8') . 
               "'>{$display}</span>";
    }
    
    /**
    * Обрабатывает значение перед сохранением
    * @param mixed $value Исходное значение
    * @return string JSON строка
    */
    public function processValue($value) {
        if ($value === null) {
            return json_encode([]);
        }

        if (is_array($value)) {
            return json_encode(array_values($value));
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $value;
            }
        }
        
        return json_encode([]);
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
        
        return "
            <div class='mb-3'>
                <label class='form-label'>" . LANG_FIELD_MULTISELECT_OPTIONS_LABEL . "</label>
                <textarea class='form-control' name='config[options_text]' rows='6' placeholder='" . LANG_FIELD_MULTISELECT_OPTIONS_PLACEHOLDER . "'>" . trim($optionsText) . "</textarea>
                <div class='form-text'>" . LANG_FIELD_MULTISELECT_OPTIONS_HINT . "</div>
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
    * Валидирует значение поля
    * @param mixed $value Значение для проверки
    * @return bool true если значение корректно
    */
    public function validate($value): bool {
        if (isset($this->config['required']) && $this->config['required']) {
            if (empty($value)) return false;
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                return !empty($decoded);
            }
            return !empty($value);
        }
        return true;
    }
    
    /**
    * Получает значения как массив 
    * @param mixed $value Значение поля
    * @return array Массив выбранных значений
    */
    public function getValuesArray($value): array {
        if (empty($value)) {
            return [];
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return (array)$value;
    }
    
    /**
    * Возвращает шорткод для MultiSelectField
    * @return string Имя шорткода
    */
    public function getShortcode(): string {
        $systemName = $this->getSystemName();
        $entityType = $this->getEntityType();
        
        $shortcodeName = $entityType . '_' . $systemName;
        
        Shortcodes::add($shortcodeName, function($attrs, $content = null) {
            if ($content !== null) {
                return $this->renderPairedShortcode($attrs, $content);
            }
            return $this->renderSimpleShortcode($attrs);
        });
        
        return $shortcodeName;
    }
    
    /**
    * Рендерит простой шорткод (без содержимого)
    * @param array $attrs Атрибуты шорткода
    * @return string HTML результат
    */
    private function renderSimpleShortcode($attrs): string {
        $entityId = $attrs['id'] ?? $this->entityId;
        $value = $this->getValue($entityId);
        
        if (empty($value)) {
            return $attrs['default'] ?? '';
        }
        
        $selectedValues = json_decode($value, true) ?? [];
        $options = $this->config['options'] ?? [];
        
        $format = $attrs['format'] ?? 'list';
        $separator = $attrs['separator'] ?? ', ';
        $badgeClass = $attrs['badge_class'] ?? 'badge bg-secondary';
        
        if (empty($selectedValues)) {
            return $attrs['empty'] ?? LANG_FIELD_MULTISELECT_NOT_SELECTED;
        }
        
        switch ($format) {
            case 'badges':
                $labels = [];
                foreach ($selectedValues as $val) {
                    $label = $options[$val] ?? $val;
                    $labels[] = '<span class="' . html($badgeClass, ENT_QUOTES, 'UTF-8') . ' me-1">' . 
                               html($label, ENT_QUOTES, 'UTF-8') . '</span>';
                }
                return implode('', $labels);
                
            case 'comma':
                $labels = [];
                foreach ($selectedValues as $val) {
                    $labels[] = $options[$val] ?? $val;
                }
                return implode($separator, array_map(function($label) {
                    return html($label, ENT_QUOTES, 'UTF-8');
                }, $labels));
                
            case 'count':
                $count = count($selectedValues);
                if (isset($attrs['show_labels']) && $attrs['show_labels'] === 'true' && $count > 0) {
                    $firstValue = $selectedValues[0];
                    $firstLabel = $options[$firstValue] ?? $firstValue;
                    $result = html($firstLabel, ENT_QUOTES, 'UTF-8');
                    if ($count > 1) {
                        $result .= " +" . ($count - 1);
                    }
                    return $result;
                }
                return (string)$count;
                
            case 'list':
            default:
                $labels = [];
                foreach ($selectedValues as $val) {
                    $labels[] = $options[$val] ?? $val;
                }
                return '<ul class="list-unstyled mb-0"><li>' . 
                       implode('</li><li>', array_map(function($label) {
                           return html($label, ENT_QUOTES, 'UTF-8');
                       }, $labels)) . 
                       '</li></ul>';
        }
    }
    
    /**
    * Рендерит парный шорткод для итерации по выбранным значениям
    * @param array $attrs Атрибуты шорткода
    * @param string $content Содержимое шорткода (шаблон для каждого элемента)
    * @return string HTML результат
    */
    private function renderPairedShortcode($attrs, $content): string {
        $entityId = $attrs['id'] ?? $this->entityId;
        $value = $this->getValue($entityId);
        
        if (empty($value)) {
            return $attrs['empty'] ?? '';
        }
        
        $selectedValues = json_decode($value, true) ?? [];
        $options = $this->config['options'] ?? [];
        
        $result = '';
        $counter = 0;
        
        foreach ($selectedValues as $index => $val) {
            $counter++;
            $itemContent = $content;
            
            $itemContent = str_replace('{index}', $counter, $itemContent);
            $itemContent = str_replace('{value}', html($val, ENT_QUOTES, 'UTF-8'), $itemContent);
            $itemContent = str_replace('{label}', html($options[$val] ?? $val, ENT_QUOTES, 'UTF-8'), $itemContent);
            
            $itemContent = $this->processConditionalBlocks($itemContent, $counter, count($selectedValues));
            
            $result .= $itemContent;
        }
        
        return $result;
    }

    /**
    * Обрабатывает условные блоки в парном шорткоде 
    * @param string $content Содержимое для обработки
    * @param int $counter Текущая позиция (1-индексированная)
    * @param int $total Общее количество элементов
    * @return string Обработанное содержимое
    */
    private function processConditionalBlocks($content, $counter, $total) {

        if ($counter === 1) {
            $content = preg_replace('/\{if_first\}(.*?)\{\/if_first\}/s', '$1', $content);
        } else {
            $content = preg_replace('/\{if_first\}(.*?)\{\/if_first\}/s', '', $content);
        }
        
        if ($counter === $total) {
            $content = preg_replace('/\{if_last\}(.*?)\{\/if_last\}/s', '$1', $content);
        } else {
            $content = preg_replace('/\{if_last\}(.*?)\{\/if_last\}/s', '', $content);
        }
        
        if ($counter % 2 === 0) {
            $content = preg_replace('/\{if_even\}(.*?)\{\/if_even\}/s', '$1', $content);
            $content = preg_replace('/\{if_odd\}(.*?)\{\/if_odd\}/s', '', $content);
        } else {
            $content = preg_replace('/\{if_even\}(.*?)\{\/if_even\}/s', '', $content);
            $content = preg_replace('/\{if_odd\}(.*?)\{\/if_odd\}/s', '$1', $content);
        }
        
        return $content;
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
    
    /**
    * Форматирует значение для шорткода
    * @param mixed $value Значение поля
    * @param array $attrs Атрибуты шорткода
    * @return string Отформатированное значение
    */
    protected function formatShortcodeValue($value, $attrs): string {
        return $this->renderSimpleShortcode(array_merge($attrs, ['id' => $this->entityId]));
    }
}