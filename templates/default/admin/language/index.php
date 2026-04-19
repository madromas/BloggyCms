<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'translate', '24', '#000', 'me-2'); ?>
            Языковые настройки
        </h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?php echo ADMIN_URL; ?>/language/save" id="language-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <?php echo bloggy_icon('bs', 'shield-lock', '16', '#0d6efd', 'me-1'); ?>
                                Язык панели управления
                            </label>
                            <select name="admin_language" class="form-select">
                                <?php foreach ($availableLocales as $locale) { ?>
                                    <option value="<?php echo $locale['code']; ?>" 
                                        <?php echo $settings['admin_language'] === $locale['code'] ? 'selected' : ''; ?>>
                                        <?php echo html($locale['name']); ?> (<?php echo $locale['code']; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="form-text">Язык интерфейса административной панели</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <?php echo bloggy_icon('bs', 'globe2', '16', '#198754', 'me-1'); ?>
                                Язык сайта (фронтенд)
                            </label>
                            <select name="site_language" class="form-select">
                                <?php foreach ($availableLocales as $locale) { ?>
                                    <option value="<?php echo $locale['code']; ?>" 
                                        <?php echo $settings['site_language'] === $locale['code'] ? 'selected' : ''; ?>>
                                        <?php echo html($locale['name']); ?> (<?php echo $locale['code']; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="form-text">Язык отображения контента для посетителей</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="allow_user_switch" 
                               name="allow_user_switch" value="1"
                               <?php echo $settings['allow_user_switch'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="allow_user_switch">
                            <?php echo bloggy_icon('bs', 'people', '16', '#ffc107', 'me-1'); ?>
                            Разрешить пользователям изменять язык сайта
                        </label>
                    </div>
                    <div class="form-text">
                        Авторизованные пользователи смогут выбрать язык интерфейса в своём профиле
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <?php echo bloggy_icon('bs', 'info-circle', '16', '#0d6efd', 'me-2'); ?>
                    <strong>Важно:</strong> Изменение языка панели управления вступит в силу после перезагрузки страницы.
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <?php echo bloggy_icon('bs', 'check-lg', '16', '#fff', 'me-2'); ?>
                        Сохранить настройки
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    document.getElementById('language-form').addEventListener('submit', function(e) {
        const btn = document.getElementById('save-btn');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Сохранение...';
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }, 3000);
    });
</script>
<?php admin_bottom_js(ob_get_clean()); ?>