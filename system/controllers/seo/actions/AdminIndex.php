<?php
namespace seo\actions;

/**
* Действие отображения главной страницы SEO настроек
*/
class AdminIndex extends SeoAction {
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа к этому разделу');
            $this->redirect(ADMIN_URL . '/login');
            return;
        }

        try {
            $robotsSettings = $this->seoModel->getRobotsSettings();
            $sitemapSettings = $this->seoModel->getSitemapSettings();
            $rssSettings = $this->seoModel->getRssSettings();

            $this->render('admin/seo/index', [
                'robots_settings' => $robotsSettings,
                'sitemap_settings' => $sitemapSettings,
                'rss_settings' => $rssSettings,
                'pageTitle' => 'SEO Настройки'
            ]);
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке SEO настроек: ' . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }
}