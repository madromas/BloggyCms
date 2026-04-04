<?php

namespace fields\actions;

/**
* Действие отображения полей для конкретной сущности в админ-панели
* @package fields\actions
*/
class AdminEntity extends FieldAction {
    
    /**
    * Метод выполнения отображения полей сущности
    * @return void
    */
    public function execute() {
        $entityType = $this->params['entityType'] ?? null;
        
        if (!$entityType) {
            \Notification::error('Тип сущности не указан');
            $this->redirect(ADMIN_URL . '/fields');
            return;
        }
        
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Поля', ADMIN_URL . '/fields');
        $this->addBreadcrumb($this->getEntityName($entityType, true));
        
        $fields = $this->fieldModel->getByEntityType($entityType);
        
        $this->render('admin/fields/entity', [
            'fields' => $fields,
            'entityType' => $entityType,
            'entityName' => $this->getEntityName($entityType),
            'fieldModel' => $this->fieldModel,
            'pageTitle' => 'Поля для ' . $this->getEntityName($entityType)
        ]);
    }

}