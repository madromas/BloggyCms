<?php

/**
* Модель для работы со страницами в базе данных
* @package Models
*/
class PageModel implements ModelAPI {

    use APIAware;

    protected $allowedAPIMethods = [
        'getBySlug',
        'getById',
        'getAll',
        'getBlocks',
        'getRecent',
        'search',
        'getStats'
    ];
    
    private $db;
    private $postBlockModel;
    
    /**
    * Конструктор модели
    * @param object $db Подключение к базе данных
    */
    public function __construct($db) {
        $this->db = $db;
        $this->postBlockModel = new PostBlockModel($db);
    }
    
    /**
    * Получает список всех страниц 
    * @return array Массив всех страниц, отсортированных по заголовку
    */
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM pages ORDER BY title");
    }
    
    /**
    * Получает страницу по её ID
    * @param int $id ID страницы
    * @return array|null Данные страницы или null, если страница не найдена
    */
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM pages WHERE id = ?", [$id]);
    }
    
    /**
    * Получает страницу по её URL-адресу (slug)
    * @param string $slug URL-адрес страницы
    * @return array|null Данные страницы или null, если страница не найдена
    */
    public function getBySlug($slug) {
        return $this->db->fetch("SELECT * FROM pages WHERE slug = ?", [$slug]);
    }
    
    /**
    * Создает новую страницу
    */
    public function create($data) {
        $slug = !empty($data['slug']) 
            ? $this->createUniqueSlug($data['slug']) 
            : $this->createUniqueSlug($data['title']);
        
        $sql = "INSERT INTO pages (title, slug, status) VALUES (?, ?, ?)";
        $this->db->query($sql, [
            $data['title'],
            $slug,
            $data['status'] ?? 'draft'
        ]);

        $pageId = $this->db->lastInsertId();

        Event::trigger('page.created', [
            $pageId,
            $data['title'],
            $slug,
            $data
        ]);
        
        return $pageId;
    }
    
    /**
    * Обновляет существующую страницу
    * @param int $id ID страницы
    * @param array $data Данные для обновления
    * @return bool Результат выполнения запроса
    */
    public function update($id, $data) {

        $oldPage = $this->getById($id);
        
        if (!$oldPage) {
            throw new Exception('Страница не найдена');
        }
        
        $slug = !empty($data['slug']) 
            ? $this->createUniqueSlug($data['slug'], $id) 
            : $this->createUniqueSlug($data['title'], $id);
        
        $sql = "UPDATE pages SET title = ?, slug = ?, status = ? WHERE id = ?";
        
        $result = $this->db->query($sql, [
            $data['title'],
            $slug,
            $data['status'] ?? 'draft',
            $id
        ]);

        Event::trigger('page.updated', [
            $id,
            $oldPage,
            $data
        ]);
        
        return $result;
    }
    
    /**
    * Удаляет страницу и все связанные с ней блоки
    * @param int $id ID страницы
    * @return bool Результат выполнения запроса
    */
    public function delete($id) {
        try {

            $page = $this->getById($id);
            
            if (!$page) {
                throw new Exception('Страница не найдена');
            }
            
            $this->db->beginTransaction();
            
            $this->postBlockModel->deleteByPage($id);
            
            $this->db->query(
                "DELETE FROM field_values WHERE entity_type = 'page' AND entity_id = ?", 
                [$id]
            );
            
            $result = $this->db->query("DELETE FROM pages WHERE id = ?", [$id]);
            
            $this->db->commit();

            Event::trigger('page.deleted', [
                $id,
                $page['title'],
                $page['slug']
            ]);
            
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
    * Создает URL-адрес (slug) из заголовка
    * @param string $title Заголовок страницы
    * @return string Сгенерированный URL-адрес
    */
    private function createSlug($title) {

        $slug = mb_strtolower($title, 'UTF-8');

        $slug = $this->transliterate($slug);
        
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        
        $slug = preg_replace('/-+/', '-', $slug);
        
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
    * Создает уникальный URL-адрес, добавляя числовой суффикс при необходимости
    * @param string $title Заголовок страницы
    * @param int|null $excludeId ID страницы для исключения из проверки (при обновлении)
    * @return string Уникальный URL-адрес
    */
    private function createUniqueSlug($title, $excludeId = null) {
        $baseSlug = $this->createSlug($title);
        $slug = $baseSlug;
        $counter = 1;
        
        while ($this->isSlugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
    * Проверяет, существует ли уже указанный URL-адрес в базе данных 
    * @param string $slug URL-адрес для проверки
    * @param int|null $excludeId ID страницы для исключения из проверки
    * @return bool true если URL уже существует, false если свободен
    */
    private function isSlugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM pages WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
    * Транслитерирует кириллические символы в латиницу 
    * @param string $string Строка для транслитерации
    * @return string Транслитерированная строка
    */
    private function transliterate($string) {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
            
            'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
            'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
            'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
            'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
            'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
            'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
            'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya'
        );
        
        return strtr($string, $converter);
    }

    /**
    * Получает блоки контента для указанной страницы 
    * @param int $pageId ID страницы
    * @return array Массив блоков контента
    */
    public function getBlocks($pageId) {
        return $this->postBlockModel->getByPage($pageId);
    }
    
    /**
    * Создает страницу вместе с её блоками (для обратной совместимости)
    * @param array $data Данные страницы
    * @param array $blocks Массив блоков контента
    * @return int ID созданной страницы
    * @throws Exception При ошибке создания
    */
    public function createWithBlocks($data, $blocks) {
        try {
            $pageId = $this->create($data);
            
            foreach ($blocks as $order => $block) {
                $this->postBlockModel->createForPage(
                    $pageId,
                    $block['type'],
                    $block['content'],
                    $block['settings'] ?? [],
                    $order
                );
            }
            
            return $pageId;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
    * Обновляет страницу вместе с её блоками (для обратной совместимости)
    * @param int $pageId ID страницы
    * @param array $data Данные для обновления
    * @param array $blocks Массив новых блоков контента
    * @return bool true при успешном обновлении
    * @throws Exception При ошибке обновления
    */
    public function updateWithBlocks($pageId, $data, $blocks) {
        try {
            $this->update($pageId, $data);
            
            $this->postBlockModel->deleteByPage($pageId);
            
            foreach ($blocks as $order => $block) {
                $this->postBlockModel->createForPage(
                    $pageId,
                    $block['type'],
                    $block['content'],
                    $block['settings'] ?? [],
                    $order
                );
            }
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
    * Получает статистику по страницам
    * @return array Статистика с полями: общее количество страниц, количество опубликованных, количество черновиков
    */
    public function getStats() {
        return $this->db->fetch("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft
            FROM pages
        ");
    }

    /**
    * Ищет страницы по заголовку или URL-адресу
    * @param string $query Поисковый запрос
    * @return array Массив найденных страниц
    */
    public function search($query) {
        return $this->db->fetchAll("
            SELECT * FROM pages 
            WHERE title LIKE ? OR slug LIKE ?
            ORDER BY title
        ", ["%$query%", "%$query%"]);
    }

    /**
    * Получает последние созданные страницы 
    * @param int $limit Максимальное количество страниц (по умолчанию 10)
    * @return array Массив последних страниц
    */
    public function getRecent($limit = 10) {
        return $this->db->fetchAll("
            SELECT * FROM pages 
            ORDER BY created_at DESC 
            LIMIT ?
        ", [$limit]);
    }
}