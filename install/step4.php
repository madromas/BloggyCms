<?php

if (!isset($_SESSION['site_config']) || !isset($_SESSION['db_config'])) {
    $_SESSION['install_step'] = 1;
    header('Location: index.php');
    exit;
}
$siteConfig = $_SESSION['site_config'];
$dbConfig = $_SESSION['db_config'];
?>

<h2><i class="fas fa-flag-checkered" style="color: var(--accent); margin-right: 8px;"></i> Установка завершена!</h2>

<div class="alert alert-success" style="margin-bottom: 28px;">
    <i class="fas fa-check-circle"></i>
    <div>
        <strong>Поздравляем!</strong> BloggyCMS успешно установлен и готов к работе
    </div>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <h4><i class="fas fa-globe"></i> Ваш блог</h4>
        <div class="summary-item">
            <span class="label">Название</span>
            <span class="value"><?php echo htmlspecialchars($siteConfig['site_name']); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">URL</span>
            <span class="value">
                <a href="<?php echo htmlspecialchars($siteConfig['site_url']); ?>" target="_blank">
                    <?php echo htmlspecialchars($siteConfig['site_url']); ?> 
                    <i class="fas fa-external-link-alt" style="font-size: 0.7em; margin-left: 4px;"></i>
                </a>
            </span>
        </div>
    </div>
    
    <div class="summary-card">
        <h4><i class="fas fa-user-shield"></i> Администратор</h4>
        <div class="summary-item">
            <span class="label">Логин</span>
            <span class="value"><?php echo htmlspecialchars($siteConfig['admin_username']); ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Email</span>
            <span class="value"><?php echo htmlspecialchars($siteConfig['admin_email']); ?></span>
        </div>
    </div>
</div>

<div class="alert alert-warning" style="margin: 28px 0;">
    <i class="fas fa-exclamation-triangle"></i>
    <div>
        <strong>Важно для безопасности!</strong><br>
        Удалите папку <strong>/install</strong> с сервера после завершения настройки
    </div>
</div>

<div class="mt-4 flex-between">
    <a href="?restart=1" class="btn btn-outline">
        <i class="fas fa-redo"></i> Начать заново
    </a>
    <div class="flex">
        <a href="<?php echo htmlspecialchars($siteConfig['site_url']); ?>" class="btn btn-secondary" target="_blank">
            <i class="fas fa-home"></i> На сайт
        </a>
        <a href="<?php echo htmlspecialchars($siteConfig['site_url']); ?>/admin" class="btn btn-primary" target="_blank">
            <i class="fas fa-sign-in-alt"></i> В админку
        </a>
    </div>
</div>