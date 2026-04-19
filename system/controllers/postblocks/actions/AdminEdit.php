<?php

namespace postblocks\actions;

/**
* Действие редактирования настроек постблока в административной панели 
* @package postblocks\actions
*/
class AdminEdit extends PostBlockAction {
    
    /**
    * Метод выполнения редактирования постблока
    * @return void
    */
    public function execute() {

        $systemName = $_GET['system_name'] ?? '';
        
        if (empty($systemName)) {
            \Notification::error('Системное имя блока не указано');
            $this->redirect(ADMIN_URL . '/post-blocks');
            return;
        }

        $postBlock = $this->postBlockManager->getPostBlock($systemName);
        if (!$postBlock) {
            \Notification::error('Блок не найден');
            $this->redirect(ADMIN_URL . '/post-blocks');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Постблоки', ADMIN_URL . '/post-blocks');
        $this->addBreadcrumb('Редактирование: ' . $postBlock['name']);

        $settings = $this->postBlockModel->getBlockSettings($systemName);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSettingsUpdate($systemName, $postBlock, $settings);
            return;
        }

        $this->render('admin/post_blocks/edit', [
            'postBlock' => $postBlock,
            'settings' => $settings,
            'shortcodes' => $postBlock['class']->getShortcodes(),
            'pageTitle' => 'Редактирование блока: ' . $postBlock['name']
        ]);
    }

    /**
    * Обрабатывает обновление настроек блока из POST-запроса
    * @param string $systemName Системное имя блока
    * @param array $postBlock Данные постблока
    * @param array $currentSettings Текущие настройки (не используются)
    * @return void
    */
    private function handleSettingsUpdate($systemName, $postBlock, $currentSettings) {
        $enableInPosts = isset($_POST['enable_in_posts']) ? 1 : 0;
        $enableInPages = isset($_POST['enable_in_pages']) ? 1 : 0;
        $template = $_POST['template'] ?? '';
        $success = $this->postBlockModel->updateBlockSettings($systemName, [
            'enable_in_posts' => $enableInPosts,
            'enable_in_pages' => $enableInPages,
            'template' => $template
        ]);

        if ($success) {
            \Notification::success('Настройки блока обновлены');
        } else {
            \Notification::error('Ошибка при сохранении настроек');
        }

        $this->redirect(ADMIN_URL . '/post-blocks/edit?system_name=' . $systemName);
    }
}