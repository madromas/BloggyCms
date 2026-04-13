<?php
if (!isset($_SESSION['db_connected']) || !$_SESSION['db_connected']) {
    $_SESSION['install_step'] = 2;
    header('Location: index.php');
    exit;
}

function importDemoSql(PDO $pdo, string $prefix): void {
    $demoFile = __DIR__ . '/demo.sql';
    if (!file_exists($demoFile)) {
        throw new Exception("Файл demo.sql не найден");
    }
    
    $sql = file_get_contents($demoFile);
    $sql = str_replace('{#}', $prefix, $sql);
    $sql = preg_replace('/^--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            try {
                $pdo->exec($query);
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                if (strpos($msg, 'Duplicate') === false && strpos($msg, 'already exists') === false) {
                    throw new Exception("Demo SQL Error: " . $msg);
                }
            }
        }
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
}

function updateSiteSettings(PDO $pdo, string $prefix, array $siteConfig): void {
    $generalSettings = [
        'site_name' => $siteConfig['site_name'],
        'date_format' => 'd.m.Y',
        'time_format' => 'H:i',
        'site_author' => $siteConfig['admin_username'],
        'contact_email' => $siteConfig['admin_email'],
        'site_tagline' => 'Мой блог на BloggyCMS',
        'site_description' => 'Современный блог на BloggyCMS',
        'meta_keywords' => 'блог, cms, php, программирование',
        'enable_sitemap' => '1',
        'enable_robots_txt' => '1',
        'maintenance_message' => 'Сайт временно недоступен. Ведутся технические работы.'
    ];
    $generalJson = json_encode($generalSettings, JSON_UNESCAPED_UNICODE);
    
    $stmt = $pdo->prepare("SELECT id FROM `{$prefix}settings` WHERE group_key = 'general'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $sql = "UPDATE `{$prefix}settings` SET settings = :settings WHERE group_key = 'general'";
    } else {
        $sql = "INSERT INTO `{$prefix}settings` (group_key, settings) VALUES ('general', :settings)";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':settings' => $generalJson]);
    $siteSettings = [
        'base_url' => rtrim($siteConfig['site_url'], '/'),
        'site_template' => 'default',
        'template_backups_enabled' => '0',
        'template_backups_count' => '5',
        'template_backups_cleanup' => 'auto'
    ];
    $siteJson = json_encode($siteSettings, JSON_UNESCAPED_UNICODE);
    
    $stmt = $pdo->prepare("SELECT id FROM `{$prefix}settings` WHERE group_key = 'site'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $sql = "UPDATE `{$prefix}settings` SET settings = :settings WHERE group_key = 'site'";
    } else {
        $sql = "INSERT INTO `{$prefix}settings` (group_key, settings) VALUES ('site', :settings)";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':settings' => $siteJson]);
    
    try {
        $stmt = $pdo->prepare("SELECT id, settings FROM `{$prefix}html_blocks` WHERE slug = 'header' LIMIT 1");
        $stmt->execute();
        $header = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($header && !empty($header['settings'])) {
            $settings = json_decode($header['settings'], true);
            if (is_array($settings)) {
                $settings['site_name'] = $siteConfig['site_name'];
                $newSettings = json_encode($settings, JSON_UNESCAPED_UNICODE);
                $stmt = $pdo->prepare("UPDATE `{$prefix}html_blocks` SET settings = :settings WHERE id = :id");
                $stmt->execute([':settings' => $newSettings, ':id' => $header['id']]);
            }
        }
        
        $stmt = $pdo->prepare("SELECT id, settings FROM `{$prefix}html_blocks` WHERE slug = 'footer' LIMIT 1");
        $stmt->execute();
        $footer = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($footer && !empty($footer['settings'])) {
            $settings = json_decode($footer['settings'], true);
            if (is_array($settings)) {
                $settings['site_name'] = $siteConfig['site_name'];
                $newSettings = json_encode($settings, JSON_UNESCAPED_UNICODE);
                $stmt = $pdo->prepare("UPDATE `{$prefix}html_blocks` SET settings = :settings WHERE id = :id");
                $stmt->execute([':settings' => $newSettings, ':id' => $footer['id']]);
            }
        }
    } catch (PDOException $e) {
        error_log('HTML blocks update failed: ' . $e->getMessage());
    }
}

$errors = [];
$siteConfig = ['site_name' => 'Мой блог на BloggyCMS', 'site_url' => '', 'admin_email' => '', 'admin_username' => 'admin', 'admin_password' => '', 'admin_password_confirm' => ''];
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$siteConfig['site_url'] = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/');
if (isset($_SESSION['site_config'])) $siteConfig = array_merge($siteConfig, $_SESSION['site_config']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteConfig = array_merge($siteConfig, [
        'site_name' => trim($_POST['site_name']),
        'site_url' => rtrim($_POST['site_url'], '/'),
        'admin_email' => trim($_POST['admin_email']),
        'admin_username' => trim($_POST['admin_username']),
        'admin_password' => $_POST['admin_password'],
        'admin_password_confirm' => $_POST['admin_password_confirm']
    ]);
    $_SESSION['site_config'] = $siteConfig;

    if (empty($siteConfig['site_name'])) $errors[] = 'Введите название сайта';
    if (empty($siteConfig['admin_email']) || !filter_var($siteConfig['admin_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите корректный email';
    if (empty($siteConfig['admin_username']) || strlen($siteConfig['admin_username']) < 3) $errors[] = 'Имя пользователя минимум 3 символа';
    if (empty($siteConfig['admin_password'])) $errors[] = 'Введите пароль';
    elseif (strlen($siteConfig['admin_password']) < 6) $errors[] = 'Пароль минимум 6 символов';
    elseif ($siteConfig['admin_password'] !== $siteConfig['admin_password_confirm']) $errors[] = 'Пароли не совпадают';

    if (empty($errors)) {
        try {
            $db = $_SESSION['db_config'];
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
            $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 30]);
            
            $sql = file_get_contents('install.sql');
            $sql = preg_replace('/^--.*$/m', '', $sql);
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
            $prefix = $db['prefix'];
            $sql = str_replace('{#}', $prefix, $sql);
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $critical = [];
            foreach ($queries as $query) {
                if (!empty($query)) {
                    try { $pdo->exec($query); } catch (PDOException $e) {
                        $msg = $e->getMessage();
                        if (strpos($msg,'already exists')===false && strpos($msg,'Duplicate')===false) {
                            $critical[] = "SQL Error: " . $msg;
                        }
                    }
                }
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            if (!empty($critical)) throw new Exception(implode("<br>", $critical));

            if (!empty($db['install_demo'])) {
                importDemoSql($pdo, $db['prefix']);
            }
            
            updateSiteSettings($pdo, $db['prefix'], $siteConfig);

            $hashedPassword = password_hash($siteConfig['admin_password'], PASSWORD_DEFAULT);
            $usersTable = $prefix . 'users';
            $pdo->query("SELECT 1 FROM `{$usersTable}` LIMIT 1");
            $stmt = $pdo->prepare("SELECT id FROM `{$usersTable}` WHERE username = ? OR email = ?");
            $stmt->execute([$siteConfig['admin_username'], $siteConfig['admin_email']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                $stmt = $pdo->prepare("UPDATE `{$usersTable}` SET password = ?, email = ?, is_admin = 1, role = 'admin' WHERE id = ?");
                $stmt->execute([$hashedPassword, $siteConfig['admin_email'], $existing['id']]);
                $userId = $existing['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO `{$usersTable}` (username, password, email, is_admin, role, created_at, status) VALUES (?, ?, ?, 1, 'admin', NOW(), 'active')");
                $stmt->execute([$siteConfig['admin_username'], $hashedPassword, $siteConfig['admin_email']]);
                $userId = $pdo->lastInsertId();
            }

            $configDir = '../system/config';
            if (!file_exists($configDir)) mkdir($configDir, 0755, true);
            if (!is_writable($configDir)) throw new Exception("Директория не доступна для записи");

            $dbContent = "<?php
            define('DB_HOST', '".addslashes($db['host'])."');
            define('DB_NAME', '".addslashes($db['name'])."');
            define('DB_USER', '".addslashes($db['user'])."');
            define('DB_PASS', '".addslashes($db['pass'])."');
            define('DB_PREFIX', '".addslashes($db['prefix'])."');
            define('DB_CHARSET', 'utf8mb4');
            define('DB_COLLATE', 'utf8mb4_unicode_ci');";
            file_put_contents($configDir.'/database.php', $dbContent);

            $configContent = "<?php
            define('BASE_PATH', dirname(dirname(__DIR__)));
            define('SYSTEM_PATH', BASE_PATH.'/system');
            define('TEMPLATES_PATH', BASE_PATH.'/templates');
            define('UPLOADS_PATH', BASE_PATH.'/uploads');
            define('BASE_URL', '".addslashes(rtrim($siteConfig['site_url'],'/'))."');
            define('ADMIN_URL', BASE_URL.'/admin');
            define('DEFAULT_TEMPLATE', 'default');
            define('USER_ONLINE_INTERVAL', 300);
            define('CACHE_DIR', BASE_PATH.'/cache');
            define('ADDONS_TEMP_DIR', UPLOADS_PATH . '/temp_addon/');
            if(!is_dir(CACHE_DIR)) @mkdir(CACHE_DIR,0755,true);";
            file_put_contents($configDir.'/config.php', $configContent);
            file_put_contents(dirname(__DIR__).'/system/config/install.lock', date('Y-m-d H:i:s')."\n");

            $_SESSION['install_step'] = 4;
            $_SESSION['install_complete'] = true;
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = "Ошибка установки: " . $e->getMessage();
        }
    }
}
?>
<h2><i class="fas fa-user-shield" style="color: var(--accent); margin-right: 8px;"></i> Администратор</h2>
<p class="step-subtitle">Создайте учетную запись администратора</p>

<?php if (!empty($errors)) { ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><div><strong>Ошибка</strong><ul style="margin-top:8px;margin-left:20px"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div></div>
<?php } ?>
<form method="post" class="needs-validation" novalidate>
    <h3><i class="fas fa-globe"></i> Настройки блога</h3>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Название блога <span class="required">*</span></label>
            <input type="text" name="site_name" class="form-input" value="<?php echo htmlspecialchars($siteConfig['site_name']); ?>" required placeholder="Мой блог">
            <div class="form-hint"><i class="fas fa-info-circle"></i> Как будет называться ваш блог</div>
        </div>
        <div class="form-group">
            <label class="form-label">URL блога <span class="required">*</span></label>
            <input type="url" name="site_url" id="site_url" class="form-input" value="<?php echo htmlspecialchars($siteConfig['site_url']); ?>" required>
            <div class="form-hint"><i class="fas fa-info-circle"></i> Полный адрес без слеша в конце</div>
        </div>
    </div>
    <h3><i class="fas fa-user-lock"></i> Данные администратора</h3>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Имя пользователя <span class="required">*</span></label>
            <input type="text" name="admin_username" class="form-input" value="<?php echo htmlspecialchars($siteConfig['admin_username']); ?>" required minlength="3">
            <div class="form-hint"><i class="fas fa-info-circle"></i> Минимум 3 символа</div>
        </div>
        <div class="form-group">
            <label class="form-label">Email <span class="required">*</span></label>
            <input type="email" name="admin_email" class="form-input" value="<?php echo htmlspecialchars($siteConfig['admin_email']); ?>" required>
            <div class="form-hint"><i class="fas fa-info-circle"></i> Для восстановления пароля</div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Пароль <span class="required">*</span></label>
            <div class="password-wrapper">
                <input type="password" name="admin_password" id="admin_password"
                       class="form-input" required minlength="6">
                <button type="button" class="password-toggle" title="Показать/скрыть пароль">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="password-strength">
                <div class="strength-meter"><div class="strength-bar"></div></div>
                <div class="strength-text"></div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Подтверждение <span class="required">*</span></label>
            <div class="password-wrapper">
                <input type="password" name="admin_password_confirm" id="admin_password_confirm"
                       class="form-input" required>
                <button type="button" class="password-toggle" title="Показать/скрыть пароль">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="form-hint">Введите пароль еще раз</div>
        </div>
    </div>
    <div class="alert alert-info" style="margin-top:24px"><i class="fas fa-shield-alt"></i><div><strong>Важно:</strong> Сохраните данные администратора в надёжном месте</div></div>
    <div class="mt-4 flex-between">
        <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Назад</a>
        <div class="flex">
            <button type="button" onclick="generatePassword()" class="btn btn-secondary"><i class="fas fa-random"></i> Сгенерировать</button>
            <button type="submit" class="btn btn-primary" id="install-btn">Установить <i class="fas fa-check"></i></button>
        </div>
    </div>
</form>

<script>
    document.querySelectorAll('.password-toggle').forEach(btn => btn.addEventListener('click', function() {
        const input = this.previousElementSibling; const icon = this.querySelector('i');
        if (input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { input.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }));
    const pwd = document.getElementById('admin_password');
    if (pwd) {
        const bar = pwd.closest('.form-group').querySelector('.strength-bar');
        const text = pwd.closest('.form-group').querySelector('.strength-text');
        pwd.addEventListener('input', function() {
            const p = this.value; let s = 0;
            if (p.length >= 8) s++; if (p.length >= 12) s++;
            if (/[A-Z]/.test(p)) s++; if (/[0-9]/.test(p)) s++; if (/[^A-Za-z0-9]/.test(p)) s++;
            bar.className = 'strength-bar';
            if (s <= 1) { bar.classList.add('weak'); text.textContent = 'Слабый пароль'; }
            else if (s === 2) { bar.classList.add('medium'); text.textContent = 'Средний пароль'; }
            else { bar.classList.add('strong'); text.textContent = 'Надёжный пароль'; }
        });
    }
    document.querySelector('form').addEventListener('submit', function(e) {
        const p1 = document.getElementById('admin_password').value;
        const p2 = document.getElementById('admin_password_confirm').value;
        if (p1 !== p2) { e.preventDefault(); alert('❌ Пароли не совпадают'); return; }
        if (p1.length < 6) { e.preventDefault(); alert('❌ Пароль минимум 6 символов'); return; }
        const btn = document.getElementById('install-btn');
        btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Установка...';
    });
    window.generatePassword = function() {
        const c = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        let p = ''; for(let i=0;i<12;i++) p+=c.charAt(Math.floor(Math.random()*c.length));
        document.getElementById('admin_password').value = p;
        document.getElementById('admin_password_confirm').value = p;
        if (pwd) pwd.dispatchEvent(new Event('input'));
        alert('🎉 Пароль сгенерирован!');
    };
</script>