<?php
$requirements = [];
$allPassed = true;

$phpVersion = phpversion();
$phpRequired = '7.4.0';
$phpPassed = version_compare($phpVersion, $phpRequired, '>=');
$requirements[] = ['name' => 'Версия PHP', 'required' => $phpRequired, 'current' => $phpVersion, 'passed' => $phpPassed];
if (!$phpPassed) $allPassed = false;

$extensions = ['pdo_mysql' => 'PDO MySQL', 'mysqli' => 'MySQLi', 'mbstring' => 'mbstring', 'json' => 'JSON', 'fileinfo' => 'Fileinfo', 'session' => 'Session', 'openssl' => 'OpenSSL'];
foreach ($extensions as $ext => $name) {
    $loaded = extension_loaded($ext);
    $requirements[] = ['name' => "Расширение {$name}", 'required' => 'Включено', 'current' => $loaded ? 'Включено' : 'Отключено', 'passed' => $loaded];
    if (!$loaded) $allPassed = false;
}

$writableDirs = ['../uploads' => 'Папка uploads', '../system/config' => 'Папка config', '../templates' => 'Папка templates'];
foreach ($writableDirs as $dir => $name) {
    if (!file_exists($dir)) @mkdir($dir, 0755, true);
    $isWritable = is_writable($dir);
    $requirements[] = ['name' => $name, 'required' => 'Доступ на запись', 'current' => $isWritable ? 'Доступно' : 'Нет доступа', 'passed' => $isWritable];
    if (!$isWritable) $allPassed = false;
}

if (isset($_POST['next']) && $allPassed) {
    $_SESSION['install_step'] = 2;
    header('Location: index.php');
    exit;
}
?>

<h2><i class="fas fa-server" style="color: var(--accent); margin-right: 8px;"></i> Проверка системы</h2>
<p class="step-subtitle">Убедимся, что ваш сервер соответствует требованиям</p>

<?php if (!$allPassed): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <div><strong>Обнаружены проблемы</strong><p style="margin-top:4px">Исправьте ошибки ниже и обновите страницу</p></div>
</div>
<?php else: ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <div><strong>Всё отлично!</strong><p style="margin-top:4px">Ваш сервер полностью готов к установке</p></div>
</div>
<?php endif; ?>

<div class="requirements-list">
    <?php foreach ($requirements as $req): ?>
    <div class="requirement-item">
        <div class="requirement-info">
            <h4><?php echo htmlspecialchars($req['name']); ?></h4>
            <div class="requirement-detail">Требуется: <?php echo htmlspecialchars($req['required']); ?></div>
        </div>
        <div class="requirement-status">
            <span class="status-badge <?php echo $req['passed'] ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($req['current']); ?></span>
            <div class="status-icon <?php echo $req['passed'] ? 'success' : 'error'; ?>"><i class="fas fa-<?php echo $req['passed'] ? 'check' : 'times'; ?>"></i></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="mt-4 flex-between">
    <?php if ($allPassed): ?>
    <form method="post"><button type="submit" name="next" class="btn btn-primary">Продолжить <i class="fas fa-arrow-right"></i></button></form>
    <?php else: ?>
    <button class="btn btn-primary" disabled><i class="fas fa-exclamation-triangle"></i> Исправьте ошибки</button>
    <?php endif; ?>
</div>