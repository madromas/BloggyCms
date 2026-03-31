<?php

namespace fragments\actions;

/**
 * Действие управления записями фрагмента
 */
class AdminEntries extends FragmentAction {
    
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID фрагмента не указан');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $fragment = $this->fragmentModel->getById($id);
        
        if (!$fragment) {
            \Notification::error('Фрагмент не найден');
            $this->redirect(ADMIN_URL . '/fragments');
            return;
        }
        
        $this->addBreadcrumb('Главная', ADMIN_URL);
        $this->addBreadcrumb('Фрагменты', ADMIN_URL . '/fragments');
        $this->addBreadcrumb($fragment['name'], ADMIN_URL . '/fragments/edit/' . $id);
        $this->addBreadcrumb('Записи');
        $this->setPageTitle('Записи фрагмента: ' . $fragment['name']);
        
        $entries = $this->entryModel->getByFragment($id);
        $fields = $this->fragmentModel->getFields($id);
        $stats = $this->fragmentModel->getStats($id);
        
        $this->render('admin/fragments/entries', [
            'fragment' => $fragment,
            'entries' => $entries,
            'fields' => $fields,
            'stats' => $stats
        ]);
    }
}