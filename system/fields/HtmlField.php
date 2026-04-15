<?php

/**
* Поле типа "HTML-блок" для системы пользовательских полей
* @package Fields
*/
class HtmlField extends BaseField {
    
    /**
    * Возвращает тип поля
    * @return string 'html'
    */
    public function getType(): string {
        return 'html';
    }
    
    /**
    * Возвращает отображаемое название типа поля 
    * @return string 'HTML-блок'
    */
    public function getName(): string {
        return 'HTML-блок';
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
        $rows = $this->config['rows'] ?? 6;
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        
        return "
            <textarea name='{$fieldName}' 
                      class='form-control form-control-sm'
                      rows='{$rows}'
                      placeholder='Введите HTML код...'
                      {$required}>" . html($safeValue, ENT_QUOTES, 'UTF-8') . "</textarea>
            <div class='form-text'>Поддерживается HTML разметка</div>
        ";
    }
    
    /**
    * Генерирует HTML для отображения значения поля в детальном просмотре
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string Исходный HTML-код
    */
    public function renderDisplay($value, $entityType, $entityId): string {
        if ($value === null) {
            return '';
        }
        return $value;
    }
    
    /**
    * Генерирует HTML для отображения значения поля в списке 
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string Обрезанный текст без HTML-тегов
    */
    public function renderList($value, $entityType, $entityId): string {
        $safeValue = ($value === null) ? '' : $value;
        
        $stripped = strip_tags($safeValue);
        $truncated = mb_strlen($stripped) > 50 ? mb_substr($stripped, 0, 50) . '...' : $stripped;
        return "<span title='" . html($stripped, ENT_QUOTES, 'UTF-8') . "'>" . html($truncated, ENT_QUOTES, 'UTF-8') . "</span>";
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $defaultValue = html($this->config['default_value'] ?? '', ENT_QUOTES, 'UTF-8');
        $rows = html($this->config['rows'] ?? '6', ENT_QUOTES, 'UTF-8');
        
        return "
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Количество строк</label>
                        <input type='number' class='form-control' name='config[rows]' value='{$rows}' min='3' max='20'>
                    </div>
                </div>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Значение по умолчанию</label>
                <textarea class='form-control' name='config[default_value]' rows='4'>{$defaultValue}</textarea>
            </div>
        ";
    }
}