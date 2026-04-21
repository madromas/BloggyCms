<?php

namespace addons\actions;

/**
* Действие отображения списка установленных пакетов
* @package addons\actions
*/
class AdminIndex extends AddonAction {
    
    /**
    * Метод выполнения
    */
    public function execute() {
        try {
            $this->addBreadcrumb(LANG_CONTROLLER_ADDONS_ACTION_INDEX_BREADCRUMB_DASHBOARD, ADMIN_URL);
            $this->addBreadcrumb(LANG_CONTROLLER_ADDONS_ACTION_INDEX_BREADCRUMB_ADDONS);
            
            $addons = $this->addonModel->getAll();
            
            $hints = [
                LANG_CONTROLLER_ADDONS_ACTION_INDEX_HINT_1,
                LANG_CONTROLLER_ADDONS_ACTION_INDEX_HINT_2,
                LANG_CONTROLLER_ADDONS_ACTION_INDEX_HINT_3,
                LANG_CONTROLLER_ADDONS_ACTION_INDEX_HINT_4,
                LANG_CONTROLLER_ADDONS_ACTION_INDEX_HINT_5
            ];
            
            $randomHint = $hints[array_rand($hints)];
            
            $this->render('admin/addons/index', [
                'addons' => $addons,
                'randomHint' => $randomHint,
                'addonCount' => count($addons),
                'pageTitle' => LANG_CONTROLLER_ADDONS_ACTION_INDEX_PAGE_TITLE
            ]);
            
        } catch (\Exception $e) {
            \Notification::error(LANG_CONTROLLER_ADDONS_ACTION_INDEX_ERROR . $e->getMessage());
            $this->redirect(ADMIN_URL);
        }
    }
}