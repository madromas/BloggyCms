<?php

/**
* Модель для работы с логами отладки
* @package models
*/
class DebugModel implements ModelAPI {

    use APIAware;

    protected $allowedAPIMethods = [
        'getAll', 'getById', 'getStats', 'delete', 'deleteAll', 'markAsFixed'
    ];
    
    private $db;
    private $tableName;
    
    /**
    * Конструктор модели
    * @param Database $db
    */
    public function __construct($db) {
        $this->db = $db;
        $this->tableName = $this->db->getPrefix() . 'debug_logs';
    }
    
    /**
    * Сохраняет ошибку в базу данных
    * @param array $data
    * @return int ID записи
    */
    public function save($data) {
        $sql = "INSERT INTO `{$this->tableName}` (
            type, code, message, file, line, trace, context, 
            url, method, ip, user_id, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['type'],
            $data['code'] ?? null,
            $data['message'],
            $data['file'] ?? null,
            $data['line'] ?? null,
            $data['trace'] ?? null,
            $data['context'] ?? null,
            $data['url'] ?? null,
            $data['method'] ?? null,
            $data['ip'] ?? null,
            $data['user_id'] ?? null,
            date('Y-m-d H:i:s')
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
    * Получает все логи с пагинацией
    * @param int $page
    * @param int $perPage
    * @param string|null $type
    * @param bool $onlyUnfixed
    * @return array
    */
    public function getAll($page = 1, $perPage = 20, $type = null, $onlyUnfixed = false) {
        $page = (int)$page;
        $perPage = (int)$perPage;
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM `{$this->tableName}` WHERE 1=1";
        $params = [];
        
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        if ($onlyUnfixed) {
            $sql .= " AND is_fixed = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $logs = $this->db->fetchAll($sql, $params);
        
        foreach ($logs as &$log) {
            $log['trace_decoded'] = $log['trace'] ? json_decode($log['trace'], true) : [];
            $log['context_decoded'] = $log['context'] ? json_decode($log['context'], true) : [];
            $log['created_formatted'] = date('d.m.Y H:i:s', strtotime($log['created_at']));
            $log['type_label'] = $this->getTypeLabel($log['type']);
            $log['type_class'] = $this->getTypeClass($log['type']);
        }
        
        $countSql = "SELECT COUNT(*) as total FROM `{$this->tableName}` WHERE 1=1";
        $countParams = [];
        
        if ($type) {
            $countSql .= " AND type = ?";
            $countParams[] = $type;
        }
        
        if ($onlyUnfixed) {
            $countSql .= " AND is_fixed = 0";
        }
        
        $totalResult = $this->db->fetch($countSql, $countParams);
        
        return [
            'logs' => $logs,
            'total' => (int)($totalResult['total'] ?? 0),
            'pages' => ceil(($totalResult['total'] ?? 0) / $perPage),
            'current_page' => $page,
            'per_page' => $perPage
        ];
    }
    
    /**
    * Получает лог по ID
    * @param int $id
    * @return array|null
    */
    public function getById($id) {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE id = ?";
        $log = $this->db->fetch($sql, [$id]);
        
        if ($log) {
            $log['trace_decoded'] = $log['trace'] ? json_decode($log['trace'], true) : [];
            $log['context_decoded'] = $log['context'] ? json_decode($log['context'], true) : [];
            $log['created_formatted'] = date('d.m.Y H:i:s', strtotime($log['created_at']));
            $log['type_label'] = $this->getTypeLabel($log['type']);
            $log['type_class'] = $this->getTypeClass($log['type']);
        }
        
        return $log;
    }
    
    /**
    * Получает статистику по логам
    * @return array
    */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN type = 'error' THEN 1 ELSE 0 END) as errors,
                    SUM(CASE WHEN type = 'warning' THEN 1 ELSE 0 END) as warnings,
                    SUM(CASE WHEN type = 'notice' THEN 1 ELSE 0 END) as notices,
                    SUM(CASE WHEN type = 'exception' THEN 1 ELSE 0 END) as exceptions,
                    SUM(CASE WHEN is_fixed = 0 THEN 1 ELSE 0 END) as unfixed
                FROM `{$this->tableName}`";
        
        $stats = $this->db->fetch($sql);
        
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as count,
                    SUM(CASE WHEN type = 'error' THEN 1 ELSE 0 END) as errors
                FROM `{$this->tableName}`
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
        
        $dailyStats = $this->db->fetchAll($sql);
        
        return [
            'total' => (int)($stats['total'] ?? 0),
            'errors' => (int)($stats['errors'] ?? 0),
            'warnings' => (int)($stats['warnings'] ?? 0),
            'notices' => (int)($stats['notices'] ?? 0),
            'exceptions' => (int)($stats['exceptions'] ?? 0),
            'unfixed' => (int)($stats['unfixed'] ?? 0),
            'daily' => $dailyStats
        ];
    }
    
    /**
    * Удаляет лог по ID
    * @param int $id
    * @return bool
    */
    public function delete($id) {
        $sql = "DELETE FROM `{$this->tableName}` WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    /**
    * Удаляет все логи
    * @return bool
    */
    public function deleteAll() {
        $sql = "TRUNCATE TABLE `{$this->tableName}`";
        return $this->db->query($sql);
    }
    
    /**
    * Отмечает ошибку как исправленную
    * @param int $id
    * @return bool
    */
    public function markAsFixed($id) {
        $sql = "UPDATE `{$this->tableName}` SET is_fixed = 1 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    /**
    * Возвращает читаемое название типа ошибки
    * @param string $type
    * @return string
    */
    private function getTypeLabel($type) {
        $labels = [
            'error' => 'Ошибка PHP',
            'warning' => 'Предупреждение',
            'notice' => 'Уведомление',
            'exception' => 'Исключение'
        ];
        return $labels[$type] ?? $type;
    }
    
    /**
    * Возвращает CSS класс для типа ошибки
    * @param string $type
    * @return string
    */
    private function getTypeClass($type) {
        $classes = [
            'error' => 'danger',
            'warning' => 'warning',
            'notice' => 'info',
            'exception' => 'dark'
        ];
        return $classes[$type] ?? 'secondary';
    }
}