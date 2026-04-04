<?php

namespace postblocks\actions;

/**
* Действие получения HTML-шаблона постблока с шорткодами
* @package postblocks\actions
*/
class AdminGetTemplate extends PostBlockAction {
    
    /**
    * Метод выполнения получения шаблона блока 
    * @return void
    */
    public function execute() {

        header('Content-Type: application/json');
        
        try {
            $systemName = $_GET['system_name'] ?? '';
            
            if (empty($systemName)) {
                throw new \Exception('Системное имя блока не указано');
            }

            $postBlock = $this->postBlockManager->getPostBlock($systemName);
            if (!$postBlock || !$postBlock['class']) {
                throw new \Exception('Блок не найден');
            }

            $template = $postBlock['class']->getTemplateWithShortcodes();

            echo json_encode([
                'success' => true,
                'template' => $template
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}