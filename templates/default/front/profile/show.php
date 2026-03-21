<?php
/**
 * Template Name: Профиль пользователя
 */
$fieldModel = new FieldModel($this->db);
?>

<div class="tg-profile">
    <div class="tg-container">
        
        <div class="tg-profile-header">
            <div class="tg-profile-avatar">
                <?php if (!empty($user['avatar']) && $user['avatar'] !== 'default.jpg') { ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo html($user['avatar']); ?>"
                         alt="<?php echo html($user['display_name'] ?? $user['username']); ?>">
                <?php } else { ?>
                    <div class="tg-avatar-placeholder">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                <?php } ?>
                <?php if ($is_online) { ?>
                    <span class="tg-online" title="В сети"></span>
                <?php } ?>
            </div>
            <div class="tg-profile-info">
                <h1 class="tg-profile-name">
                    <?php echo html($user['display_name'] ?? $user['username']); ?>
                    <?php if ($is_online) { ?>
                        <span class="tg-online" title="В сети"></span>
                    <?php } ?>
                </h1>
                <div class="tg-profile-meta">
                    <span class="tg-username">@<?php echo html($user['username']); ?></span>
                    <?php if (!$is_online && !empty($last_activity_human)) { ?>
                        <span class="tg-last-seen">• <?php echo $last_activity_human; ?></span>
                    <?php } ?>
                </div>
                <?php if (!empty($groups)) { ?>
                    <div class="tg-profile-groups">
                        <?php foreach ($groups as $group) { ?>
                            <span class="tg-group-badge"><?php echo html($group['name']); ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php if ($is_own_profile) { ?>
                <a href="<?php echo BASE_URL; ?>/profile/edit" class="tg-edit-btn">
                    <?php echo bloggy_icon('bs', 'pencil', '16', 'currentColor'); ?>
                </a>
            <?php } ?>
        </div>

        <div class="tg-profile-grid">
            <div class="tg-profile-sidebar">

                <div class="tg-card">
                    <div class="tg-card-body">
                        <h3 class="tg-card-title">
                            <?php echo bloggy_icon('bs', 'person', '18', 'currentColor', 'tg-mr-1'); ?>
                            О себе
                        </h3>
                        <?php if (!empty($user['bio'])) { ?>
                            <div class="tg-bio"><?php echo nl2br(html($user['bio'])); ?></div>
                        <?php } else { ?>
                            <div class="tg-bio tg-bio-empty">
                                <?php echo html($user['display_name'] ?? $user['username']); ?> 
                                еще не добавил информацию о себе.
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="tg-card">
                    <div class="tg-card-body">
                        <h3 class="tg-card-title mb-3">
                            <?php echo bloggy_icon('bs', 'bar-chart', '18', 'currentColor', 'tg-mr-1'); ?>
                            Статистика
                        </h3>
                        <div class="tg-stats">
                            <div class="tg-stat">
                                <span class="tg-stat-value"><?php echo $postsCount; ?></span>
                                <span class="tg-stat-label"><?php echo plural((int)$postsCount, ['пост', 'поста', 'постов']); ?></span>
                            </div>
                            <div class="tg-stat">
                                <span class="tg-stat-value"><?php echo $total = (int)($commentsCount ?? 0); ?></span>
                                <span class="tg-stat-label"><?php echo plural($total, ['комментарий', 'комментария', 'комментариев']); ?></span>
                            </div>
                            <div class="tg-stat">
                                <span class="tg-stat-value"><?php echo $total = (int)($daysSinceRegistration ?? 0); ?></span>
                                <span class="tg-stat-label"><?php echo plural($total, ['день', 'дня', 'дней']) . ' с нами'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tg-card">
                    <div class="tg-card-body">
                        <h3 class="tg-card-title">
                            <?php echo bloggy_icon('bs', 'trophy', '18', 'currentColor', 'tg-mr-1'); ?>
                            Достижения
                        </h3>
                        <div class="tg-achievements-summary">
                            <div class="tg-stat">
                                <span class="tg-stat-value"><?php echo $unlockedCount; ?></span>
                                <span class="tg-stat-label">получено</span>
                            </div>
                            <div class="tg-stat">
                                <span class="tg-stat-value"><?php echo $totalAchievementsInSystem - $unlockedCount; ?></span>
                                <span class="tg-stat-label">осталось</span>
                            </div>
                        </div>
                        <?php if (!empty($achievements)) { ?>
                            <div class="tg-achievements-preview tg-mt-3">
                                <?php foreach (array_slice($achievements, 0, 6) as $achievement) { ?>
                                    <div class="tg-achievement-mini" title="<?php echo html($achievement['name']); ?>">
                                        <?php if (!empty($achievement['image'])) { ?>
                                            <img src="<?php echo BASE_URL; ?>/uploads/achievements/<?php echo html($achievement['image']); ?>"
                                                 alt="<?php echo html($achievement['name']); ?>">
                                        <?php } else { ?>
                                            <div class="tg-achievement-icon-compact">
                                                <?php 
                                                $iconName = str_replace('bi-', '', $achievement['icon'] ?? 'trophy');
                                                echo bloggy_icon('bs', $iconName, '14', '#fff');
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if ($unlockedCount > 6) { ?>
                                    <div class="tg-achievement-more">
                                        <span>+<?php echo $unlockedCount - 6; ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="tg-achievements-link tg-mt-3">
                            <a href="<?php echo BASE_URL; ?>/users/achievements" class="btn btn-outline-primary btn-sm w-100">
                                <?php echo bloggy_icon('bs', 'trophy-fill', '14', 'currentColor', 'me-2'); ?>
                                Все достижения системы
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="tg-profile-content">
                <?php if ($displayType === 'posts') { ?>
                    <div class="tg-profile-posts">
                        <div class="tg-card">
                            <div class="tg-card-header">
                                <h3 class="tg-card-title">
                                    <?php echo bloggy_icon('bs', 'file-text', '18', 'currentColor', 'tg-mr-1'); ?>
                                    Публикации
                                </h3>
                                <?php if (!empty($posts)) { ?>
                                    <span class="tg-posts-count"><?php echo count($posts); ?></span>
                                <?php } ?>
                            </div>
                            <?php if (!empty($posts)) { ?>
                                <div class="tg-posts-list">
                                    <?php foreach ($posts as $post) { 
                                        $featuredImage = $post['featured_image'] 
                                            ? BASE_URL . '/uploads/images/' . html($post['featured_image']) 
                                            : null;
                                    ?>
                                        <div class="tg-post-item">
                                            <?php if ($featuredImage) { ?>
                                                <a href="<?php echo BASE_URL; ?>/post/<?php echo html($post['slug']); ?>" 
                                                class="tg-post-item-image">
                                                    <img src="<?php echo $featuredImage; ?>" 
                                                        alt="<?php echo html($post['title']); ?>">
                                                </a>
                                            <?php } ?>
                                            <div class="tg-post-item-content">
                                                <h4 class="tg-post-item-title">
                                                    <a href="<?php echo BASE_URL; ?>/post/<?php echo html($post['slug']); ?>">
                                                        <?php echo html($post['title']); ?>
                                                    </a>
                                                </h4>
                                                <div class="tg-post-item-meta">
                                                    <span class="tg-post-date">
                                                        <?php echo bloggy_icon('bs', 'calendar', '12', 'currentColor', 'tg-mr-1'); ?>
                                                        <?php echo date('d.m.Y', strtotime($post['created_at'])); ?>
                                                    </span>
                                                    <span class="tg-post-views">
                                                        <?php echo bloggy_icon('bs', 'eye', '12', 'currentColor', 'tg-mr-1'); ?>
                                                        <?php echo $post['views'] ?? 0; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="tg-card-footer">
                                    <a href="<?php echo BASE_URL; ?>/posts" class="btn btn-outline-primary btn-sm w-100">
                                        <?php echo bloggy_icon('bs', 'arrow-right', '14', 'currentColor', 'me-2'); ?>
                                        Все публикации
                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="tg-empty-state tg-empty-state-small tg-text-center">
                                    <div class="tg-empty-state-icon">
                                        <?php echo bloggy_icon('bs', 'file-text', '32', 'var(--tg-text-secondary)'); ?>
                                    </div>
                                    <h4 class="tg-empty-state-title">Пока нет публикаций</h4>
                                    <p class="tg-empty-state-text">Публикации появятся здесь после создания</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                <?php } else if ($displayType === 'bookmarks') { ?>
                    <div class="tg-profile-bookmarks">
                        <div class="tg-card">
                            <div class="tg-card-header">
                                <h3 class="tg-card-title">
                                    <?php echo bloggy_icon('bs', 'bookmark-star', '18', 'currentColor', 'tg-mr-1'); ?>
                                    Избранное
                                </h3>
                                <?php if (!empty($bookmarks)) { ?>
                                    <span class="tg-bookmarks-count"><?php echo count($bookmarks); ?></span>
                                <?php } ?>
                            </div>
                            
                            <?php if (!empty($bookmarks)) { ?>
                                <div class="tg-bookmarks-list">
                                    <?php foreach ($bookmarks as $post) { 
                                        $featuredImage = $post['featured_image'] 
                                            ? BASE_URL . '/uploads/images/' . html($post['featured_image']) 
                                            : null;
                                        $userLiked = isset($post['userLiked']) && $post['userLiked'];
                                    ?>
                                    <div class="bookmark-item" data-post-id="<?php echo $post['id']; ?>">
                                        
                                        <?php if ($featuredImage) { ?>
                                        <a href="<?php echo BASE_URL . '/post/' . html($post['slug']); ?>" class="bookmark-image-link">
                                            <img src="<?php echo $featuredImage; ?>" 
                                                alt="<?php echo html($post['title']); ?>"
                                                loading="lazy">
                                        </a>
                                        <?php } ?>
                                        
                                        <div class="bookmark-content">
                                            <?php if (!empty($post['category_name'])) { ?>
                                            <a href="<?php echo BASE_URL; ?>/category/<?php echo html($post['category_slug']); ?>" 
                                            class="bookmark-category">
                                                <?php echo html($post['category_name']); ?>
                                            </a>
                                            <?php } ?>
                                            
                                            <h3 class="bookmark-title">
                                                <a href="<?php echo BASE_URL . '/post/' . html($post['slug']); ?>">
                                                    <?php echo html($post['title']); ?>
                                                </a>
                                            </h3>
                                            
                                            <?php if (!empty($post['short_description'])) { ?>
                                            <p class="bookmark-excerpt">
                                                <?php echo html(mb_strimwidth($post['short_description'], 0, 120, '...')); ?>
                                            </p>
                                            <?php } ?>
                                            
                                            <div class="bookmark-meta">
                                                <span class="bookmark-date">
                                                    <?php echo bloggy_icon('bs', 'bookmark', '12', 'currentColor', 'tg-mr-1'); ?>
                                                    Сохранено <?php echo time_ago($post['bookmarked_at']); ?>
                                                </span>
                                                
                                                <?php if ($post['views'] > 0) { ?>
                                                <span class="bookmark-views">
                                                    <?php echo bloggy_icon('bs', 'eye', '12', 'currentColor', 'tg-mr-1'); ?>
                                                    <?php echo $post['views']; ?>
                                                </span>
                                                <?php } ?>
                                                
                                                <?php if ($post['likes_count'] > 0) { ?>
                                                <span class="bookmark-likes">
                                                    <?php echo bloggy_icon('bs', 'heart', '12', 'currentColor', 'tg-mr-1'); ?>
                                                    <?php echo $post['likes_count']; ?>
                                                </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        
                                        <button class="remove-bookmark" 
                                                data-post-id="<?php echo $post['id']; ?>"
                                                title="Удалить из закладок">
                                            ✕
                                        </button>
                                    </div>
                                    <?php } ?>
                                </div>
                                
                                <div class="tg-card-footer">
                                    <a href="<?php echo BASE_URL; ?>/user/bookmarks" class="btn btn-outline-primary btn-sm w-100">
                                        <?php echo bloggy_icon('bs', 'bookmark', '14', 'currentColor', 'me-2'); ?>
                                        Все закладки
                                    </a>
                                </div>
                                
                            <?php } else { ?>
                                <div class="tg-empty-state tg-empty-state-small tg-text-center">
                                    <div class="tg-empty-state-icon">
                                        <?php echo bloggy_icon('bs', 'bookmark', '32', 'var(--tg-text-secondary)'); ?>
                                    </div>
                                    <h4 class="tg-empty-state-title">Пока нет избранных постов</h4>
                                    <p class="tg-empty-state-text">Добавляйте посты в избранное, чтобы сохранить их здесь</p>
                                    <a href="<?php echo BASE_URL; ?>/posts" class="btn btn-sm btn-outline-primary mt-2">
                                        Перейти к постам
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                <?php } else if ($displayType === 'restricted') { ?>
                    <div class="tg-profile-bookmarks">
                        <div class="tg-card">
                            <div class="tg-card-header">
                                <h3 class="tg-card-title">
                                    <?php echo bloggy_icon('bs', 'bookmark-star', '18', 'currentColor', 'tg-mr-1'); ?>
                                    Избранное
                                </h3>
                            </div>
                            <div class="tg-card-body">
                                <div class="tg-empty-state tg-empty-state-small tg-text-center">
                                    <div class="tg-empty-state-icon">
                                        <?php echo bloggy_icon('bs', 'lock', '32', 'var(--tg-text-secondary)'); ?>
                                    </div>
                                    <h4 class="tg-empty-state-title">Доступ ограничен</h4>
                                    <p class="tg-empty-state-text">
                                        <?php if ($profileUserIsAdmin) { ?>
                                            У этого пользователя нет закладок, только публикации.
                                        <?php } else { ?>
                                            Вы не можете просматривать закладки этого пользователя
                                        <?php } ?>
                                    </p>
                                    <?php if (!isset($_SESSION['user_id'])) { ?>
                                        <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-sm btn-outline-primary mt-2">
                                            Войти в систему
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php
ob_start();
?>
<script>
window.baseUrl = '<?php echo BASE_URL; ?>';
window.userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>
<?php front_bottom_js(ob_get_clean()); ?>
<?php front_js('/templates/default/front/assets/js/user-action.js'); ?>
<?php front_js('/templates/default/front/assets/js/bookmarks.js'); ?>