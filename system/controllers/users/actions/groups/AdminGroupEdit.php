<?php

namespace users\actions\groups;

/**
* Действие редактирования группы пользователей в административной панели
* @package users\actions\groups
*/
class AdminGroupEdit extends AdminGroupAction {
    
    /**
    * Метод выполнения редактирования группы
    * @return void
    */
    public function execute() {
        try {
            if (!$this->checkAdminAccess()) {
                \Notification::error('У вас нет прав доступа');
                $this->redirect(ADMIN_URL);
                return;
            }

            $id = $this->params['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID группы не указан');
            }

            $group = $this->userModel->getGroupById($id);
            if (!$group) {
                throw new \Exception('Группа не найдена');
            }

            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Группы пользователей', ADMIN_URL . '/user-groups');
            $this->addBreadcrumb('Редактирование: ' . $group['name']);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handlePostRequest($id);
                return;
            }

            $this->renderEditForm($group);

        } catch (\Exception $e) {
            \Notification::error($e->getMessage());
            $this->redirect(ADMIN_URL . '/user-groups');
        }
    }
    
    /**
    * Обрабатывает POST-запрос на обновление группы
    * @param int $id ID группы
    * @return void
    * @throws \Exception При ошибках валидации
    */
    private function handlePostRequest($id) {

        if (empty($_POST['name'])) {
            throw new \Exception('Название группы обязательно');
        }

        $groupData = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
            'is_default' => isset($_POST['is_default']) ? 1 : 0
        ];

        $this->userModel->updateGroup($id, $groupData);

        \Notification::success('Группа успешно обновлена');
        $this->redirect(ADMIN_URL . '/user-groups');
    }
    
    /**
    * Отображает форму редактирования группы
    * @param array $group Данные группы
    * @return void
    */
    private function renderEditForm($group) {
        $this->render('admin/user-groups/edit', [
            'group' => $group,
            'pageTitle' => 'Редактирование группы'
        ]);
    }
}