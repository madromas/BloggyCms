<?php add_admin_css('templates/default/admin/assets/css/controllers/blocked.css'); ?>

<div class="blocked-container">
    <div class="blocked-icon">🚫</div>
    <div class="blocked-title"><?php echo LANG_TEMPLATE_AUTH_BLOCKED_TITLE; ?></div>
    <div class="blocked-message"><?php echo LANG_TEMPLATE_AUTH_BLOCKED_MAX_ATTEMPTS; ?></div>
    <div class="blocked-message"><?php echo LANG_TEMPLATE_AUTH_BLOCKED_UNAVAILABLE; ?></div>
    <div class="blocked-time">
        <?php echo LANG_TEMPLATE_AUTH_BLOCKED_RESTORE_TIME; ?><br>
        <?= date('d.m.Y ' . LANG_TEMPLATE_AUTH_BLOCKED_AT . ' H:i:s', $unlockTime) ?>
    </div>
    <div class="blocked-message">
        <?php echo LANG_TEMPLATE_AUTH_BLOCKED_REMAINING; ?> <strong><?= $remainingMinutes ?> <?php echo LANG_TEMPLATE_AUTH_BLOCKED_MINUTES; ?></strong>
    </div>
    <div class="attempts-info">
        <?php echo LANG_TEMPLATE_AUTH_BLOCKED_SECURITY_INFO; ?>
    </div>
</div>

<?php ob_start(); ?>
<script>
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>
<?php admin_bottom_js(ob_get_clean()); ?>