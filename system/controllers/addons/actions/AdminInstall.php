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
        $this->addBreadcrumb(LANG_CONTROLLER_ADDONS_ACTION_INSTALL_BREADCRUMB_DASHBOARD, ADMIN_URL);
        $this->addBreadcrumb(LANG_CONTROLLER_ADDONS_ACTION_INSTALL_BREADCRUMB_ADDONS, ADMIN_URL . '/addons');
        $this->addBreadcrumb(LANG_CONTROLLER_ADDONS_ACTION_INSTALL_BREADCRUMB_INSTALL);
        
        $this->render('admin/addons/install', [
            'pageTitle' => LANG_CONTROLLER_ADDONS_ACTION_INSTALL_PAGE_TITLE
        ]);
    }
}