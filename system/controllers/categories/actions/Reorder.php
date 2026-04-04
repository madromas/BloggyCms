<?php

namespace categories\actions;

/**
* Действие изменения порядка сортировки категорий
* @package categories\actions
*/
class Reorder extends CategoryAction {
    
    /**
    * Метод выполнения изменения порядка категорий
    * @return void
    */
    public function execute() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['order']) || !is_array($input['order'])) {
                throw new \Exception('Invalid order data');
            }
            
            foreach ($input['order'] as $item) {
                if (!isset($item['id']) || !isset($item['order'])) {
                    continue;
                }
                
                $this->categoryModel->updateOrder($item['id'], $item['order']);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Порядок категорий обновлен'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
        
        exit;
    }
}