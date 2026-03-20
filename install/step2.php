<?php
$errors = [];
$dbConfig = ['host' => 'localhost', 'name' => 'bloggycms', 'user' => 'root', 'pass' => '', 'prefix' => 'bc_', 'port' => '3306'];
if (isset($_SESSION['db_config'])) $dbConfig = array_merge($dbConfig, $_SESSION['db_config']);
$dbConfig['install_demo'] = isset($_SESSION['db_config']['install_demo']) ? $_SESSION['db_config']['install_demo'] : 0;

if (isset($_SESSION['db_connected']) && $_SESSION['db_connected']) {
    $_SESSION['install_step'] = 3;
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbConfig = array_merge($dbConfig, [
        'host' => $_POST['db_host'] ?? 'localhost',
        'name' => $_POST['db_name'] ?? 'bloggycms',
        'user' => $_POST['db_user'] ?? 'root',
        'pass' => $_POST['db_pass'] ?? '',
        'prefix' => $_POST['db_prefix'] ?? 'bc_',
        'port' => $_POST['db_port'] ?? '3306',
        'install_demo' => isset($_POST['install_demo']) ? 1 : 0
    ]);
    $_SESSION['db_config'] = $dbConfig;

    try {
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
        
        $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbConfig['name']}'");
        if ($stmt->fetch()) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$dbConfig['name']}'");
            if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
                $errors[] = "База данных уже содержит таблицы.";
            } else {
                $_SESSION['db_connected'] = true;
                $_SESSION['install_step'] = 3;
                header('Location: index.php');
                exit;
            }
        } else {
            $pdo->exec("CREATE DATABASE `{$dbConfig['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $_SESSION['db_connected'] = true;
            $_SESSION['install_step'] = 3;
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        $errors[] = "Ошибка подключения: " . $e->getMessage();
    }
}
?>
<h2><i class="fas fa-database" style="color: var(--accent); margin-right: 8px;"></i> База данных</h2>
<p class="step-subtitle">Настройте подключение к MySQL</p>
<?php if (!empty($errors)): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><div><strong>Ошибка</strong><ul style="margin-top:8px;margin-left:20px"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div></div>
<?php endif; ?>
<form method="post" class="needs-validation" id="db-form" novalidate>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Хост <span class="required">*</span></label>
            <input type="text" name="db_host" class="form-input" value="<?php echo htmlspecialchars($dbConfig['host']); ?>" required>
            <div class="form-hint"><i class="fas fa-info-circle"></i> Обычно localhost</div>
        </div>
        <div class="form-group">
            <label class="form-label">Порт <span class="required">*</span></label>
            <input type="number" name="db_port" class="form-input" value="<?php echo htmlspecialchars($dbConfig['port']); ?>" required>
            <div class="form-hint"><i class="fas fa-info-circle"></i> Обычно 3306</div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Имя БД <span class="required">*</span></label>
            <input type="text" name="db_name" class="form-input" value="<?php echo htmlspecialchars($dbConfig['name']); ?>" required>
            <div class="form-hint"><i class="fas fa-info-circle"></i> Будет создана автоматически</div>
        </div>
        <div class="form-group">
            <label class="form-label">Префикс таблиц</label>
            <input type="text" name="db_prefix" class="form-input" value="<?php echo htmlspecialchars($dbConfig['prefix']); ?>">
            <div class="form-hint"><i class="fas fa-info-circle"></i> Например: bc_</div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Пользователь <span class="required">*</span></label>
            <input type="text" name="db_user" class="form-input" value="<?php echo htmlspecialchars($dbConfig['user']); ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Пароль</label>
            <div class="password-wrapper">
                <input type="password" name="db_pass" class="form-input"
                       value="<?php echo htmlspecialchars($dbConfig['pass']); ?>" id="db_pass">
                <button type="button" class="password-toggle" title="Показать/скрыть пароль">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <div class="form-check" style="background: var(--surface-alt); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
            <input type="checkbox" name="install_demo" id="install_demo" class="form-check-input" value="1" <?php echo !empty($dbConfig['install_demo']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="install_demo" style="font-weight: 600; color: var(--text-primary); cursor: pointer;">
                <i class="fas fa-seedling" style="color: var(--accent); margin-right: 6px;"></i>
                Установить демо данные
            </label>
            <div class="form-hint" style="margin-top: 8px; margin-left: 24px;">
                <i class="fas fa-info-circle"></i> 
                Будут добавлены: 3 категории, 3 статьи, меню, страницы, настройки и теги для быстрого старта
            </div>
        </div>
    </div>

    <div class="alert alert-info" style="margin-top:24px"><i class="fas fa-info-circle"></i><div><strong>Важно:</strong> Пользователь должен иметь права на создание БД и таблиц</div></div>
    <div class="mt-4 flex-between">
        <a href="?restart=1" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Назад</a>
        <div class="flex">
            <button type="button" id="test-connection" class="btn btn-secondary"><i class="fas fa-plug"></i> Проверить</button>
            <button type="submit" class="btn btn-primary">Подключить <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>
</form>
<script>
document.querySelectorAll('.password-toggle').forEach(btn => btn.addEventListener('click', function() {
    const input = this.previousElementSibling;
    const icon = this.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
    else { input.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
}));
document.getElementById('db-form')?.addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Проверка...';
});
</script>