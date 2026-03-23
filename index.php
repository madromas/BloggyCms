<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', __DIR__);

if (!file_exists(ROOT_PATH . '/system/config/config.php') || 
    !file_exists(ROOT_PATH . '/system/config/database.php')) {
    header('Location: /install/');
    exit;
}

require_once ROOT_PATH . '/system/config/database.php';
require_once ROOT_PATH . '/system/config/config.php';

$requiredConstants = [
    'BASE_PATH', 'SYSTEM_PATH', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_PREFIX'
];
foreach ($requiredConstants as $const) {
    if (!defined($const)) {
        die("Критическая ошибка: Не определена константа {$const}");
    }
}

if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', BASE_URL . '/admin');
}
if (!defined('USER_ONLINE_INTERVAL')) {
    define('USER_ONLINE_INTERVAL', 300);
}
if (!defined('CACHE_DIR')) {
    define('CACHE_DIR', BASE_PATH . '/cache');
    if (!is_dir(CACHE_DIR)) {
        @mkdir(CACHE_DIR, 0755, true);
    }
}

$requiredDirs = [
    BASE_PATH . '/cache',
    BASE_PATH . '/uploads',
    BASE_PATH . '/system/logs'
];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

$coreClasses = [
    'Event',
    'Database', 
    'Controller',
    'Router',
    'App'
];

foreach ($coreClasses as $className) {
    $filePath = SYSTEM_PATH . '/core/' . $className . '.php';
    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        die("Критическая ошибка: Не найден файл {$filePath}");
    }
}

try {
    $db = Database::getInstance();
} catch (Exception $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

spl_autoload_register(function ($class) use ($db) {
    if ($class === 'AchievementTriggers') {
        $file = ROOT_PATH . '/system/controllers/users/AchievementTriggers.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    $classPath = str_replace('\\', '/', $class);

    $basePaths = [
        ROOT_PATH . '/system/controllers',
        ROOT_PATH . '/system',
        ROOT_PATH . '/system/core',
        ROOT_PATH . '/system/helpers',
        ROOT_PATH . '/system/fields',
        ROOT_PATH . '/system/html_blocks',
        ROOT_PATH . '/system/post_blocks'
    ];
    
    $helpersDir = ROOT_PATH . '/system/helpers';
    if (is_dir($helpersDir)) {
        $helperSubdirs = glob($helpersDir . '/*', GLOB_ONLYDIR);
        foreach ($helperSubdirs as $subdir) {
            $basePaths[] = $subdir;
        }
    }
    
    $controllersDir = ROOT_PATH . '/system/controllers';
    if (is_dir($controllersDir)) {
        $modules = glob($controllersDir . '/*', GLOB_ONLYDIR);
        foreach ($modules as $moduleDir) {
            $basePaths[] = $moduleDir;
            
            $modelsSubdir = $moduleDir . '/models';
            if (is_dir($modelsSubdir)) {
                $basePaths[] = $modelsSubdir;
            }
            
            if (is_dir($moduleDir . '/actions')) {
                $basePaths[] = $moduleDir . '/actions';
            }
        }
    }
    
    if (preg_match('/(.+?)Model$/', $class, $matches)) {
        $baseName = $matches[1];
        $classNameWithoutModel = str_replace('Model', '', $class);
        
        $possibleFiles = [
            $class . '.php',
            $classNameWithoutModel . 'Model.php',
            'Model.php'
        ];
        
        $controllerDirs = glob(ROOT_PATH . '/system/controllers/*', GLOB_ONLYDIR);
        
        foreach ($controllerDirs as $controllerDir) {
            foreach ($possibleFiles as $fileName) {
                $fullPath = $controllerDir . '/' . $fileName;
                if (file_exists($fullPath)) {
                    require_once $fullPath;
                    if (class_exists($class)) {
                        return;
                    }
                }
                
                $modelSubdirPath = $controllerDir . '/models/' . $fileName;
                if (file_exists($modelSubdirPath)) {
                    require_once $modelSubdirPath;
                    if (class_exists($class)) {
                        return;
                    }
                }
            }
        }

        $modelName = strtolower($baseName);
        $modelDir = ROOT_PATH . '/system/controllers/' . $modelName;
        
        if (is_dir($modelDir)) {
            foreach ($possibleFiles as $fileName) {
                $modelFile = $modelDir . '/' . $fileName;
                if (file_exists($modelFile)) {
                    require_once $modelFile;
                    if (class_exists($class)) {
                        return;
                    }
                }
                
                $modelSubdirFile = $modelDir . '/models/' . $fileName;
                if (file_exists($modelSubdirFile)) {
                    require_once $modelSubdirFile;
                    if (class_exists($class)) {
                        return;
                    }
                }
            }
        }
    }

    $possibleFiles = [
        $classPath . '.php',
        $class . '.php',
        basename($classPath) . '.php',
    ];
    
    foreach ($basePaths as $basePath) {
        foreach ($possibleFiles as $file) {
            $fullPath = $basePath . '/' . $file;
            if (file_exists($fullPath)) {
                require_once $fullPath;
                return;
            }
        }
    }
});

function loadAllHelpers($dir) {
    if (!is_dir($dir)) return;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fullPath = $dir . '/' . $file;
        
        if (is_dir($fullPath)) {
            loadAllHelpers($fullPath);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            require_once $fullPath;
        }
    }
}

$helpersPath = ROOT_PATH . '/system/helpers';
if (is_dir($helpersPath)) {
    loadAllHelpers($helpersPath);
}

define('CONTROLLERS_PATH', ROOT_PATH . '/system/controllers');
$permissionsFiles = glob(CONTROLLERS_PATH . '/*/permissions.php');
foreach ($permissionsFiles as $file) {
    if (file_exists($file) && is_readable($file)) {
        require_once $file;
    }
}

if (isset($_SESSION['user_id'])) {
    if (file_exists(SYSTEM_PATH . '/core/UserActivityManager.php')) {
        require_once SYSTEM_PATH . '/core/UserActivityManager.php';
        if (class_exists('UserActivityManager')) {
            $activityManager = UserActivityManager::getInstance();
            if ($activityManager) {
                $activityManager->touch($_SESSION['user_id']);
            }
        }
    }
}

if (class_exists('AssetManager')) {
    AssetManager::getInstance()->clear();
}

try {
    if (class_exists('DatabaseRegistry')) {
        DatabaseRegistry::init($db);
    }
    
    if (class_exists('Event')) {
        if (method_exists('Event', 'initialize')) {
            Event::initialize();
        }
        Event::trigger('app.init', [
            'db' => $db,
            'app' => null // будет создан в App
        ]);
    }
    
    $app = new App();
    $app->run();
    
} catch (Exception $e) {
    
    if (defined('DEBUG') && DEBUG === true) {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        header("HTTP/1.0 500 Internal Server Error");
        if (file_exists(ROOT_PATH . '/templates/' . DEFAULT_TEMPLATE . '/500.php')) {
            require ROOT_PATH . '/templates/' . DEFAULT_TEMPLATE . '/500.php';
        } else {
            echo '<h1>500 Internal Server Error</h1>';
        }
    }
}