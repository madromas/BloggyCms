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
        $this->render('admin/addons/install', [
            'pageTitle' => 'Установка пакета'
        ]);
    }
}
