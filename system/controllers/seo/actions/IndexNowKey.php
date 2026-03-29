<?php
namespace seo\actions;

/**
* Действие отдачи файла с ключом IndexNow
*/
class IndexNowKey extends SeoAction {
    
    public function execute() {
        $key = $this->params['key'] ?? '';
        
        if (empty($key)) {
            $this->redirect(BASE_URL . '/404');
            return;
        }
        
        try {
            $settings = $this->seoModel->getIndexNowSettings();
            $engines = $this->seoModel->getIndexNowEngines();
            $isValid = false;
            foreach (array_keys($engines) as $key_name) {
                if (!empty($settings[$key_name]) && $settings[$key_name] === $key) {
                    $isValid = true;
                    break;
                }
            }
            
            if (!$isValid) {
                $this->redirect(BASE_URL . '/404');
                return;
            }
            
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: inline; filename="' . $key . '.txt"');
            echo $key;
            exit;
            
        } catch (\Exception $e) {
            $this->redirect(BASE_URL . '/404');
            return;
        }
    }
}