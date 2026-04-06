<?php

/**
* Поле типа "чекбокс" для системы полей
* @package Fields
*/
class FieldCheckbox extends Field {
    
    /**
    * Рендерит HTML-код поля чекбокса 
    * @param mixed $currentValue Текущее значение поля
    * @return string HTML-код поля
    */
    public function render($currentValue = null) {

        $isChecked = $currentValue !== null ? (bool)$currentValue : (bool)($this->options['default'] ?? false);
        $checked = $isChecked ? ' checked' : '';
        
        $switchClass = $this->options['switch'] ?? true ? 'form-switch' : '';
        
        $attributes = $this->getCheckboxAttributes();
        
        $hiddenField = "<input type=\"hidden\" name=\"{$this->getFieldName()}\" value=\"0\">";
        
        $fieldHtml = "
        {$hiddenField}
        <div class=\"form-check {$switchClass}\">
            <input type=\"checkbox\" value=\"1\"{$checked}{$attributes}>
            <label class=\"form-check-label\" for=\"{$this->name}\">
                {$this->options['title']}
            </label>
        </div>";
        
        $hint = $this->options['hint'] ? '<div class="form-text mt-2">' . htmlspecialchars($this->options['hint']) . '</div>' : '';
        
        return "
        <div class=\"mb-3\">
            {$fieldHtml}
            {$hint}
        </div>";
    }
    
    /**
    * Получает имя поля с учетом admin_mode
    * @return string Имя поля для использования в форме
    */
    private function getFieldName() {
        if ($this->options['admin_mode'] ?? false) {
            return $this->name;
        } else {
            return "settings[{$this->name}]";
        }
    }
    
    /**
    * Получает атрибуты для чекбокса
    * @return string Строка с HTML-атрибутами
    */
    private function getCheckboxAttributes() {
        $attrs = [];
        
        $attrs['name'] = $this->getFieldName();
        $attrs['id'] = $this->name;
        $attrs['class'] = "form-check-input {$this->options['class']}";
        
        if ($this->options['required']) {
            $attrs['required'] = 'required';
        }
        
        foreach ($this->options['attributes'] as $key => $value) {
            $attrs[$key] = $value;
        }
        
        $attributesString = '';
        foreach ($attrs as $key => $value) {
            if (!empty($value)) {
                $attributesString .= " {$key}=\"" . htmlspecialchars($value) . "\"";
            }
        }
        
        return $attributesString;
    }
}