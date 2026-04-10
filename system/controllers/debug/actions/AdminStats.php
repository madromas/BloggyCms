<?php

namespace debug\actions;

/**
* Действие получения статистики (AJAX)
* @package debug\actions
*/
class AdminStats extends DebugAction {
    
    public function execute() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Только AJAX запросы']);
            return;
        }
        
        $stats = $this->debugModel->getStats();
        
        $this->jsonResponse([
            'success' => true,
            'stats' => $stats
        ]);
    }
}