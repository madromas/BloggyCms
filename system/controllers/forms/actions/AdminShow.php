<?php

namespace forms\actions;

/**
* Действие просмотра отправок формы
*/
class AdminShow extends FormAction {
    
    /**
    * Действие просмотра отправок формы
    */
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
        $this->addBreadcrumb(html($form['name']), ADMIN_URL . '/forms/edit/' . $id);
        $this->addBreadcrumb('Отправки');
        
        $page = $_GET['page'] ?? 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $submissions = $this->formModel->getSubmissions($id, $perPage, $offset);
        $totalSubmissions = $this->formModel->getSubmissionsCount($id);
        $totalPages = ceil($totalSubmissions / $perPage);
        
        $statusStats = [
            'new' => 0,
            'read' => 0,
            'processed' => 0,
            'spam' => 0
        ];
        
        foreach ($submissions as $submission) {
            if (isset($statusStats[$submission['status']])) {
                $statusStats[$submission['status']]++;
            }
        }
        
        $this->render('admin/forms/show', [
            'form' => $form,
            'submissions' => $submissions,
            'submissionsCount' => $totalSubmissions,
            'newCount' => $statusStats['new'],
            'processedCount' => $statusStats['processed'],
            'spamCount' => $statusStats['spam'],
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pageTitle' => 'Отправки формы: ' . html($form['name']),
            'formModel' => $this->formModel
        ]);
    }

}