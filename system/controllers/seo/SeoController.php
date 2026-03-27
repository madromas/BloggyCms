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
    * Настройки robots.txt
    */
    public function adminRobotsAction() {
        $action = new \seo\actions\AdminRobots($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Настройки sitemap.xml
    */
    public function adminSitemapAction() {
        $action = new \seo\actions\AdminSitemap($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Настройки RSS
    */
    public function adminRssAction() {
        $action = new \seo\actions\AdminRss($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Генерация sitemap.xml (публичный доступ)
    */
    public function sitemapAction() {
        $action = new \seo\actions\GenerateSitemap($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Генерация robots.txt (публичный доступ)
    */
    public function robotsAction() {
        $action = new \seo\actions\GenerateRobots($this->db);
        $action->setController($this);
        return $action->execute();
    }

    /**
    * Генерация RSS ленты (публичный доступ)
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

}
