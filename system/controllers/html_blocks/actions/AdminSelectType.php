<?php

namespace html_blocks\actions;

/**
* Действие выбора типа HTML-блока при создании
* @package html_blocks\actions
*/
class AdminSelectType extends HtmlBlockAction {
    
    /**
    * Метод выполнения выбора типа блока
    * @return void
    */
    public function execute() {
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Контент-блоки', ADMIN_URL . '/html-blocks');
        $this->addBreadcrumb('Выбор типа блока');
        
        $blockTypes = $this->blockTypeManager->getBlockTypes();
        
        $currentTemplate = get_current_template();
        
        $defaultBlock = [
            'DefaultBlock' => [
                'name' => 'Дефолтный блок',
                'system_name' => 'DefaultBlock',
                'description' => 'Произвольный HTML-код с поддержкой шорткодов',
                'icon' => 'bi bi-code-slash',
                'author' => 'BloggyCMS',
                'version' => '1.0.0',
                'author_website' => '',
                'short_description' => 'Создавайте произвольные HTML-блоки с поддержкой всех системных шорткодов',
                'template' => 'all'
            ]
        ];
        
        $allBlocks = $defaultBlock + $blockTypes;
        
        $availableTemplates = $this->getAvailableTemplates($blockTypes);
        
        $this->render('admin/html_blocks/select_type', [
            'blockTypes' => $allBlocks,
            'availableTemplates' => $availableTemplates,
            'currentTemplate' => $currentTemplate,
            'pageTitle' => 'Выбор типа HTML-блока'
        ]);
    }

}