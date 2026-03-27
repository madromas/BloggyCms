<?php
namespace seo\actions;

class AdminSettings extends SeoAction {
    
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа');
            $this->redirect(ADMIN_URL . '/seo');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!\CsrfToken::verify($_POST['csrf_token'] ?? '', 'seo_settings')) {
                    throw new \Exception('Неверный CSRF токен');
                }

                $robotsSettings = [
                    'enabled' => isset($_POST['robots_enabled']) ? 1 : 0,
                    'disallow_paths' => array_filter(array_map('trim', explode("\n", $_POST['robots_disallow'] ?? ''))),
                    'allow_paths' => array_filter(array_map('trim', explode("\n", $_POST['robots_allow'] ?? ''))),
                    'crawl_delay' => (int)($_POST['robots_crawl_delay'] ?? 0),
                    'sitemap_url' => trim($_POST['robots_sitemap_url'] ?? '')
                ];

                $sitemapSettings = [
                    'enabled' => isset($_POST['sitemap_enabled']) ? 1 : 0,
                    'include_posts' => isset($_POST['sitemap_include_posts']) ? 1 : 0,
                    'include_pages' => isset($_POST['sitemap_include_pages']) ? 1 : 0,
                    'include_categories' => isset($_POST['sitemap_include_categories']) ? 1 : 0,
                    'include_tags' => isset($_POST['sitemap_include_tags']) ? 1 : 0,
                    'max_posts' => (int)($_POST['sitemap_max_posts'] ?? 1000),
                    'cache_enabled' => isset($_POST['sitemap_cache_enabled']) ? 1 : 0,
                    'cache_lifetime' => (int)($_POST['sitemap_cache_lifetime'] ?? 3600)
                ];

                $rssSettings = [
                    'enabled' => isset($_POST['rss_enabled']) ? 1 : 0,
                    'posts_limit' => (int)($_POST['rss_limit'] ?? 20),
                    'include_full_content' => isset($_POST['rss_full_content']) ? 1 : 0,
                    'copyright' => trim($_POST['rss_copyright'] ?? ''),
                    'language' => trim($_POST['rss_language'] ?? 'ru-ru')
                ];

                $this->seoModel->saveSettings('seo_robots', $robotsSettings);
                $this->seoModel->saveSettings('seo_sitemap', $sitemapSettings);
                $this->seoModel->saveSettings('seo_rss', $rssSettings);
                $this->forceGenerateFiles();
                $this->seoModel->clearCache();
                
                \Notification::success('Настройки SEO успешно сохранены и файлы обновлены');
                
            } catch (\Exception $e) {
                \Notification::error('Ошибка: ' . $e->getMessage());
            }
        }

        $this->redirect(ADMIN_URL . '/seo');
    }
    
    private function forceGenerateFiles() {
        $rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(dirname(dirname(dirname(__DIR__))));
        
        $robots = $this->seoModel->generateRobots();
        if (!empty($robots)) {
            $robotsPath = $rootPath . '/robots.txt';
            file_put_contents($robotsPath, $robots);
            error_log("SEO: robots.txt generated at " . $robotsPath);
        } else {
            error_log("SEO: robots.txt is empty");
        }
        
        $sitemap = $this->seoModel->generateSitemap();
        if (!empty($sitemap)) {
            $sitemapPath = $rootPath . '/sitemap.xml';
            file_put_contents($sitemapPath, $sitemap);
            error_log("SEO: sitemap.xml generated at " . $sitemapPath);
        } else {
            error_log("SEO: sitemap.xml is empty");
        }
        
        $rss = $this->seoModel->generateRss();
        if (!empty($rss)) {
            $rssPath = $rootPath . '/rss.xml';
            file_put_contents($rssPath, $rss);
            error_log("SEO: rss.xml generated at " . $rssPath);
        } else {
            error_log("SEO: rss.xml is empty");
        }
    }
}