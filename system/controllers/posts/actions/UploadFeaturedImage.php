<?php

namespace posts\actions;

/**
* Действие загрузки главного изображения (обложки) для поста
* @package posts\actions
*/
class UploadFeaturedImage extends PostAction {
    
    /**
    * Метод выполнения загрузки главного изображения
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {

            if (!isset($_FILES['featured_image']) || $_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Ошибка при загрузке файла');
            }

            $file = $_FILES['featured_image'];
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                throw new \Exception('Недопустимый тип файла. Разрешены: JPEG, PNG, GIF, WebP');
            }
            
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new \Exception('Файл слишком большой. Максимальный размер: 5MB');
            }

            $uploadDir = UPLOADS_PATH . '/images/';
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
                'url' => BASE_URL . '/uploads/images/' . $fileName,
                'path' => $fileName,
                'message' => 'Изображение успешно загружено'
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