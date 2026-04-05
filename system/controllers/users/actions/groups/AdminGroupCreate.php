<?php

namespace users\actions\groups;

/**
* Действие создания новой группы пользователей в административной панели
* @package users\actions\groups
*/
class AdminGroupCreate extends AdminGroupAction {
    
    /**
    * Метод выполнения создания группы
    * @return void
    */
    public function execute() {
        try {
            if (!$this->checkAdminAccess()) {
                \Notification::error('У вас нет прав доступа');
                $this->redirect(ADMIN_URL);
                return;
            }

            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Группы пользователей', ADMIN_URL . '/user-groups');
            $this->addBreadcrumb('Создание группы');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handlePostRequest();
                return;
            }

            $this->renderCreateForm();

        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }
    
    /**
    * Обрабатывает POST-запрос на создание группы
    * @return void
    * @throws \Exception При ошибках валидации
    */
    private function handlePostRequest() {
        if (empty($_POST['name'])) {
            throw new \Exception('Название группы обязательно');
        }

        $groupData = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
            'is_default' => isset($_POST['is_default']) ? 1 : 0
        ];

        $groupId = $this->userModel->createGroup($groupData);

        \Notification::success('Группа успешно создана');
        $this->redirect(ADMIN_URL . '/user-groups');
    }
    
    /**
    * Отображает форму создания группы
    * @return void
    */
    private function renderCreateForm() {
        $this->render('admin/user-groups/create', [
            'pageTitle' => 'Создание группы'
        ]);
    }
    
    /**
    * Обрабатывает ошибку при создании группы 
    * @param \Exception $e Исключение
    * @return void
    */
    private function handleError($e) {
        \Notification::error($e->getMessage());
        
        $this->render('admin/user-groups/create', [
            'group' => $_POST,
            'pageTitle' => 'Создание группы'
        ]);
    }
}