<?php

namespace forms\actions;

/**
* Действие показа формы
*/
class ShowForm extends FormAction {
    
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

        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Формы', ADMIN_URL . '/forms');
        $this->addBreadcrumb(html($form['name']));
        
        $this->render('forms/view', [
            'form' => $form,
            'pageTitle' => html($form['name'])
        ]);
    }
}