<?php

/**
* Контроллер управления фрагментами в админ-панели
* @package controllers\fragments
*/
class AdminFragmentController extends Controller {
    
    /**
    * @var FragmentModel
    */
    private $fragmentModel;
    
    /**
    * Конструктор
    * 
    * @param \Database $db
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->fragmentModel = new FragmentModel($db);
    }
    
    /**
    * Главная страница управления фрагментами
    */
    public function adminIndexAction() {
        $action = new \fragments\actions\AdminIndex($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Создание фрагмента
    */
    public function createAction() {
        $action = new \fragments\actions\AdminCreate($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Редактирование фрагмента
    * @param int $id
    */
    public function editAction($id) {
        $action = new \fragments\actions\AdminEdit($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Удаление фрагмента
    * @param int $id
    */
    public function deleteAction($id) {
        $action = new \fragments\actions\AdminDelete($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Управление полями фрагмента 
    * @param int $id
    */
    public function fieldsAction($id) {
        $action = new \fragments\actions\AdminFields($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Управление записями фрагмента
    * @param int $id
    */
    public function entriesAction($id) {
        $action = new \fragments\actions\AdminEntries($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Создание записи фрагмента
    * @param int $id
    */
    public function entryCreateAction($id) {
        $action = new \fragments\actions\AdminEntryCreate($this->db, ['fragment_id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Редактирование записи фрагмента
    * @param int $id
    */
    public function entryEditAction($id) {
        $action = new \fragments\actions\AdminEntryEdit($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Удаление записи фрагмента
    * @param int $id
    */
    public function entryDeleteAction($id) {
        $action = new \fragments\actions\AdminEntryDelete($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Сортировка записей (AJAX)
    */
    public function reorderEntriesAction() {
        $action = new \fragments\actions\AdminReorderEntries($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Создание поля фрагмента
    * @param int $fragment_id
    */
    public function fieldCreateAction($fragment_id) {
        $action = new \fragments\actions\AdminFieldCreate($this->db, ['fragment_id' => $fragment_id]);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Редактирование поля фрагмента
    * @param int $id
    */
    public function fieldEditAction($id) {
        $action = new \fragments\actions\AdminFieldEdit($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Удаление поля фрагмента
    * @param int $id
    */
    public function fieldDeleteAction($id) {
        $action = new \fragments\actions\AdminFieldDelete($this->db, ['id' => $id]);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Сортировка полей (AJAX)
    */
    public function fieldReorderAction() {
        $action = new \fragments\actions\AdminFieldReorder($this->db);
        $action->setController($this);
        return $action->execute();
    }

}