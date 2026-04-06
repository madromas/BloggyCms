<?php

/**
* Модель дополнительных полей
* @package models
*/
class FieldModel implements ModelAPI {

    use APIAware;

    protected $allowedAPIMethods = [
        'getByEntityType',
        'getActiveByEntityType',
        'getFieldBySystemName',
        'getFieldValue',
        'getFieldValues',
        'getFieldTypes',
        'renderFieldDisplay',
        'renderFieldList'
    ];
    
    private $db;
    private $fieldManager;
    
    /**
    * Конструктор модели полей
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        $this->db = $db;
        $this->fieldManager = new FieldManager($db);
    }
    
    /**
    * Получение всех полей системы с количеством использования
    * @return array Массив полей с данными об использовании
    */
    public function getAll() {
        return $this->db->fetchAll("
            SELECT f.*, 
                   COUNT(fv.id) as usage_count 
            FROM fields f 
            LEFT JOIN field_values fv ON f.id = fv.field_id 
            GROUP BY f.id 
            ORDER BY f.entity_type, f.sort_order, f.name
        ");
    }

    /**
    * Получение поля по системному имени и типу сущности
    * @param string $systemName Уникальное системное имя поля
    * @param string $entityType Тип сущности (post, category, user и т.д.)
    * @return array|null Данные поля или null если не найдено
    */
    public function getFieldBySystemName($systemName, $entityType) {
        return $this->db->fetch(
            "SELECT * FROM fields WHERE system_name = ? AND entity_type = ?",
            [$systemName, $entityType]
        );
    }
    
    /**
    * Получение всех полей для указанного типа сущности
    * @param string $entityType Тип сущности
    * @return array Массив полей для сущности
    */
    public function getByEntityType($entityType) {
        return $this->db->fetchAll("
            SELECT * FROM fields 
            WHERE entity_type = ? 
            ORDER BY sort_order, name
        ", [$entityType]);
    }
    
    /**
    * Получение активных полей для указанного типа сущности
    * @param string $entityType Тип сущности
    * @return array Массив активных полей
    */
    public function getActiveByEntityType($entityType) {
        return $this->db->fetchAll("
            SELECT * FROM fields 
            WHERE entity_type = ? AND is_active = 1 
            ORDER BY sort_order, name
        ", [$entityType]);
    }
    
    /**
    * Получение поля по ID
    * @param int $id ID поля
    * @return array|null Данные поля или null если не найдено
    */
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM fields WHERE id = ?", [$id]);
    }
    
    /**
    * Получение всех значений полей для конкретной сущности
    * @param int $entityId ID сущности
    * @param string $entityType Тип сущности
    * @return array Массив значений полей
    */
    public function getFieldValues($entityId, $entityType) {
        $result = $this->db->fetchAll("
            SELECT f.system_name, fv.value 
            FROM field_values fv 
            JOIN fields f ON fv.field_id = f.id 
            WHERE fv.entity_type = ? AND fv.entity_id = ?
        ", [$entityType, $entityId]);
        
        $values = [];
        foreach ($result as $row) {
            $values[$row['system_name']] = $row['value'];
        }
        
        return $values;
    }
    
    /**
    * Получение значения поля для конкретной сущности
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @param string $fieldSystemName Системное имя поля
    * @return mixed Значение поля или null если не найдено
    */
    public function getFieldValue($entityType, $entityId, $fieldSystemName) {
        $result = $this->db->fetch("
            SELECT fv.value 
            FROM field_values fv 
            JOIN fields f ON fv.field_id = f.id 
            WHERE fv.entity_type = ? AND fv.entity_id = ? AND f.system_name = ?
        ", [$entityType, $entityId, $fieldSystemName]);
        
        return $result ? $result['value'] : null;
    }
    
    /**
    * Создание нового поля
    * @param array $data Массив данных поля
    * @return int ID созданного поля
    * @throws Exception При ошибках валидации или дублировании системного имени
    */
    public function create($data) {

        if (!isset($data['system_name']) || !isset($data['entity_type'])) {
            throw new Exception('Отсутствуют обязательные поля system_name или entity_type');
        }
        
        $existing = $this->db->fetch(
            "SELECT COUNT(*) as count FROM fields WHERE system_name = ? AND entity_type = ?",
            [$data['system_name'], $data['entity_type']]
        );
        
        if ($existing['count'] > 0) {
            throw new Exception('Поле с таким системным именем уже существует для этого типа сущности');
        }
        
        $sql = "INSERT INTO fields (name, system_name, type, entity_type, description, is_required, is_active, sort_order, config, show_in_post, show_in_list) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['name'] ?? '',
            $data['system_name'],
            $data['type'] ?? 'string',
            $data['entity_type'],
            $data['description'] ?? '',
            $data['is_required'] ?? 0,
            $data['is_active'] ?? 1,
            $data['sort_order'] ?? 0,
            $data['config'] ?? '{}',
            $data['show_in_post'] ?? 1,
            $data['show_in_list'] ?? 0 
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
    * Обновление существующего поля
    * @param int $id ID обновляемого поля
    * @param array $data Массив данных для обновления
    * @return bool Результат выполнения запроса
    * @throws Exception При ошибках валидации или дублировании системного имени
    */
    public function update($id, $data) {
        if (!isset($data['system_name'])) {
            throw new Exception('Отсутствует обязательное поле system_name');
        }
        
        $currentField = $this->getById($id);
        if (!$currentField) {
            throw new Exception('Поле не найдено');
        }
        
        $existing = $this->db->fetch(
            "SELECT COUNT(*) as count FROM fields WHERE system_name = ? AND entity_type = ? AND id != ?",
            [$data['system_name'], $currentField['entity_type'], $id]
        );
        
        if ($existing['count'] > 0) {
            throw new Exception('Поле с таким системным именем уже существует для этого типа сущности');
        }
        
        $sql = "UPDATE fields SET 
            name = ?, system_name = ?, type = ?, description = ?, 
            is_required = ?, is_active = ?, sort_order = ?, config = ?, 
            show_in_post = ?, show_in_list = ? 
            WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['name'] ?? '',
            $data['system_name'],
            $data['type'] ?? 'string',
            $data['description'] ?? '',
            $data['is_required'] ?? 0,
            $data['is_active'] ?? 0,
            $data['sort_order'] ?? 0,
            $data['config'] ?? '{}',
            $data['show_in_post'] ?? 1,
            $data['show_in_list'] ?? 0,
            $id
        ]);
    }
    
    /**
    * Удаление поля
    * @param int $id ID удаляемого поля
    * @return bool Результат выполнения запроса
    */
    public function delete($id) {
        $this->db->query("DELETE FROM field_values WHERE field_id = ?", [$id]);
        return $this->db->query("DELETE FROM fields WHERE id = ?", [$id]);
    }
    
    /**
    * Получение количества полей для типа сущности
    * @param string $entityType Тип сущности
    * @return int Количество полей
    */
    public function getCountByEntityType($entityType) {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM fields WHERE entity_type = ?",
            [$entityType]
        );
        return $result['count'];
    }
    
    /**
    * Получение доступных типов полей
    * @return array Массив типов полей
    */
    public function getFieldTypes() {
        return $this->fieldManager->getAvailableFieldTypes();
    }
    
    /**
    * Генерация HTML-кода для ввода значения поля
    * @param array $field Данные поля
    * @param mixed $value Текущее значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код поля ввода
    */
    public function renderFieldInput($field, $value, $entityType, $entityId): string {
        $config = [];
        
        if (isset($field['config'])) {
            if (is_string($field['config'])) {
                $decoded = json_decode($field['config'], true);
                $config = is_array($decoded) ? $decoded : [];
            } elseif (is_array($field['config'])) {
                $config = $field['config'];
            }
        }
        
        return $this->fieldManager->renderFieldInput(
            $field['type'],
            $field['system_name'],
            $value,
            $config,
            $entityType,
            $entityId
        );
    }
    
    /**
    * Генерация HTML-кода для отображения значения поля
    * @param array $field Данные поля
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код отображения значения
    */
    public function renderFieldDisplay($field, $value, $entityType, $entityId): string {
        $config = json_decode($field['config'] ?? '{}', true);
        return $this->fieldManager->renderFieldDisplay(
            $field['type'],
            $value,
            $config,
            $entityType,
            $entityId
        );
    }
    
    /**
    * Генерация HTML-кода для отображения значения поля в списке
    * @param array $field Данные поля
    * @param mixed $value Значение поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @return string HTML-код отображения в списке
    */
    public function renderFieldList($field, $value, $entityType, $entityId): string {
        $config = json_decode($field['config'] ?? '{}', true);
        return $this->fieldManager->renderFieldList(
            $field['type'],
            $value,
            $config,
            $entityType,
            $entityId
        );
    }
    
    /**
    * Сохранение значения поля
    * @param string $entityType Тип сущности
    * @param int $entityId ID сущности
    * @param string $fieldSystemName Системное имя поля
    * @param mixed $value Сохраняемое значение
    * @return bool Результат выполнения запроса
    */
    public function saveFieldValue($entityType, $entityId, $fieldSystemName, $value) {
        $field = $this->getFieldBySystemName($fieldSystemName, $entityType);
        
        if (!$field) {
            return false;
        }
        
        $fieldId = $field['id'];
        
        $existing = $this->db->fetch(
            "SELECT id FROM field_values WHERE field_id = ? AND entity_type = ? AND entity_id = ?",
            [$fieldId, $entityType, $entityId]
        );
        
        if ($value === null || $value === '') {
            if ($existing) {
                return $this->db->query(
                    "DELETE FROM field_values WHERE field_id = ? AND entity_type = ? AND entity_id = ?",
                    [$fieldId, $entityType, $entityId]
                );
            }
            return true;
        }
        
        if ($existing) {
            return $this->db->query(
                "UPDATE field_values SET value = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE field_id = ? AND entity_type = ? AND entity_id = ?",
                [$value, $fieldId, $entityType, $entityId]
            );
        } else {
            return $this->db->query(
                "INSERT INTO field_values (field_id, entity_type, entity_id, value) 
                VALUES (?, ?, ?, ?)",
                [$fieldId, $entityType, $entityId, $value]
            );
        }
    }
    
    /**
    * Обработка конфигурации поля
    * @param string $fieldType Тип поля
    * @param array $config Конфигурация поля
    * @return array Обработанная конфигурация
    */
    public function processFieldConfig($fieldType, $config) {
        return $this->fieldManager->processFieldConfig($fieldType, $config);
    }
}