<?php
namespace seo\actions;

/**
* Действие обработки очереди IndexNow
*/
class AdminProcessQueue extends SeoAction {
    
    public function execute() {
        $token = $_GET['token'] ?? '';
        $secretToken = \SettingsHelper::get('seo', 'cron_token', '');
        
        if (empty($secretToken) || $token !== $secretToken) {
            http_response_code(403);
            echo "Access denied";
            exit;
        }
        
        try {
            $processed = $this->seoModel->processQueue(20);
            echo "Processed {$processed} tasks";
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
        
        exit;
    }
}