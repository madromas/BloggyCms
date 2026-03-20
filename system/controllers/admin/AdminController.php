<?php

class AdminController extends Controller {
    private $userModel;

    protected $controllerInfo = [
        'name' => 'Панель управления',
        'author' => 'BloggyCMS', 
        'version' => '1.0.0',
        'has_settings' => true,
        'description' => 'Управление админ-панелью, блоками статистики и многим другим'
    ];
    
    /**
     * Конструктор контроллера администратора
     * Инициализирует модель пользователя и проверяет аутентификацию
     * для всех действий, кроме логина
     *
     * @param Database $db Объект подключения к базе данных
     */
    public function __construct($db) {
        parent::__construct($db);
        $this->userModel = new UserModel($db);
        
        $currentAction = $this->getCurrentAction();
        
        if ($currentAction !== 'login') {
            if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
                Notification::error('Доступ запрещен');
                $this->redirect(BASE_URL);
                exit;
            }
        }
    }

    /**
     * Определяет текущее действие из URI
     * Парсит URL для получения названия вызываемого метода
     *
     * @return string Название текущего действия или пустая строка
     */
    private function getCurrentAction() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($uri, '/'));
        
        if (count($pathParts) >= 2 && $pathParts[0] === 'admin') {
            return $pathParts[1];
        }
        
        return '';
    }
    
    /**
     * Главная страница панели управления
     * Отображает дашборд с ключевой статистикой и виджетами
     *
     * @throws Exception При ошибках
     */
    public function indexAction() {
        $this->pageTitle = 'Bloggy';
        
        try {
            $stats = [
                'posts' => $this->db->fetch("SELECT COUNT(*) as count FROM posts")['count'],
                'categories' => $this->db->fetch("SELECT COUNT(*) as count FROM categories")['count'],
                'tags' => $this->db->fetch("SELECT COUNT(*) as count FROM tags")['count'],
                'pages' => $this->db->fetch("SELECT COUNT(*) as count FROM pages")['count'],
                'comments' => $this->db->fetch("SELECT COUNT(*) as count FROM comments")['count'],
                'users' => $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'],
                'content_blocks' => $this->db->fetch("SELECT COUNT(*) as count FROM html_block_types")['count']
            ];

            $count_posts = SettingsHelper::get('controller_admin', 'count_posts', 4);

            $recentPosts = $this->db->fetchAll("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT $count_posts");
            $popularPosts = $this->db->fetchAll("SELECT * FROM posts WHERE status = 'published' AND views > 0 ORDER BY views DESC LIMIT $count_posts");
            $commentedPosts = $this->db->fetchAll("
                SELECT 
                    p.*,
                    COUNT(c.id) as comments_count
                FROM posts p
                LEFT JOIN comments c ON p.id = c.post_id AND c.status = 'approved'
                WHERE p.status = 'published'
                GROUP BY p.id
                HAVING comments_count > 0
                ORDER BY comments_count DESC
                LIMIT $count_posts
            ");
            $draftPosts = $this->db->fetchAll("SELECT * FROM posts WHERE status = 'draft' ORDER BY created_at DESC LIMIT $count_posts");
            $recentSearches = [];
            $popularSearches = [];
            
            try {
                $searchModel = new SearchModel($this->db);
                $recentSearches = $searchModel->getRecentSearchQueries(5);
                $popularSearches = $searchModel->getPopularSearchQueries(5);
            } catch (Exception $e) {
                Notification::warning('Не удалось загрузить данные о поисковых запросах');
            }
            
            $this->render('admin/dashboard', [
                'stats' => $stats,
                'recentPosts' => $recentPosts,
                'popularPosts' => $popularPosts,
                'commentedPosts' => $commentedPosts,
                'draftPosts' => $draftPosts,
                'recentSearches' => $recentSearches,
                'popularSearches' => $popularSearches,
                'hasQuickActions' => $this->hasQuickActions()
            ]);
            
        } catch (Exception $e) {
            Notification::error('Ошибка при загрузке данных панели управления');
            $this->redirect(ADMIN_URL);
        }
    }
    
    /**
     * Страница аутентификации администратора
     */
    public function loginAction() {
        if (isset($_SESSION['user_id'])) {
            Notification::info('Вы уже авторизованы');
            $this->redirect(ADMIN_URL);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
        
                $user = $this->userModel->authenticate($username, $password);
        
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    Notification::success('Добро пожаловать, ' . $user['username'] . '!');
                    $this->redirect(ADMIN_URL);
                    return;
                } else {
                    Notification::error('Неверные имя пользователя или пароль');
                    $this->render('admin/login');
                }
            } catch (Exception $e) {
                Notification::error('Ошибка при попытке авторизации');
                $this->render('admin/login');
            }
        } else {
            $this->render('admin/login');
        }
    }
    
    /**
     * Завершение сессии администратора
     */
    public function logoutAction() {
        try {
            $username = $_SESSION['username'] ?? 'Пользователь';
            session_destroy();
            Notification::success($username . ', вы успешно вышли из системы');
        } catch (Exception $e) {
            Notification::error('Ошибка при выходе из системы');
        }
        $this->redirect(ADMIN_URL . '/login');
    }

    /**
     * Управление шаблонами сайта
     */
    public function templatesAction() {
        $this->pageTitle = 'Управление шаблонами';
        
        $templates = $this->getAvailableTemplates();
        $currentTemplate = SettingsHelper::get('site', 'site_template', 'default');
        
        $this->render('admin/templates/index', [
            'templates' => $templates,
            'currentTemplate' => $currentTemplate
        ]);
    }

    /**
     * API: Получение списка файлов шаблона
     */
    public function getTemplateFilesAction() {
        header('Content-Type: application/json');
        
        $template = $_GET['template'] ?? 'default';
        $files = $this->getTemplateFiles($template);
        
        echo json_encode($files);
        exit;
    }

    /**
     * API: Получение содержимого файла шаблона
     */
    public function getTemplateFileAction() {
        header('Content-Type: application/json');
        
        $template = $_GET['template'] ?? 'default';
        $filePath = $_GET['file'] ?? '';
        
        if (empty($filePath) || strpos($filePath, '..') !== false) {
            echo json_encode(['success' => false, 'error' => 'Некорректный путь к файлу']);
            exit;
        }
        
        $fullPath = TEMPLATES_PATH . '/' . $template . '/' . $filePath;
        
        $normalizedPath = $this->normalizePath($fullPath);
        $templateBasePath = $this->normalizePath(TEMPLATES_PATH . '/' . $template);
        
        if (strpos($normalizedPath, $templateBasePath) !== 0) {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        
        if (!file_exists($fullPath)) {
            echo json_encode(['success' => false, 'error' => 'Файл не найден: ' . $fullPath]);
            exit;
        }
        
        if (!is_file($fullPath)) {
            echo json_encode(['success' => false, 'error' => 'Это директория, а не файл']);
            exit;
        }
        
        $content = file_get_contents($fullPath);
        $fileInfo = $this->getFileInfo($fullPath, $filePath);
        
        echo json_encode([
            'success' => true,
            'content' => $content,
            'info' => $fileInfo
        ]);
        exit;
    }

    /**
     * API: Сохранение изменений в файле шаблона
     */
    public function saveTemplateFileAction() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $template = $input['template'] ?? 'default';
        $filePath = $input['file'] ?? '';
        $content = $input['content'] ?? '';
        
        if (empty($filePath) || strpos($filePath, '..') !== false) {
            echo json_encode(['success' => false, 'error' => 'Некорректный путь к файлу']);
            exit;
        }
        
        $fullPath = TEMPLATES_PATH . '/' . $template . '/' . $filePath;
        
        $normalizedPath = $this->normalizePath($fullPath);
        $templateBasePath = $this->normalizePath(TEMPLATES_PATH . '/' . $template);
        
        if (strpos($normalizedPath, $templateBasePath) !== 0) {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $backupCreated = false;
        if (file_exists($fullPath)) {
            $backupCreated = BackupHelper::createBackup($fullPath);
        }
        
        if (file_put_contents($fullPath, $content) !== false) {
            $response = ['success' => true];
            if ($backupCreated) {
                $response['backup_created'] = true;
                $response['message'] = 'Файл сохранен. Резервная копия создана.';
            } else {
                $response['message'] = 'Файл сохранен.';
            }
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка сохранения файла']);
        }
        exit;
    }

    /**
     * Нормализация пути к файлу
     */
    private function normalizePath($path) {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        
        return implode('/', $absolutes);
    }

    /**
     * Получение списка доступных шаблонов
     */
    private function getAvailableTemplates() {
        $templates = [];
        $templatesPath = TEMPLATES_PATH;
        
        if (is_dir($templatesPath)) {
            $items = scandir($templatesPath);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..' && is_dir($templatesPath . '/' . $item)) {
                    $templates[] = [
                        'name' => $item,
                        'path' => $templatesPath . '/' . $item
                    ];
                }
            }
        }
        
        return $templates;
    }

    /**
     * Рекурсивное получение файлов шаблона с кэшированием
     */
    private function getTemplateFiles($template) {
        
        $templatePath = TEMPLATES_PATH . '/' . $template;
        
        if (!is_dir($templatePath)) {
            return [];
        }

        $cacheKey = 'tmpl_files_' . md5($template);
        $cacheFile = CACHE_DIR . '/' . $cacheKey . '.json';
        
        if (file_exists($cacheFile) && is_writable(CACHE_DIR)) {
            $cacheData = @json_decode(file_get_contents($cacheFile), true);
            if ($cacheData && isset($cacheData['cached_at'], $cacheData['template_mtime'], $cacheData['files'])) {
                $templateMtime = filemtime($templatePath);
                if ((time() - $cacheData['cached_at']) < 600 && $cacheData['template_mtime'] === $templateMtime) {
                    return $cacheData['files'];
                }
            }
        }

        $files = [];
        $excludeDirs = ['.git', 'node_modules', 'vendor', '.cache', 'tmp', '.idea', '.vscode'];
        
        $scanDir = function($dir, $basePath) use (&$scanDir, &$files, $excludeDirs) {
            $items = @scandir($dir);
            if ($items === false) return;
            
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
                $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $fullPath);
                $relativePath = str_replace('\\', '/', $relativePath);
                
                if (is_dir($fullPath)) {
                    if (!in_array($item, $excludeDirs)) {
                        $scanDir($fullPath, $basePath);
                    }
                    continue;
                }
                
                if (is_file($fullPath) && pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
                    $parsed = $this->parseTemplateHeader($fullPath);
                    if ($parsed['is_template']) {
                        $files[] = [
                            'name' => basename($relativePath),
                            'path' => $relativePath,
                            'size' => $this->formatFileSize(filesize($fullPath)),
                            'description' => $parsed['description'],
                            'full_path' => $fullPath
                        ];
                    }
                }
            }
        };
        
        $scanDir($templatePath, $templatePath);
        usort($files, fn($a, $b) => strcmp($a['path'], $b['path']));

        if (is_writable(CACHE_DIR)) {
            $cacheData = [
                'cached_at' => time(),
                'template_mtime' => filemtime($templatePath),
                'files' => $files
            ];
            @file_put_contents($cacheFile, json_encode($cacheData), LOCK_EX);
        }

        return $files;
    }

    /**
     * Парсер заголовка шаблона
     * @param string $filePath Полный путь к файлу
     * @return array ['is_template' => bool, 'description' => string]
     */
    private function parseTemplateHeader($filePath) {

        $content = @file_get_contents($filePath, false, null, 0, 4096);
        if ($content === false || $content === '') {
            return ['is_template' => false, 'description' => ''];
        }
        
        if (preg_match('/\/\*\*\s*\*\s*Template Name:\s*(.+?)\s*\*\//s', $content, $matches)) {
            return ['is_template' => true, 'description' => trim($matches[1])];
        }
        
        if (preg_match('/\/\/\s*Template Name:\s*(.+?)$/m', $content, $matches)) {
            return ['is_template' => true, 'description' => trim($matches[1])];
        }
        
        return ['is_template' => false, 'description' => ''];
    }

    /**
     * Проверка, является ли файл редактируемым по расширению
     */
    private function isEditableFile($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return $extension === 'php';
    }

    /**
     * Получение метаинформации о файле
     */
    private function getFileInfo($fullPath, $relativePath) {
        $size = @filesize($fullPath);
        if ($size === false) $size = 0;
        
        $parsed = $this->parseTemplateHeader($fullPath);
        
        return [
            'name' => basename($relativePath),
            'path' => $relativePath,
            'size' => $this->formatFileSize($size),
            'description' => $parsed['description'],
            'full_path' => $fullPath,
            'updated_at' => filemtime($fullPath)
        ];
    }

    /**
     * Форматирование размера файла
     */
    private function formatFileSize($size) {
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1048576) {
            return round($size / 1024, 2) . ' KB';
        } else {
            return round($size / 1048576, 2) . ' MB';
        }
    }

    /**
     * Проверка активных быстрых действий
     */
    public function hasQuickActions() {
        $actions = [
            'add_post', 'add_page', 'add_category', 'add_tag',
            'add_user', 'add_content_block', 'add_field', 'add_form'
        ];
        
        foreach ($actions as $action) {
            if (SettingsHelper::get('controller_admin', $action, false)) {
                return true;
            }
        }
        return false;
    }
}