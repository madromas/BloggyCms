<?php
namespace seo\actions;

/**
* Действие сохранения настроек robots.txt
*/
class AdminRobots extends SeoAction {
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа');
            $this->redirect(ADMIN_URL . '/seo');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $settings = [
                    'enabled' => isset($_POST['enabled']) ? 1 : 0,
                    'disallow_paths' => explode("\n", $_POST['disallow_paths'] ?? ''),
                    'allow_paths' => explode("\n", $_POST['allow_paths'] ?? ''),
                    'crawl_delay' => $_POST['crawl_delay'] ?? '',
                    'sitemap_url' => $_POST['sitemap_url'] ?? ''
                ];

                $this->seoModel->saveSettings('seo_robots', $settings);
                \Notification::success('Настройки robots.txt сохранены');
            } catch (\Exception $e) {
                \Notification::error('Ошибка: ' . $e->getMessage());
            }
        }

        $this->redirect(ADMIN_URL . '/seo');
    }
}