<?php

namespace debug\actions;

/**
* Действие удаления всех логов (AJAX)
* @package debug\actions
*/
class AdminDeleteAll extends DebugAction {
    
    public function execute() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Только AJAX запросы']);
            return;
        }
        
        $result = $this->debugModel->deleteAll();
        
        if ($result) {
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Все логи успешно удалены'
            ];
        }
        
        $this->jsonResponse([
            'success' => $result,
            'message' => $result ? 'Все логи удалены' : 'Ошибка при удалении'
        ]);
    }
}