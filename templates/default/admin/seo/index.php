<?php

$robots_settings = $robots_settings ?? [
    'enabled' => true,
    'disallow_paths' => ['/admin/', '/system/'],
    'allow_paths' => [],
    'crawl_delay' => 0,
    'sitemap_url' => ''
];

$sitemap_settings = $sitemap_settings ?? [
    'enabled' => true,
    'include_posts' => true,
    'include_pages' => true,
    'include_categories' => true,
    'include_tags' => true,
    'max_posts' => 1000,
    'cache_enabled' => true,
    'cache_lifetime' => 3600
];

$rss_settings = $rss_settings ?? [
    'enabled' => true,
    'posts_limit' => 20,
    'include_full_content' => false,
    'copyright' => '',
    'language' => 'ru-ru'
];

$indexnow_settings = $indexnow_settings ?? [];
if (empty($indexnow_settings)) {
    $settingsModel = new SettingsModel($this->db);
    $indexnow_settings = $settingsModel->get('seo_indexnow');
}
$indexnow_settings = array_merge([
    'enabled' => false,
    'ya_key' => '',
    'bing_key' => '',
    'seznam_key' => '',
    'auto_submit' => true,
    'submit_delay' => 0,
    'notify_error' => true
], $indexnow_settings);

?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <?php echo bloggy_icon('bs', 'graph-up', '20', 'var(--bs-primary)', 'me-2') ?>
                SEO Инструменты
            </h1>
            <p class="text-muted mb-0">
                Управление файлами robots.txt, sitemap.xml и RSS-лентами
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL ?>/sitemap.xml" target="_blank" class="btn btn-outline-secondary btn-sm">
                <?php echo bloggy_icon('bs', 'file-earmark-code', '14', 'currentColor', 'me-1') ?>
                sitemap.xml
            </a>
            <a href="<?php echo BASE_URL ?>/robots.txt" target="_blank" class="btn btn-outline-secondary btn-sm">
                <?php echo bloggy_icon('bs', 'file-text', '14', 'currentColor', 'me-1') ?>
                robots.txt
            </a>
            <a href="<?php echo BASE_URL ?>/rss.xml" target="_blank" class="btn btn-outline-secondary btn-sm">
                <?php echo bloggy_icon('bs', 'rss', '14', 'currentColor', 'me-1') ?>
                RSS
            </a>
        </div>
    </div>

    <form method="POST" action="<?php echo ADMIN_URL ?>/seo/settings" enctype="multipart/form-data" id="seo-settings-form">
        <?php echo \CsrfToken::field('seo_settings') ?>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0">
                <ul class="nav nav-tabs nav-tabs-custom" id="seoSettingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="robots-tab" data-bs-toggle="tab" data-bs-target="#robots" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'file-text', '14', 'currentColor', 'me-1') ?>
                            Robots.txt
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sitemap-tab" data-bs-toggle="tab" data-bs-target="#sitemap" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'map', '14', 'currentColor', 'me-1') ?>
                            Sitemap.xml
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rss-tab" data-bs-toggle="tab" data-bs-target="#rss" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'rss', '14', 'currentColor', 'me-1') ?>
                            RSS Ленты
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="indexnow-tab" data-bs-toggle="tab" data-bs-target="#indexnow" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'rocket', '14', 'currentColor', 'me-1') ?>
                            IndexNow
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="seoSettingsTabContent">
                    <div class="tab-pane fade show active" id="robots" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="robots_enabled" id="robots_enabled" value="1" <?php echo $robots_settings['enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="robots_enabled">
                                        Включить автоматическую генерацию robots.txt
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Crawl-delay (секунд)</label>
                                <input type="number" class="form-control" name="robots_crawl_delay" value="<?php echo $robots_settings['crawl_delay'] ?>">
                                <div class="form-text">Задержка между запросами робота (0 - не указывать)</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Запрещенные пути (Disallow)</label>
                                <textarea class="form-control" name="robots_disallow" rows="5" placeholder="/admin/&#10;/system/"><?php echo html(implode("\n", $robots_settings['disallow_paths'])) ?></textarea>
                                <div class="form-text">Каждый путь с новой строки</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Разрешенные пути (Allow)</label>
                                <textarea class="form-control" name="robots_allow" rows="5" placeholder="/assets/&#10;/images/"><?php echo html(implode("\n", $robots_settings['allow_paths'])) ?></textarea>
                                <div class="form-text">Каждый путь с новой строки</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL Sitemap (опционально)</label>
                            <input type="url" class="form-control" name="robots_sitemap_url" value="<?php echo html($robots_settings['sitemap_url']) ?>" placeholder="https://example.com/sitemap.xml">
                            <div class="form-text">Если оставить пустым, будет использован стандартный <?php echo BASE_URL ?>/sitemap.xml</div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="sitemap" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sitemap_enabled" id="sitemap_enabled" value="1" <?php echo $sitemap_settings['enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="sitemap_enabled">
                                        Включить автоматическую генерацию sitemap.xml
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sitemap_cache_enabled" id="sitemap_cache_enabled" value="1" <?php echo $sitemap_settings['cache_enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="sitemap_cache_enabled">
                                        Включить кэширование
                                    </label>
                                </div>
                                <?php if ($sitemap_settings['cache_enabled']) { ?>
                                    <div class="mt-2">
                                        <label class="form-label">Время жизни кэша (секунд)</label>
                                        <input type="number" class="form-control" name="sitemap_cache_lifetime" value="<?php echo $sitemap_settings['cache_lifetime'] ?>">
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Включать в карту сайта</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sitemap_include_posts" id="sitemap_include_posts" value="1" <?php echo $sitemap_settings['include_posts'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="sitemap_include_posts">Посты</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sitemap_include_pages" id="sitemap_include_pages" value="1" <?php echo $sitemap_settings['include_pages'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="sitemap_include_pages">Страницы</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sitemap_include_categories" id="sitemap_include_categories" value="1" <?php echo $sitemap_settings['include_categories'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="sitemap_include_categories">Категории</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="sitemap_include_tags" id="sitemap_include_tags" value="1" <?php echo $sitemap_settings['include_tags'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="sitemap_include_tags">Теги</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Максимальное количество постов в карте</label>
                            <input type="number" class="form-control" name="sitemap_max_posts" value="<?php echo $sitemap_settings['max_posts'] ?>" min="1" max="50000">
                            <div class="form-text">Ограничение количества постов в sitemap.xml для больших сайтов</div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="rss" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="rss_enabled" id="rss_enabled" value="1" <?php echo $rss_settings['enabled'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="rss_enabled">
                                        Включить RSS ленту
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="rss_full_content" id="rss_full_content" value="1" <?php echo $rss_settings['include_full_content'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="rss_full_content">
                                        Включать полное содержимое постов
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Количество постов в ленте</label>
                                <input type="number" class="form-control" name="rss_limit" value="<?php echo $rss_settings['posts_limit'] ?>" min="1" max="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Язык (RFC 4646)</label>
                                <input type="text" class="form-control" name="rss_language" value="<?php echo html($rss_settings['language']) ?>" placeholder="ru-ru">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Авторские права (Copyright)</label>
                            <input type="text" class="form-control" name="rss_copyright" value="<?php echo html($rss_settings['copyright']) ?>" placeholder="© 2025 My Blog">
                        </div>
                    </div>

                    <div class="tab-pane fade" id="indexnow" role="tabpanel">
                        <form method="POST" action="<?php echo ADMIN_URL ?>/seo/settings">
                            <?php echo \CsrfToken::field('seo_settings') ?>
                            
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="card-title mb-0">
                                        <?php echo bloggy_icon('bs', 'rocket', '20', 'var(--bs-primary)', 'me-2') ?>
                                        IndexNow - Быстрая индексация
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <strong>Что такое IndexNow?</strong><br>
                                        Протокол для мгновенного уведомления поисковых систем об изменениях на сайте.
                                        Поддерживается Яндекс и Bing.
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="indexnow_enabled" id="indexnow_enabled" value="1" 
                                                    <?php echo $indexnow_settings['enabled'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="indexnow_enabled">
                                                    Включить IndexNow
                                                </label>
                                                <div class="form-text">
                                                    Автоматически уведомлять поисковые системы об изменениях на сайте
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="indexnow_auto_submit" id="indexnow_auto_submit" value="1" 
                                                    <?php echo $indexnow_settings['auto_submit'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="indexnow_auto_submit">
                                                    Автоматическая отправка
                                                </label>
                                                <div class="form-text">
                                                    Отправлять уведомления при создании/обновлении/удалении контента
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <?php echo bloggy_icon('bs', 'clock', '14', 'currentColor', 'me-1') ?>
                                                Задержка отправки (секунд)
                                            </label>
                                            <input type="number" class="form-control" 
                                                name="indexnow_submit_delay" 
                                                value="<?php echo (int)$indexnow_settings['submit_delay'] ?>" 
                                                min="0" max="300">
                                            <div class="form-text">
                                                Отложить отправку для группировки изменений (0 - сразу)
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="indexnow_notify_error" id="indexnow_notify_error" value="1" 
                                                    <?php echo $indexnow_settings['notify_error'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="indexnow_notify_error">
                                                    Уведомлять об ошибках
                                                </label>
                                                <div class="form-text">
                                                    Отправлять уведомление администраторам при сбоях отправки
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <h6 class="mb-3">
                                        <?php echo bloggy_icon('bs', 'key', '18', 'currentColor', 'me-2') ?>
                                        Ключи верификации
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <?php echo bloggy_icon('bs', 'yandex', '14', '#fc3f1d', 'me-1') ?>
                                                Ключ для Яндекс
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" 
                                                    name="indexnow_ya_key" 
                                                    value="<?php echo html($indexnow_settings['ya_key'] ?? '') ?>" 
                                                    placeholder="Будет сгенерирован автоматически" 
                                                    pattern="[a-zA-Z0-9-]+">
                                                <button type="button" class="btn btn-outline-secondary" 
                                                        onclick="generateKey(this, 'ya_key')">
                                                    <?php echo bloggy_icon('bs', 'arrow-repeat', '14', 'currentColor') ?>
                                                </button>
                                            </div>
                                            <div class="form-text">
                                                Только латинские буквы, цифры и дефис
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <?php echo bloggy_icon('bs', 'microsoft', '14', '#00a4ef', 'me-1') ?>
                                                Ключ для Bing
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" 
                                                    name="indexnow_bing_key" 
                                                    value="<?php echo html($indexnow_settings['bing_key'] ?? '') ?>" 
                                                    placeholder="Будет сгенерирован автоматически" 
                                                    pattern="[a-zA-Z0-9-]+">
                                                <button type="button" class="btn btn-outline-secondary" 
                                                        onclick="generateKey(this, 'bing_key')">
                                                    <?php echo bloggy_icon('bs', 'arrow-repeat', '14', 'currentColor') ?>
                                                </button>
                                            </div>
                                            <div class="form-text">
                                                Только латинские буквы, цифры и дефис
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info mt-3" id="indexnow_keys_info"  style="<?php echo !$indexnow_settings['enabled'] ? 'display: none;' : '' ?>">
                                        <div class="d-flex align-items-start">
                                            <?php echo bloggy_icon('bs', 'info-circle', '18', 'currentColor', 'me-2 mt-1') ?>
                                            <div>
                                                <strong>Файлы с ключами:</strong><br>
                                                <?php
                                                $hasKeys = false;
                                                if (!empty($indexnow_settings['ya_key'])) {
                                                    $hasKeys = true;
                                                    $keyExists = $indexnow_settings['ya_key_exists'] ?? false;
                                                    $statusIcon = $keyExists ? 
                                                        bloggy_icon('bs', 'check-circle-fill', '14', '#198754', 'me-1') : 
                                                        bloggy_icon('bs', 'x-circle-fill', '14', '#dc3545', 'me-1');
                                                    $statusText = $keyExists ? 'Файл существует' : 'Файл не найден!';
                                                    echo '<div class="d-flex align-items-center mt-1">' . $statusIcon . 
                                                        '<code><a href="' . BASE_URL . '/' . $indexnow_settings['ya_key'] . '.txt" target="_blank">' . 
                                                        BASE_URL . '/' . $indexnow_settings['ya_key'] . '.txt</a></code> (Яндекс) ' .
                                                        '<span class="badge ' . ($keyExists ? 'bg-success' : 'bg-danger') . ' ms-2">' . $statusText . '</span></div>';
                                                }
                                                if (!empty($indexnow_settings['bing_key'])) {
                                                    $hasKeys = true;
                                                    $keyExists = $indexnow_settings['bing_key_exists'] ?? false;
                                                    $statusIcon = $keyExists ? 
                                                        bloggy_icon('bs', 'check-circle-fill', '14', '#198754', 'me-1') : 
                                                        bloggy_icon('bs', 'x-circle-fill', '14', '#dc3545', 'me-1');
                                                    $statusText = $keyExists ? 'Файл существует' : 'Файл не найден!';
                                                    echo '<div class="d-flex align-items-center mt-1">' . $statusIcon . 
                                                        '<code><a href="' . BASE_URL . '/' . $indexnow_settings['bing_key'] . '.txt" target="_blank">' . 
                                                        BASE_URL . '/' . $indexnow_settings['bing_key'] . '.txt</a></code> (Bing) ' .
                                                        '<span class="badge ' . ($keyExists ? 'bg-success' : 'bg-danger') . ' ms-2">' . $statusText . '</span></div>';
                                                }
                                                if (!$hasKeys) {
                                                    echo '<span class="text-muted">Ключи не заданы. Сохраните настройки для генерации ключей.</span>';
                                                }
                                                ?>
                                                <div class="mt-2 small text-muted">
                                                    Убедитесь, что файлы доступны. При ошибке 404 проверьте настройки веб-сервера (.htaccess) и права на запись в корень сайта.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-light border mt-3" id="indexnow_test_block" style="<?php echo !$indexnow_settings['enabled'] ? 'display: none;' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php echo bloggy_icon('bs', 'flask', '18', 'currentColor', 'me-2') ?>
                                                <strong>Тестирование IndexNow</strong>
                                                <div class="small text-muted mt-1">
                                                    Отправьте тестовый запрос для проверки работоспособности
                                                </div>
                                                <?php if (!empty($indexnow_settings['is_localhost'])) { ?>
                                                <div class="alert alert-warning mt-2 mb-0 py-2 small">
                                                    <?php echo bloggy_icon('bs', 'exclamation-triangle', '14', '#856404', 'me-1') ?>
                                                    <strong>Внимание!</strong> Вы работаете на локальном домене. 
                                                    IndexNow требует публичный доступ к сайту.
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <a href="<?php echo ADMIN_URL ?>/seo/test-indexnow" 
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="return confirm('Отправить тестовый запрос в поисковые системы?')">
                                                <?php echo bloggy_icon('bs', 'send', '14', 'currentColor', 'me-1') ?>
                                                Отправить тест
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            
            <div class="card-footer bg-white border-0">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?php echo bloggy_icon('bs', 'save', '14', 'currentColor', 'me-1') ?>
                        Сохранить настройки
                    </button>
                    <a href="<?php echo ADMIN_URL ?>/seo/clear-cache" 
                       class="btn btn-outline-danger"
                       onclick="return confirm('Очистить кэш SEO-файлов?')">
                        <?php echo bloggy_icon('bs', 'trash', '14', 'currentColor', 'me-1') ?>
                        Очистить кэш
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php ob_start(); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const triggerTabList = document.querySelectorAll('#seoSettingsTab button');
            triggerTabList.forEach(triggerEl => {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                triggerEl.addEventListener('click', event => {
                    event.preventDefault();
                    tabTrigger.show();
                });
            });
            
            const cacheCheckbox = document.getElementById('sitemap_cache_enabled');
            if (cacheCheckbox) {
                const cacheSettings = cacheCheckbox.closest('.col-md-6').querySelector('.mt-2');
                if (cacheSettings) {
                    const toggleCacheSettings = () => {
                        cacheSettings.style.display = cacheCheckbox.checked ? 'block' : 'none';
                    };
                    cacheCheckbox.addEventListener('change', toggleCacheSettings);
                    toggleCacheSettings();
                }
            }
        });

        function generateKey(btn, fieldName) {
            const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
            let key = '';
            for (let i = 0; i < 32; i++) {
                key += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            
            const input = btn.closest('.input-group').querySelector('input');
            input.value = key;

            const toast = document.createElement('div');
            toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
            toast.innerHTML = 'Ключ сгенерирован. Не забудьте сохранить настройки.';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const enabledCheckbox = document.getElementById('indexnow_enabled');
            const keysInfo = document.getElementById('indexnow_keys_info');
            const testBlock = document.getElementById('indexnow_test_block');
            
            if (enabledCheckbox) {
                const toggleBlocks = () => {
                    const isEnabled = enabledCheckbox.checked;
                    if (keysInfo) keysInfo.style.display = isEnabled ? 'block' : 'none';
                    if (testBlock) testBlock.style.display = isEnabled ? 'block' : 'none';
                };
                
                enabledCheckbox.addEventListener('change', toggleBlocks);
                toggleBlocks();
            }
            
            const yaKeyInput = document.querySelector('input[name="indexnow_ya_key"]');
            const bingKeyInput = document.querySelector('input[name="indexnow_bing_key"]');
            
            if (yaKeyInput && !yaKeyInput.value) {
                yaKeyInput.placeholder = 'Будет сгенерирован автоматически';
            }
            if (bingKeyInput && !bingKeyInput.value) {
                bingKeyInput.placeholder = 'Будет сгенерирован автоматически';
            }
        });
    </script>
<?php admin_bottom_js(ob_get_clean()); ?>