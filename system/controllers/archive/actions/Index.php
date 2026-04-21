<?php

namespace archive\actions;

/**
* Реализует отображение главной страницы архива с группировкой постов по месяцам
*/
class Index extends ArchiveAction {
    
    /**
    * Выполнение действия по отображению архива
    * @throws \Exception В случае ошибки отображает страницу 500
    */
    public function execute() {
        try {
            $this->addBreadcrumb(LANG_ACTION_ARCHIVE_INDEX_BREADCRUMB_HOME, BASE_URL);
            $this->addBreadcrumb(LANG_ACTION_ARCHIVE_INDEX_BREADCRUMB_ARCHIVE);
            $this->setPageTitle(LANG_ACTION_ARCHIVE_INDEX_PAGE_TITLE);
            
            $archiveData = $this->postModel->getArchive();

            $postsByMonth = [];
            
            foreach ($archiveData as $archiveItem) {
                $year = $archiveItem['year'];
                $month = $archiveItem['month'];
                $posts = $this->postModel->getPostsByArchive($year, $month);
                $postsByMonth[$year][$month] = $posts;
            }
            
            $this->render('front/archive/archive', [
                'archiveData' => $archiveData,
                'postsByMonth' => $postsByMonth
            ]);
            
        } catch (\Exception $e) {
            $this->render('front/500', [], 500);
        }
    }
}