<?php

namespace posts\actions;

/**
* Действие загрузки изображений для блоков контента 
* @package posts\actions
*/
class UploadBlockImage extends PostAction {
    
    /**
    * Метод выполнения загрузки изображения для блока
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {

            if (!isset($_FILES['block_image']) || $_FILES['block_image']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Ошибка при загрузке файла');
            }

            $file = $_FILES['block_image'];
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                throw new \Exception('Недопустимый тип файла');
            }
            
            if ($file['size'] > 10 * 1024 * 1024) {
                throw new \Exception('Файл слишком большой. Максимальный размер: 10MB');
            }

            $uploadDir = UPLOADS_PATH . '/images/blocks/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = uniqid() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new \Exception('Не удалось сохранить файл');
            }

            echo json_encode([
                'success' => true,
                'url' => BASE_URL . '/uploads/images/blocks/' . $fileName,
                'path' => $fileName,
                'message' => 'Изображение блока успешно загружено'
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