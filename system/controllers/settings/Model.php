<?php

/**
* Модель для работы с настройками в базе данных
* @package Models
*/
class SettingsModel implements ModelAPI {

    use APIAware;

    protected $allowedAPIMethods = [
        'get'
    ];
    
    private $db;
    private static $cache = [];
    
    /**
    * Конструктор модели
    * @param object $db Подключение к базе данных
    */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
    * Получить все настройки определенной группы
    * @param string $group Ключ группы настроек
    * @return array Массив настроек группы
    */
    public function get($group) {

        if (isset(self::$cache[$group])) {
            return self::$cache[$group];
        }
        
        $result = $this->db->fetch(
            "SELECT settings FROM settings WHERE group_key = ?",
            [$group]
        );
        
        $settings = $result ? json_decode($result['settings'], true) : [];
        
        self::$cache[$group] = $settings;
        
        return $settings;
    }
    
    /**
    * Сохранить настройки группы в базу данных
    * @param string $group Ключ группы настроек
    * @param array $settings Массив настроек для сохранения
    * @return bool true при успешном сохранении
    */
    public function save($group, $settings) {
        $existing = $this->db->fetch(
            "SELECT id, settings FROM settings WHERE group_key = ? ORDER BY id DESC LIMIT 1",
            [$group]
        );
        
        if ($existing) {
            $this->db->query(
                "UPDATE settings SET settings = ?, updated_at = NOW() WHERE id = ?",
                [json_encode($settings), $existing['id']]
            );
        } else {
            $this->db->query(
                "INSERT INTO settings (group_key, settings) VALUES (?, ?)",
                [$group, json_encode($settings)]
            );
        }
        
        self::$cache[$group] = $settings;
        
        if (class_exists('SettingsHelper')) {
            SettingsHelper::updateCache($group, $settings);
        }
        
        return true;
    }
    
    /**
    * Получить список всех групп настроек 
    * @return array Массив с ключами групп настроек
    */
    public function getAllGroups() {
        return $this->db->fetchAll("SELECT group_key FROM settings");
    }
    
    /**
    * Объединить новые настройки с существующими
    * @param string $group Ключ группы настроек
    * @param array $newSettings Массив новых настроек для объединения
    * @return bool true при успешном сохранении
    */
    public function merge($group, $newSettings) {
        $currentSettings = $this->get($group);
        $mergedSettings = array_merge($currentSettings, $newSettings);
        return $this->save($group, $mergedSettings);
    }
}