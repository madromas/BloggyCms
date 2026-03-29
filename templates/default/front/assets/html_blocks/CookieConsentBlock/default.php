<?php
/**
 * Шаблон блока "Согласие с cookies"
 */

$message = nl2br(html($settings['message'] ?? ''));
$acceptText = html($settings['accept_button_text'] ?? 'Принять');
$declineText = html($settings['decline_button_text'] ?? 'Отклонить');
$policyLinkText = html($settings['policy_link_text'] ?? 'Политика конфиденциальности');
$policyUrl = html($settings['policy_url'] ?? '/privacy');
$showPolicyLink = !empty($settings['show_policy_link']);
$position = $settings['position'] ?? 'bottom';
$theme = $settings['theme'] ?? 'light';
$bgColor = $settings['background_color'] ?? ($theme === 'dark' ? '#1f2937' : '#ffffff');
$textColor = $settings['text_color'] ?? ($theme === 'dark' ? '#f9fafb' : '#111827');
$accentColor = $settings['accent_color'] ?? '#2563eb';
$showShadow = !empty($settings['show_shadow']);
$cookieName = $settings['cookie_name'] ?? 'cookie_consent';
$autoShow = !empty($settings['auto_show']);
$cookieExpiryDays = (int)($settings['cookie_expiry_days'] ?? 365);
$customId = !empty($settings['custom_id']) ? html($settings['custom_id']) : 'cookie-consent';
$customClass = !empty($settings['custom_css_class']) ? ' ' . html($settings['custom_css_class']) : '';

$style = "position: fixed; z-index: 10000; left: 0; right: 0; display: flex; justify-content: center; padding: 16px 24px; background-color: {$bgColor}; color: {$textColor}; font-family: inherit; font-size: 14px; line-height: 1.5; transition: transform 0.3s ease, opacity 0.3s ease;";

if ($position === 'bottom') {
    $style .= " bottom: 0; transform: translateY(100%);";
} else {
    $style .= " top: 0; transform: translateY(-100%);";
}

if ($showShadow) {
    $style .= " box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);";
}

if ($theme === 'custom') {
    $style .= " background-color: {$bgColor}; color: {$textColor};";
}

$containerStyle = "display: flex; align-items: center; justify-content: space-between; gap: 20px; max-width: 1200px; width: 100%; flex-wrap: wrap;";
$messageStyle = "flex: 1; margin: 0;";
$buttonsStyle = "display: flex; gap: 12px; flex-shrink: 0;";
$btnStyle = "padding: 8px 20px; border-radius: 30px; border: none; cursor: pointer; font-weight: 500; font-size: 14px; transition: all 0.2s ease;";
$acceptBtnStyle = $btnStyle . " background-color: {$accentColor}; color: #ffffff;";
$declineBtnStyle = $btnStyle . " background-color: transparent; color: {$textColor}; border: 1px solid currentColor;";
$linkStyle = " color: {$accentColor}; text-decoration: underline; margin-left: 8px;";
?>

<div id="<?php echo $customId; ?>" class="cookie-consent-container<?php echo $customClass; ?>" style="<?php echo $style; ?>" data-cookie-name="<?php echo $cookieName; ?>" data-cookie-expiry="<?php echo $cookieExpiryDays; ?>" data-auto-show="<?php echo $autoShow ? '1' : '0'; ?>" data-position="<?php echo $position; ?>">
    <div class="cookie-consent-inner" style="<?php echo $containerStyle; ?>">
        <div class="cookie-message" style="<?php echo $messageStyle; ?>">
            <?php echo $message; ?>
            <?php if ($showPolicyLink && $policyUrl && $policyLinkText) { ?>
                <a href="<?php echo $policyUrl; ?>" target="_blank" style="<?php echo $linkStyle; ?>"><?php echo $policyLinkText; ?></a>
            <?php } ?>
        </div>
        <div class="cookie-buttons" style="<?php echo $buttonsStyle; ?>">
            <button type="button" class="cookie-accept-btn" style="<?php echo $acceptBtnStyle; ?>"><?php echo $acceptText; ?></button>
            <button type="button" class="cookie-decline-btn" style="<?php echo $declineBtnStyle; ?>"><?php echo $declineText; ?></button>
        </div>
    </div>
</div>