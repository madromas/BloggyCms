<?php

namespace addons\actions;

/**
* Действие отображения формы установки пакета
* 
* @package addons\actions
*/
class AdminInstall extends AddonAction {
    
    /**
    * Метод выполнения
    */
    public function execute() {
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Пакеты', ADMIN_URL . '/addons');
        $this->addBreadcrumb('Установка пакета');
        
        $this->render('admin/addons/install', [
            'pageTitle' => 'Установка пакета'
        ]);
    }
}
