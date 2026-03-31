<?php

namespace fragments\actions;

/**
 * Действие создания фрагмента
 */
class AdminCreate extends FragmentAction {
    
    public function execute() {
        $this->addBreadcrumb('Главная', ADMIN_URL);
        $this->addBreadcrumb('Фрагменты', ADMIN_URL . '/fragments');
        $this->addBreadcrumb('Создание фрагмента');
        $this->setPageTitle('Создание фрагмента');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty($_POST['name'])) {
                    throw new \Exception('Название фрагмента обязательно');
                }
                
                if (empty($_POST['system_name'])) {
                    throw new \Exception('Системное имя обязательно');
                }
                
                if ($this->fragmentModel->isSystemNameExists($_POST['system_name'])) {
                    throw new \Exception('Фрагмент с таким системным именем уже существует');
                }
                
                $data = [
                    'system_name' => $this->sanitizeSystemName($_POST['system_name']),
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'status' => $_POST['status'] ?? 'active'
                ];
                
                $data = $this->handleFragmentAssets($data);
                $fragmentId = $this->fragmentModel->create($data);
                
                if (!$fragmentId || !is_numeric($fragmentId)) {
                    throw new \Exception('Не удалось создать фрагмент');
                }
                
                \Notification::success('Фрагмент успешно создан');
                $this->redirect(ADMIN_URL . '/fragments');
                
            } catch (\Exception $e) {
                \Notification::error($e->getMessage());
            }
        }
        
        $this->render('admin/fragments/form', [
            'fragment' => $_POST ?? null,
            'isEdit' => false
        ]);
    }
}