<?php
/**
 * Tags Sidebar Compact
 */

$theme = $settings['theme'] ?? 'light';
$displayStyle = $settings['display_style'] ?? 'compact';
$showPostCount = !empty($settings['show_post_count']);
$showIcon = !empty($settings['show_icon']);
$accentColor = $settings['accent_color'] ?? '#2b5278';

$tags = $this->tags ?? [];

$tagIcon = '';
if(function_exists('bloggy_icon')) {
    $tagIcon = bloggy_icon('bs', 'tag', '10', 'currentColor', '');
} else {
    $tagIcon = '<svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor"><path d="M2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z"/><path d="M5.5 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/></svg>';
}
?>

<div class="tg-sidebar-tags">
    
    <?php if(!empty($settings['title'])) { ?>
        <div class="tg-sidebar-tags-header">
            <h3 class="tg-sidebar-tags-title">
                <?php if($showIcon) { ?>
                    <span class="tg-sidebar-tags-title-icon">
                        <?php echo bloggy_icon('bs', 'tags', '18', $accentColor, ''); ?>
                    </span>
                <?php } ?>
                <?php echo html($settings['title']); ?>
            </h3>
            <?php if(!empty($settings['description'])) { ?>
                <p class="tg-sidebar-tags-description"><?php echo html($settings['description']); ?></p>
            <?php } ?>
        </div>
    <?php } ?>
    
    <?php if(!empty($tags)) { ?>
        <div class="tg-sidebar-tags-list">
            <?php foreach($tags as $tag) { ?>
                <a href="/tag/<?php echo html($tag['slug']); ?>" class="tg-sidebar-tag">
                    <span class="tg-sidebar-tag-hash">#</span>
                    <span class="tg-sidebar-tag-name"><?php echo html($tag['name']); ?></span>
                    <?php if($showPostCount && ($tag['posts_count'] ?? 0) > 0) { ?>
                        <span class="tg-sidebar-tag-count"><?php echo $tag['posts_count'] ?? 0; ?></span>
                    <?php } ?>
                </a>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="tg-sidebar-tags-empty">
            <p>Теги не найдены</p>
        </div>
    <?php } ?>
    
</div>