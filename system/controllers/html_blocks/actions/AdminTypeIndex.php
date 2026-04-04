<?php

namespace html_blocks\actions;

/**
* Действие отображения списка типов HTML-блоков в админ-панели
* @package html_blocks\actions
*/
class AdminTypeIndex extends HtmlBlockAction {
    
    /**
    * Метод выполнения отображения списка типов блоков
    * @return void
    */
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа к этом разделу');
            $this->redirect(ADMIN_URL . '/login');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Контент-блоки', ADMIN_URL . '/html-blocks');
        $this->addBreadcrumb('Типы блоков');
        
        try {
            $allBlockTypes = $this->blockTypeManager->getAllBlockTypes();
            
            $activeBlockTypes = $this->blockTypeManager->getBlockTypes();
            
            foreach ($allBlockTypes as $systemName => &$type) {
                if ($systemName !== 'DefaultBlock') {
                    $type['is_active'] = $this->blockTypeManager->isBlockTypeActive($systemName);
                } else {
                    $type['is_active'] = true;
                }
                
                $type['is_visible_in_creation'] = isset($activeBlockTypes[$systemName]);
            }
            
            $this->render('admin/html_blocks/types_index', [
                'blockTypes' => $allBlockTypes,
                'pageTitle' => 'Управление типами HTML-блоков'
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке типов блоков: ' . $e->getMessage());
            $this->redirect(ADMIN_URL . '/html-blocks');
        }
    }

}