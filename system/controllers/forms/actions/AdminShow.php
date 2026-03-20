<?php

namespace forms\actions;

/**
 * Действие просмотра отправок формы
 */
class AdminShow extends FormAction {
    
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
            'pageTitle' => 'Отправки формы: ' . htmlspecialchars($form['name']),
            'formModel' => $this->formModel
        ]);
    }
}