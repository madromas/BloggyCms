<?php

namespace categories\actions;

/**
* Действие создания новой категории
* @package categories\actions
*/
class Create extends CategoryAction {
    
    protected $pageTitle = 'Создание категории';
    
    /**
    * Метод выполнения создания категории
    * @return void
    */
    public function execute() {

        $this->pageTitle = 'Создание категории';
        $this->addBreadcrumb('Категории', ADMIN_URL . '/categories');
        $this->addBreadcrumb('Создание категории');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'meta_title' => trim($_POST['meta_title'] ?? ''),
                    'meta_description' => trim($_POST['meta_description'] ?? ''),
                    'canonical_url' => trim($_POST['canonical_url'] ?? ''),
                    'noindex' => isset($_POST['noindex']) ? 1 : 0,
                    'sort_order' => (int)($_POST['sort_order'] ?? 0),
                    'password_protected' => isset($_POST['password_protected']) ? 1 : 0,
                    'password' => isset($_POST['password_protected']) && !empty($_POST['password']) 
                        ? trim($_POST['password']) 
                        : null
                ];
                
                if (!empty($_FILES['image']['name'])) {
                    $uploadDir = UPLOADS_PATH . '/images/categories';
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $maxSize = 5120;
                    $fileName = \FileUpload::upload($_FILES['image'], $uploadDir, $allowedTypes, $maxSize);
                    $data['image'] = 'categories/' . $fileName;
                } else {
                    $data['image'] = '';
                }
                
                $categoryId = $this->categoryModel->create($data);
                
                $fieldModel = new \FieldModel($this->db);
                $fieldManager = new \FieldManager($this->db);
                
                $customFields = $fieldModel->getActiveByEntityType('category');
                
                foreach ($customFields as $field) {
                    try {
                        $value = $fieldManager->processFieldValue($field, $_POST, $_FILES);
                        
                        if ($value !== null) {
                            $config = is_array($field['config']) 
                                ? $field['config'] 
                                : json_decode($field['config'] ?? '{}', true);
                            $fieldModel->saveFieldValue(
                                'category',
                                $categoryId,
                                $field['system_name'],
                                $value
                            );
                        }
                    } catch (\Exception $e) {
                        \Notification::error("Ошибка обработки поля {$field['name']}: " . $e->getMessage());
                    }
                }
                
                \Notification::success('Категория успешно создана');
                $this->redirect(ADMIN_URL . '/categories');
                return;
                
            } catch (\Exception $e) {
                \Notification::error('Ошибка при создании категории: ' . $e->getMessage());

                $this->render('admin/categories/form', [
                    'data' => $_POST,
                    'pageTitle' => $this->pageTitle
                ]);
                return;
            }
        }
        
        $this->render('admin/categories/form', [
            'pageTitle' => $this->pageTitle
        ]);
    }
}