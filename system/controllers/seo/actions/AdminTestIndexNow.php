<?php
namespace seo\actions;

/**
* Действие тестовой отправки IndexNow
*/
class AdminTestIndexNow extends SeoAction {
    
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа к этому разделу');
            $this->redirect(ADMIN_URL . '/login');
            return;
        }
        
        try {
            $settings = $this->seoModel->getIndexNowSettings();
            $host = $this->seoModel->getHost();
            
            if ($this->seoModel->isLocalhost($host)) {
                \Notification::warning(
                    'IndexNow не работает с локальными доменами (' . $host . '). ' .
                    'Для полноценной работы требуется публичный доступ к сайту.'
                );
                $this->redirect(ADMIN_URL . '/seo?tab=indexnow');
                return;
            }
            
            $testUrl = rtrim(BASE_URL, '/') . '/';
            
            if (!preg_match('#^https?://#', $testUrl)) {
                \Notification::error(
                    'Неверный формат BASE_URL в конфиге. Должен начинаться с http:// или https://. ' .
                    'Текущее значение: ' . BASE_URL
                );
                $this->redirect(ADMIN_URL . '/seo?tab=indexnow');
                return;
            }
            
            $results = [];
            
            if (!empty($settings['ya_key'])) {
                $yaResult = $this->seoModel->sendIndexNowPing('ya_key', $testUrl);
                $results['yandex'] = $yaResult;
            }
            
            if (!empty($settings['bing_key'])) {
                $bingResult = $this->seoModel->sendIndexNowPing('bing_key', $testUrl);
                $results['bing'] = $bingResult;
            }
            
            if (empty($results)) {
                \Notification::error('Ключи IndexNow не настроены. Включите и сохраните настройки.');
            } else {
                $successCount = 0;
                $messages = [];
                
                foreach ($results as $engine => $result) {
                    if ($result['success']) {
                        $successCount++;
                        $messages[] = ucfirst($engine) . ": HTTP " . $result['code'] . " ✓";
                    } else {
                        $errorMsg = $result['error'] ?? 'Неизвестная ошибка';
                        if (strlen($errorMsg) > 150) {
                            $errorMsg = substr($errorMsg, 0, 150) . '...';
                        }
                        $messages[] = ucfirst($engine) . ": HTTP " . $result['code'] . " ✗ " . $errorMsg;
                    }
                }
                
                if ($successCount > 0) {
                    \Notification::success("IndexNow тест: " . implode(", ", $messages));
                } else {
                    \Notification::error("IndexNow тест: " . implode(", ", $messages));
                }
            }
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка тестирования IndexNow: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/seo?tab=indexnow');
    }
}