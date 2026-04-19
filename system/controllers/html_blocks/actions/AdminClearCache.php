<?php

namespace html_blocks\actions;

class AdminClearCache extends HtmlBlockAction {
    
    public function execute() {
        
        try {
            clear_blocks_assets_cache();
            
            regenerate_blocks_css();
            
            \Notification::success('Кеш CSS блоков успешно очищен и перегенерирован');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при очистке кеша: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/html-blocks');
    }
}