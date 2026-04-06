<?php

/**
* Поле типа "число" для системы полей
* @package Fields
* @extends Field
*/
class FieldNumber extends Field {
    
    /**
    * Рендерит HTML-код поля для ввода числа
    * @param mixed $currentValue Текущее значение поля
    * @return string HTML-код поля
    */
    public function render($currentValue = null) {
        $value = $currentValue !== null ? $currentValue : $this->options['default'];
        
        $attributes = $this->getAttributes();
        
        $min = isset($this->options['min']) ? " min=\"{$this->options['min']}\"" : '';
        $max = isset($this->options['max']) ? " max=\"{$this->options['max']}\"" : '';
        
        $fieldHtml = "<input type=\"number\" value=\"" . htmlspecialchars($value) . "\"{$attributes}{$min}{$max}>";
        
        return $this->renderFieldGroup($fieldHtml);
    }
}