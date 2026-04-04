<?php

namespace categories\actions;

/**
* Действие редактирования существующей категории
* @package categories\actions
*/
class Edit extends CategoryAction {
    
    protected $pageTitle = 'Редактирование категории';
    
    /**
    * Метод выполнения редактирования категории
    * @return void
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID категории не указан');
            $this->redirect(ADMIN_URL . '/categories');
            return;
        }

        $this->pageTitle = 'Редактирование категории';
        $this->addBreadcrumb('Категории', ADMIN_URL . '/categories');
        
        try {
            $category = $this->categoryModel->getById($id);
            
            if (!$category) {
                \Notification::error('Категория не найдена');
                $this->redirect(ADMIN_URL . '/categories');
                return;
            }
            
            $this->addBreadcrumb('Редактирование: ' . $category['name']);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $data = [
                        'name' => trim($_POST['name']),
                        'slug' => trim($_POST['slug'] ?? ''),
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
                        
                        if (!empty($category['image'])) {
                            $oldImagePath = UPLOADS_PATH . '/images/' . $category['image'];
                            \FileUpload::delete($oldImagePath);
                        }
                    } 
                    elseif (isset($_POST['delete_image']) && $_POST['delete_image']) {
                        if (!empty($category['image'])) {
                            $oldImagePath = UPLOADS_PATH . '/images/' . $category['image'];
                            \FileUpload::delete($oldImagePath);
                        }
                        $data['image'] = '';
                    } 
                    else {
                        $data['image'] = $category['image'] ?? '';
                    }
                    
                    if (!isset($_POST['password_protected']) || !$_POST['password_protected']) {
                        $data['password'] = null;
                    }
                    
                    $result = $this->categoryModel->update($id, $data);
                    
                    if (!$result) {
                        throw new \Exception('Не удалось обновить категорию');
                    }
                    
                    $fieldModel = new \FieldModel($this->db);
                    $fieldManager = new \FieldManager($this->db);
                    $customFields = $fieldModel->getActiveByEntityType('category');
                    
                    $currentValues = [];
                    foreach ($customFields as $field) {
                        $currentValues[$field['system_name']] = $fieldModel->getFieldValue(
                            'category', 
                            $id, 
                            $field['system_name']
                        );
                    }

                    foreach ($customFields as $field) {
                        try {
                            $value = $fieldManager->processFieldValue(
                                $field, 
                                $_POST, 
                                $_FILES, 
                                $currentValues
                            );
                            
                            if ($value !== null) {
                                $config = is_array($field['config']) 
                                    ? $field['config'] 
                                    : json_decode($field['config'] ?? '{}', true);
                                
                                $fieldModel->saveFieldValue(
                                    $field['id'], 
                                    'category', 
                                    $id, 
                                    $value,
                                    $field['type'],
                                    $config
                                );
                            }
                        } catch (\Exception $e) {
                            \Notification::error("Ошибка обработки поля {$field['name']}: " . $e->getMessage());
                        }
                    }
                    
                    \Notification::success('Категория успешно обновлена');
                    $this->redirect(ADMIN_URL . '/categories');
                    return;
                    
                } catch (\Exception $e) {
                    \Notification::error('Ошибка при обновлении категории: ' . $e->getMessage());
                    $category = $this->categoryModel->getById($id);
                    $this->render('admin/categories/form', [
                        'category' => $category, 
                        'data' => array_merge($category, $_POST),
                        'pageTitle' => $this->pageTitle
                    ]);
                    return;
                }
            }
            
            $this->render('admin/categories/form', [
                'category' => $category,
                'pageTitle' => $this->pageTitle
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке категории: ' . $e->getMessage());
            $this->redirect(ADMIN_URL . '/categories');
        }
    }
}