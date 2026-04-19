<?php

namespace html_blocks\actions;

/**
* Действие отображения списка HTML-блоков в админ-панели 
* @package html_blocks\actions
*/
class AdminIndex extends HtmlBlockAction {
    
    /**
    * Метод выполнения отображения списка HTML-блоков 
    * @return void
    */
    public function execute() {
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Контент-блоки');
        
        try {
            $blocks = $this->htmlBlockModel->getAll();
            
            $allBlockTypes = $this->blockTypeManager->getAllBlockTypes();
            
            foreach ($blocks as &$block) {
                $blockTypeName = $block['block_type'] ?? 'DefaultBlock';
                $block['type_is_active'] = $this->blockTypeManager->isBlockTypeActive($blockTypeName);
            }
            
            $this->render('admin/html_blocks/index', [
                'blocks' => $blocks,
                'blockTypes' => $this->blockTypeManager->getBlockTypes(),
                'pageTitle' => 'Управление HTML-блоками'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке списка HTML-блоков: ' . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }

}