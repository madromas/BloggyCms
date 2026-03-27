<?php
namespace seo\actions;

class GenerateRobots extends SeoAction {
    
    public function execute() {
        try {
            
            $settings = $this->seoModel->getRobotsSettings();
            $robots = $this->seoModel->generateRobots();
            
            header('Content-Type: text/plain; charset=utf-8');
            echo $robots;
            exit;
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo "# Error: " . $e->getMessage();
            exit;
        }
    }
}