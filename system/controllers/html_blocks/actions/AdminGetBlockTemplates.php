<?php

namespace html_blocks\actions;

/**
* Действие получения доступных шаблонов для типа блока через AJAX
* @package html_blocks\actions
*/
class AdminGetBlockTemplates extends HtmlBlockAction {
    
    /**
    * Метод выполнения получения шаблонов блока
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {
            $blockTypeName = $_GET['block_type'] ?? '';
            
            if (empty($blockTypeName) || $blockTypeName === 'DefaultBlock') {
                echo json_encode([
                    'success' => true,
                    'templates' => ['default' => 'Стандартный шаблон']
                ]);
                return;
            }
            
            $blockType = $this->blockTypeManager->getBlockType($blockTypeName);
            
            if ($blockType && $blockType['class']) {
                $templates = $blockType['class']->getAvailableTemplates();
                
                echo json_encode([
                    'success' => true,
                    'templates' => $templates
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Тип блока не найден'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при получении шаблонов: ' . $e->getMessage()
            ]);
        }
    }
}