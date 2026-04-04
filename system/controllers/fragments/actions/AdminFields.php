<?php

namespace fragments\actions;

/**
* Действие управления полями фрагмента
*/
class AdminFields extends FragmentAction {
    
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID фрагмента не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $fragment = $this->fragmentModel->getById($id);
        
        if (!$fragment) {
            \Notification::error('Фрагмент не найден');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Фрагменты', ADMIN_URL . '/fragments');
        $this->addBreadcrumb($fragment['name'], ADMIN_URL . '/fragments/edit/' . $id);
        $this->addBreadcrumb('Поля');
        $this->setPageTitle('Поля фрагмента: ' . $fragment['name']);
        
        $fields = $this->fragmentModel->getFields($id);
        $fieldTypes = $this->fieldManager->getAvailableFieldTypes();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fields'])) {
            try {
                $fieldsConfig = $this->processFields($_POST['fields']);
                
                $fieldsJson = json_encode($fieldsConfig, JSON_UNESCAPED_UNICODE);
                
                $sql = "UPDATE `{$this->fragmentModel->getTableName()}` SET fields_config = ? WHERE id = ?";
                $this->db->query($sql, [$fieldsJson, $id]);
                
                \Notification::success('Поля успешно сохранены');
                $this->redirect(ADMIN_URL . '/fragments/fields/' . $id);
                
            } catch (\Exception $e) {
                \Notification::error($e->getMessage());
            }
        }
        
        $this->render('admin/fragments/fields', [
            'fragment' => $fragment,
            'fields' => $fields,
            'fieldTypes' => $fieldTypes
        ]);
    }
    
    /**
    * Обработка полей 
    * @param array $fields
    * @return array
    */
    private function processFields($fields) {
        $processed = [];
        $usedSystemNames = [];
        
        if (!is_array($fields)) {
            throw new \Exception('Неверный формат данных полей');
        }
        
        foreach ($fields as $index => $field) {
            if (empty($field['name'])) {
                continue;
            }
            
            $systemName = '';
            if (!empty($field['system_name'])) {
                $systemName = $this->sanitizeSystemName($field['system_name']);
            }
            
            if (empty($systemName)) {
                $systemName = $this->generateSystemNameFromTitle($field['name']);
            }
            
            if (in_array($systemName, $usedSystemNames)) {
                $counter = 2;
                $originalName = $systemName;
                while (in_array($systemName, $usedSystemNames)) {
                    $systemName = $originalName . '_' . $counter;
                    $counter++;
                }
            }
            $usedSystemNames[] = $systemName;
            
            $config = [];
            if (isset($field['config'])) {
                if (is_string($field['config'])) {
                    $decoded = json_decode($field['config'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $config = $decoded;
                    }
                } elseif (is_array($field['config'])) {
                    $config = $field['config'];
                }
            }
            
            $fieldType = $field['type'] ?? 'string';
            
            $processedConfig = $this->fieldManager->processFieldConfig($fieldType, $config);
            
            $processed[] = [
                'system_name' => $systemName,
                'name' => trim($field['name']),
                'type' => $fieldType,
                'description' => trim($field['description'] ?? ''),
                'is_required' => isset($field['is_required']) ? 1 : 0,
                'show_in_list' => isset($field['show_in_list']) ? 1 : 0,
                'config' => $processedConfig,
                'sort_order' => (int)($field['sort_order'] ?? $index)
            ];
        }
        
        usort($processed, function($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });
        
        return $processed;
    }
    
    /**
    * Генерация системного имени из названия
    * @param string $title
    * @return string
    */
    private function generateSystemNameFromTitle($title) {
        $name = mb_strtolower($title, 'UTF-8');
        
        $cyr = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            ' ', '_', '-'
        ];
        $lat = [
            'a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p',
            'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
            '_', '_', '_'
        ];
        
        $name = str_replace($cyr, $lat, $name);
        $name = preg_replace('/[^a-z0-9_]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $name = trim($name, '_');
        
        return $name;
    }
}