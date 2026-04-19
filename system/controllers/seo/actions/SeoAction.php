<?php
namespace seo\actions;

/**
* Абстрактный базовый класс для всех действий модуля SEO
* @package seo\actions
*/
abstract class SeoAction {

    protected $db;
    protected $params;
    protected $controller;
    protected $seoModel;
    protected $postModel;
    protected $pageModel;
    protected $categoryModel;
    protected $tagModel;
    protected $settingsModel;
    protected $breadcrumbs;
    protected $pageTitle;

    /**
    * Конструктор класса действия
    *
    * @param object $db Подключение к базе данных
    * @param array $params Параметры запроса
    */
    public function __construct($db, $params = []) {
        $this->db = $db;
        $this->params = $params;
        $this->seoModel = new \SeoModel($db);
        $this->postModel = new \PostModel($db);
        $this->pageModel = new \PageModel($db);
        $this->categoryModel = new \CategoryModel($db);
        $this->tagModel = new \TagModel($db);
        $this->settingsModel = new \SettingsModel($db);
        $this->breadcrumbs = new \BreadcrumbsManager($db);
        $this->pageTitle = '';
        \BreadcrumbsHelper::setManager($this->breadcrumbs);
    }

    /**
    * Устанавливает контроллер
    */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
    * Абстрактный метод выполнения действия
    */
    abstract public function execute();
    
    /**
    * Добавляет элемент в хлебные крошки
    * @param string $title Название элемента
    * @param string|null $url URL элемента
    * @return self
    */
    protected function addBreadcrumb($title, $url = null) {
        $this->breadcrumbs->add($title, $url);
        return $this;
    }
    
    /**
    * Устанавливает заголовок страницы
    * @param string $title Заголовок
    * @return self
    */
    protected function setPageTitle($title) {
        $this->pageTitle = $title;
        return $this;
    }
    
    /**
    * Возвращает менеджер хлебных крошек 
    * @return \BreadcrumbsManager
    */
    protected function getBreadcrumbs() {
        return $this->breadcrumbs;
    }

    /**
    * Рендерит шаблон
    */
    protected function render($template, $data = []) {
        if ($this->controller) {
            if (!isset($data['breadcrumbs'])) {
                $data['breadcrumbs'] = $this->breadcrumbs;
            }
            if (!isset($data['title']) && $this->pageTitle) {
                $data['title'] = $this->pageTitle;
            }
            $this->controller->render($template, $data);
        } else {
            throw new \Exception('Controller not set for Action');
        }
    }

    /**
    * Перенаправление
    */
    protected function redirect($url) {
        if ($this->controller) {
            $this->controller->redirect($url);
        } else {
            header('Location: ' . $url);
            exit;
        }
    }

    /**
    * Проверка AJAX запроса
    */
    protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
    * Отправка JSON ответа
    */
    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
    * Отправка XML ответа
    */
    protected function xmlResponse($xml, $filename = null) {
        header('Content-Type: application/xml; charset=utf-8');
        if ($filename) {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        }
        echo $xml;
        exit;
    }

    /**
    * Отправка TXT ответа
    */
    protected function textResponse($text, $filename = null) {
        header('Content-Type: text/plain; charset=utf-8');
        if ($filename) {
            header('Content-Disposition: inline; filename="' . $filename . '"');
        }
        echo $text;
        exit;
    }

    /**
    * Получение базового URL
    */
    protected function getBaseUrl() {
        return defined('BASE_URL') ? BASE_URL : 'http://localhost';
    }

    /**
    * Экранирование для XML
    */
    protected function escapeXml($string) {
        return html($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
    * Форматирование даты для XML
    */
    protected function formatXmlDate($timestamp) {
        if (empty($timestamp)) {
            return date('Y-m-d\TH:i:s+00:00');
        }
        return date('Y-m-d\TH:i:s+00:00', strtotime($timestamp));
    }
}