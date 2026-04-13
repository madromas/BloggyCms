<?php
/**
* Footer Block Template
* Современный тёмный футер с гибкой настройкой
*/

$logoUrl = $this->getLogoUrl($settings);
$logoAlt = html($settings['logo_alt'] ?? 'Логотип сайта');
$siteName = html($settings['site_name'] ?? 'BloggyCMS');
$siteDescription = html($settings['site_description'] ?? '');
$footerMenu1 = !empty($settings['footer_menu_1']) ? MenuRenderer::renderById($settings['footer_menu_1']) : '';
$footerMenu2 = !empty($settings['footer_menu_2']) ? MenuRenderer::renderById($settings['footer_menu_2']) : '';
$menu1Title = html($settings['menu_1_title'] ?? 'Навигация');
$menu2Title = html($settings['menu_2_title'] ?? 'Информация');
$showRecentPosts = !empty($settings['show_recent_posts']);
$recentPostsTitle = html($settings['recent_posts_title'] ?? 'Последние посты');
$showRecentTags = !empty($settings['show_recent_tags']);
$recentTagsTitle = html($settings['recent_tags_title'] ?? 'Популярные теги');
$showCategories = !empty($settings['show_categories']);
$categoriesTitle = html($settings['categories_title'] ?? 'Категории');
$categoriesStyle = $settings['categories_style'] ?? 'pills';
$categoriesShowCount = !empty($settings['categories_show_count']);
$showContacts = !empty($settings['show_contacts']);
$contactsTitle = html($settings['contacts_title'] ?? 'Свяжитесь с нами');
$contactEmail = html($settings['contact_email'] ?? '');
$contactPhone = html($settings['contact_phone'] ?? '');
$contactAddress = html($settings['contact_address'] ?? '');
$socialLinks = is_array($settings['social_links'] ?? []) ? $settings['social_links'] : [];
$copyrightText = $settings['copyright_text'] ?? '© ' . date('Y') . ' ' . $siteName;
$footerLinks = is_array($settings['footer_links'] ?? []) ? $settings['footer_links'] : [];
$bgColor = html($settings['background_color'] ?? '#111827');
$textColor = html($settings['text_color'] ?? '#9ca3af');
$accentColor = html($settings['accent_color'] ?? '#2563eb');
$headingColor = html($settings['heading_color'] ?? '#f9fafb');
$paddingTop = (int)($settings['padding_top'] ?? 80);
$paddingBottom = (int)($settings['padding_bottom'] ?? 40);

$customStyles = [
    '--footer-bg: ' . $bgColor,
    '--footer-text: ' . $textColor,
    '--footer-accent: ' . $accentColor,
    '--footer-heading: ' . $headingColor,
    '--footer-padding-top: ' . $paddingTop . 'px',
    '--footer-padding-bottom: ' . $paddingBottom . 'px',
];

$recentPosts = $this->recentPosts ?? [];
$recentTags = $this->recentTags ?? [];
$categories = $this->categories ?? [];

$socialIcons = [
    'telegram' => ['icon' => 'telegram-plane', 'label' => 'Telegram'],
    'vk' => ['icon' => 'vk', 'label' => 'ВКонтакте'],
    'youtube' => ['icon' => 'youtube', 'label' => 'YouTube'],
    'github' => ['icon' => 'github', 'label' => 'GitHub'],
    'twitter' => ['icon' => 'twitter', 'label' => 'Twitter/X'],
    'instagram' => ['icon' => 'instagram', 'label' => 'Instagram'],
    'facebook' => ['icon' => 'facebook-square', 'label' => 'Facebook'],
    'linkedin' => ['icon' => 'linkedin', 'label' => 'LinkedIn'],
    'odnoklassniki' => ['icon' => 'odnoklassniki', 'label' => 'Одноклассники'],
    'behance' => ['icon' => 'behance', 'label' => 'Behance'],
    'reddit' => ['icon' => 'reddit', 'label' => 'Reddit'],
];

$activeColumns = 0;
if ($footerMenu1) $activeColumns++;
if ($footerMenu2) $activeColumns++;
if ($showRecentPosts) $activeColumns++;
if ($showRecentTags) $activeColumns++;
?>

<footer class="site-footer" style="
    background-color: <?php echo $bgColor; ?>;
    color: <?php echo $textColor; ?>;
    --footer-bg: <?php echo $bgColor; ?>;
    --footer-text: <?php echo $textColor; ?>;
    --footer-accent: <?php echo $accentColor; ?>;
    --footer-heading: <?php echo $headingColor; ?>;
    --footer-padding-top: <?php echo $paddingTop; ?>px;
    --footer-padding-bottom: <?php echo $paddingBottom; ?>px;
">
    
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                
                <div class="footer-col footer-brand">
                    <?php if ($logoUrl) { ?>
                        <a href="/" class="footer-logo">
                            <img src="<?php echo $logoUrl; ?>" alt="<?php echo $logoAlt; ?>" class="footer-logo-img">
                        </a>
                    <?php } ?>
                    
                    <?php if ($siteName) { ?>
                        <h3 class="footer-site-name">
                            <a href="/"><?php echo $siteName; ?></a>
                        </h3>
                    <?php } ?>
                    
                    <?php if ($siteDescription) { ?>
                        <p class="footer-description"><?php echo $siteDescription; ?></p>
                    <?php } ?>
                    
                    <?php if (!empty($socialLinks)) { ?>
                        <div class="footer-social">
                            <?php foreach ($socialLinks as $link) {
                                if (empty($link['network']) || empty($link['url'])) continue;
                                $iconData = $socialIcons[$link['network']] ?? null;
                                if (!$iconData) continue;
                            ?>
                            <a href="<?php echo html($link['url']); ?>" 
                            class="footer-social-link" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            aria-label="<?php echo $iconData['label']; ?>">
                                <?php if(function_exists('bloggy_icon')) {
                                    echo bloggy_icon('brands', $iconData['icon'], '20 20', 'currentColor', '');
                                } ?>
                            </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                
                <?php if ($footerMenu1) { ?>
                    <div class="footer-col footer-menu">
                        <h4 class="footer-title"><?php echo $menu1Title; ?></h4>
                        <nav class="footer-nav">
                            <?php echo $footerMenu1; ?>
                        </nav>
                    </div>
                <?php } ?>
                
                <?php if ($footerMenu2) { ?>
                    <div class="footer-col footer-menu">
                        <h4 class="footer-title"><?php echo $menu2Title; ?></h4>
                        <nav class="footer-nav">
                            <?php echo $footerMenu2; ?>
                        </nav>
                    </div>
                <?php } ?>
                
                <?php if ($showRecentPosts || $showRecentTags) { ?>
                    <div class="footer-col footer-widgets">
                        
                        <?php if ($showRecentPosts && !empty($recentPosts)) { ?>
                            <div class="footer-widget">
                                <h4 class="footer-title"><?php echo $recentPostsTitle; ?></h4>
                                <ul class="footer-posts-list">
                                    <?php foreach ($recentPosts as $post) { ?>
                                        <li class="footer-post-item">
                                            <a href="/post/<?php echo html($post['slug'] ?? $post['id']); ?>" class="footer-post-link">
                                                <span class="footer-post-title"><?php echo html($post['title'] ?? ''); ?></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                        
                        <?php if ($showRecentTags && !empty($recentTags)) { ?>
                            <div class="footer-widget">
                                <h4 class="footer-title"><?php echo $recentTagsTitle; ?></h4>
                                <div class="footer-tags-cloud">
                                    <?php foreach ($recentTags as $tag) { ?>
                                    <a href="/tag/<?php echo html($tag['slug'] ?? ''); ?>" class="footer-tag">
                                        #<?php echo html($tag['name'] ?? ''); ?>
                                    </a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        
                    </div>
                <?php } ?>
                
            </div>
        </div>
    </div>
    
    <?php if ($showCategories && !empty($categories)) { ?>
        <div class="footer-categories-bar">
            <div class="container">
                <div class="categories-bar-inner">
                    <?php if ($categoriesTitle) { ?>
                        <span class="categories-bar-label"><?php echo $categoriesTitle; ?>:</span>
                    <?php } ?>
                    <div class="categories-bar-list">
                        <?php foreach ($categories as $category) {
                            $catUrl = '/category/' . html($category['slug'] ?? $category['id']);
                            $catName = html($category['name'] ?? '');
                            $catCount = $category['posts_count'] ?? 0;
                        ?>
                        <?php if ($categoriesStyle === 'pills') { ?>
                            <a href="<?php echo $catUrl; ?>" class="category-pill">
                                <?php echo $catName; ?>
                                <?php if ($categoriesShowCount) { ?>
                                <span class="category-count"><?php echo $catCount; ?></span>
                                <?php } ?>
                            </a>
                        <?php } elseif ($categoriesStyle === 'chips') { ?>
                            <a href="<?php echo $catUrl; ?>" class="category-chip">
                                <?php echo $catName; ?>
                                <?php if ($categoriesShowCount) { ?>
                                <span class="category-count"><?php echo $catCount; ?></span>
                                <?php } ?>
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo $catUrl; ?>" class="category-link">
                                <?php echo $catName; ?>
                                <?php if ($categoriesShowCount) { ?>
                                <span class="category-count">(<?php echo $catCount; ?>)</span>
                                <?php } ?>
                            </a>
                        <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    
    <?php if ($showContacts && ($contactEmail || $contactPhone || $contactAddress)) { ?>
        <div class="footer-contacts-bar">
            <div class="container">
                <div class="contacts-bar-inner">
                    <?php if ($contactsTitle) { ?>
                        <span class="contacts-bar-label"><?php echo $contactsTitle; ?></span>
                    <?php } ?>
                        <div class="contacts-bar-list">
                        <?php if ($contactEmail) { ?>
                            <a href="mailto:<?php echo $contactEmail; ?>" class="contact-item">
                                <?php if(function_exists('bloggy_icon')) {
                                    echo bloggy_icon('bs', 'envelope', '16 16', 'currentColor', 'me-1');
                                } ?>
                                <?php echo $contactEmail; ?>
                            </a>
                        <?php } ?>
                        <?php if ($contactPhone) { ?>
                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contactPhone); ?>" class="contact-item">
                                <?php if(function_exists('bloggy_icon')) {
                                    echo bloggy_icon('bs', 'telephone', '16 16', 'currentColor', 'me-1');
                                } ?>
                                <?php echo $contactPhone; ?>
                            </a>
                        <?php } ?>
                        <?php if ($contactAddress) { ?>
                            <span class="contact-item">
                                <?php if(function_exists('bloggy_icon')) {
                                    echo bloggy_icon('bs', 'geo-alt', '16 16', 'currentColor', 'me-1');
                                } ?>
                                <?php echo $contactAddress; ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <div class="footer-copyright">
                    <?php echo $copyrightText; ?>
                </div>
                
                <?php if (!empty($footerLinks)) { ?>
                    <nav class="footer-legal">
                        <?php foreach ($footerLinks as $link) {
                            if (empty($link['title']) || empty($link['url'])) continue;
                            $linkTitle = html($link['title']);
                            $linkUrl = html($link['url']);
                            $linkTarget = html($link['target'] ?? '_self');
                        ?>
                        <a href="<?php echo $linkUrl; ?>" 
                        class="footer-legal-link" 
                        target="<?php echo $linkTarget; ?>">
                            <?php echo $linkTitle; ?>
                        </a>
                        <?php } ?>
                    </nav>
                <?php } ?>
            </div>
        </div>
    </div>
    
</footer>