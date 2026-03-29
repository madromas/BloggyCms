<?php
/**
 * Шаблон блока "Кнопка вверх"
 */

$scrollThreshold = (int)($settings['scroll_threshold'] ?? 300);
$animationDuration = (int)($settings['animation_duration'] ?? 500);
$position = $settings['position'] ?? 'bottom-right';
$offsetBottom = (int)($settings['offset_bottom'] ?? 20);
$offsetSide = (int)($settings['offset_side'] ?? 20);
$size = $settings['size'] ?? 'md';
$shape = $settings['shape'] ?? 'circle';
$bgColor = $settings['background_color'] ?? '#2563eb';
$textColor = $settings['text_color'] ?? '#ffffff';
$showShadow = !empty($settings['show_shadow']);
$customIcon = $settings['custom_icon'] ?? '';

$sizeMap = [
    'sm' => 40,
    'md' => 50,
    'lg' => 60,
];
$buttonSize = $sizeMap[$size] ?? 50;

$customId = !empty($settings['custom_id']) ? html($settings['custom_id']) : 'scroll-to-top-btn';

$style = "position: fixed; z-index: 9999; display: flex; align-items: center; justify-content: center; width: {$buttonSize}px; height: {$buttonSize}px; background-color: {$bgColor}; color: {$textColor}; border: none; border-radius: " . ($shape === 'circle' ? '50%' : '12px') . "; cursor: pointer; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.2s ease; text-decoration: none;";

if ($position === 'bottom-right') {
    $style .= " bottom: {$offsetBottom}px; right: {$offsetSide}px;";
} else {
    $style .= " bottom: {$offsetBottom}px; left: {$offsetSide}px;";
}

if ($showShadow) {
    $style .= " box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);";
}

$buttonClasses = 'scroll-to-top-btn';
if (!empty($settings['custom_css_class'])) {
    $buttonClasses .= ' ' . html($settings['custom_css_class']);
}

$iconHtml = '';
if (!empty($customIcon)) {
    $iconParts = explode(':', $customIcon);
    $iconSet = $iconParts[0] ?? 'bs';
    $iconName = $iconParts[1] ?? 'arrow-up';
    if (function_exists('bloggy_icon')) {
        $iconHtml = bloggy_icon($iconSet, $iconName, ($buttonSize * 0.5) . ' ' . ($buttonSize * 0.5), 'currentColor', '');
    }
} else {
    if (function_exists('bloggy_icon')) {
        $iconHtml = bloggy_icon('bs', 'arrow-up', ($buttonSize * 0.5) . ' ' . ($buttonSize * 0.5), 'currentColor', '');
    } else {
        $iconHtml = '<svg width="' . ($buttonSize * 0.5) . '" height="' . ($buttonSize * 0.5) . '" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/></svg>';
    }
}
?>

<button type="button" id="<?php echo $customId; ?>" class="<?php echo $buttonClasses; ?>" style="<?php echo $style; ?>" aria-label="Прокрутить вверх" data-scroll-threshold="<?php echo $scrollThreshold; ?>" data-animation-duration="<?php echo $animationDuration; ?>">
    <?php echo $iconHtml; ?>
</button>

<?php if (!empty($settings['custom_css_class'])) { ?>
    <style>
        .<?php echo $buttonClasses; ?>:hover {
            transform: scale(1.05);
            opacity: 0.9 !important;
        }
    </style>
<?php } ?>