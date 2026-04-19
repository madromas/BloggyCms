<?php

/**
* Класс для управления языками и переводами
* @package Core
*/
class Language {
    
    private static $currentLocale = 'ru_RU';
    private static $initialized = false;
    private static $loadedFiles = [];
    private static $adminLocale = null;
    private static $siteLocale = null;
    private static $allowUserSwitch = false;
    private static $languageNames = [];
    private static $languageCodes = [];
    
    /**
    * Инициализация языковой системы
    * @param string|null $locale Принудительная установка локали
    */
    public static function init($locale = null) {
        if (self::$initialized && $locale === null) {
            return;
        }
        
        if ($locale !== null) {
            self::$currentLocale = $locale;
        } else {
            self::loadLanguageSettings();
            self::determineLocale();
        }
        
        self::$initialized = true;
    }
    
    /**
    * Принудительная перезагрузка языковой системы
    */
    public static function refresh() {
        self::$initialized = false;
        self::$loadedFiles = [];
        self::$adminLocale = null;
        self::$siteLocale = null;
        self::$languageNames = [];
        self::$languageCodes = [];
        self::init();
    }
    
    /**
    * Загрузка настроек языков из базы данных
    */
    private static function loadLanguageSettings() {
        try {
            $db = Database::getInstance();
            $settingsModel = new SettingsModel($db);
            $generalSettings = $settingsModel->get('general');
            
            self::$adminLocale = $generalSettings['admin_language'] ?? 'ru_RU';
            self::$siteLocale = $generalSettings['site_language'] ?? 'ru_RU';
            self::$allowUserSwitch = !empty($generalSettings['allow_user_language_switch']);
            
        } catch (Exception $e) {
            self::$adminLocale = 'ru_RU';
            self::$siteLocale = 'ru_RU';
            self::$allowUserSwitch = false;
        }
    }
    
    /**
    * Определение текущей локали на основе контекста
    */
    private static function determineLocale() {
        $isAdmin = self::isAdminArea();
        
        if (self::$allowUserSwitch && isset($_SESSION['user_language'])) {
            $userLocale = $_SESSION['user_language'];
            if (self::isValidLocale($userLocale)) {
                self::$currentLocale = $userLocale;
                return;
            }
        }
        
        if ($isAdmin) {
            if (isset($_SESSION['admin_language'])) {
                self::$currentLocale = $_SESSION['admin_language'];
                return;
            }
            self::$currentLocale = self::$adminLocale;
        } else {
            self::$currentLocale = self::$siteLocale;
        }
    }
    
    /**
    * Проверка, находится ли пользователь в административной панели
    * @return bool
    */
    private static function isAdminArea() {
        $currentUri = $_SERVER['REQUEST_URI'] ?? '';
        
        if (defined('ADMIN_URL')) {
            $adminPath = parse_url(ADMIN_URL, PHP_URL_PATH);
            if ($adminPath && strpos($currentUri, $adminPath) === 0) {
                return true;
            }
        }
        
        return strpos($currentUri, '/admin/') === 0;
    }
    
    /**
    * Проверка валидности локали
    * @param string $locale
    * @return bool
    */
    private static function isValidLocale($locale) {
        $langDir = LANGUAGES_PATH . '/' . $locale;
        return is_dir($langDir);
    }
    
    /**
    * Загрузка языкового файла
    * @param string $path Путь к файлу относительно папки языка
    * @return bool
    */
    public static function load($path) {
        $cacheKey = self::$currentLocale . '/' . $path;
        
        if (isset(self::$loadedFiles[$cacheKey])) {
            return true;
        }
        
        $langFile = LANGUAGES_PATH . '/' . self::$currentLocale . '/' . $path . '.php';
        
        if (!file_exists($langFile)) {
            $fallbackFile = LANGUAGES_PATH . '/ru_RU/' . $path . '.php';
            if (!file_exists($fallbackFile)) {
                return false;
            }
            $langFile = $fallbackFile;
        }
        
        require_once $langFile;
        self::$loadedFiles[$cacheKey] = true;
        
        return true;
    }
    
    /**
    * Загрузка языкового файла для класса
    * @param string $className
    */
    public static function loadForClass($className) {
        $className = basename(str_replace('\\', '/', $className));
        
        if (strpos($className, 'Controller') !== false) {
            $controllerName = str_replace('Controller', '', $className);
            $isAdmin = strpos($controllerName, 'Admin') === 0;
            if ($isAdmin) {
                $controllerName = substr($controllerName, 5);
            }
            $controllerPath = strtolower($controllerName);
            self::load('controllers/' . $controllerPath . '/' . $className);
        }
        
        if (substr($className, -5) === 'Field') {
            self::load('fields/' . $className);
        }
        
        if (strpos($className, 'Block') !== false) {
            if (file_exists(LANGUAGES_PATH . '/' . self::$currentLocale . '/html_blocks/' . $className . '.php')) {
                self::load('html_blocks/' . $className);
            } elseif (file_exists(LANGUAGES_PATH . '/' . self::$currentLocale . '/post_blocks/' . $className . '.php')) {
                self::load('post_blocks/' . $className);
            }
        }
        
        if (file_exists(LANGUAGES_PATH . '/' . self::$currentLocale . '/core/' . $className . '.php')) {
            self::load('core/' . $className);
        }
    }
    
    /**
    * Получение названия языка по коду локали
    * @param string $locale
    * @return string
    */
    public static function getLanguageName($locale) {
        if (isset(self::$languageNames[$locale])) {
            return self::$languageNames[$locale];
        }
        
        $langFile = LANGUAGES_PATH . '/' . $locale . '/core/Language.php';
        $name = $locale;
        
        if (file_exists($langFile)) {

            $content = file_get_contents($langFile);
            
            if (preg_match("/define\s*\(\s*['\"]LANG_NAME['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches)) {
                $name = $matches[1];
            }

            elseif (preg_match("/define\s*\(\s*\"LANG_NAME\"\s*,\s*\"([^\"]+)\"\s*\)/", $content, $matches)) {
                $name = $matches[1];
            }
        }
        
        self::$languageNames[$locale] = $name;
        return $name;
    }
    
    /**
    * Получение кода языка
    * @param string $locale
    * @return string
    */
    public static function getLanguageCode($locale) {
        if (isset(self::$languageCodes[$locale])) {
            return self::$languageCodes[$locale];
        }
        
        $langFile = LANGUAGES_PATH . '/' . $locale . '/core/Language.php';
        $code = str_replace('_', '-', $locale);
        
        if (file_exists($langFile)) {

            $content = file_get_contents($langFile);
            
            if (preg_match("/define\s*\(\s*['\"]LANG_CODE['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches)) {
                $code = $matches[1];
            }

            elseif (preg_match("/define\s*\(\s*\"LANG_CODE\"\s*,\s*\"([^\"]+)\"\s*\)/", $content, $matches)) {
                $code = $matches[1];
            }
        }
        
        self::$languageCodes[$locale] = $code;
        return $code;
    }
    
    /**
    * Получение списка доступных локалей
    * @return array
    */
    public static function getAvailableLocales() {
        if (!is_dir(LANGUAGES_PATH)) {
            return ['ru_RU'];
        }
        
        $locales = [];
        $dirs = glob(LANGUAGES_PATH . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $locales[] = basename($dir);
        }
        return empty($locales) ? ['ru_RU'] : $locales;
    }
    
    /**
    * Получение текущей локали
    * @return string
    */
    public static function getCurrentLocale() {
        return self::$currentLocale;
    }
    
    /**
    * Получение языка админ-панели
    * @return string
    */
    public static function getAdminLocale() {
        return self::$adminLocale;
    }
    
    /**
    * Получение языка сайта
    * @return string
    */
    public static function getSiteLocale() {
        return self::$siteLocale;
    }
    
    /**
    * Разрешено ли пользователям переключать язык
    * @return bool
    */
    public static function isAllowUserSwitch() {
        return self::$allowUserSwitch;
    }
    
    /**
    * Переключение языка пользователем
    * @param string $locale
    * @return bool
    */
    public static function switchLanguage($locale) {
        if (!self::$allowUserSwitch) {
            return false;
        }
        
        if (!self::isValidLocale($locale)) {
            return false;
        }
        
        $_SESSION['user_language'] = $locale;
        self::$currentLocale = $locale;
        self::$loadedFiles = [];
        
        return true;
    }
}