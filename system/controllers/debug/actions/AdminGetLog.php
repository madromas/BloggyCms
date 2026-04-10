<?php

namespace debug\actions;

/**
* Действие получения деталей лога (AJAX)
* @package debug\actions
*/
class AdminGetLog extends DebugAction {
    
    public function execute() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Только AJAX запросы']);
            return;
        }
        
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'ID не указан']);
            return;
        }
        
        $log = $this->debugModel->getById($id);
        
        if (!$log) {
            $this->jsonResponse(['success' => false, 'message' => 'Лог не найден']);
            return;
        }
        
        $this->jsonResponse([
            'success' => true,
            'log' => $log
        ]);
    }
}