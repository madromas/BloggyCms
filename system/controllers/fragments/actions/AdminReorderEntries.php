<?php

namespace fragments\actions;

/**
* Действие сортировки записей (AJAX)
*/
class AdminReorderEntries extends FragmentAction {
    
    public function execute() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['order']) || !is_array($input['order'])) {
            echo json_encode(['success' => false, 'message' => 'Неверные данные']);
            return;
        }
        
        try {
            $success = $this->entryModel->updateOrder($input['order']);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Порядок записей обновлен']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении порядка']);
            }
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}