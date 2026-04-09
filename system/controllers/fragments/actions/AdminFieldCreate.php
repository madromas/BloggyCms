<?php

namespace fragments\actions;

/**
* Действие создания поля фрагмента
*/
class AdminFieldCreate extends FragmentAction {
    
    public function execute() {
        $fragmentId = $this->params['fragment_id'] ?? null;
        
        if (!$fragmentId) {
            \Notification::error('ID фрагмента не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $fragment = $this->fragmentModel->getById($fragmentId);
        
        if (!$fragment) {
            \Notification::error('Фрагмент не найден');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Фрагменты', ADMIN_URL . '/fragments');
        $this->addBreadcrumb($fragment['name'], ADMIN_URL . '/fragments/edit/' . $fragmentId);
        $this->addBreadcrumb('Поля', ADMIN_URL . '/fragments/fields/' . $fragmentId);
        $this->addBreadcrumb('Создание поля');
        $this->setPageTitle('Создание поля для фрагмента: ' . $fragment['name']);
        
        $fieldTypes = $this->fieldManager->getAvailableFieldTypes();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty($_POST['name'])) {
                    throw new \Exception('Название поля обязательно');
                }
                
                if (empty($_POST['system_name'])) {
                    throw new \Exception('Системное имя обязательно');
                }
                
                if ($this->fragmentModel->isFieldSystemNameExists($fragmentId, $_POST['system_name'])) {
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
                    'is_active' => isset($_POST['is_active']) ? 1 : 1,
                    'show_in_list' => isset($_POST['show_in_list']) ? 1 : 0,
                    'sort_order' => (int)($_POST['sort_order'] ?? 0),
                    'config' => $config
                ];
                
                $this->fragmentModel->createField($fragmentId, $data);
                
                \Notification::success('Поле успешно создано');
                $this->redirect(ADMIN_URL . '/fragments/fields/' . $fragmentId);
                
            } catch (\Exception $e) {
                \Notification::error($e->getMessage());
            }
        }
        
        $this->render('admin/fragments/field_form', [
            'fragment' => $fragment,
            'fieldTypes' => $fieldTypes,
            'field' => null,
            'isEdit' => false
        ]);
    }
}