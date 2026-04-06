<?php

/**
* Контроллер HTML-блоков для фронтенда
* @package controllers
*/
class HtmlBlockController extends Controller {
    
    private $htmlBlockModel;
    private $blockTypeManager;
    
    /**
    * Конструктор контроллера HTML-блоков
    * @param Database $db Объект подключения к базе данных
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->htmlBlockModel = new HtmlBlockModel($db);
        $this->blockTypeManager = new HtmlBlockTypeManager($db);
    }
    
    /**
    * Действие: Отображение HTML-блока на фронтенде
    * @param string|null $slug URL-идентификатор блока
    * @return void
    */
    public function showAction($slug = null) {
        if (!$slug) {
            \Notification::error('Slug блока не указан');
            $this->redirect(BASE_URL . '/404');
            return;
        }
        
        try {
            $block = $this->htmlBlockModel->getBySlug($slug);
        
            if (!$block) {
                \Notification::error('HTML-блок не найден');
                $this->redirect(BASE_URL . '/404');
                return;
            }
            
            $this->loadBlockAssetsFromDatabase($block);
            
            if (!empty($block['block_type']) && $block['block_type'] !== 'DefaultBlock') {
                $this->blockTypeManager->loadBlockFrontendAssets($block['block_type']);
            }
            
            $settings = [];
            if (!empty($block['settings'])) {
                $settings = json_decode($block['settings'], true);
            }
            
                $blockContent = '';
                if (!empty($block['block_type'])) {
                    if ($block['block_type'] === 'DefaultBlock') {
                        $blockContent = $settings['html'] ?? '';
                        
                        if (function_exists('process_shortcodes')) {
                            $blockContent = process_shortcodes($blockContent);
                        }
                    } else {
                        $blockContent = $this->blockTypeManager->renderBlockFront(
                            $block['block_type'], 
                            $settings,
                            $block['template'] ?? null
                        );
                    }
                }
            
            if (empty($blockContent)) {
                $blockContent = '<div class="alert alert-info">Блок "' . html($block['name'] ?? '') . '" не имеет содержимого.</div>';
            }
            
            $this->render('front/html_block', [
                'block' => $block,
                'content' => $blockContent,
                'title' => $block['name']
            ]);
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при загрузке HTML-блока: ' . $e->getMessage());
            $this->redirect(BASE_URL);
        }
    }
    
    /**
    * Загрузка ресурсов блока из базы данных
    * @param array $block Данные HTML-блока
    * @return void
    */
    private function loadBlockAssetsFromDatabase($block) {
        if (!empty($block['css_files'])) {
            $cssFiles = json_decode($block['css_files'], true);
            foreach ($cssFiles as $cssFile) {
                add_frontend_css($cssFile);
            }
        }
        
        if (!empty($block['js_files'])) {
            $jsFiles = json_decode($block['js_files'], true);
            foreach ($jsFiles as $jsFile) {
                add_frontend_js($jsFile);
            }
        }
        
        if (!empty($block['inline_css'])) {
            add_inline_css($block['inline_css']);
        }
        
        if (!empty($block['inline_js'])) {
            add_inline_js($block['inline_js']);
        }
    }
    
    /**
    * Статический метод для рендеринга ресурсов блока
    * @param array $block Данные HTML-блока
    * @return void
    */
    public static function renderBlockAssets($block) {
        if (!empty($block['css_files'])) {
            $cssFiles = json_decode($block['css_files'], true);
            foreach ($cssFiles as $cssFile) {
                add_frontend_css($cssFile);
            }
        }
        
        if (!empty($block['js_files'])) {
            $jsFiles = json_decode($block['js_files'], true);
            foreach ($jsFiles as $jsFile) {
                add_frontend_js($jsFile);
            }
        }
        
        if (!empty($block['inline_css'])) {
            add_inline_css($block['inline_css']);
        }
        
        if (!empty($block['inline_js'])) {
            add_inline_js($block['inline_js']);
        }
    }
}