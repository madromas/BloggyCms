<?php

/**
* Вспомогательный класс для работы с маршрутами
* @package Core
*/
class RouteHelper {
    
    /**
    * Получает ВСЕ фронтенд маршруты из всех контроллеров 
    * @return array Массив маршрутов с полями: route, controller, action, name, module
    */
    public static function getAllFrontendRoutes() {
        $routes = [];
        
        $routes[] = [
            'route' => '*',
            'controller' => 'All',
            'action' => 'All',
            'name' => 'Все страницы'
        ];
        
        $routes[] = [
            'route' => 'home',
            'controller' => 'Home',
            'action' => 'index',
            'name' => 'Главная страница'
        ];
        
        $routes[] = [
            'route' => '404',
            'controller' => 'Error',
            'action' => 'notFound',
            'name' => 'Страница 404'
        ];
        
        $routes[] = [
            'route' => '500',
            'controller' => 'Error',
            'action' => 'serverError',
            'name' => 'Страница 500'
        ];
        
        $controllersDir = __DIR__ . '/../controllers/';
        
        if (!is_dir($controllersDir)) {
            return $routes;
        }
        
        $controllerFolders = scandir($controllersDir);
        
        foreach ($controllerFolders as $folder) {
            if ($folder === '.' || $folder === '..' || !is_dir($controllersDir . $folder)) {
                continue;
            }
            
            $routesFile = $controllersDir . $folder . '/routes.php';
            
            if (file_exists($routesFile)) {
                $controllerRoutes = include $routesFile;
                
                foreach ($controllerRoutes as $routePattern => $config) {

                    if (isset($config['admin']) && $config['admin'] === true) {
                        continue;
                    }
                    
                    $name = self::generateRouteName($config['controller'], $config['action'], $folder);
                    
                    $routes[] = [
                        'route' => $routePattern,
                        'controller' => $config['controller'],
                        'action' => $config['action'],
                        'name' => $name,
                        'module' => $folder
                    ];
                }
            }
        }
        
        usort($routes, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        return $routes;
    }
    
    /**
    * Получает маршруты для определенного контроллера
    * 
    * @param string $controllerName Имя контроллера (например 'Post', 'User')
    * @return array Отфильтрованные маршруты
    */
    public static function getRoutesForController($controllerName) {
        $allRoutes = self::getAllFrontendRoutes();
        
        return array_filter($allRoutes, function($route) use ($controllerName) {
            return $route['controller'] === $controllerName && $route['route'] !== '*';
        });
    }
    
    /**
    * Генерирует понятное имя для маршрута 
    * @param string $controller Имя контроллера
    * @param string $action Имя действия
    * @param string $module Имя модуля (опционально)
    * @return string Понятное название маршрута
    */
    private static function generateRouteName($controller, $action, $module = '') {
        $nameMap = [
            'Post' => [
                'index' => 'Список постов',
                'show' => 'Страница поста',
                'all' => 'Все посты'
            ],
            'Category' => [
                'index' => 'Список категорий',
                'show' => 'Страница категории'
            ],
            'Tag' => [
                'index' => 'Список тегов',
                'show' => 'Страница тега'
            ],
            'Page' => [
                'index' => 'Страницы',
                'show' => 'Страница'
            ],
            'User' => [
                'index' => 'Список пользователей',
                'show' => 'Профиль пользователя'
            ],
            'Search' => [
                'index' => 'Поиск'
            ],
            'Archive' => [
                'index' => 'Архив'
            ],
            'HtmlBlock' => [
                'show' => 'HTML-блок'
            ],
            'Profile' => [
                'index' => 'Профиль',
                'show' => 'Профиль пользователя',
                'edit' => 'Редактирование профиля'
            ],
            'Auth' => [
                'login' => 'Вход',
                'register' => 'Регистрация',
                'logout' => 'Выход'
            ]
        ];
        
        if (isset($nameMap[$controller][$action])) {
            return $nameMap[$controller][$action];
        }
        
        $controllerName = preg_replace('/([a-z])([A-Z])/', '$1 $2', $controller);
        $actionName = ucfirst($action);
        
        return $controllerName . ' - ' . $actionName;
    }
    
}