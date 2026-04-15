<?php

/**
* Поле типа "число" для системы пользовательских полей
* @package Fields
*/
class NumberField extends BaseField {
    
    /**
    * Возвращает тип поля
    * @return string 'number'
    */
    public function getType(): string {
        return 'number';
    }
    
    /**
    * Возвращает отображаемое название типа поля
    * @return string 'Число'
    */
    public function getName(): string {
        return 'Число';
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
        
        $min = html($this->config['min'] ?? '', ENT_QUOTES, 'UTF-8');
        $max = html($this->config['max'] ?? '', ENT_QUOTES, 'UTF-8');
        $step = html($this->config['step'] ?? '1', ENT_QUOTES, 'UTF-8');
        $placeholder = html($this->config['placeholder'] ?? '', ENT_QUOTES, 'UTF-8');
        $required = isset($this->config['required']) && $this->config['required'] ? 'required' : '';
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        
        return "
            <input type='number' 
                   name='{$fieldName}' 
                   value='" . html($safeValue, ENT_QUOTES, 'UTF-8') . "'
                   class='form-control'
                   placeholder='{$placeholder}'
                   min='{$min}'
                   max='{$max}'
                   step='{$step}'
                   {$required}>
        ";
    }
    
    /**
    * Генерирует HTML для отображения значения поля в детальном просмотре 
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения
    */
    public function renderDisplay($value, $entityType, $entityId): string {
        $safeValue = ($value === null) ? '' : $value;
        
        return "<span class='field-number'>" . html($safeValue, ENT_QUOTES, 'UTF-8') . "</span>";
    }
    
    /**
    * Генерирует HTML для отображения значения поля в списке
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения в списке
    */
    public function renderList($value, $entityType, $entityId): string {
        $safeValue = ($value === null) ? '' : $value;
        
        return "<span class='field-number'>" . html($safeValue, ENT_QUOTES, 'UTF-8') . "</span>";
    }
    
    /**
    * Валидирует значение поля
    * @param mixed $value Значение для проверки
    * @return bool true если значение корректно
    */
    public function validate($value): bool {
        if (isset($this->config['required']) && $this->config['required']) {
            if ($value === null || $value === '') {
                return false;
            }
        }
        
        if ($value !== null && $value !== '') {
            if (!is_numeric($value)) {
                return false;
            }
            
            $numericValue = (float)$value;
            
            if (isset($this->config['min']) && $this->config['min'] !== '') {
                if ($numericValue < (float)$this->config['min']) {
                    return false;
                }
            }
            
            if (isset($this->config['max']) && $this->config['max'] !== '') {
                if ($numericValue > (float)$this->config['max']) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
    * Обрабатывает значение перед сохранением
    * @param mixed $value Исходное значение
    * @return string|int|null Обработанное значение
    */
    public function processValue($value) {
        if ($value === null || $value === '') {
            return '';
        }
        
        if (is_numeric($value)) {
            if (is_float($value)) {
                return (float)$value;
            }
            return (int)$value;
        }
        
        return $value;
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $min = html($this->config['min'] ?? '', ENT_QUOTES, 'UTF-8');
        $max = html($this->config['max'] ?? '', ENT_QUOTES, 'UTF-8');
        $step = html($this->config['step'] ?? '1', ENT_QUOTES, 'UTF-8');
        $placeholder = html($this->config['placeholder'] ?? '', ENT_QUOTES, 'UTF-8');
        $defaultValue = html($this->config['default_value'] ?? '', ENT_QUOTES, 'UTF-8');
        
        return "
            <div class='row'>
                <div class='col-md-4'>
                    <div class='mb-3'>
                        <label class='form-label'>Минимальное значение</label>
                        <input type='number' class='form-control' name='config[min]' value='{$min}'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='mb-3'>
                        <label class='form-label'>Максимальное значение</label>
                        <input type='number' class='form-control' name='config[max]' value='{$max}'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='mb-3'>
                        <label class='form-label'>Шаг</label>
                        <input type='number' class='form-control' name='config[step]' value='{$step}' step='0.01'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Плейсхолдер</label>
                        <input type='text' class='form-control' name='config[placeholder]' value='{$placeholder}'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Значение по умолчанию</label>
                        <input type='number' class='form-control' name='config[default_value]' value='{$defaultValue}'>
                    </div>
                </div>
            </div>
        ";
    }
}