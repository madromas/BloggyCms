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
    </script>
<?php admin_bottom_js(ob_get_clean()); ?>