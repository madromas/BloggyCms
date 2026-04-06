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
        return 'Текстовая область';
    }
    
    /**
    * Генерирует HTML для редактирования поля в форме 
    * @param mixed $value Текущее значение поля
    * @param string $entityType Тип сущности (post, user, category и т.д.)
    * @param int $entityId ID сущности
    * @return string HTML-код для редактирования
    */
    public function renderInput($value, $entityType, $entityId): string {
        $placeholder = $this->config['placeholder'] ?? '';
        $rows = $this->config['rows'] ?? 4;
        $required = isset($this->config['required']) && $this->config['required'] ? 'required' : '';
        
        return "
            <textarea name='field_{$this->systemName}' 
                      class='form-control'
                      rows='{$rows}'
                      placeholder='" . htmlspecialchars($placeholder) . "'
                      {$required}>" . htmlspecialchars($value) . "</textarea>
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
        return "<div class='field-text'>" . nl2br(htmlspecialchars($value)) . "</div>";
    }
    
    /**
    * Генерирует HTML для отображения значения поля в списке
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения в списке
    */
    public function renderList($value, $entityType, $entityId): string {
        $truncated = mb_strlen($value) > 100 ? mb_substr($value, 0, 100) . '...' : $value;
        return "<span title='" . htmlspecialchars($value) . "'>" . htmlspecialchars($truncated) . "</span>";
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $placeholder = htmlspecialchars($this->config['placeholder'] ?? '');
        $rows = htmlspecialchars($this->config['rows'] ?? '4');
        $defaultValue = htmlspecialchars($this->config['default_value'] ?? '');
        
        return "
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Плейсхолдер</label>
                        <input type='text' class='form-control' name='config[placeholder]' value='{$placeholder}'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Количество строк</label>
                        <input type='number' class='form-control' name='config[rows]' value='{$rows}' min='1' max='20'>
                    </div>
                </div>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Значение по умолчанию</label>
                <textarea class='form-control' name='config[default_value]' rows='3'>{$defaultValue}</textarea>
            </div>
        ";
    }
}