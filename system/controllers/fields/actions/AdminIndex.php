<?php

namespace fields\actions;

/**
* Действие отображения общего списка полей в админ-панели
* @package fields\actions
*/
class AdminIndex extends FieldAction {
    
    /**
    * Метод выполнения отображения списка полей
    * @return void
    */
    public function execute() {
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Поля');
        
        $fields = $this->fieldModel->getAll();
        
        $this->render('admin/fields/index', [
            'fields' => $fields,
            'fieldModel' => $this->fieldModel,
            'pageTitle' => 'Управление полями'
        ]);
    }

}