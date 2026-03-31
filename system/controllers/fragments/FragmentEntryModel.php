<?php

/**
* Модель для работы с записями фрагментов
* 
* @package models
*/
class FragmentEntryModel {
    
    /**
    * @var \Database
    */
    private $db;
    
    /**
    * @var string
    */
    private $tableName;
    
    /**
    * Конструктор
    * @param \Database $db
    */
    public function __construct($db) {
        $this->db = $db;
        $this->tableName = $this->db->getPrefix() . 'fragment_entries';
    }
    
    /**
    * Получение записей фрагмента
    * @param int $fragmentId
    * @param string $status
    * @return array
    */
    public function getByFragment($fragmentId, $status = null) {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE fragment_id = ?";
        $params = [$fragmentId];
        
        if ($status !== null) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY sort_order ASC, created_at DESC";
        
        $entries = $this->db->fetchAll($sql, $params);
        
        foreach ($entries as &$entry) {
            if ($entry['data']) {
                $decoded = json_decode($entry['data'], true);
                $entry['data'] = is_array($decoded) ? $decoded : [];
            } else {
                $entry['data'] = [];
            }
        }
        
        return $entries;
    }
    
    /**
    * Получение записи по ID
    * @param int $id
    * @return array|null
    */
    public function getById($id) {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE id = ?";
        $entry = $this->db->fetch($sql, [$id]);
        
        if ($entry && $entry['data']) {
            $decoded = json_decode($entry['data'], true);
            $entry['data'] = is_array($decoded) ? $decoded : [];
        }
        
        return $entry;
    }
    
    /**
    * Создание записи 
    * @param int $fragmentId
    * @param array $data
    * @param int $sortOrder
    * @return int
    */
    public function create($fragmentId, $data, $sortOrder = 0) {
        $entryData = [
            'fragment_id' => $fragmentId,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'sort_order' => $sortOrder,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $sql = "INSERT INTO `{$this->tableName}` (
            fragment_id, data, sort_order, status, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $entryData['fragment_id'],
            $entryData['data'],
            $entryData['sort_order'],
            $entryData['status'],
            $entryData['created_at'],
            $entryData['updated_at']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
    * Обновление записи
    * @param int $id
    * @param array $data
    * @return bool
    */
    public function update($id, $data) {
        $updateData = [
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $sql = "UPDATE `{$this->tableName}` SET 
            data = ?,
            updated_at = ?
        WHERE id = ?";
        
        return $this->db->query($sql, [
            $updateData['data'],
            $updateData['updated_at'],
            $id
        ]);
    }
    
    /**
    * Удаление записи
    * @param int $id
    * @return bool
    */
    public function delete($id) {
        $sql = "DELETE FROM `{$this->tableName}` WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    /**
    * Удаление всех записей фрагмента
    * @param int $fragmentId
    * @return bool
    */
    public function deleteByFragment($fragmentId) {
        $sql = "DELETE FROM `{$this->tableName}` WHERE fragment_id = ?";
        return $this->db->query($sql, [$fragmentId]);
    }
    
    /**
    * Изменение статуса записи 
    * @param int $id
    * @param string $status
    * @return bool
    */
    public function setStatus($id, $status) {
        $sql = "UPDATE `{$this->tableName}` SET status = ?, updated_at = ? WHERE id = ?";
        return $this->db->query($sql, [$status, date('Y-m-d H:i:s'), $id]);
    }
    
    /**
    * Обновление порядка записей 
    * @param array $order
    * @return bool
    */
    public function updateOrder($order) {
        $success = true;
        foreach ($order as $item) {
            $sql = "UPDATE `{$this->tableName}` SET sort_order = ? WHERE id = ?";
            $result = $this->db->query($sql, [$item['order'], $item['id']]);
            if (!$result) {
                $success = false;
            }
        }
        return $success;
    }
    
    /**
    * Получение количества записей фрагмента 
    * @param int $fragmentId
    * @param string|null $status
    * @return int
    */
    public function getCountByFragment($fragmentId, $status = null) {
        $sql = "SELECT COUNT(*) as count FROM `{$this->tableName}` WHERE fragment_id = ?";
        $params = [$fragmentId];
        
        if ($status !== null) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $result = $this->db->fetch($sql, $params);
        return (int)($result['count'] ?? 0);
    }
}