<?php

namespace fragments\actions;

/**
 * Действие редактирования фрагмента
 */
class AdminEdit extends FragmentAction {
    
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
        $this->addBreadcrumb('Редактирование: ' . $fragment['name']);
        $this->setPageTitle('Редактирование фрагмента: ' . $fragment['name']);
        
        $stats = $this->fragmentModel->getStats($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty($_POST['name'])) {
                    throw new \Exception('Название фрагмента обязательно');
                }
                
                if (empty($_POST['system_name'])) {
                    throw new \Exception('Системное имя обязательно');
                }
                
                if ($this->fragmentModel->isSystemNameExists($_POST['system_name'], $id)) {
                    throw new \Exception('Фрагмент с таким системным именем уже существует');
                }
                
                $data = [
                    'system_name' => $this->sanitizeSystemName($_POST['system_name']),
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'status' => $_POST['status'] ?? 'active'
                ];
                
                $data = $this->handleFragmentAssets($data);
                $this->fragmentModel->update($id, $data);
                
                \Notification::success('Фрагмент успешно обновлен');
                $this->redirect(ADMIN_URL . '/fragments/edit/' . $id);
                
            } catch (\Exception $e) {
                \Notification::error($e->getMessage());
            }
        }
        
        $this->render('admin/fragments/form', [
            'fragment' => $fragment,
            'stats' => $stats,
            'isEdit' => true
        ]);
    }
}