<?php

/**
* Класс для группировки полей в логические блоки (fieldset)
* @package Fields
*/
class Fieldset {
    
    /** @var string Заголовок группы */
    private $title;
    
    /** @var string Иконка для заголовка (класс Bootstrap Icons) */
    private $icon;
    
    /** @var string|array Режим колонок ('custom' для индивидуальной настройки или число для всех полей) */
    private $columns;
    
    /** @var array Массив полей в группе */
    private $fields;
    
    /**
    * Конструктор fieldset 
    * @param string $title Заголовок группы
    * @param array $options Опции группы:
    * - icon: класс иконки (например 'bi bi-gear')
    * - columns: режим колонок ('custom' или число, например '6', '4', '3')
    * - fields: массив начальных полей
    */
    public function __construct($title, $options = []) {
        $this->title = $title;
        $this->icon = $options['icon'] ?? '';
        $this->columns = $options['columns'] ?? '6';
        $this->fields = $options['fields'] ?? [];
    }
    
    /**
    * Добавляет поле в группу 
    * @param Field $field Объект поля
    * @return self Для цепочки вызовов
    */
    public function addField($field) {
        $this->fields[] = $field;
        return $this;
    }
    
    /**
    * Рендерит всю группу полей 
    * @param array $currentSettings Текущие значения настроек
    * @return string HTML-код группы
    */
    public function render($currentSettings) {
        $formData = ['settings' => $currentSettings];
        
        ob_start();
        ?>
        <div class="settings-group mb-4" data-fieldset-name="<?= htmlspecialchars($this->title) ?>">
            <h6 class="settings-group-title bg-light p-3 rounded">
                <?php if ($this->icon) { ?>
                    <i class="<?= $this->icon ?> me-2"></i>
                <?php } ?>
                <?= $this->title ?>
            </h6>
            <div class="p-3">
                <div class="row">
                    <?php 
                    $groupedFields = $this->groupFieldsByDependency($formData);
                    
                    foreach ($groupedFields as $fieldOrGroup) {
                        if (is_array($fieldOrGroup)) {
                            echo $this->renderDependentGroup($fieldOrGroup, $formData);
                        } else {
                            $field = $fieldOrGroup;
                            echo $this->renderSingleField($field, $formData);
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
    * Группирует поля по зависимостям
    * @param array $formData Данные формы
    * @return array Массив, где элементы могут быть либо полями, либо массивами зависимых полей
    */
    private function groupFieldsByDependency($formData) {
        $independentFields = [];
        $dependentFields = [];
        $dependencyMap = [];
        
        foreach ($this->fields as $field) {
            if (method_exists($field, 'isConditional') && $field->isConditional()) {
                $parentField = $this->getParentFieldName($field);
                if ($parentField) {
                    $dependentFields[$parentField][] = $field;
                    $dependencyMap[$parentField] = true;
                } else {
                    $independentFields[] = $field;
                }
            } else {
                $independentFields[] = $field;
            }
        }

        $result = [];
        foreach ($independentFields as $field) {
            $result[] = $field;
            $fieldName = $field->getName();
            
            if (isset($dependentFields[$fieldName])) {
                $result[] = $dependentFields[$fieldName];
            }
        }
        
        return $result;
    }

    /**
    * Получает имя родительского поля из условия 
    * @param Field $field Поле с условием
    * @return string|null Имя родительского поля или null
    */
    private function getParentFieldName($field) {
        if (!method_exists($field, 'getShowCondition')) {
            return null;
        }
        
        $condition = $field->getShowCondition();
        if (preg_match('/^field:(\w+)/', $condition, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
    * Рендерит группу зависимых полей 
    * @param array $dependentFields Массив зависимых полей
    * @param array $formData Данные формы
    * @return string HTML-код группы зависимых полей
    */
    private function renderDependentGroup($dependentFields, $formData) {
        ob_start();
        ?>
        <div class="col-12 dependent-group-container mb-3">
            <div class="dependent-group">
                <div class="row">
                    <?php foreach ($dependentFields as $field) { ?>
                        <?php 
                        $conditionalAttrs = '';
                        if (method_exists($field, 'isConditional') && $field->isConditional()) {
                            $shouldShow = $field->shouldShow($formData);
                            $hiddenClass = $shouldShow ? '' : 'd-none';
                            $conditionalAttrs = " data-conditional=\"true\" data-condition=\"" . htmlspecialchars($field->getShowCondition()) . "\" class=\"field-conditional {$hiddenClass}\"";
                        }
                        
                        $colClass = $this->getFieldColumnClass($field);
                        ?>
                        
                        <div class="<?= $colClass ?>"<?= $conditionalAttrs ?>>
                            <?= $field->render($formData['settings'][$field->getName()] ?? null) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
    * Рендерит одиночное поле 
    * @param Field $field Объект поля
    * @param array $formData Данные формы
    * @return string HTML-код поля в колонке
    */
    private function renderSingleField($field, $formData) {
        $conditionalAttrs = '';
        if (method_exists($field, 'isConditional') && $field->isConditional()) {
            $shouldShow = $field->shouldShow($formData);
            $hiddenClass = $shouldShow ? '' : 'd-none';
            $conditionalAttrs = " data-conditional=\"true\" data-condition=\"" . htmlspecialchars($field->getShowCondition()) . "\" class=\"field-conditional {$hiddenClass}\"";
        }
        
        $colClass = $this->getFieldColumnClass($field);
        
        ob_start();
        ?>
        <div class="<?= $colClass ?>"<?= $conditionalAttrs ?>>
            <?= $field->render($formData['settings'][$field->getName()] ?? null) ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
    * Получает CSS класс для колонки поля
    * @param Field $field Объект поля
    * @return string CSS класс для Bootstrap колонки
    */
    private function getFieldColumnClass($field) {

        if ($this->columns === 'custom') {
            $options = method_exists($field, 'getOptions') ? $field->getOptions() : [];
            
            if (isset($options['column'])) {
                return "col-md-{$options['column']}";
            }
            
            return "col-md-12";
        }
        
        if ($this->shouldFieldTakeFullWidth($field)) {
            return 'col-12';
        }
        
        return "col-md-{$this->columns}";
    }
    
    /**
    * Проверяет, должно ли поле занимать всю ширину (col-12) 
    * @param Field $field Объект поля
    * @return bool true если поле должно быть на всю ширину
    */
    private function shouldFieldTakeFullWidth($field) {
        if (method_exists($field, 'getOptions')) {
            $options = $field->getOptions();
            if (isset($options['full_width']) && $options['full_width'] === true) {
                return true;
            }
        }
        
        if ($field instanceof FieldAlert) {
            return true;
        }
        
        if (method_exists($field, 'isConditional') && $field->isConditional()) {
            $options = $field->getOptions();
            if (isset($options['full_width']) && $options['full_width'] === true) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
    * Получает список всех условных полей в fieldset
    * @return array Массив полей, у которых есть условие показа
    */
    public function getConditionalFields() {
        $conditionalFields = [];
        foreach ($this->fields as $field) {
            if (method_exists($field, 'isConditional') && $field->isConditional()) {
                $conditionalFields[] = $field;
            }
        }
        return $conditionalFields;
    }
}