<?php
/**
 * Template Name: Страница всех тегов 
 */

$minPostsToShow = SettingsHelper::get('controller_tags', 'min_posts_to_show', 1);
$defaultTagImage = SettingsHelper::get('controller_tags', 'default_tag_image', '');
$tagPrefix = SettingsHelper::get('controller_tags', 'tag_prefix', '#');

$colorPalette = [
    '#c11c3b', '#0a11a8', '#002306', '#c76234',
    '#8a2be2', '#20c997', '#fd7e14', '#6f42c1',
    '#139090', '#d59801', '#296e4a'
];
?>
<style>
.tg-tags-page {
    background: linear-gradient(180deg, #f9fafb 0%, #f3f4f6 100%);
    padding: 3rem 0;
    min-height: 100vh;
}

.tg-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.tg-page-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-top: 1rem;
}

.tg-page-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #111827;
    margin: 0 0 1rem;
    letter-spacing: -0.03em;
    line-height: 1.2;
}

.tg-page-title-dot {
    color: #4f46e5;
    font-weight: 900;
}

.tg-page-description {
    font-size: 1.125rem;
    color: #6b7280;
    max-width: 650px;
    margin: 0 auto;
    line-height: 1.7;
}

.tg-tags-stats {
    margin-bottom: 3rem;
}

.tg-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.tg-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
}

.tg-card-body {
    padding: 2rem;
}

.tg-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1.5rem;
    display: flex;
    align-items: center;
}

.tg-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
}

.tg-stat-card {
    background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.25s ease;
    border: 1px solid #e5e7eb;
    text-decoration: none !important;
}

.tg-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.12);
    border-color: #c7d2fe;
}

.tg-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tg-stat-content {
    flex: 1;
    min-width: 0;
}

.tg-stat-label {
    display: block;
    font-size: 0.8125rem;
    color: #6b7280;
    margin-bottom: 0.35rem;
    font-weight: 500;
}

.tg-stat-value {
    display: block;
    font-size: 1.375rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
    word-break: break-word;
}

.tg-tags-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.tg-section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.tg-section-title-count {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #6b7280;
    background: #f3f4f6;
    padding: 0.35rem 0.875rem;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
}

.tg-tags-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.tg-tag-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 280px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.tg-tag-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
    border-color: #c7d2fe;
}

.tg-tag-card .tg-card-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tg-tag-card .tg-card-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    line-height: 1.4;
}

.tg-tag-card .tg-card-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.tg-tag-card .tg-card-title a:hover {
    color: #4f46e5;
}

.tg-card-text {
    font-size: 0.9375rem;
    color: #6b7280;
    line-height: 1.65;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.tg-tag-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
    margin-top: auto;
}

.tg-tag-date {
    font-size: 0.8125rem;
    color: #9ca3af;
    font-weight: 500;
}

.tg-card-footer {
    padding: 1rem 1.5rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    min-height: auto;
}

.tg-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    background: #e5e7eb;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    white-space: nowrap;
    transition: background 0.2s ease;
}

.tg-badge:hover {
    background: #d1d5db;
}

.tg-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9375rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.25s ease;
    border: 1px solid transparent;
    white-space: nowrap;
}

.tg-btn-sm {
    padding: 0.4375rem 0.875rem;
    font-size: 0.875rem;
}

.tg-btn-lg {
    padding: 0.875rem 1.75rem;
    font-size: 1rem;
}

.tg-btn-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: #ffffff;
    border-color: #4f46e5;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
}

.tg-btn-primary:hover {
    background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
    border-color: #4338ca;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    transform: translateY(-1px);
}

.tg-btn-outline {
    background: #ffffff;
    color: #4b5563;
    border-color: #d1d5db;
}

.tg-btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #111827;
    transform: translateY(-1px);
}

.tg-empty-state {
    background: #ffffff;
    border-radius: 16px;
    padding: 4rem 2rem;
    text-align: center;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
    max-width: 550px;
    margin: 0 auto;
}

.tg-empty-state-icon {
    margin-bottom: 1.5rem;
    color: #9ca3af;
    display: flex;
    justify-content: center;
}

.tg-empty-state-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.75rem;
}

.tg-empty-state-text {
    font-size: 1rem;
    color: #6b7280;
    margin: 0 0 2rem;
    line-height: 1.7;
}

.tg-empty-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.tg-pagination {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.tg-mb-1 { margin-bottom: 0.25rem !important; }
.tg-mb-3 { margin-bottom: 1rem !important; }
.tg-mb-4 { margin-bottom: 1.5rem !important; }
.tg-mb-5 { margin-bottom: 2rem !important; }
.tg-mt-5 { margin-top: 2rem !important; }
.tg-mr-1 { margin-right: 0.25rem !important; }
.tg-mr-2 { margin-right: 0.5rem !important; }
.tg-mr-3 { margin-right: 1rem !important; }
.tg-ml-1 { margin-left: 0.25rem !important; }
.tg-ml-2 { margin-left: 0.5rem !important; }
.tg-py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }

.tg-text-muted { color: #6b7280 !important; }
.tg-text-center { text-align: center !important; }
.tg-text-dark { color: #111827 !important; }
.tg-text-decoration-none { text-decoration: none !important; }

.d-flex { display: flex !important; }
.d-flex-wrap { flex-wrap: wrap !important; }
.gap-1 { gap: 0.5rem !important; }
.align-items-start { align-items: flex-start !important; }
.align-items-center { align-items: center !important; }
.justify-content-between { justify-content: space-between !important; }
.justify-content-center { justify-content: center !important; }
.flex-grow-1 { flex-grow: 1 !important; }
.flex-shrink-0 { flex-shrink: 0 !important; }
.h-100 { height: 100% !important; }
.w-100 { width: 100% !important; }
.small { font-size: 0.875rem !important; }
.rounded-circle { border-radius: 50% !important; }
.overflow-hidden { overflow: hidden !important; }
.bg-transparent { background: transparent !important; }

@media (max-width: 1024px) {
    .tg-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .tg-tags-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .tg-tags-page {
        padding: 2rem 0;
    }
    
    .tg-container {
        padding: 0 1rem;
    }
    
    .tg-page-title {
        font-size: 1.875rem;
    }
    
    .tg-page-description {
        font-size: 1rem;
    }
    
    .tg-card-body {
        padding: 1.5rem;
    }
    
    .tg-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .tg-tags-grid {
        grid-template-columns: 1fr;
    }
    
    .tg-tag-card {
        min-height: auto;
    }
    
    .tg-section-title {
        font-size: 1.25rem;
    }
    
    .tg-empty-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .tg-empty-actions .tg-btn {
        width: 100%;
    }
    
    .tg-tag-card-footer {
        flex-direction: column;
        gap: 0.75rem;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .tg-page-title {
        font-size: 1.625rem;
    }
    
    .tg-card-body {
        padding: 1.25rem;
    }
    
    .tg-stat-card {
        padding: 1rem;
    }
    
    .tg-stat-icon {
        width: 42px;
        height: 42px;
    }
    
    .tg-stat-value {
        font-size: 1.125rem;
    }
}
</style>


<div class="tg-tags-page">
    <div class="tg-container">

        <div class="tg-page-header tg-mb-5">
            <h1 class="tg-page-title">
                Коллекция тегов
                <span class="tg-page-title-dot">.</span>
            </h1>
            <p class="tg-page-description tg-text-muted">
                Исследуйте все теги, используемые на сайте, чтобы найти интересующие вас темы
            </p>
        </div>
        
        <div class="tg-tags-stats tg-mb-5">
            <div class="tg-card">
                <div class="tg-card-body">
                    <h3 class="tg-card-title tg-mb-4">
                        <?php echo bloggy_icon('bs', 'tags', '18', '#4f46e5', 'tg-mr-2'); ?>
                        Статистика тегов
                    </h3>
                    
                    <div class="tg-stats-grid">
                        <div class="tg-stat-card">
                            <div class="tg-stat-icon" style="background: rgba(79, 70, 229, 0.1);">
                                <?php echo bloggy_icon('bs', 'tag-fill', '18', '#4f46e5'); ?>
                            </div>
                            <div class="tg-stat-content">
                                <span class="tg-stat-label">Всего тегов</span>
                                <span class="tg-stat-value"><?php echo count($tags); ?></span>
                            </div>
                        </div>
                        
                        <?php 
                        $mostPopularTag = null;
                        $maxPostsCount = 0;
                        
                        foreach ($tags as $tag) {
                            if ($tag['posts_count'] > $maxPostsCount) {
                                $maxPostsCount = $tag['posts_count'];
                                $mostPopularTag = $tag;
                            }
                        }
                        ?>
                        
                        <?php if ($mostPopularTag) { ?>
                        <a href="<?php echo BASE_URL; ?>/tag/<?php echo html($mostPopularTag['slug']); ?>" class="tg-stat-card">
                            <div class="tg-stat-icon" style="background: rgba(220, 53, 69, 0.1);">
                                <?php echo bloggy_icon('bs', 'fire', '18', '#dc3545'); ?>
                            </div>
                            <div class="tg-stat-content">
                                <span class="tg-stat-label">Популярный тег</span>
                                <span class="tg-stat-value"><?php echo html($tagPrefix); ?><?php echo html($mostPopularTag['name']); ?></span>
                            </div>
                        </a>
                        <?php } ?>
                        
                        <div class="tg-stat-card">
                            <div class="tg-stat-icon" style="background: rgba(79, 70, 229, 0.1);">
                                <?php echo bloggy_icon('bs', 'newspaper', '18', '#4f46e5'); ?>
                            </div>
                            <div class="tg-stat-content">
                                <span class="tg-stat-label">Всего постов</span>
                                <span class="tg-stat-value">
                                    <?php 
                                    $totalPostsInTags = 0;
                                    foreach ($tags as $tag) {
                                        $totalPostsInTags += $tag['posts_count'];
                                    }
                                    echo $totalPostsInTags; ?>
                                </span>
                            </div>
                        </div>
                        
                        <a href="<?php echo BASE_URL; ?>/posts" class="tg-stat-card">
                            <div class="tg-stat-icon" style="background: rgba(79, 70, 229, 0.1);">
                                <?php echo bloggy_icon('bs', 'grid-3x3-gap', '18', '#4f46e5'); ?>
                            </div>
                            <div class="tg-stat-content">
                                <span class="tg-stat-label">Все посты</span>
                                <span class="tg-stat-value">Смотреть все</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($tags)) { ?>
        
        <div class="tg-tags-header tg-mb-4">
            <h2 class="tg-section-title">
                Все теги
                <span class="tg-section-title-count"><?php echo count($tags); ?></span>
            </h2>
        </div>
        
        <div class="tg-tags-grid">
            <?php foreach ($tags as $index => $tag) { 
                $bgColor = $colorPalette[$index % count($colorPalette)];
                $tagImage = '';
                
                if (!empty($tag['image'])) {
                    $tagImage = BASE_URL . '/uploads/tags/' . html($tag['image']);
                } elseif (!empty($defaultTagImage)) {
                    $tagImage = BASE_URL . '/uploads/settings/tags/' . html($defaultTagImage);
                }
            ?>
            <div class="tg-tag-card">
                <div class="tg-card-body">
                    <div class="d-flex align-items-start tg-mb-3">
                        <div class="tg-mr-3 flex-shrink-0">
                            <?php if ($tagImage) { ?>
                            <div class="rounded-circle overflow-hidden" style="width: 56px; height: 56px;">
                                <img src="<?php echo $tagImage; ?>" 
                                     alt="<?php echo html($tag['name']); ?>" 
                                     class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <?php } else { ?>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 56px; height: 56px; background-color: <?php echo $bgColor; ?>20;">
                                <?php echo bloggy_icon('bs', 'tag', '24', $bgColor); ?>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <div class="flex-grow-1" style="min-width: 0;">
                            <h5 class="tg-card-title tg-mb-1">
                                <a href="<?php echo BASE_URL; ?>/tag/<?php echo html($tag['slug']); ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo html($tagPrefix); ?><?php echo html($tag['name']); ?>
                                </a>
                            </h5>
                            <div class="tg-text-muted small">
                                <span class="d-flex align-items-center">
                                    <?php echo bloggy_icon('bs', 'file-text', '12', 'currentColor', 'tg-mr-1'); ?>
                                    <?php echo $tag['posts_count']; ?> 
                                    <?php echo plural_form($tag['posts_count'], ['пост', 'поста', 'постов']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($tag['description'])) { ?>
                    <div class="tg-card-text tg-text-muted small tg-mb-3">
                        <?php echo html(mb_strimwidth($tag['description'], 0, 120, '...')); ?>
                    </div>
                    <?php } ?>
                    
                    <div class="tg-tag-card-footer">
                        <span class="tg-tag-date">
                            <?php if (!empty($tag['created_at'])) { ?>
                            <?php echo date('d.m.Y', strtotime($tag['created_at'])); ?>
                            <?php } ?>
                        </span>
                        
                        <a href="<?php echo BASE_URL; ?>/tag/<?php echo html($tag['slug']); ?>" 
                           class="tg-btn tg-btn-sm tg-btn-outline">
                            Смотреть
                            <?php echo bloggy_icon('bs', 'arrow-right', '14', 'currentColor', 'tg-ml-1'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="tg-card-footer">
                    <span class="tg-badge">
                        <?php echo $tag['posts_count']; ?> постов
                    </span>
                    <?php if (!empty($tag['updated_at'])) { ?>
                    <span class="tg-badge">
                        Обновлён <?php echo date('d.m', strtotime($tag['updated_at'])); ?>
                    </span>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <?php if (!empty($pagination) && $pagination['total_pages'] > 1) { ?>
        <div class="tg-pagination tg-mt-5 tg-text-center">
            <?php if ($pagination['current_page'] < $pagination['total_pages']) { ?>
            <a href="<?php echo BASE_URL; ?>/tags?page=<?php echo $pagination['current_page'] + 1; ?>" class="tg-btn tg-btn-outline tg-btn-lg">
                <?php echo bloggy_icon('bs', 'arrow-down', '16', 'currentColor', 'tg-mr-1'); ?>
                Показать еще
            </a>
            <?php } else { ?>
            <div class="tg-text-muted tg-py-3">
                Вы просмотрели все теги
            </div>
            <?php } ?>
        </div>
        <?php } ?>
        
        <?php } else { ?>

        <div class="tg-empty-state">
            <div class="tg-empty-state-icon">
                <?php echo bloggy_icon('bs', 'tags', '48', '#9ca3af'); ?>
            </div>
            <h3 class="tg-empty-state-title">Теги не найдены</h3>
            <p class="tg-empty-state-text tg-text-muted">
                Пока на сайте нет тегов или они не назначены постам.
            </p>
            <div class="tg-empty-actions">
                <a href="<?php echo BASE_URL; ?>/posts" class="tg-btn tg-btn-primary">
                    <?php echo bloggy_icon('bs', 'newspaper', '16', 'currentColor', 'tg-mr-1'); ?>
                    Все посты
                </a>
                <a href="<?php echo BASE_URL; ?>/categories" class="tg-btn tg-btn-outline">
                    <?php echo bloggy_icon('bs', 'folder', '16', 'currentColor', 'tg-mr-1'); ?>
                    Категории
                </a>
            </div>
        </div>
        
        <?php } ?>
        
    </div>
</div>