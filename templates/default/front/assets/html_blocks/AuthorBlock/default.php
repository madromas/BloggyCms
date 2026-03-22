<?php
/**
 * Шаблон блока "Автор блога"
 */
?>

<div class="tg-author-card <?php echo $settings['custom_css_class'] ?? ''; ?>" 
     <?php if (!empty($settings['custom_id'])) { ?>id="<?php echo $settings['custom_id']; ?>"<?php } ?> style="--accent-color: <?php echo $settings['accent_color'] ?? '#2563eb'; ?>;<?php if ($settings['theme'] === 'custom') { ?>background-color: <?php echo $settings['background_color'] ?? 'transparent'; ?>; color: <?php echo $settings['text_color'] ?? 'inherit'; ?>;<?php } ?> padding-bottom: <?php echo $settings['padding_bottom']; ?>px;">
    
    <div class="tg-author-card-inner" style="
        <?php if ($settings['show_shadow']) { ?>box-shadow: 0 4px 20px rgba(0,0,0,0.08);<?php } ?>
        <?php if (!empty($settings['background_color']) && $settings['theme'] === 'custom') { ?>
            background-color: <?php echo $settings['background_color']; ?>;
        <?php } ?>
    ">
        
        <?php if (!empty($this->avatarUrl)) { ?>
            <div class="tg-author-avatar tg-avatar-<?php echo $settings['avatar_style'] ?? 'circle'; ?>">
                <img src="<?php echo $this->avatarUrl; ?>" alt="<?php echo html($settings['name'] ?? 'Автор'); ?>">
            </div>
        <?php } ?>
        
        <?php if (!empty($settings['name'])) { ?>
            <h3 class="tg-author-name"><?php echo html($settings['name']); ?></h3>
        <?php } ?>
        
        <?php if (!empty($settings['role'])) { ?>
            <div class="tg-author-role"><?php echo html($settings['role']); ?></div>
        <?php } ?>
        
        <?php if (!empty($settings['description'])) { ?>
            <div class="tg-author-description">
                <?php echo nl2br(html($settings['description'])); ?>
            </div>
        <?php } ?>
        
        <?php if (!empty($this->socialLinks)) { ?>
            <div class="tg-author-social">
                <?php foreach ($this->socialLinks as $link) { ?>
                    <?php 
                    $network = $link['network'];
                    $url = $link['url'];
                    $icon = $network;
                    ?>
                    <a href="<?php echo html($url); ?>" class="tg-social-link tg-social-<?php echo $network; ?>" 
                       target="_blank" rel="noopener noreferrer"
                       title="<?php echo ucfirst($network); ?>">
                        <?php echo bloggy_icon('bs', $icon, '18', 'currentColor'); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
        
        <?php if (!empty($settings['email']) || !empty($settings['phone']) || !empty($settings['website'])) { ?>
            <div class="tg-author-contacts">
                <?php if (!empty($settings['email'])) { ?>
                    <a href="mailto:<?php echo html($settings['email']); ?>" class="tg-contact-link">
                        <?php echo bloggy_icon('bs', 'envelope', '16', 'currentColor'); ?>
                        <?php echo html($settings['email']); ?>
                    </a>
                <?php } ?>
                <?php if (!empty($settings['phone'])) { ?>
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['phone']); ?>" class="tg-contact-link">
                        <?php echo bloggy_icon('bs', 'telephone', '16', 'currentColor'); ?>
                        <?php echo html($settings['phone']); ?>
                    </a>
                <?php } ?>
                <?php if (!empty($settings['website'])) { ?>
                    <a href="<?php echo html($settings['website']); ?>" class="tg-contact-link" target="_blank" rel="noopener">
                        <?php echo bloggy_icon('bs', 'globe2', '16', 'currentColor'); ?>
                        <?php echo preg_replace('#^https?://#', '', html($settings['website'])); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
        
        <?php if (!empty($settings['show_button']) && !empty($settings['button_text'])) { ?>
            <div class="tg-author-button">
                <a href="<?php echo html($settings['button_url'] ?? '#'); ?>" 
                   class="tg-btn tg-btn-primary"
                   target="<?php echo $settings['button_target'] ?? '_self'; ?>">
                    <?php echo html($settings['button_text']); ?>
                </a>
            </div>
        <?php } ?>
        
    </div>
</div>