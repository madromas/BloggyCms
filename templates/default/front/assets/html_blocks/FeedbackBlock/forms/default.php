<?php
/**
 * @name Стандартный шаблон
 * @description Минималистичный шаблон формы для блока обратной связи
 * @version 1.0.0
 */
?>

<form action="<?php echo html($actionUrl); ?>" 
      method="POST" 
      class="feedback-form" 
      enctype="multipart/form-data"
      id="feedback-form-<?php echo html($formSlug); ?>"
      data-ajax="true">
    
    <input type="hidden" name="form_id" value="<?php echo html($formId); ?>">
    <input type="hidden" name="form_slug" value="<?php echo html($formSlug); ?>">
    
    <?php if (!empty($csrfToken)): ?>
    <input type="hidden" name="csrf_token" value="<?php echo html($csrfToken); ?>">
    <?php endif; ?>
    
    <div class="row g-3">
        <?php foreach ($structure as $field): ?>
            <?php if ($field['type'] === 'hidden'): ?>
                <input type="hidden" 
                       name="<?php echo html($field['name']); ?>" 
                       value="<?php echo html($field['default_value'] ?? ''); ?>">
            
            <?php elseif ($field['type'] === 'submit'): ?>
                <?php if (!empty($captchaHtml)): ?>
                <div class="col-12">
                    <?php echo $captchaHtml; ?>
                </div>
                <?php endif; ?>
                <div class="col-12">
                    <button type="submit" class="feedback-submit-btn">
                        <?php echo html($field['label'] ?? 'Отправить'); ?>
                        <?php if(function_exists('bloggy_icon')): ?>
                            <?php echo bloggy_icon('ti', 'arrow-narrow-right', '18 18', 'currentColor', ''); ?>
                        <?php endif; ?>
                    </button>
                </div>
            
            <?php else: ?>
                <?php 
                $colClass = 'col-lg-6 col-md-6';
                if ($field['type'] === 'textarea') $colClass = 'col-lg-12';
                $value = html($field['default_value'] ?? '');
                $placeholder = html($field['placeholder'] ?? '');
                $required = !empty($field['required']) ? 'required' : '';
                ?>
                <div class="<?php echo $colClass; ?>">
                    <input type="<?php echo html($field['type']); ?>" 
                           name="<?php echo html($field['name']); ?>"
                           class="form-control"
                           placeholder="<?php echo $placeholder; ?>"
                           value="<?php echo $value; ?>"
                           <?php echo $required; ?>>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('feedback-form-<?php echo html($formSlug); ?>');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Отправка...';
        
        const formData = new FormData(form);
        fetch(form.action, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    form.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    if (data.redirect) setTimeout(() => window.location.href = data.redirect, 2000);
                } else {
                    // При ошибке CSRF — перезагружаем страницу
                    if (data.message.indexOf('токен') !== -1 || data.message.indexOf('CSRF') !== -1) {
                        alert('Сессия истекла. Страница будет перезагружена.');
                        window.location.reload();
                    } else {
                        alert(data.message);
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                }
            })
            .catch(() => {
                alert('Ошибка отправки');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
});
</script>