<?php

/**
* Контроллер управления языковыми настройками
*/
class LanguageController extends Controller {
    
    /**
    * Конструктор
    */
    public function __construct($db) {
        parent::__construct($db);
    }
    
    /**
    * Главная страница языковых настроек
    */
    public function indexAction() {
        $this->addBreadcrumb('Панель управления', ADMIN_URL);
        $this->addBreadcrumb('Языковые настройки');
        
        $settings = $this->getLanguageSettings();
        
        $this->render('admin/language/index', [
            'settings' => $settings,
            'availableLocales' => $this->getAvailableLocales(),
            'pageTitle' => 'Языковые настройки'
        ]);
    }
    
    /**
    * Сохранение языковых настроек
    */
    public function saveAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Notification::error('Неверный метод запроса');
            $this->redirect(ADMIN_URL . '/language');
            return;
        }
        
        try {
            $adminLanguage = $_POST['admin_language'] ?? 'ru_RU';
            $siteLanguage = $_POST['site_language'] ?? 'ru_RU';
            $allowUserSwitch = isset($_POST['allow_user_switch']) ? 1 : 0;

            $availableLocales = $this->getAvailableLocales();
            if (!isset($availableLocales[$adminLanguage]) || !isset($availableLocales[$siteLanguage])) {
                throw new Exception('Недопустимый язык');
            }
            
            $generalSettings = $this->settingsModel->get('general');
            $generalSettings['admin_language'] = $adminLanguage;
            $generalSettings['site_language'] = $siteLanguage;
            $generalSettings['allow_user_language_switch'] = $allowUserSwitch;
            $this->settingsModel->save('general', $generalSettings);
            
            if ($adminLanguage != ($_SESSION['admin_language'] ?? '')) {
                $_SESSION['admin_language'] = $adminLanguage;
                Language::refresh();
            }
            
            Notification::success('Языковые настройки сохранены');
            
        } catch (Exception $e) {
            Notification::error('Ошибка: ' . $e->getMessage());
        }
        
        $this->redirect(ADMIN_URL . '/language');
    }
    
    /**
    * Получение языковых настроек
    * @return array
    */
    private function getLanguageSettings() {
        $generalSettings = $this->settingsModel->get('general');
        
        return [
            'admin_language' => $generalSettings['admin_language'] ?? 'ru_RU',
            'site_language' => $generalSettings['site_language'] ?? 'ru_RU',
            'allow_user_switch' => $generalSettings['allow_user_language_switch'] ?? false
        ];
    }
    
    /**
    * Получение списка доступных языков
    * @return array
    */
    private function getAvailableLocales() {
        $locales = [];
        $available = Language::getAvailableLocales();
        
        foreach ($available as $locale) {
            $locales[$locale] = [
                'code' => $locale,
                'name' => Language::getLanguageName($locale)
            ];
        }
        
        return $locales;
    }
}