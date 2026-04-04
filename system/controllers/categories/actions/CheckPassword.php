<?php

namespace categories\actions;

/**
* Действие проверки пароля для защищенных категорий
* @package categories\actions
*/
class CheckPassword extends CategoryAction {
    
    /**
    * Метод выполнения проверки пароля
    * @return void
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'ID категории не указан'
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Метод не поддерживается'
            ]);
            return;
        }
        
        try {
            $password = $_POST['password'] ?? '';
            
            $category = $this->categoryModel->getById($id);
            
            if (!$category) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'message' => 'Категория не найдена'
                ]);
                return;
            }
            
            if (!$category['password_protected'] || $category['password'] === $password) {
                if (!isset($_SESSION['category_access'])) {
                    $_SESSION['category_access'] = [];
                }
                
                $_SESSION['category_access'][$category['id']] = true;
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'message' => 'Неверный пароль'
                ]);
            }
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Ошибка при проверке пароля'
            ]);
        }
    }
}