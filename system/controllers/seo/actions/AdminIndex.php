<?php
namespace seo\actions;

/**
* Действие отображения главной страницы SEO настроек
*/
class AdminIndex extends SeoAction {
    
    /**
    * Действие отображения главной страницы SEO настроек
    */
    public function execute() {
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('SEO');
        
        try {
            $robotsSettings = $this->seoModel->getRobotsSettings();
            $sitemapSettings = $this->seoModel->getSitemapSettings();
            $rssSettings = $this->seoModel->getRssSettings();
            $indexnowSettings = $this->seoModel->getIndexNowSettings();
            $schemaSettings = $this->seoModel->getSchemaSettings();
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
                'schema_settings' => $schemaSettings,
                'pageTitle' => 'SEO Настройки'
            ]);
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке SEO настроек: ' . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }

}