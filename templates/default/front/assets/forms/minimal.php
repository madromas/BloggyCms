<?php
/**
 * @name Минималистичный
 * @description Чистый шаблон без лишних элементов
 * @version 1.0.0
 */
?>

<?php echo add_frontend_css('/templates/default/front/assets/forms/css/minimal.css'); ?>

<div class="form-minimal">
    <?php if (!empty($formName)) { ?>
        <h2 class="form-title"><?php echo html($formName); ?></h2>
    <?php } ?>
    
    <?php if (!empty($formDescription)) { ?>
        <p class="form-description"><?php echo nl2br(html($formDescription)); ?></p>
    <?php } ?>
    
    <form action="<?php echo html($actionUrl); ?>" method="POST" class="modern-form" enctype="multipart/form-data" id="form-<?php echo html($formSlug); ?>" data-ajax="true">
        
        <input type="hidden" name="form_id" value="<?php echo html($formId); ?>">
        <input type="hidden" name="form_slug" value="<?php echo html($formSlug); ?>">
        <?php if (!empty($csrfToken)) { ?>
            <input type="hidden" name="csrf_token" value="<?php echo html($csrfToken); ?>">
        <?php } ?>
        
        <?php foreach ($structure as $field) { ?>
            <?php if ($field['type'] === 'hidden') { ?>
                <input type="hidden" name="<?php echo html($field['name']); ?>" value="<?php echo html($field['default_value'] ?? ''); ?>">
            <?php } elseif ($field['type'] === 'submit') { ?>
                <?php if (!empty($captchaHtml)) { ?>
                    <div class="form-group captcha-group">
                        <?php echo $captchaHtml; ?>
                    </div>
                <?php } ?>
                <button type="submit" class="minimal-btn">
                    <?php echo html($field['label'] ?? 'Отправить'); ?>
                </button>
            <?php } else { ?>
                <div class="minimal-field">
                    <?php if (!empty($options['show_labels']) && !empty($field['label'])) { ?>
                        <label for="field-<?php echo html($field['name']); ?>">
                            <?php echo html($field['label']); ?>
                            <?php if (!empty($field['required'])) { ?><span>*</span><?php } ?>
                        </label>
                    <?php } ?>
                    
                    <?php
                        $value = html($field['default_value'] ?? '');
                        $placeholder = html($field['placeholder'] ?? '');
                        $required = !empty($field['required']) ? 'required' : '';
                    ?>
                    
                    <?php if (in_array($field['type'], ['text', 'email', 'tel', 'number', 'password', 'date'])) { ?>
                        <input type="<?php echo html($field['type']); ?>" id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required; ?>>
                    <?php } elseif ($field['type'] === 'textarea') { ?>
                        <textarea id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" rows="<?php echo html($field['rows'] ?? 3); ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required; ?>><?php echo $value; ?></textarea>
                    <?php } elseif ($field['type'] === 'select') { ?>
                        <select id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" <?php echo $required; ?>>
                            <option value=""><?php echo html($placeholder ?: 'Выбрать'); ?></option>
                            <?php foreach ($field['options'] ?? [] as $option) { ?>
                                <option value="<?php echo html($option['value'] ?? ''); ?>">
                                    <?php echo html($option['label'] ?? ''); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php } elseif ($field['type'] === 'checkbox') { ?>
                        <label class="checkbox-inline"> <input type="checkbox" name="<?php echo html($field['name']); ?>" value="<?php echo html($field['checkbox_value'] ?? '1'); ?>" <?php echo $required; ?>>
                            <?php echo html($field['label']); ?>
                        </label>
                    <?php } elseif ($field['type'] === 'file') { ?>
                        <input type="file" name="<?php echo html($field['name']); ?>" <?php echo $required; ?>>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </form>
</div>