<?php
/**
 * @name С иконками
 * @description Шаблон формы с иконками для каждого поля
 * @version 1.0.0
 */
?>

<?php echo add_frontend_css('/templates/default/front/assets/html_blocks/FeedbackBlock/css/iconic-form.css'); ?>

<form action="<?php echo html($actionUrl); ?>" 
      method="POST" 
      class="feedback-form iconic-form"
      id="feedback-form-<?php echo html($formSlug); ?>"
      data-ajax="true">
    
    <input type="hidden" name="form_id" value="<?php echo html($formId); ?>">
    <input type="hidden" name="form_slug" value="<?php echo html($formSlug); ?>">
    <?php if (!empty($csrfToken)): ?>
    <input type="hidden" name="csrf_token" value="<?php echo html($csrfToken); ?>">
    <?php endif; ?>
    
    <?php 
    $fieldIcons = [
        'name' => 'user-line',
        'email' => 'mail-line', 
        'phone' => 'phone-line',
        'tel' => 'phone-line',
        'subject' => 'file-text-line',
        'message' => 'edit-box-line',
    ];
    ?>
    
    <div class="row g-3">
        <?php foreach ($structure as $field): ?>
            <?php if ($field['type'] === 'hidden'): ?>
                <input type="hidden" name="<?php echo html($field['name']); ?>" 
                       value="<?php echo html($field['default_value'] ?? ''); ?>">
            
            <?php elseif ($field['type'] === 'submit'): ?>
                <?php if (!empty($captchaHtml)): ?>
                <div class="col-12"><?php echo $captchaHtml; ?></div>
                <?php endif; ?>
                <div class="col-12">
                    <button type="submit" class="feedback-submit-btn iconic-btn">
                        <?php if(function_exists('bloggy_icon')): ?>
                            <?php echo bloggy_icon('ri', 'send-plane-fill', '18 18', 'currentColor', ''); ?>
                        <?php endif; ?>
                        <?php echo html($field['label'] ?? 'Отправить'); ?>
                    </button>
                </div>
            
            <?php else: ?>
                <?php 
                $colClass = 'col-lg-6 col-md-6';
                if ($field['type'] === 'textarea') $colClass = 'col-lg-12';
                $value = html($field['default_value'] ?? '');
                $placeholder = html($field['placeholder'] ?? '');
                $required = !empty($field['required']) ? 'required' : '';
                $iconKey = strtolower($field['name']);
                $icon = $fieldIcons[$iconKey] ?? 'input-field';
                ?>
                <div class="<?php echo $colClass; ?>">
                    <div class="input-with-icon">
                        <?php if(function_exists('bloggy_icon')): ?>
                            <span class="input-icon"><?php echo bloggy_icon('ri', $icon, '18 18', 'currentColor', ''); ?></span>
                        <?php endif; ?>
                        <input type="<?php echo html($field['type']); ?>" 
                               name="<?php echo html($field['name']); ?>"
                               class="form-control"
                               placeholder="<?php echo $placeholder; ?>"
                               value="<?php echo $value; ?>"
                               <?php echo $required; ?>>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</form>