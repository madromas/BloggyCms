<?php

namespace fragments\actions;

/**
* Действие отображения списка фрагментов
*/
class AdminIndex extends FragmentAction {
    
    /**
    * Действие отображения списка фрагментов
    */
    public function execute() {
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Фрагменты');
        $this->setPageTitle('Управление фрагментами');
        
        $fragments = $this->fragmentModel->getAll();
        
        $hints = [
            "Фрагменты позволяют создавать повторяемые блоки контента с произвольными полями",
            "После создания фрагмента вы можете добавить поля и заполнить их данными",
            "Для вывода фрагмента на сайте используйте шорткод {имя_фрагмента} или {ctype:имя_фрагмента}...{/ctype:имя_фрагмента}",
            "Вы можете подключить CSS и JS файлы для стилизации фрагмента",
            "Поля фрагмента создаются точно так же, как и обычные поля в системе"
        ];
        
        $randomHint = $hints[array_rand($hints)];
        
        $this->render('admin/fragments/index', [
            'fragments' => $fragments,
            'randomHint' => $randomHint,
            'totalFragments' => count($fragments)
        ]);
    }

}