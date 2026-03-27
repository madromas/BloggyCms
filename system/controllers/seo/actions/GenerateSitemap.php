<?php
namespace seo\actions;

/**
* Действие генерации sitemap.xml
*/
class GenerateSitemap extends SeoAction {
    
    public function execute() {
        try {
            $cacheFile = CACHE_DIR . '/sitemap.xml';
            $settings = $this->seoModel->getSitemapSettings();
            
            if ($settings['cache_enabled'] && file_exists($cacheFile)) {
                $cacheTime = filemtime($cacheFile);
                if (time() - $cacheTime < $settings['cache_lifetime']) {
                    $this->sendXmlResponse(file_get_contents($cacheFile));
                }
            }
            
            $xml = $this->seoModel->generateSitemap();
            
            if (empty($xml)) {
                http_response_code(404);
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<error>Sitemap disabled</error>';
                exit;
            }
            
            if ($settings['cache_enabled']) {
                if (!is_dir(CACHE_DIR)) {
                    mkdir(CACHE_DIR, 0755, true);
                }
                file_put_contents($cacheFile, $xml);
            }
            
            $this->sendXmlResponse($xml);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<error>' . $this->escapeXml($e->getMessage()) . '</error>';
            exit;
        }
    }
    
    private function sendXmlResponse($xml) {
        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');
        echo $xml;
        exit;
    }
}