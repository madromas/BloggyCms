<?php

namespace menu\actions;

/**
* Действие удаления меню в админ-панели
* @package menu\actions
*/
class AdminDelete extends MenuAction {
    
    /**
    * Метод выполнения удаления меню
    * @return void
    */
    public function execute() {
        $id = $this->params['id'] ?? null;
        
        if (!$id) {
            \Notification::error('ID меню не указан');
            $this->redirect(ADMIN_URL . '/menu');
            return;
        }
        
        try {

            $menu = $this->menuModel->getById($id);
            
            if (!$menu) {
                throw new \Exception('Меню не найдено');
            }

            $this->menuModel->delete($id);
            
            \Notification::success('Меню успешно удалено');
            
        } catch (\Exception $e) {
            \Notification::error('Ошибка при удалении меню: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/menu');
    }
}