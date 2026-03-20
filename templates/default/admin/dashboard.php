<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">
                <?php echo bloggy_icon('bs', 'speedometer2', '28', '#0088cc', 'me-2'); ?>
                Панель управления
            </h1>
            <p class="dashboard-subtitle">Добро пожаловать в панель управления блога!</p>
        </div>
    </div>

    <div class="stats-grid">
        <?php if(SettingsHelper::get('controller_admin', 'all_posts') == true) { ?>
            <div class="stat-card stat-card-posts">
                <div class="stat-icon">
                    <?php echo bloggy_icon('bs', 'pencil', '28', '#0088cc'); ?>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Посты</span>
                    <span class="stat-number"><?= $stats['posts'] ?? 0 ?></span>
                </div>
                <?php if(SettingsHelper::get('controller_admin', 'show_button') == true) { ?>
                    <a href="<?= ADMIN_URL ?>/posts" class="stat-link">Управление →</a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'categories') == true) { ?>
            <div class="stat-card stat-card-categories">
                <div class="stat-icon">
                    <?php echo bloggy_icon('bs', 'folder2-open', '28', '#10b981'); ?>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Категории</span>
                    <span class="stat-number"><?= $stats['categories'] ?? 0 ?></span>
                </div>
                <?php if(SettingsHelper::get('controller_admin', 'show_button') == true) { ?>
                    <a href="<?= ADMIN_URL ?>/categories" class="stat-link">Управление →</a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'tags') == true) { ?>
            <div class="stat-card stat-card-tags">
                <div class="stat-icon">
                    <?php echo bloggy_icon('bs', 'hash', '28', '#f59e0b'); ?>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Теги</span>
                    <span class="stat-number"><?= $stats['tags'] ?? 0 ?></span>
                </div>
                <?php if(SettingsHelper::get('controller_admin', 'show_button') == true) { ?>
                    <a href="<?= ADMIN_URL ?>/tags" class="stat-link">Управление →</a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'pages') == true) { ?>
            <div class="stat-card stat-card-pages">
                <div class="stat-icon">
                    <?php echo bloggy_icon('bs', 'file-earmark-richtext', '28', '#8b5cf6'); ?>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Страницы</span>
                    <span class="stat-number"><?= $stats['pages'] ?? 0 ?></span>
                </div>
                <?php if(SettingsHelper::get('controller_admin', 'show_button') == true) { ?>
                    <a href="<?= ADMIN_URL ?>/pages" class="stat-link">Управление →</a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'comments') == true) { ?>
            <div class="stat-card stat-card-comments">
                <div class="stat-icon">
                    <?php echo bloggy_icon('bs', 'chat-left-dots', '28', '#ef4444'); ?>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Комментарии</span>
                    <span class="stat-number"><?= $stats['comments'] ?? 0 ?></span>
                </div>
                <?php if(SettingsHelper::get('controller_admin', 'show_button') == true) { ?>
                    <a href="<?= ADMIN_URL ?>/comments" class="stat-link">Управление →</a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'users') == true) { ?>
            <div class="stat-card stat-card-users">
                <div class="stat-icon">
                    <?php echo bloggy_icon('bs', 'person', '28', '#06b6d4'); ?>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Пользователи</span>
                    <span class="stat-number"><?= $stats['users'] ?? 0 ?></span>
                </div>
                <?php if(SettingsHelper::get('controller_admin', 'show_button') == true) { ?>
                    <a href="<?= ADMIN_URL ?>/users" class="stat-link">Управление →</a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'content_blocks') == true) { ?>
            <div class="stat-card stat-card-blocks">
                <div class="stat-icon">
                    <?php echo bloggy_icon('bs', 'layout-wtf', '28', '#ec489a'); ?>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Контент-блоки</span>
                    <span class="stat-number"><?= $stats['content_blocks'] ?? 0 ?></span>
                </div>
                <?php if(SettingsHelper::get('controller_admin', 'show_button') == true) { ?>
                    <a href="<?= ADMIN_URL ?>/html-blocks" class="stat-link">Управление →</a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <div class="content-grid">
        <?php if(SettingsHelper::get('controller_admin', 'last_posts') == true) { ?>
            <div class="content-card">
                <div class="card-header-modern">
                    <?php echo bloggy_icon('bs', 'clock', '20', '#0088cc', 'me-2'); ?>
                    <h3>Новые посты</h3>
                </div>
                <div class="card-body-modern">
                    <?php if(!empty($recentPosts)) { ?>
                        <div class="post-list">
                            <?php foreach($recentPosts as $post): ?>
                                <a href="<?= BASE_URL ?>/post/<?= $post['slug'] ?>" target="_blank" class="post-item">
                                    <div class="post-content">
                                        <h4><?php echo html($post['title']) ?></h4>
                                        <div class="post-meta">
                                            <?php echo bloggy_icon('bs', 'calendar', '12', '#6c757d', 'me-1'); ?>
                                            <span><?= date('d.m.Y', strtotime($post['created_at'])) ?></span>
                                        </div>
                                    </div>
                                    <?php echo bloggy_icon('bs', 'chevron-right', '16', '#cbd5e1'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state-modern">
                            <?php echo bloggy_icon('bs', 'file-text', '48', '#cbd5e1', 'mb-3'); ?>
                            <p>Постов пока нет</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'popular_posts') == true) { ?>
            <div class="content-card">
                <div class="card-header-modern">
                    <?php echo bloggy_icon('bs', 'fire', '20', '#10b981', 'me-2'); ?>
                    <h3>Популярные</h3>
                </div>
                <div class="card-body-modern">
                    <?php if(!empty($popularPosts)) { ?>
                        <div class="post-list">
                            <?php foreach($popularPosts as $post): ?>
                                <a href="<?= BASE_URL ?>/post/<?= $post['slug'] ?>" target="_blank" class="post-item">
                                    <div class="post-content">
                                        <h4><?php echo html($post['title']) ?></h4>
                                        <div class="post-meta">
                                            <?php echo bloggy_icon('bs', 'eye', '12', '#10b981', 'me-1'); ?>
                                            <span><?php echo html($post['views']) ?> просмотров</span>
                                        </div>
                                    </div>
                                    <?php echo bloggy_icon('bs', 'chevron-right', '16', '#cbd5e1'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state-modern">
                            <?php echo bloggy_icon('bs', 'bar-chart', '48', '#cbd5e1', 'mb-3'); ?>
                            <p>Популярных постов нет</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'comments_posts') == true) { ?>
            <div class="content-card">
                <div class="card-header-modern">
                    <?php echo bloggy_icon('bs', 'chat-dots', '20', '#ef4444', 'me-2'); ?>
                    <h3>Обсуждаемые</h3>
                </div>
                <div class="card-body-modern">
                    <?php if(!empty($commentedPosts)) { ?>
                        <div class="post-list">
                            <?php foreach($commentedPosts as $post): ?>
                                <a href="<?= BASE_URL ?>/post/<?= $post['slug'] ?>" target="_blank" class="post-item">
                                    <div class="post-content">
                                        <h4><?php echo html($post['title']) ?></h4>
                                        <div class="post-meta">
                                            <?php echo bloggy_icon('bs', 'chat', '12', '#ef4444', 'me-1'); ?>
                                            <span><?php echo html($post['comments_count']) ?> комментариев</span>
                                        </div>
                                    </div>
                                    <?php echo bloggy_icon('bs', 'chevron-right', '16', '#cbd5e1'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state-modern">
                            <?php echo bloggy_icon('bs', 'chat', '48', '#cbd5e1', 'mb-3'); ?>
                            <p>Обсуждаемых постов нет</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php if(SettingsHelper::get('controller_admin', 'show_drafts') == true) { ?>
            <div class="content-card">
                <div class="card-header-modern">
                    <?php echo bloggy_icon('bs', 'file-earmark', '20', '#f59e0b', 'me-2'); ?>
                    <h3>Черновики</h3>
                </div>
                <div class="card-body-modern">
                    <?php if(!empty($draftPosts)) { ?>
                        <div class="post-list">
                            <?php foreach($draftPosts as $post): ?>
                                <div class="post-item draft-item">
                                    <div class="post-content">
                                        <h4><?php echo html($post['title']) ?></h4>
                                        <div class="post-meta">
                                            <?php echo bloggy_icon('bs', 'clock', '12', '#f59e0b', 'me-1'); ?>
                                            <span><?= date('d.m.Y', strtotime($post['created_at'])) ?></span>
                                        </div>
                                    </div>
                                    <div class="draft-actions">
                                        <a href="<?= ADMIN_URL ?>/posts/edit/<?= $post['id'] ?>" class="action-btn" title="Редактировать">
                                            <?php echo bloggy_icon('bs', 'pencil', '14', '#0088cc'); ?>
                                        </a>
                                        <a href="<?= ADMIN_URL ?>/posts/toggle-status/<?= $post['id'] ?>" 
                                           class="action-btn" 
                                           title="Опубликовать"
                                           onclick="return confirm('Опубликовать пост?')">
                                            <?php echo bloggy_icon('bs', 'check-lg', '14', '#10b981'); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state-modern">
                            <?php echo bloggy_icon('bs', 'file-earmark', '48', '#cbd5e1', 'mb-3'); ?>
                            <p>Черновиков нет</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if(SettingsHelper::get('controller_admin', 'show_search') == true || SettingsHelper::get('controller_admin', 'show_popular_search') == true) { ?>
        <div class="search-grid">
            <?php if(SettingsHelper::get('controller_admin', 'show_search') == true) { ?>
                <div class="content-card search-card">
                    <div class="card-header-modern">
                        <?php echo bloggy_icon('bs', 'search', '20', '#0088cc', 'me-2'); ?>
                        <h3>Последние поисковые запросы</h3>
                    </div>
                    <div class="card-body-modern">
                        <?php if(!empty($recentSearches)): ?>
                            <div class="search-list">
                                <?php foreach($recentSearches as $search): ?>
                                    <div class="search-item">
                                        <a href="<?= BASE_URL ?>/search?q=<?= urlencode($search['query']) ?>" target="_blank" class="search-query">
                                            <?php echo bloggy_icon('bs', 'search', '14', '#0088cc', 'me-2'); ?>
                                            <?= htmlspecialchars($search['query']) ?>
                                        </a>
                                        <div class="search-stats">
                                            <span class="search-count">
                                                <?php echo bloggy_icon('bs', 'arrow-repeat', '12', '#6c757d', 'me-1'); ?>
                                                <?= $search['count'] ?> <?= plural_form($search['count'], ['раз', 'раза', 'раз']) ?>
                                            </span>
                                            <span class="search-time">
                                                <?php echo bloggy_icon('bs', 'clock', '12', '#6c757d', 'me-1'); ?>
                                                <?= date('d.m.Y H:i', strtotime($search['last_searched_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="card-footer-modern">
                                <a href="<?= ADMIN_URL ?>/search-history" class="link-btn">Все запросы →</a>
                            </div>
                        <?php else: ?>
                            <div class="empty-state-modern">
                                <?php echo bloggy_icon('bs', 'search', '48', '#cbd5e1', 'mb-3'); ?>
                                <p>Поисковых запросов пока нет</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php } ?>

            <?php if(SettingsHelper::get('controller_admin', 'show_popular_search') == true) { ?>
                <div class="content-card search-card">
                    <div class="card-header-modern">
                        <?php echo bloggy_icon('bs', 'star', '20', '#f59e0b', 'me-2'); ?>
                        <h3>Популярные запросы</h3>
                    </div>
                    <div class="card-body-modern">
                        <?php if(!empty($popularSearches)): ?>
                            <div class="search-list">
                                <?php foreach($popularSearches as $search): ?>
                                    <div class="search-item popular">
                                        <a href="<?= BASE_URL ?>/search?q=<?= urlencode($search['query']) ?>" target="_blank" class="search-query">
                                            <?php echo bloggy_icon('bs', 'fire', '14', '#f59e0b', 'me-2'); ?>
                                            <?= htmlspecialchars($search['query']) ?>
                                        </a>
                                        <div class="search-stats">
                                            <span class="search-count">
                                                <?php echo bloggy_icon('bs', 'arrow-repeat', '12', '#6c757d', 'me-1'); ?>
                                                <?= $search['count'] ?> <?= plural_form($search['count'], ['раз', 'раза', 'раз']) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="card-footer-modern">
                                <a href="<?= ADMIN_URL ?>/search-history" class="link-btn">Все запросы →</a>
                            </div>
                        <?php else: ?>
                            <div class="empty-state-modern">
                                <?php echo bloggy_icon('bs', 'star', '48', '#cbd5e1', 'mb-3'); ?>
                                <p>Популярных запросов пока нет</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>