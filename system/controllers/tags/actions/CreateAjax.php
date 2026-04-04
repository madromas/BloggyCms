<?php

namespace tags\actions;

/**
* Действие создания тега через AJAX (для автодополнения)
* @package tags\actions
* @extends TagAction
*/
class CreateAjax extends TagAction {
    
    /**
    * Метод выполнения создания тега через AJAX
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            $name = trim($_POST['name'] ?? '');
            
            if (empty($name)) {
                throw new \Exception('Название тега не может быть пустым');
            }
            
            $allTags = $this->tagModel->getAll();
            $existingTag = null;
            
            foreach ($allTags as $tag) {
                if (mb_strtolower($tag['name']) === mb_strtolower($name)) {
                    $existingTag = $tag;
                    break;
                }
            }
            
            if ($existingTag) {
                echo json_encode([
                    'success' => true,
                    'tag' => $existingTag,
                    'message' => 'Тег уже существует'
                ]);
                exit;
            }
            
            $tagId = $this->tagModel->createWithSlug($name);
            $tag = $this->tagModel->getById($tagId);
            
            echo json_encode([
                'success' => true,
                'tag' => $tag,
                'message' => 'Тег успешно создан'
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}