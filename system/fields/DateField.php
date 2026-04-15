<?php

/**
* Поле типа "дата" для системы пользовательских полей
* @package Fields
*/
class DateField extends BaseField {
    
    /**
    * Возвращает тип поля
    * @return string 'date'
    */
    public function getType(): string {
        return 'date';
    }
    
    /**
    * Возвращает отображаемое название типа поля
    * @return string 'Дата'
    */
    public function getName(): string {
        return 'Дата';
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
        $format = $this->config['format'] ?? 'Y-m-d';

        $dateValue = '';
        if (!empty($safeValue)) {
            $timestamp = strtotime($safeValue);
            if ($timestamp !== false) {
                $dateValue = date('Y-m-d', $timestamp);
            }
        }
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        
        return "
            <input type='date' 
                   name='{$fieldName}' 
                   value='" . html($dateValue, ENT_QUOTES, 'UTF-8') . "'
                   class='form-control form-control-sm'
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
        if (empty($value) && $value !== '0') {
            return '<span class="text-muted">Не указана</span>';
        }
        
        $format = $this->config['format'] ?? 'd.m.Y';
        $timestamp = strtotime($value);
        
        if ($timestamp === false) {
            return '<span class="text-muted">Неверный формат даты</span>';
        }
        
        $formattedDate = date($format, $timestamp);
        
        return "<span class='field-date'>" . html($formattedDate, ENT_QUOTES, 'UTF-8') . "</span>";
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
        
        $format = $this->config['format'] ?? 'd.m.Y';
        $timestamp = strtotime($value);
        
        if ($timestamp === false) {
            return '<span class="text-muted">-</span>';
        }
        
        $formattedDate = date($format, $timestamp);
        
        return "<span class='field-date'>" . html($formattedDate, ENT_QUOTES, 'UTF-8') . "</span>";
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $format = html($this->config['format'] ?? 'd.m.Y', ENT_QUOTES, 'UTF-8');
        $defaultValue = html($this->config['default_value'] ?? '', ENT_QUOTES, 'UTF-8');
        
        return "
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Формат даты</label>
                        <input type='text' class='form-control' name='config[format]' value='{$format}' placeholder='d.m.Y'>
                        <div class='form-text'>d - день, m - месяц, Y - год</div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Значение по умолчанию</label>
                        <input type='date' class='form-control' name='config[default_value]' value='{$defaultValue}'>
                        <div class='form-text'>Дата, которая будет установлена по умолчанию</div>
                    </div>
                </div>
            </div>
        ";
    }
}