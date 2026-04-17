<?php

/**
* Получает значение пользовательского поля 
* @param string $entityType Тип сущности (post, page, user, category)
* @param int $entityId ID сущности
* @param string $fieldSystemName Системное имя поля
* @return mixed Значение поля или null
*/
function get_custom_field_value($entityType, $entityId, $fieldSystemName) {
    static $cache = [];
    
    $cacheKey = $entityType . '_' . $entityId . '_' . $fieldSystemName;
    
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }
    
    try {
        $db = DatabaseRegistry::getDb();
        $fieldModel = new FieldModel($db);
        $value = $fieldModel->getFieldValue($entityType, $entityId, $fieldSystemName);
        $cache[$cacheKey] = $value;
        return $value;
    } catch (Exception $e) {
        return null;
    }
}

/**
* Получает отрендеренное значение пользовательского поля
* @param string $entityType Тип сущности (post, page, user, category)
* @param int $entityId ID сущности
* @param string $fieldSystemName Системное имя поля
* @param string $default Значение по умолчанию, если поле пустое
* @return string HTML для отображения
*/
function get_custom_field_display($entityType, $entityId, $fieldSystemName, $default = '') {
    static $cache = [];
    
    $cacheKey = $entityType . '_' . $entityId . '_' . $fieldSystemName . '_display';
    
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }
    
    try {
        $db = DatabaseRegistry::getDb();
        $fieldModel = new FieldModel($db);
        
        $field = $fieldModel->getFieldBySystemName($fieldSystemName, $entityType);
        if (!$field) {
            return $default;
        }
        
        $value = $fieldModel->getFieldValue($entityType, $entityId, $fieldSystemName);
        
        if (empty($value) && $value !== '0') {
            return $default;
        }
        
        $result = $fieldModel->renderFieldDisplay($field, $value, $entityType, $entityId);
        $cache[$cacheKey] = $result;
        return $result;
        
    } catch (Exception $e) {
        return $default;
    }
}

/**
* Проверяет, заполнено ли пользовательское поле
* @param string $entityType Тип сущности (post, page, user, category)
* @param int $entityId ID сущности
* @param string $fieldSystemName Системное имя поля
* @return bool true если поле не пустое
*/
function has_custom_field($entityType, $entityId, $fieldSystemName) {
    $value = get_custom_field_value($entityType, $entityId, $fieldSystemName);
    return !empty($value) || $value === '0';
}

/**
* Получает все пользовательские поля для сущности
* @param string $entityType Тип сущности (post, page, user, category)
* @param int $entityId ID сущности
* @param bool $onlyFilled Только заполненные поля
* @return array Массив полей с данными
*/
function get_custom_fields($entityType, $entityId, $onlyFilled = true) {
    try {
        $db = DatabaseRegistry::getDb();
        $fieldModel = new FieldModel($db);
        
        $fields = $fieldModel->getActiveByEntityType($entityType);
        $result = [];
        
        foreach ($fields as $field) {
            $value = $fieldModel->getFieldValue($entityType, $entityId, $field['system_name']);
            
            if ($onlyFilled && (empty($value) && $value !== '0')) {
                continue;
            }
            
            $result[] = [
                'field' => $field,
                'value' => $value,
                'display' => $fieldModel->renderFieldDisplay($field, $value, $entityType, $entityId)
            ];
        }
        
        return $result;
        
    } catch (Exception $e) {
        return [];
    }
}

/**
* Специальный хелпер для поля "иконка со стилями"
* Возвращает отдельные компоненты иконки
* @param string $entityType Тип сущности (post, page, user, category)
* @param int $entityId ID сущности
* @param string $fieldSystemName Системное имя поля
* @return array|null Массив с компонентами иконки или null
*/
function get_icon_field_data($entityType, $entityId, $fieldSystemName) {
    $value = get_custom_field_value($entityType, $entityId, $fieldSystemName);
    
    if (empty($value)) {
        return null;
    }
    
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'set' => $decoded['set'] ?? 'bs',
                'name' => $decoded['name'] ?? '',
                'color' => $decoded['color'] ?? '#000000',
                'size' => $decoded['size'] ?? 24,
                'raw' => $value
            ];
        }
    }
    
    if (is_array($value)) {
        return [
            'set' => $value['set'] ?? 'bs',
            'name' => $value['name'] ?? '',
            'color' => $value['color'] ?? '#000000',
            'size' => $value['size'] ?? 24,
            'raw' => json_encode($value)
        ];
    }
    
    return null;
}

/**
* Рендерит иконку из поля "иконка со стилями"
* @param string $entityType Тип сущности (post, page, user, category)
* @param int $entityId ID сущности
* @param string $fieldSystemName Системное имя поля
* @param array $overrides Переопределение параметров (color, size, class)
* @return string HTML иконки
*/
function render_icon_field($entityType, $entityId, $fieldSystemName, $overrides = []) {
    $iconData = get_icon_field_data($entityType, $entityId, $fieldSystemName);
    
    if (!$iconData || empty($iconData['name'])) {
        return '';
    }
    
    $set = $overrides['set'] ?? $iconData['set'];
    $name = $iconData['name'];
    $color = $overrides['color'] ?? $iconData['color'];
    $size = $overrides['size'] ?? $iconData['size'];
    $class = $overrides['class'] ?? '';
    
    if (function_exists('bloggy_icon')) {
        return bloggy_icon($set, $name, $size . ' ' . $size, $color, $class);
    }
    
    return '<span class="icon-placeholder">' . html($name) . '</span>';
}