<?php

/**
* Модель попыток входа в систему
* @package models
*/
class LoginAttemptModel {
    
    private $db;
    
    /**
    * Конструктор модели попыток входа
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
    * Получение текущего IP адреса клиента
    * @return string IP адрес клиента
    */
    public function getClientIP() {
        $ip = $_SERVER['HTTP_CLIENT_IP'] ?? 
              $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
              $_SERVER['HTTP_X_FORWARDED'] ?? 
              $_SERVER['HTTP_FORWARDED_FOR'] ?? 
              $_SERVER['HTTP_FORWARDED'] ?? 
              $_SERVER['REMOTE_ADDR'] ?? 
              '0.0.0.0';
        
        if (strpos($ip, ',') !== false) {
            $ips = explode(',', $ip);
            $ip = trim($ips[0]);
        }
        
        return $ip;
    }

    /**
    * Получение информации о попытках входа для IP адреса
    * @param string|null $ip IP адрес (если null, используется текущий)
    * @return array|null Данные о попытках входа или null если записей нет
    */
    public function getLoginAttempts($ip = null) {
        if (!$ip) {
            $ip = $this->getClientIP();
        }

        $result = $this->db->fetch(
            "SELECT * FROM login_attempts WHERE ip_address = ?",
            [$ip]
        );

        if ($result) {
            $this->cleanupOldRecords();
            
            return $result;
        }

        return null;
    }

    /**
    * Увеличение счетчика попыток входа
    * @param string|null $ip IP адрес (если null, используется текущий)
    * @param int $maxAttempts Максимальное количество попыток до блокировки
    * @param int $blockTimeMinutes Время блокировки в минутах
    */
    public function incrementAttempt($ip = null, $maxAttempts = 3, $blockTimeMinutes = 20) {
        if (!$ip) {
            $ip = $this->getClientIP();
        }

        $attempts = $this->getLoginAttempts($ip);

        if ($attempts) {
            $newAttempts = $attempts['attempts'] + 1;
            $blockedUntil = null;

            if ($newAttempts >= $maxAttempts) {
                $blockedUntil = date('Y-m-d H:i:s', time() + ($blockTimeMinutes * 60));
            }

            $this->db->query(
                "UPDATE login_attempts SET attempts = ?, last_attempt = NOW(), blocked_until = ? WHERE ip_address = ?",
                [$newAttempts, $blockedUntil, $ip]
            );

            return [
                'attempts' => $newAttempts,
                'blocked_until' => $blockedUntil,
                'is_blocked' => $blockedUntil !== null
            ];
        } else {
            $this->db->query(
                "INSERT INTO login_attempts (ip_address, attempts, last_attempt) VALUES (?, 1, NOW())",
                [$ip]
            );

            return [
                'attempts' => 1,
                'blocked_until' => null,
                'is_blocked' => false
            ];
        }
    }

    /**
    * Сброс счетчика попыток входа
    * @param string|null $ip IP адрес (если null, используется текущий)
    * @return void
    */
    public function resetAttempts($ip = null) {
        if (!$ip) {
            $ip = $this->getClientIP();
        }

        $this->db->query(
            "DELETE FROM login_attempts WHERE ip_address = ?",
            [$ip]
        );
    }

    /**
    * Проверка блокировки IP адреса
    * @param string|null $ip IP адрес (если null, используется текущий)
    * @return bool true если IP заблокирован
    */
    public function isBlocked($ip = null) {
        if (!$ip) {
            $ip = $this->getClientIP();
        }

        $result = $this->db->fetch(
            "SELECT blocked_until FROM login_attempts WHERE ip_address = ? AND blocked_until IS NOT NULL AND blocked_until > NOW()",
            [$ip]
        );

        return $result !== false;
    }

    /**
    * Получение времени разблокировки IP
    * @param string|null $ip IP адрес (если null, используется текущий)
    * @return int Временная метка разблокировки или 0 если нет блокировки
    */
    public function getUnlockTime($ip = null) {
        if (!$ip) {
            $ip = $this->getClientIP();
        }

        $result = $this->db->fetch(
            "SELECT blocked_until FROM login_attempts WHERE ip_address = ?",
            [$ip]
        );

        return $result ? strtotime($result['blocked_until']) : 0;
    }

    /**
    * Очистка старых записей о попытках входа
    * @return void
    */
    public function cleanupOldRecords() {
        $this->db->query(
            "DELETE FROM login_attempts WHERE 
            (blocked_until IS NULL AND last_attempt < DATE_SUB(NOW(), INTERVAL 24 HOUR)) OR
            (blocked_until IS NOT NULL AND blocked_until < DATE_SUB(NOW(), INTERVAL 1 HOUR))"
        );
    }

    /**
    * Получение статистики по попыткам входа для IP
    * @param string|null $ip IP адрес (если null, используется текущий)
    */
    public function getAttemptsInfo($ip = null) {
        if (!$ip) {
            $ip = $this->getClientIP();
        }

        $attempts = $this->getLoginAttempts($ip);
        
        if (!$attempts) {
            return [
                'attempts' => 0,
                'is_blocked' => false,
                'blocked_until' => null
            ];
        }

        return [
            'attempts' => $attempts['attempts'],
            'is_blocked' => $attempts['blocked_until'] && strtotime($attempts['blocked_until']) > time(),
            'blocked_until' => $attempts['blocked_until'] ? strtotime($attempts['blocked_until']) : null
        ];
    }

    /**
    * Умное увеличение счетчика попыток входа
    * @param string|null $username Имя пользователя (дополнительный идентификатор)
    * @param int $maxAttempts Максимальное количество попыток для идентификатора клиента
    * @param int $blockTimeMinutes Время блокировки в минутах
    * @return bool true если любой из идентификаторов заблокирован
    */
    public function smartIncrementAttempt($username = null, $maxAttempts = 5, $blockTimeMinutes = 30) {

        $client = $this->getClientIdentifier();
        
        $clientAttempts = $this->incrementByIdentifier($client['identifier'], $maxAttempts, $blockTimeMinutes);
        
        $ipAttempts = $this->incrementByIP($client['ip'], $maxAttempts + 3, $blockTimeMinutes);
        
        if ($username) {
            $usernameAttempts = $this->incrementByUsername($username, $maxAttempts - 1, $blockTimeMinutes);
        }
        
        return $clientAttempts['is_blocked'] || $ipAttempts['is_blocked'] || 
               (isset($usernameAttempts) && $usernameAttempts['is_blocked']);
    }
}