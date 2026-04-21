<?php

/**
* Поле типа "текстовая область" для системы пользовательских полей
* @package Fields
* @extends BaseField
*/
class TextField extends BaseField {

    /**
    * Возвращает тип поля 
    * @return string 'text'
    */
    public function getType(): string {
        return 'text';
    }
    
    /**
    * Возвращает отображаемое название типа поля 
    * @return string 'Текстовая область'
    */
    public function getName(): string {
        return LANG_FIELD_TEXT_TITLE;
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
        
        $placeholder = html($this->config['placeholder'] ?? '', ENT_QUOTES, 'UTF-8');
        $rows = html($this->config['rows'] ?? '4', ENT_QUOTES, 'UTF-8');
        $required = isset($this->config['required']) && $this->config['required'] ? 'required' : '';
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        
        return "
            <textarea name='{$fieldName}' 
                      class='form-control'
                      rows='{$rows}'
                      placeholder='{$placeholder}'
                      {$required}>" . html($safeValue, ENT_QUOTES, 'UTF-8') . "</textarea>
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
        
        return "<div class='field-text'>" . nl2br(html($safeValue, ENT_QUOTES, 'UTF-8')) . "</div>";
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
        
        $truncated = mb_strlen($safeValue) > 100 ? mb_substr($safeValue, 0, 100) . '...' : $safeValue;
        return "<span title='" . html($safeValue, ENT_QUOTES, 'UTF-8') . "'>" . 
               html($truncated, ENT_QUOTES, 'UTF-8') . "</span>";
    }
    
    /**
    * Валидирует значение поля
    * @param mixed $value Значение для проверки
    * @return bool true если значение корректно
    */
    public function validate($value): bool {
        if (isset($this->config['required']) && $this->config['required']) {
            if (($value === null || $value === '') && $value !== '0') {
                return false;
            }
        }
        
        if (!empty($this->config['maxlength']) && mb_strlen($value) > $this->config['maxlength']) {
            return false;
        }
        
        if (!empty($this->config['minlength']) && mb_strlen($value) < $this->config['minlength']) {
            return false;
        }
        
        return true;
    }
    
    /**
    * Обрабатывает значение перед сохранением
    * @param mixed $value Исходное значение
    * @return string Обработанное значение
    */
    public function processValue($value) {
        return ($value === null) ? '' : $value;
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $placeholder = html($this->config['placeholder'] ?? '', ENT_QUOTES, 'UTF-8');
        $rows = html($this->config['rows'] ?? '4', ENT_QUOTES, 'UTF-8');
        $defaultValue = html($this->config['default_value'] ?? '', ENT_QUOTES, 'UTF-8');
        
        return "
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>" . LANG_FIELD_TEXT_PLACEHOLDER_LABEL . "</label>
                        <input type='text' class='form-control' name='config[placeholder]' value='{$placeholder}'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>" . LANG_FIELD_TEXT_ROWS_LABEL . "</label>
                        <input type='number' class='form-control' name='config[rows]' value='{$rows}' min='1' max='20'>
                    </div>
                </div>
            </div>
            <div class='mb-3'>
                <label class='form-label'>" . LANG_FIELD_TEXT_DEFAULT_LABEL . "</label>
                <textarea class='form-control' name='config[default_value]' rows='3'>{$defaultValue}</textarea>
            </div>
        ";
    }
}