<?php

/**
* Модель для работы с установленными пакетами
* @package models
*/
class AddonModel implements ModelAPI {

    use APIAware;

    protected $allowedAPIMethods = [
        'getAll',
        'getById',
        'getBySystemName',
        'isInstalled',
        'getInstalledVersion'
    ];
    
    private $db;
    private $tableName;
    
    /**
    * Конструктор модели 
    * @param Database $db
    */
    public function __construct($db) {
        $this->db = $db;
        $this->tableName = $this->db->getPrefix() . 'installed_addons';
    }
    
    /**
    * Получение всех установленных пакетов 
    * @return array
    */
    public function getAll() {
        $sql = "SELECT * FROM `{$this->tableName}` ORDER BY installed_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    /**
    * Получение пакета по ID
    * @param int $id
    * @return array|null
    */
    public function getById($id) {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
    * Получение пакета по системному имени 
    * @param string $systemName
    * @return array|null
    */
    public function getBySystemName($systemName) {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE system_name = ?";
        return $this->db->fetch($sql, [$systemName]);
    }
    
    /**
    * Проверка, установлен ли пакет 
    * @param string $systemName
    * @return bool
    */
    public function isInstalled($systemName) {
        $sql = "SELECT id FROM `{$this->tableName}` WHERE system_name = ?";
        $result = $this->db->fetch($sql, [$systemName]);
        return !empty($result);
    }
    
    /**
    * Получение установленной версии пакета
    * @param string $systemName
    * @return string|null
    */
    public function getInstalledVersion($systemName) {
        $sql = "SELECT version_string FROM `{$this->tableName}` WHERE system_name = ?";
        $result = $this->db->fetch($sql, [$systemName]);
        return $result ? $result['version_string'] : null;
    }
    
    /**
    * Регистрация установленного пакета
    * @param array $packageInfo
    * @return int ID созданной записи
    */
    public function register($packageInfo) {
        $sql = "INSERT INTO `{$this->tableName}` (
            system_name, title, version_major, version_minor, version_build,
            version_string, author_name, author_url, author_email, description,
            type, installed_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $this->db->query($sql, [
            $packageInfo['system_name'],
            $packageInfo['title'],
            $packageInfo['version_major'],
            $packageInfo['version_minor'],
            $packageInfo['version_build'],
            $packageInfo['version_string'],
            $packageInfo['author_name'] ?? null,
            $packageInfo['author_url'] ?? null,
            $packageInfo['author_email'] ?? null,
            $packageInfo['description'] ?? null,
            $packageInfo['type'] ?? 'install'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
    * Обновление информации о пакете (при обновлении) 
    * @param string $systemName
    * @param array $packageInfo
    * @return bool
    */
    public function update($systemName, $packageInfo) {
        $sql = "UPDATE `{$this->tableName}` SET
            title = ?,
            version_major = ?,
            version_minor = ?,
            version_build = ?,
            version_string = ?,
            author_name = ?,
            author_url = ?,
            author_email = ?,
            description = ?,
            type = ?,
            updated_at = NOW()
            WHERE system_name = ?";
        
        return $this->db->query($sql, [
            $packageInfo['title'],
            $packageInfo['version_major'],
            $packageInfo['version_minor'],
            $packageInfo['version_build'],
            $packageInfo['version_string'],
            $packageInfo['author_name'] ?? null,
            $packageInfo['author_url'] ?? null,
            $packageInfo['author_email'] ?? null,
            $packageInfo['description'] ?? null,
            $packageInfo['type'] ?? 'update',
            $systemName
        ]);
    }
    
    /**
    * Удаление пакета из базы данных
    * @param int $id
    * @return bool
    */
    public function delete($id) {
        $sql = "DELETE FROM `{$this->tableName}` WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    /**
    * Удаление пакета по системному имени 
    * @param string $systemName
    * @return bool
    */
    public function deleteBySystemName($systemName) {
        $sql = "DELETE FROM `{$this->tableName}` WHERE system_name = ?";
        return $this->db->query($sql, [$systemName]);
    }
    
    /**
    * Получение количества установленных пакетов
    * @return int
    */
    public function getCount() {
        $sql = "SELECT COUNT(*) as count FROM `{$this->tableName}`";
        $result = $this->db->fetch($sql);
        return $result['count'] ?? 0;
    }
    
    /**
    * Проверка, существует ли пакет с таким системным именем
    * @param string $systemName
    * @return bool
    */
    public function exists($systemName) {
        $sql = "SELECT id FROM `{$this->tableName}` WHERE system_name = ?";
        $result = $this->db->fetch($sql, [$systemName]);
        return !empty($result);
    }
}