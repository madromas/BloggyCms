<?php

namespace posts\actions;

/**
* Действие множественной загрузки изображений для галереи поста
* @package posts\actions
*/
class UploadGalleryImages extends PostAction {
    
    /**
    * Метод выполнения множественной загрузки изображений
    * @return void
    */
    public function execute() {
        
        header('Content-Type: application/json');
        
        try {
            if (!isset($_FILES['gallery_images']) || empty($_FILES['gallery_images']['name'][0])) {
                throw new \Exception('Файлы не загружены');
            }

            $uploadedFiles = [];
            
            $uploadDir = UPLOADS_PATH . '/images/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            foreach ($_FILES['gallery_images']['name'] as $index => $name) {
                if ($_FILES['gallery_images']['error'][$index] === UPLOAD_ERR_OK) {
                    $fileType = mime_content_type($_FILES['gallery_images']['tmp_name'][$index]);
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        continue;
                    }

                    $fileName = uniqid() . '_' . basename($name);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$index], $targetPath)) {
                        $uploadedFiles[] = [
                            'url' => BASE_URL . '/uploads/images/gallery/' . $fileName,
                            'path' => $fileName,
                            'name' => $name
                        ];
                    }
                }
            }

            if (empty($uploadedFiles)) {
                throw new \Exception('Не удалось загрузить файлы');
            }

            echo json_encode([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => 'Изображения успешно загружены'
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