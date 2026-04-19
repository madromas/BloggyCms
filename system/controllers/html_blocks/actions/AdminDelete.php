<?php

namespace html_blocks\actions;

/**
* Действие удаления HTML-блока в админ-панели
* @package html_blocks\actions
*/
class AdminDelete extends HtmlBlockAction {
    
    /**
    * Метод выполнения удаления HTML-блока
    * @return void
    */
    public function execute() {
        
        try {
            $this->htmlBlockModel->delete($this->id);

            \Event::trigger('html_block.deleted', $this->id);
            
            \Notification::success('HTML-блок успешно удален');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении HTML-блока');
        }
        
        $this->redirect(ADMIN_URL . '/html-blocks');
    }
}