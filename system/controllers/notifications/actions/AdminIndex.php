<?php

namespace notifications\actions;

/**
* Действие отображения главной страницы уведомлений в админ-панели
* @package notifications\actions
*/
class AdminIndex extends NotificationsAction {
    
    /**
    * Метод выполнения отображения страницы уведомлений 
    * @return void
    */
    public function execute() {
        try {
            $this->addBreadcrumb('Панель управления', ADMIN_URL);
            $this->addBreadcrumb('Уведомления');
            
            $userId = $this->getCurrentUserId();

            $stats = $this->notificationModel->getStats($userId);
            
            $viewData = $this->prepareViewData($userId, $stats);
            
            $this->render('admin/notifications/index', $viewData);
            
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }
    
    /**
    * Подготавливает данные для передачи в шаблон 
    * @param int $userId ID текущего пользователя
    * @param array $stats Статистика уведомлений
    * @return array Массив данных для шаблона
    */
    private function prepareViewData($userId, $stats) {
        return [
            'stats' => $stats,
            'notificationModel' => $this->notificationModel,
            'userModel' => $this->userModel,
            'currentUserId' => $userId,
            'pageTitle' => 'Уведомления'
        ];
    }
    
    /**
    * Обрабатывает ошибки при загрузке страницы уведомлений
    * @param \Exception $e Исключение
    * @return void
    */
    private function handleError($e) {
        \Notification::error('Ошибка при загрузке уведомлений: ' . $e->getMessage());
        
        $this->redirect(ADMIN_URL);
    }
}