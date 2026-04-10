<?php

namespace debug\actions;

/**
* Действие удаления лога (AJAX)
* @package debug\actions
*/
class AdminDelete extends DebugAction {
    
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
        
        $result = $this->debugModel->delete($id);
        
        $this->jsonResponse([
            'success' => $result,
            'message' => $result ? 'Лог удален' : 'Ошибка при удалении'
        ]);
    }
}