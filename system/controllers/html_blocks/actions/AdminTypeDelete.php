<?php

namespace html_blocks\actions;

/**
* Действие удаления типа HTML-блока в админ-панели
* @package html_blocks\actions
*/
class AdminTypeDelete extends HtmlBlockAction {
    
    private $systemName;
    
    /**
    * Установка системного имени типа блока
    */
    public function setSystemName($systemName) {
        $this->systemName = $systemName;
    }
    
    /**
    * Метод выполнения удаления типа блока
    * @return void
    */
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа к этому разделу');
            $this->redirect(ADMIN_URL . '/login');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Контент-блоки', ADMIN_URL . '/html-blocks');
        $this->addBreadcrumb('Типы блоков', ADMIN_URL . '/html-blocks/types');
        $this->addBreadcrumb('Удаление типа: ' . $this->systemName);
        
        if (!$this->systemName) {
            \Notification::error('Системное имя типа блока не указано');
            $this->redirect(ADMIN_URL . '/html-blocks/types');
            return;
        }
        
        if ($this->systemName === 'DefaultBlock') {
            \Notification::error('Нельзя удалить дефолтный тип блока');
            $this->redirect(ADMIN_URL . '/html-blocks/types');
            return;
        }
        
        try {
            if ($this->blockTypeManager->hasBlocks($this->systemName)) {
                \Notification::error('Нельзя удалить тип блока, так как существуют созданные блоки этого типа');
                $this->redirect(ADMIN_URL . '/html-blocks/types');
                return;
            }
            
            $blockFile = __DIR__ . '/../../../html_blocks/' . $this->systemName . '.php';
            
            if (file_exists($blockFile)) {
                if (!unlink($blockFile)) {
                    \Notification::error('Не удалось удалить файл блока');
                    $this->redirect(ADMIN_URL . '/html-blocks/types');
                    return;
                }
                \Notification::success('Файл блока успешно удален');
            } else {
                \Notification::warning('Файл блока не найден, но запись будет удалена из базы');
            }
            
            $this->blockTypeManager->deleteBlockType($this->systemName);
            
            \Notification::success('Тип блока успешно удален из системы');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении типа блока: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/html-blocks/types');
    }
}