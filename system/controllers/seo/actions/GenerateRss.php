<?php
namespace seo\actions;

/**
* Действие генерации RSS ленты
*/
class GenerateRss extends SeoAction {
    
    public function execute() {
        try {
            $type = $this->params['type'] ?? 'posts';
            $slug = $this->params['slug'] ?? null;
            $filter = null;
            
            if ($slug) {
                if ($type === 'category') {
                    $category = $this->categoryModel->getBySlug($slug);
                    $filter = $category['id'] ?? null;
                } elseif ($type === 'tag') {
                    $tag = $this->tagModel->getBySlug($slug);
                    $filter = $tag['id'] ?? null;
                }
            }
            
            $rss = $this->seoModel->generateRss($type, $filter);
            
            if (empty($rss)) {
                http_response_code(404);
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<error>RSS disabled</error>';
                exit;
            }
            
            $filename = $type === 'posts' ? 'rss.xml' : "{$type}-{$slug}.xml";
            $this->sendXmlResponse($rss, $filename);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<error>' . $this->escapeXml($e->getMessage()) . '</error>';
            exit;
        }
    }
    
    private function sendXmlResponse($xml, $filename = null) {
        header('Content-Type: application/xml; charset=utf-8');
        if ($filename) {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        }
        echo $xml;
        exit;
    }
}