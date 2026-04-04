<?php

namespace html_blocks\actions;

/**
* Действие создания нового HTML-блока в админ-панели
* @package html_blocks\actions
*/
class AdminCreate extends HtmlBlockAction {
    
    /**
    * Метод выполнения создания HTML-блока
    * @return void
    */
    public function execute() {
        if (!$this->checkAdminAccess()) {
            \Notification::error('У вас нет прав доступа к этому разделу');
            $this->redirect(ADMIN_URL . '/login');
            return;
        }
        
        $blockTypeName = $_GET['type'] ?? 'DefaultBlock';

        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Контент-блоки', ADMIN_URL . '/html-blocks');
    
        $blockTypeLabel = $blockTypeName === 'DefaultBlock' ? 'Дефолтный блок' : $blockTypeName;
        $this->addBreadcrumb('Выбор типа блока', ADMIN_URL . '/html-blocks/select-type');
        $this->addBreadcrumb('Создание: ' . $blockTypeLabel);
        
        if ($blockTypeName !== 'DefaultBlock') {
            $this->blockTypeManager->loadBlockAssets($blockTypeName);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_FILES)) {
            try {
                if (empty($_POST['name']) || empty($_POST['slug'])) {
                    \Notification::error('Название и идентификатор блока обязательны для заполнения');
                    $this->renderFormWithData($_POST, $blockTypeName);
                    return;
                }

                $typeId = null;
                $settings = [];
                
                if ($blockTypeName !== 'DefaultBlock') {
                    $blockType = $this->blockTypeManager->getBlockType($blockTypeName);
                    if ($blockType) {
                        $typeId = $blockType['id'];
                        $blockInstance = $blockType['class'];
                        $settings = $_POST['settings'] ?? [];
                        
                        list($isValid, $errors) = $blockInstance->validateSettings($settings);
                        if (!$isValid) {
                            \Notification::error('Ошибки в настройках: ' . implode(', ', $errors));
                            $this->renderFormWithData($_POST, $blockTypeName);
                            return;
                        }
                        
                        $settings = $blockInstance->prepareSettings($settings);
                    }
                } else {
                    $settings = [
                        'html' => $_POST['settings']['html'] ?? '',
                        'use_fragment' => isset($_POST['settings']['use_fragment']) ? (int)$_POST['settings']['use_fragment'] : 0,
                        'selected_fragment' => $_POST['settings']['selected_fragment'] ?? ''
                    ];
                }

                $cssFiles = $this->processAssetFiles($_POST['css_files'] ?? []);
                $jsFiles = $this->processAssetFiles($_POST['js_files'] ?? []);
                
                if ($blockTypeName !== 'DefaultBlock' && isset($blockInstance)) {
                    $systemCss = $blockInstance->getSystemCss();
                    $systemJs = $blockInstance->getSystemJs();
                    
                    $cssFiles = array_merge($systemCss, $cssFiles);
                    $jsFiles = array_merge($systemJs, $jsFiles);
                }

                $data = [
                    'name' => $_POST['name'],
                    'slug' => $_POST['slug'],
                    'content' => '',
                    'type_id' => $typeId,
                    'settings' => $settings,
                    'css_files' => $cssFiles,
                    'js_files' => $jsFiles,
                    'inline_css' => $_POST['inline_css'] ?? '',
                    'inline_js' => $_POST['inline_js'] ?? '',
                    'template' => $_POST['template'] ?? 'default'
                ];

                $this->htmlBlockModel->create($data);
                
                \Notification::success('HTML-блок успешно создан');
                
                $this->redirect(ADMIN_URL . '/html-blocks');
                
            } catch (\Exception $e) {
                \Notification::error('Ошибка при создании HTML-блока: ' . $e->getMessage());
                $this->renderFormWithData($_POST, $blockTypeName);
            }
        } 
        else {
            $this->renderForm(null, $blockTypeName);
        }
    }
}