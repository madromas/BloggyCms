<?php

namespace fragments\actions;

/**
 * Действие редактирования записи фрагмента
 */
class AdminEntryEdit extends FragmentAction {
    
    public function execute() {
        $entryId = $this->params['id'] ?? null;
        
        if (!$entryId) {
            \Notification::error('ID записи не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $entry = $this->entryModel->getById($entryId);
        
        if (!$entry) {
            \Notification::error('Запись не найдена');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $fragment = $this->fragmentModel->getById($entry['fragment_id']);
        
        if (!$fragment) {
            \Notification::error('Фрагмент не найден');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $this->addBreadcrumb('Главная', ADMIN_URL);
        $this->addBreadcrumb('Фрагменты', ADMIN_URL . '/fragments');
        $this->addBreadcrumb($fragment['name'], ADMIN_URL . '/fragments/edit/' . $fragment['id']);
        $this->addBreadcrumb('Записи', ADMIN_URL . '/fragments/entries/' . $fragment['id']);
        $this->addBreadcrumb('Редактирование записи #' . $entryId);
        $this->setPageTitle('Редактирование записи: ' . $fragment['name']);
        
        $fields = $this->fragmentModel->getFields($fragment['id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = $this->processEntryData($fields, $_POST, $_FILES, $entry['data']);
                
                $this->entryModel->update($entryId, $data);
                
                \Notification::success('Запись успешно обновлена');
                $this->redirect(ADMIN_URL . '/fragments/entries/' . $fragment['id']);
                
            } catch (\Exception $e) {
                \Notification::error($e->getMessage());
            }
        }
        
        $this->render('admin/fragments/entry_form', [
            'fragment' => $fragment,
            'fields' => $fields,
            'entry' => $entry,
            'isEdit' => true
        ]);
    }
    
    /**
     * Обработка данных записи
     * 
     * @param array $fields
     * @param array $postData
     * @param array $filesData
     * @param array $currentData
     * @return array
     */
    private function processEntryData($fields, $postData, $filesData, $currentData = []) {
        $data = [];
        $errors = [];
        
        foreach ($fields as $field) {
            $systemName = $field['system_name'];
            $fieldType = $field['type'];
            $config = $field['config'];
            
            $fieldData = [
                'type' => $fieldType,
                'system_name' => $systemName,
                'config' => $config,
                'is_required' => $field['is_required']
            ];
            
            $value = $this->fieldManager->processFieldValue($fieldData, $postData, $filesData, $currentData);
            
            if ($field['is_required'] && (empty($value) && $value !== '0')) {
                $errors[] = "Поле '{$field['name']}' обязательно для заполнения";
                continue;
            }
            
            $validationResult = $this->fieldManager->validateFieldValue($fieldData, $value, $postData, $filesData);
            if (!$validationResult['is_valid']) {
                $errors[] = $validationResult['message'];
                continue;
            }
            
            if ($value !== null) {
                $data[$systemName] = $value;
            }
        }
        
        if (!empty($errors)) {
            throw new \Exception(implode('<br>', $errors));
        }
        
        return $data;
    }
}