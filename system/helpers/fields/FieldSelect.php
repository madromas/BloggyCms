<?php

/**
* Поле типа "выпадающий список" для системы полей
* @package Fields
*/
class FieldSelect extends Field {
    
    /**
    * Рендерит HTML-код выпадающего списка
    * @param mixed $currentValue Текущее значение поля
    * @return string HTML-код поля
    */
    public function render($currentValue = null) {
        $value = $currentValue !== null ? $currentValue : $this->options['default'];
        
        $attributes = $this->getAttributes();
        
        $optionsHtml = '';
        foreach ($this->options['options'] as $optionValue => $optionLabel) {
            $selected = $value == $optionValue ? ' selected' : '';
            $optionsHtml .= "<option value=\"" . html($optionValue) . "\"{$selected}>" . html($optionLabel) . "</option>";
        }
        
        $fieldHtml = "<select{$attributes}>{$optionsHtml}</select>";
        
        return $this->renderFieldGroup($fieldHtml);
    }
}