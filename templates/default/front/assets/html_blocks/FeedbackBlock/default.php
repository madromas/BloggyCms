<?php
/**
 * Шаблон блока "Обратная связь"
 * Двухколоночный макет с формой и контактной информацией
 */

$layout = $settings['layout'] ?? 'form-left';
$theme = $settings['theme'] ?? 'light';
$showContactInfo = !empty($settings['show_contact_info']);
$showFormTitle = !empty($settings['show_form_title']);
$formExists = $settings['form_exists'] ?? false;
$formHtml = $settings['form_html'] ?? '';

// CSS переменные
$customStyles = [];
if ($theme === 'custom') {
    if (!empty($settings['background_color'])) {
        $customStyles[] = '--fb-bg-color: ' . html($settings['background_color']);
    }
    if (!empty($settings['text_color'])) {
        $customStyles[] = '--fb-text-color: ' . html($settings['text_color']);
    }
}
if (!empty($settings['accent_color'])) {
    $customStyles[] = '--fb-accent: ' . html($settings['accent_color']);
    $hex = ltrim($settings['accent_color'], '#');
    if (strlen($hex) === 6) {
        $rgb = hexdec(substr($hex, 0, 2)) . ', ' .
               hexdec(substr($hex, 2, 2)) . ', ' .
               hexdec(substr($hex, 4, 2));
        $customStyles[] = '--fb-accent-rgb: ' . $rgb;
    }
}
$paddingTop = (int)($settings['padding_top'] ?? 80);
$paddingBottom = (int)($settings['padding_bottom'] ?? 80);
$customStyles[] = '--fb-padding-top: ' . $paddingTop . 'px';
$customStyles[] = '--fb-padding-bottom: ' . $paddingBottom . 'px';

$sectionClass = 'feedback-block theme-' . $theme . ' layout-' . $layout;
if (!empty($settings['custom_css_class'])) {
    $sectionClass .= ' ' . html($settings['custom_css_class']);
}
$styleAttr = !empty($customStyles) ? ' style="' . implode('; ', $customStyles) . '"' : '';

// Иконки
$phoneIcon = function_exists('bloggy_icon') ? bloggy_icon('ri', 'phone-line', '20 20', 'currentColor', '') : '<i class="ri-phone-line"></i>';
$emailIcon = function_exists('bloggy_icon') ? bloggy_icon('ri', 'mail-line', '20 20', 'currentColor', '') : '<i class="ri-mail-line"></i>';
$mapIcon = function_exists('bloggy_icon') ? bloggy_icon('ri', 'map-pin-line', '20 20', 'currentColor', '') : '<i class="ri-map-pin-line"></i>';
?>

<section id="<?php echo html($settings['custom_id'] ?? ''); ?>" 
         class="<?php echo $sectionClass; ?>"<?php echo $styleAttr; ?>>
    <div class="container">
        <div class="row g-4 align-items-center">
            
            <?php
            $formOrder = ($layout === 'form-right') ? 'order-lg-2' : 'order-lg-1';
            $infoOrder = ($layout === 'form-right') ? 'order-lg-1' : 'order-lg-2';
            ?>
            
            <!-- Колонка с формой -->
            <div class="col-lg-6 <?php echo $formOrder; ?>">
                <div class="feedback-form-card">
                    <?php if ($showFormTitle && !empty($settings['form_title'])): ?>
                    <h3 class="feedback-form-title"><?php echo html($settings['form_title']); ?></h3>
                    <?php endif; ?>
                    
                    <?php if ($formExists && !empty($formHtml)): ?>
                        <?php echo $formHtml; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <?php echo empty($settings['form_slug']) ? 'Форма не выбрана' : 'Выбранная форма не найдена'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Колонка с контактной информацией -->
            <?php if ($showContactInfo): ?>
            <div class="col-lg-6 <?php echo $infoOrder; ?>">
                <div class="feedback-info-content">
                    <?php if (!empty($settings['contact_title'])): ?>
                    <h3 class="feedback-info-title"><?php echo html($settings['contact_title']); ?></h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($settings['contact_description'])): ?>
                    <p class="feedback-info-description"><?php echo nl2br(html($settings['contact_description'])); ?></p>
                    <?php endif; ?>
                    
                    <div class="feedback-contacts">
                        <?php if (!empty($settings['contact_phone'])): ?>
                        <div class="feedback-contact-item">
                            <div class="feedback-contact-icon"><?php echo $phoneIcon; ?></div>
                            <div class="feedback-contact-content">
                                <span class="feedback-contact-label">Позвоните нам:</span>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['contact_phone']); ?>" 
                                   class="feedback-contact-value">
                                    <?php echo html($settings['contact_phone']); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['contact_email'])): ?>
                        <div class="feedback-contact-item">
                            <div class="feedback-contact-icon"><?php echo $emailIcon; ?></div>
                            <div class="feedback-contact-content">
                                <span class="feedback-contact-label">Напишите нам:</span>
                                <a href="mailto:<?php echo html($settings['contact_email']); ?>" 
                                   class="feedback-contact-value">
                                    <?php echo html($settings['contact_email']); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['contact_address'])): ?>
                        <div class="feedback-contact-item">
                            <div class="feedback-contact-icon"><?php echo $mapIcon; ?></div>
                            <div class="feedback-contact-content">
                                <span class="feedback-contact-label">Адрес:</span>
                                <?php if (!empty($settings['contact_map_url'])): ?>
                                <a href="<?php echo html($settings['contact_map_url']); ?>" 
                                   target="_blank" rel="noopener noreferrer"
                                   class="feedback-contact-value">
                                    <?php echo html($settings['contact_address']); ?>
                                </a>
                                <?php else: ?>
                                <span class="feedback-contact-value"><?php echo html($settings['contact_address']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</section>