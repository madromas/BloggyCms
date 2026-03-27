<?php
/**
* Модель для работы с SEO-настройками
* Управляет robots.txt, sitemap.xml, RSS-лентами и мета-тегами
*
* @package Models
*/
class SeoModel implements ModelAPI {
    use APIAware;
    
    protected $allowedAPIMethods = [
        'getRobotsSettings',
        'getSitemapSettings',
        'getRssSettings',
        'getMetaSettings',
        'saveSettings',
        'generateSitemap',
        'generateRobots',
        'generateRss'
    ];

    /** @var \Database Подключение к базе данных */
    private $db;

    /** @var \SettingsModel Модель настроек */
    private $settingsModel;

    /**
    * Конструктор модели
    */
    public function __construct($db) {
        $this->db = $db;
        $this->settingsModel = new \SettingsModel($db);
    }

    /**
    * Получение настроек robots.txt
    */
    public function getRobotsSettings() {
        $default = [
            'enabled' => true,
            'disallow_paths' => [
                '/admin/',
                '/system/',
                '/uploads/temp/',
                '/cache/'
            ],
            'allow_paths' => [],
            'sitemap_url' => '',
            'crawl_delay' => '',
            'user_agents' => []
        ];
        
        $settings = $this->settingsModel->get('seo_robots', []);
        return array_merge($default, $settings);
    }

    /**
    * Получение настроек sitemap
    */
    public function getSitemapSettings() {
        $default = [
            'enabled' => true,
            'include_posts' => true,
            'include_pages' => true,
            'include_categories' => true,
            'include_tags' => true,
            'include_users' => false,
            'posts_priority' => 0.8,
            'pages_priority' => 0.9,
            'categories_priority' => 0.7,
            'tags_priority' => 0.6,
            'users_priority' => 0.5,
            'change_frequency_posts' => 'weekly',
            'change_frequency_pages' => 'monthly',
            'change_frequency_categories' => 'weekly',
            'change_frequency_tags' => 'weekly',
            'max_posts' => 1000,
            'cache_enabled' => true,
            'cache_lifetime' => 3600
        ];
        
        $settings = $this->settingsModel->get('seo_sitemap', []);
        return array_merge($default, $settings);
    }

    /**
    * Получение настроек RSS
    */
    public function getRssSettings() {
        $default = [
            'enabled' => true,
            'posts_enabled' => true,
            'posts_limit' => 20,
            'categories_enabled' => true,
            'tags_enabled' => true,
            'include_full_content' => false,
            'include_images' => true,
            'copyright' => '',
            'language' => 'ru-ru',
            'ttl' => 60
        ];
        
        $settings = $this->settingsModel->get('seo_rss', []);
        return array_merge($default, $settings);
    }

    /**
    * Получение общих SEO настроек
    */
    public function getMetaSettings() {
        $default = [
            'default_title' => '',
            'default_description' => '',
            'default_keywords' => '',
            'title_separator' => '-',
            'title_format' => '{title} {separator} {site_name}',
            'canonical_enabled' => true,
            'og_enabled' => true,
            'twitter_enabled' => true,
            'schema_enabled' => true,
            'robots_meta_default' => 'index, follow'
        ];
        
        $settings = $this->settingsModel->get('seo_meta', []);
        return array_merge($default, $settings);
    }

    /**
    * Сохранение настроек
    */
    public function saveSettings($group, $settings) {
        return $this->settingsModel->save($group, $settings);
    }

    /**
    * Генерация sitemap.xml
    */
    public function generateSitemap() {
        $settings = $this->getSitemapSettings();
        
        if (!$settings['enabled']) {
            return '';
        }
        
        $baseUrl = $this->getBaseUrl();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Главная страница
        $xml .= $this->createUrlEntry($baseUrl, date('Y-m-d'), 'daily', 1.0);
        
        // Посты
        if ($settings['include_posts']) {
            $xml .= $this->generatePostsSitemap($settings);
        }
        
        // Страницы
        if ($settings['include_pages']) {
            $xml .= $this->generatePagesSitemap($settings);
        }
        
        // Категории
        if ($settings['include_categories']) {
            $xml .= $this->generateCategoriesSitemap($settings);
        }
        
        // Теги
        if ($settings['include_tags']) {
            $xml .= $this->generateTagsSitemap($settings);
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    /**
    * Генерация записей для постов
    */
    private function generatePostsSitemap($settings) {
        $baseUrl = $this->getBaseUrl();
        $limit = (int)$settings['max_posts'];
        $priority = (float)$settings['posts_priority'];
        $changefreq = $settings['change_frequency_posts'];

        $posts = $this->db->fetchAll("
            SELECT id, slug, created_at, updated_at, featured_image
            FROM posts
            WHERE status = 'published'
            ORDER BY created_at DESC
            LIMIT ?
        ", [$limit]);

        $xml = '';
        foreach ($posts as $post) {
            $loc = $baseUrl . '/post/' . $post['slug'];
            $lastmod = !empty($post['updated_at']) ? $post['updated_at'] : $post['created_at'];
            $skip = false;
            \Event::trigger('seo.sitemap.post_url', [
                'post' => $post,
                'url' => &$loc,
                'skip' => &$skip
            ]);
            
            if ($skip) continue;

            $xml .= $this->createUrlEntry($loc, $lastmod, $changefreq, $priority);
        }

        return $xml;
    }

    /**
    * Генерация записей для страниц
    */
    private function generatePagesSitemap($settings) {
        $baseUrl = $this->getBaseUrl();
        $priority = (float)$settings['pages_priority'];
        $changefreq = $settings['change_frequency_pages'];

        $pages = $this->db->fetchAll("
            SELECT id, slug, created_at, updated_at
            FROM pages
            WHERE status = 'published'
            ORDER BY created_at DESC
        ");

        $xml = '';
        foreach ($pages as $page) {
            $loc = $baseUrl . '/page/' . $page['slug'];
            $lastmod = !empty($page['updated_at']) ? $page['updated_at'] : $page['created_at'];
            
            $skip = false;
            \Event::trigger('seo.sitemap.page_url', [
                'page' => $page,
                'url' => &$loc,
                'skip' => &$skip
            ]);
            
            if ($skip) continue;

            $xml .= $this->createUrlEntry($loc, $lastmod, $changefreq, $priority);
        }

        return $xml;
    }

    /**
    * Генерация записей для категорий
    */
    private function generateCategoriesSitemap($settings) {
        $baseUrl = $this->getBaseUrl();
        $priority = (float)$settings['categories_priority'];
        $changefreq = $settings['change_frequency_categories'];

        $categories = $this->db->fetchAll("
            SELECT id, slug, created_at
            FROM categories
            ORDER BY created_at DESC
        ");

        $xml = '';
        foreach ($categories as $category) {
            $loc = $baseUrl . '/category/' . $category['slug'];
            
            $skip = false;
            \Event::trigger('seo.sitemap.category_url', [
                'category' => $category,
                'url' => &$loc,
                'skip' => &$skip
            ]);
            
            if ($skip) continue;

            $xml .= $this->createUrlEntry($loc, $category['created_at'], $changefreq, $priority);
        }

        return $xml;
    }

    /**
    * Генерация записей для тегов
    */
    private function generateTagsSitemap($settings) {
        $baseUrl = $this->getBaseUrl();
        $priority = (float)$settings['tags_priority'];
        $changefreq = $settings['change_frequency_tags'];

        $tags = $this->db->fetchAll("
            SELECT id, slug, created_at
            FROM tags
            ORDER BY created_at DESC
        ");

        $xml = '';
        foreach ($tags as $tag) {
            $loc = $baseUrl . '/tag/' . $tag['slug'];
            
            $skip = false;
            \Event::trigger('seo.sitemap.tag_url', [
                'tag' => $tag,
                'url' => &$loc,
                'skip' => &$skip
            ]);
            
            if ($skip) continue;

            $xml .= $this->createUrlEntry($loc, $tag['created_at'], $changefreq, $priority);
        }

        return $xml;
    }

    /**
    * Создание XML записи URL
    */
    private function createUrlEntry($loc, $lastmod, $changefreq, $priority) {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . $this->escapeXml($loc) . "</loc>\n";
        $xml .= "    <lastmod>" . $this->formatXmlDate($lastmod) . "</lastmod>\n";
        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        $xml .= "    <priority>" . number_format($priority, 1, '.', '') . "</priority>\n";
        $xml .= "  </url>\n";
        return $xml;
    }

    /**
    * Генерация robots.txt
    */
    public function generateRobots() {
        $settings = $this->getRobotsSettings();
        
        if (!$settings['enabled']) {
            return '';
        }
        
        $baseUrl = $this->getBaseUrl();
        $robots = "# Robots.txt для " . parse_url($baseUrl, PHP_URL_HOST) . "\n";
        $robots .= "# Сгенерировано: " . date('Y-m-d H:i:s') . "\n\n";
        
        $robots .= "User-agent: *\n";
        
        // Disallow
        if (!empty($settings['disallow_paths']) && is_array($settings['disallow_paths'])) {
            foreach ($settings['disallow_paths'] as $path) {
                $path = trim($path);
                if (!empty($path)) {
                    $robots .= "Disallow: " . $path . "\n";
                }
            }
        } else {
            $robots .= "Disallow: /admin/\n";
            $robots .= "Disallow: /system/\n";
        }
        
        // Allow (если есть)
        if (!empty($settings['allow_paths']) && is_array($settings['allow_paths'])) {
            foreach ($settings['allow_paths'] as $path) {
                $path = trim($path);
                if (!empty($path)) {
                    $robots .= "Allow: " . $path . "\n";
                }
            }
        }
        
        // Crawl-delay
        if (!empty($settings['crawl_delay']) && $settings['crawl_delay'] > 0) {
            $robots .= "Crawl-delay: " . (int)$settings['crawl_delay'] . "\n";
        }
        
        // Sitemap
        $sitemapUrl = !empty($settings['sitemap_url']) 
            ? $settings['sitemap_url'] 
            : $baseUrl . '/sitemap.xml';
        $robots .= "\nSitemap: " . $sitemapUrl . "\n";
        
        return $robots;
    }

    /**
    * Генерация RSS ленты
    */
    public function generateRss($type = 'posts', $filter = null) {
        $settings = $this->getRssSettings();
        
        if (!$settings['enabled']) {
            return '';
        }
        
        $baseUrl = $this->getBaseUrl();
        $siteName = 'BloggyCMS';
        
        try {
            $settingsModel = new SettingsModel($this->db);
            $generalSettings = $settingsModel->get('general');
            $siteName = $generalSettings['site_name'] ?? 'BloggyCMS';
        } catch (Exception $e) {}
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">' . "\n";
        $xml .= "<channel>\n";
        $xml .= "  <title>" . $this->escapeXml($siteName) . "</title>\n";
        $xml .= "  <link>" . $this->escapeXml($baseUrl) . "</link>\n";
        $xml .= "  <description>RSS лента блога</description>\n";
        $xml .= "  <language>" . $settings['language'] . "</language>\n";
        $xml .= "  <ttl>" . (int)$settings['posts_limit'] . "</ttl>\n";
        $xml .= "  <atom:link href=\"" . $this->escapeXml($baseUrl) . "/rss.xml\" rel=\"self\" type=\"application/rss+xml\" />\n";
        
        if (!empty($settings['copyright'])) {
            $xml .= "  <copyright>" . $this->escapeXml($settings['copyright']) . "</copyright>\n";
        }
        
        $posts = $this->getRssItems($type, $filter, $settings);
        
        foreach ($posts as $post) {
            $xml .= $this->createRssItem($post, $settings, $baseUrl);
        }
        
        $xml .= "</channel>\n</rss>";
        
        return $xml;
    }

    /**
    * Получение элементов для RSS
    */
    private function getRssItems($type, $filter, $settings) {
        $limit = (int)$settings['posts_limit'];

        switch ($type) {
            case 'category':
                $categoryId = (int)$filter;
                return $this->db->fetchAll("
                    SELECT p.*, c.name as category_name, c.slug as category_slug,
                           u.username as author_name
                    FROM posts p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN users u ON p.user_id = u.id
                    WHERE p.status = 'published' AND p.category_id = ?
                    ORDER BY p.created_at DESC
                    LIMIT ?
                ", [$categoryId, $limit]);

            case 'tag':
                $tagId = (int)$filter;
                return $this->db->fetchAll("
                    SELECT DISTINCT p.*, u.username as author_name
                    FROM posts p
                    LEFT JOIN post_tags pt ON p.id = pt.post_id
                    LEFT JOIN users u ON p.user_id = u.id
                    WHERE p.status = 'published' AND pt.tag_id = ?
                    ORDER BY p.created_at DESC
                    LIMIT ?
                ", [$tagId, $limit]);

            case 'posts':
            default:
                return $this->db->fetchAll("
                    SELECT p.*, c.name as category_name, c.slug as category_slug,
                           u.username as author_name
                    FROM posts p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN users u ON p.user_id = u.id
                    WHERE p.status = 'published'
                    ORDER BY p.created_at DESC
                    LIMIT ?
                ", [$limit]);
        }
    }

    /**
    * Создание RSS элемента
    */
    private function createRssItem($post, $settings, $baseUrl) {
        $xml = "  <item>\n";
        $xml .= "    <title>" . $this->escapeXml($post['title']) . "</title>\n";
        $xml .= "    <link>" . $this->escapeXml($baseUrl . '/post/' . $post['slug']) . "</link>\n";
        $xml .= "    <guid isPermaLink=\"true\">" . $this->escapeXml($baseUrl . '/post/' . $post['slug']) . "</guid>\n";
        $xml .= "    <pubDate>" . date('D, d M Y H:i:s O', strtotime($post['created_at'])) . "</pubDate>\n";
        
        $description = $post['short_description'] ?? '';
        if (empty($description) && !empty($post['content'])) {
            $description = strip_tags($post['content']);
            $description = mb_substr($description, 0, 500);
        }
        $xml .= "    <description>" . $this->escapeXml($description) . "</description>\n";
        if ($settings['include_full_content'] && !empty($post['content'])) {
            $content = $post['content'];
            $xml .= "    <content:encoded><![CDATA[" . $content . "]]></content:encoded>\n";
        }

        if (!empty($post['author_name'])) {
            $xml .= "    <author>" . $this->escapeXml($post['author_name']) . "</author>\n";
        }

        if (!empty($post['category_name'])) {
            $xml .= "    <category>" . $this->escapeXml($post['category_name']) . "</category>\n";
        }

        $xml .= "  </item>\n";
        
        return $xml;
    }

    /**
    * Очистка кэша SEO
    */
    public function clearCache() {
        \Event::trigger('seo.cache.clear');
        return true;
    }

    /**
    * Получение базового URL сайта
    * @return string
    */
    private function getBaseUrl() {
        return defined('BASE_URL') ? BASE_URL : 'http://localhost';
    }

    /**
    * Экранирование для XML
    * @param string $string
    * @return string
    */
    protected function escapeXml($string) {
        return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
    * Форматирование даты для XML
    * @param string|int $timestamp
    * @return string
    */
    private function formatXmlDate($timestamp) {
        if (empty($timestamp)) {
            return date('Y-m-d\TH:i:s+00:00');
        }
        return date('Y-m-d\TH:i:s+00:00', strtotime($timestamp));
    }

}