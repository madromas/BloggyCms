<?php

namespace html_blocks\actions;

/**
* Действие получения списка фрагментов и их полей
*/
class AdminGetFragments extends HtmlBlockAction {
    
    public function execute() {
        header('Content-Type: application/json');
        
        try {
            $fragmentModel = new \FragmentModel($this->db);
            $fragments = $fragmentModel->getActive();
            
            $result = [];
            foreach ($fragments as $fragment) {
                $fields = $fragmentModel->getFields($fragment['id']);
                $fieldList = [];
                
                foreach ($fields as $field) {
                    $fieldList[] = [
                        'system_name' => $field['system_name'],
                        'name' => $field['name'],
                        'type' => $field['type'],
                        'shortcode' => '{field:' . $field['system_name'] . '}',
                        'display_shortcode' => '{field_display:' . $field['system_name'] . '}'
                    ];
                }
                
                $result[] = [
                    'id' => $fragment['id'],
                    'name' => $fragment['name'],
                    'system_name' => $fragment['system_name'],
                    'fields' => $fieldList,
                    'shortcode_simple' => '{' . $fragment['system_name'] . '}',
                    'shortcode_loop' => '{ctype:' . $fragment['system_name'] . '}...{/ctype:' . $fragment['system_name'] . '}'
                ];
            }
            
            echo json_encode([
                'success' => true,
                'fragments' => $result
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