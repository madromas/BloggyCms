<?php

/**
* Поле типа "дата" для системы полей
* @package Fields
* @extends Field
*/
class FieldDate extends Field {
    
    /**
    * Рендерит HTML-код поля для выбора даты 
    * @param mixed $currentValue Текущее значение поля (дата в формате YYYY-MM-DD)
    * @return string HTML-код поля
    */
    public function render($currentValue = null) {
        $value = $currentValue !== null ? $currentValue : $this->options['default'];
        
        $fieldHtml = "<input type=\"date\" 
                   value=\"" . htmlspecialchars($value) . "\" 
                   name=\"settings[{$this->name}]\" 
                   class=\"form-control\">";
        
        return $this->renderFieldGroup($fieldHtml);
    }
}