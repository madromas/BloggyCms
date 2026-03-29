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
            $indexnowSettings = $this->seoModel->getIndexNowSettings();
            
            $rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(dirname(dirname(dirname(__DIR__))));
            $indexnowSettings['ya_key_exists'] = !empty($indexnowSettings['ya_key']) && 
                file_exists($rootPath . '/' . $indexnowSettings['ya_key'] . '.txt');
            $indexnowSettings['bing_key_exists'] = !empty($indexnowSettings['bing_key']) && 
                file_exists($rootPath . '/' . $indexnowSettings['bing_key'] . '.txt');
            
            $indexnowSettings['is_localhost'] = $this->seoModel->isLocalhost($this->seoModel->getHost());
            
            $this->render('admin/seo/index', [
                'robots_settings' => $robotsSettings,
                'sitemap_settings' => $sitemapSettings,
                'rss_settings' => $rssSettings,
                'indexnow_settings' => $indexnowSettings,
                'pageTitle' => 'SEO Настройки'
            ]);
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке SEO настроек: ' . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }
}