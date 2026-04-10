<?php

namespace debug\actions;

/**
* Действие отметки лога как исправленного (AJAX)
* @package debug\actions
*/
class AdminMarkFixed extends DebugAction {
    
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
        
        $result = $this->debugModel->markAsFixed($id);
        
        $this->jsonResponse([
            'success' => $result,
            'message' => $result ? 'Ошибка отмечена как исправленная' : 'Ошибка при обновлении'
        ]);
    }
}