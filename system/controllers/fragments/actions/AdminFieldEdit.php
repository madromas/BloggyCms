<?php

namespace fragments\actions;

/**
* Действие редактирования поля фрагмента
*/
class AdminFieldEdit extends FragmentAction {
    
    public function execute() {
        $fieldId = $this->params['id'] ?? null;
        
        if (!$fieldId) {
            \Notification::error('ID поля не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $field = $this->fragmentModel->getFieldById($fieldId);
        
        if (!$field) {
            \Notification::error('Поле не найдено');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $fragment = $this->fragmentModel->getById($field['fragment_id']);
        
        if (!$fragment) {
            \Notification::error('Фрагмент не найден');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Фрагменты', ADMIN_URL . '/fragments');
        $this->addBreadcrumb($fragment['name'], ADMIN_URL . '/fragments/edit/' . $fragment['id']);
        $this->addBreadcrumb('Поля', ADMIN_URL . '/fragments/fields/' . $fragment['id']);
        $this->addBreadcrumb('Редактирование поля: ' . $field['name']);
        $this->setPageTitle('Редактирование поля: ' . $field['name']);
        
        $fieldTypes = $this->fieldManager->getAvailableFieldTypes();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty($_POST['name'])) {
                    throw new \Exception('Название поля обязательно');
                }
                
                if (empty($_POST['system_name'])) {
                    throw new \Exception('Системное имя обязательно');
                }
                
                if ($this->fragmentModel->isFieldSystemNameExists($fragment['id'], $_POST['system_name'], $fieldId)) {
                    throw new \Exception('Поле с таким системным именем уже существует');
                }
                
                $config = $_POST['config'] ?? [];
                $fieldType = $_POST['type'] ?? 'string';
                
                $config = $this->fieldManager->processFieldConfig($fieldType, $config);
                
                $data = [
                    'system_name' => $this->sanitizeSystemName($_POST['system_name']),
                    'name' => trim($_POST['name']),
                    'type' => $fieldType,
                    'description' => trim($_POST['description'] ?? ''),
                    'is_required' => isset($_POST['is_required']) ? 1 : 0,
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'show_in_list' => isset($_POST['show_in_list']) ? 1 : 0,
                    'config' => $config
                ];
                
                $this->fragmentModel->updateField($fieldId, $data);
                
                \Notification::success('Поле успешно обновлено');
                $this->redirect(ADMIN_URL . '/fragments/fields/' . $fragment['id']);
                
            } catch (\Exception $e) {
                \Notification::error($e->getMessage());
            }
        }
        
        $this->render('admin/fragments/field_form', [
            'fragment' => $fragment,
            'fieldTypes' => $fieldTypes,
            'field' => $field,
            'isEdit' => true
        ]);
    }
}