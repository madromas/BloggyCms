<?php

namespace fields\actions;

/**
* Действие редактирования дополнительного поля в админ-панели
* @package fields\actions
*/
class AdminEdit extends FieldAction {
    
    /**
    * Метод выполнения редактирования поля
    * @return void
    */
    public function execute() {

        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID поля не указан');
            $this->redirect(ADMIN_URL . '/fields');
            return;
        }
        
        $field = $this->fieldModel->getById($id);
        if (!$field) {
            \Notification::error('Поле не найдено');
            $this->redirect(ADMIN_URL . '/fields');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Поля', ADMIN_URL . '/fields');
        $this->addBreadcrumb($this->getEntityName($field['entity_type'], true), ADMIN_URL . '/fields/entity/' . $field['entity_type']);
        $this->addBreadcrumb('Редактирование поля: ' . $field['name']);
        
        $config = json_decode($field['config'] ?? '{}', true);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $config = $_POST['config'] ?? [];
                
                $config = $this->fieldModel->processFieldConfig($_POST['type'], $config);
                
                $data = [
                    'name' => $_POST['name'],
                    'system_name' => $_POST['system_name'],
                    'type' => $_POST['type'],
                    'description' => $_POST['description'] ?? '',
                    'is_required' => isset($_POST['is_required']) ? 1 : 0,
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'sort_order' => $_POST['sort_order'] ?? 0,
                    'show_in_post' => isset($_POST['show_in_post']) ? 1 : 0,
                    'show_in_list' => isset($_POST['show_in_list']) ? 1 : 0,
                    'config' => json_encode($config)
                ];
                
                $this->fieldModel->update($id, $data);
                
                \Notification::success('Поле успешно обновлено');
                
                $this->redirect(ADMIN_URL . "/fields/entity/{$field['entity_type']}");
                
            } catch (\Exception $e) {
                \Notification::error('Ошибка при обновлении поля: ' . $e->getMessage());
            }
        }
        
        $fieldTypes = $this->fieldModel->getFieldTypes();
        
        if (!empty($field['config'])) {
            $decodedConfig = json_decode($field['config'], true);
            
            $fieldManager = new \FieldManager($this->db);
            $fieldInstance = $fieldManager->getFieldInstance($field['type'], $decodedConfig);
            
            if ($fieldInstance && method_exists($fieldInstance, 'prepareConfigForForm')) {
                $decodedConfig = $fieldInstance->prepareConfigForForm($decodedConfig);
                $field['config'] = json_encode($decodedConfig);
            }
        }
        
        $this->render('admin/fields/form', [
            'field' => $field,
            'fieldTypes' => $fieldTypes,
            'entityType' => $field['entity_type'],
            'entityName' => $this->getEntityName($field['entity_type']),
            'pageTitle' => 'Редактирование поля'
        ]);
    }

}