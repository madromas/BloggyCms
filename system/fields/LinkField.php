<?php

/**
* Поле типа "ссылка" для системы пользовательских полей
* @package Fields
*/
class LinkField extends BaseField {
    
    /**
    * Возвращает тип поля 
    * @return string 'link'
    */
    public function getType(): string {
        return 'link';
    }
    
    /**
    * Возвращает отображаемое название типа поля 
    * @return string 'Ссылка'
    */
    public function getName(): string {
        return 'Ссылка';
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
        $placeholder = html($this->config['placeholder'] ?? 'https://example.com', ENT_QUOTES, 'UTF-8');
        
        $fieldName = 'field_' . ($this->systemName ?? '');
        
        return "
            <input type='url' 
                   name='{$fieldName}' 
                   value='" . html($safeValue, ENT_QUOTES, 'UTF-8') . "'
                   class='form-control form-control-sm'
                   placeholder='{$placeholder}'
                   {$required}>
        ";
    }
    
    /**
    * Генерирует HTML для отображения значения поля в детальном просмотре
    * @param mixed $value Значение поля (URL)
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения
    */
    public function renderDisplay($value, $entityType, $entityId): string {
        if (empty($value) && $value !== '0') {
            return '<span class="text-muted">Не указана</span>';
        }
        
        $safeValue = html($value, ENT_QUOTES, 'UTF-8');
        $text = (!empty($this->config['link_text'])) 
            ? html($this->config['link_text'], ENT_QUOTES, 'UTF-8') 
            : $safeValue;
        $target = !empty($this->config['new_tab']) ? 'target="_blank" rel="noopener noreferrer"' : '';
        
        return "<a href='{$safeValue}' {$target} class='field-link'>{$text}</a>";
    }
    
    /**
    * Генерирует HTML для отображения значения поля в списке
    * @param mixed $value Значение поля (URL)
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код для отображения в списке
    */
    public function renderList($value, $entityType, $entityId): string {
        if (empty($value) && $value !== '0') {
            return '<span class="text-muted">-</span>';
        }
        
        $safeValue = html($value, ENT_QUOTES, 'UTF-8');
        
        return "<a href='{$safeValue}' target='_blank' rel='noopener noreferrer' class='text-decoration-none' title='{$safeValue}'>🔗</a>";
    }

    /**
    * Обрабатывает конфигурацию перед сохранением
    */
    public function processConfig(array $config): array {
        if (isset($config['placeholder'])) {
            $config['placeholder'] = trim($config['placeholder']);
        }
        if (isset($config['link_text'])) {
            $config['link_text'] = trim($config['link_text']);
        }
        if (isset($config['default_value'])) {
            $config['default_value'] = trim($config['default_value']);
        }
        
        $config['new_tab'] = isset($config['new_tab']) && $config['new_tab'];
        
        return $config;
    }
    
    /**
    * Возвращает HTML-форму для настройки поля в административной панели
    * @return string HTML-код формы настроек
    */
    public function getSettingsForm(): string {
        $placeholder = html($this->config['placeholder'] ?? 'https://example.com', ENT_QUOTES, 'UTF-8');
        $linkText = html($this->config['link_text'] ?? '', ENT_QUOTES, 'UTF-8');
        $newTab = isset($this->config['new_tab']) && $this->config['new_tab'] ? 'checked' : '';
        $defaultValue = html($this->config['default_value'] ?? '', ENT_QUOTES, 'UTF-8');
        
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
                        <label class='form-label'>Текст ссылки</label>
                        <input type='text' class='form-control' name='config[link_text]' value='{$linkText}' placeholder='Оставить пустым для отображения URL'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='mb-3'>
                        <label class='form-label'>Значение по умолчанию</label>
                        <input type='url' class='form-control' name='config[default_value]' value='{$defaultValue}'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='mb-3 pt-4'>
                        <div class='form-check'>
                            <input class='form-check-input' type='checkbox' name='config[new_tab]' id='new_tab' value='1' {$newTab}>
                            <label class='form-check-label' for='new_tab'>
                                Открывать в новой вкладке
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }
}