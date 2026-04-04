<?php

namespace forms\actions;

/**
* Действие предварительного просмотра формы
*/
class AdminPreview extends FormAction {
    
    public function execute() {
        $id = $this->params['id'] ?? null;
        if (!$id) {
            \Notification::error('ID формы не указан');
            $this->redirect(ADMIN_URL . '/forms');
            return;
        }
        
        $form = $this->formModel->getById($id);
        if (!$form) {
            \Notification::error('Форма не найдена');
            $this->redirect(ADMIN_URL . '/forms');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Формы', ADMIN_URL . '/forms');
        $this->addBreadcrumb('Предпросмотр: ' . html($form['name']));
        
        $settings = $form['settings'] ?? [];
        
        $formHtml = \FormRenderer::render($form['slug'], [
            'class' => 'form-preview',
            'ajax' => $settings['ajax_enabled'] ?? false,
            'show_labels' => $settings['show_labels'] ?? true,
            'show_descriptions' => $settings['show_descriptions'] ?? true,
            'captcha' => $settings['captcha_enabled'] ?? false,
            'captcha_type' => $settings['captcha_type'] ?? 'math',
            'captcha_question' => $settings['captcha_question'] ?? '',
            'captcha_secret' => $settings['captcha_secret'] ?? 'bloggy_cms_captcha',
            'csrf_protection' => $settings['csrf_protection'] ?? true
        ]);
        
        $this->render('admin/forms/preview', [
            'form' => $form,
            'formHtml' => $formHtml,
            'pageTitle' => 'Предпросмотр формы: ' . html($form['name'])
        ]);
    }

}