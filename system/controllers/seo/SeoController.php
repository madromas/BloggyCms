<?php
/**
 * Контроллер управления SEO-настройками
 * Предоставляет интерфейс для управления robots.txt, sitemap.xml и RSS-лентами
 *
 * @package Controllers
 * @extends Controller
 */
class SeoController extends Controller {
    /** @var SeoModel Модель для работы с SEO */
    private $seoModel;
    
    /**
    * Конструктор контроллера
    */
    public function __construct($db) {
        parent::__construct($db);
        $this->seoModel = new SeoModel($db);
    }
    
    /**
    * Главная страница управления SEO (админка)
    */
    public function adminIndexAction() {
        $action = new \seo\actions\AdminIndex($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Настройка SEO (админка)
    */
    public function adminSettingsAction() {
        $action = new \seo\actions\AdminSettings($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Настройка robots.txt
    */
    public function adminRobotsAction() {
        $action = new \seo\actions\AdminRobots($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Настройка sitemap.xml
    */
    public function adminSitemapAction() {
        $action = new \seo\actions\AdminSitemap($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Настройка RSS
    */
    public function adminRssAction() {
        $action = new \seo\actions\AdminRss($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Очистка кэша SEO
    */
    public function adminClearCacheAction() {
        $this->seoModel->clearCache();
        \Notification::success('Кэш SEO очищен');
        $this->redirect(ADMIN_URL . '/seo');
    }
    
    /**
    * Тестовая отправка IndexNow
    * Доступно в админке: /admin/seo/test-indexnow
    */
    public function adminTestIndexNowAction() {
        $action = new \seo\actions\AdminTestIndexNow($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Обработка очереди задач IndexNow (для cron)
    * Доступно по URL: /admin/seo/process-queue?token={secret}
    */
    public function adminProcessQueueAction() {
        $action = new \seo\actions\AdminProcessQueue($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Отдача файла с ключом IndexNow
    * URL: /{key}.txt
    */
    public function indexnowKeyAction($key) {
        $action = new \seo\actions\IndexNowKey($this->db, ['key' => $key]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Генерация sitemap.xml
    */
    public function sitemapAction() {
        $action = new \seo\actions\GenerateSitemap($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Генерация robots.txt
    */
    public function robotsAction() {
        $action = new \seo\actions\GenerateRobots($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * Генерация RSS
    */
    public function rssAction() {
        $action = new \seo\actions\GenerateRss($this->db);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * RSS для категории
    */
    public function rssCategoryAction($slug) {
        $action = new \seo\actions\GenerateRss($this->db, ['type' => 'category', 'slug' => $slug]);
        $action->setController($this);
        return $action->execute();
    }
    
    /**
    * RSS для тега
    */
    public function rssTagAction($slug) {
        $action = new \seo\actions\GenerateRss($this->db, ['type' => 'tag', 'slug' => $slug]);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Настройка Schema.org
    */
    public function adminSchemaAction() {
        $action = new \seo\actions\AdminSchema($this->db);
        $action->setController($this);
        return $action->execute();
    }

}