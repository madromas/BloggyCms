<?php

namespace forms\actions;

/**
* Действие просмотра формы
*/
class FormView extends FormAction {
    
    public function execute() {
        $slug = $this->params['slug'] ?? null;
        if (!$slug) {
            \Notification::error('Форма не указана');
            $this->redirect(BASE_URL);
            return;
        }
        
        $form = $this->formModel->getBySlug($slug);
        if (!$form || $form['status'] !== 'active') {
            \Notification::error('Форма не найдена или неактивна');
            $this->redirect(BASE_URL);
            return;
        }
        
        $csrfToken = \FormRenderer::generateToken();
        $settings = $form['settings'] ?? [];
        $ajaxEnabled = $settings['ajax_enabled'] ?? true;
        $showLabels = $settings['show_labels'] ?? true;
        $showDescriptions = $settings['show_descriptions'] ?? true;
        $recaptchaSiteKey = $settings['recaptcha_site_key'] ?? '';
        
        $formHtml = \FormRenderer::render($slug, [
            'class' => 'form-view',
            'ajax' => $ajaxEnabled,
            'show_labels' => $showLabels,
            'show_descriptions' => $showDescriptions,
            'recaptcha' => $recaptchaEnabled,
            'recaptcha_site_key' => $recaptchaSiteKey
        ]);
        
        $this->render('forms/view', [
            'form' => $form,
            'formHtml' => $formHtml,
            'csrfToken' => $csrfToken,
            'ajaxEnabled' => $ajaxEnabled,
            'additionalScripts' => $additionalScripts,
            'pageTitle' => html($form['name'])
        ]);
    }
}