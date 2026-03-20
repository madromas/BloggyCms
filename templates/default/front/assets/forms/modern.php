<?php
/**
 * @name Современный
 * @description Стильный шаблон с градиентами и тенями
 * @version 2.0.0
 */
?>

<?php echo add_frontend_css('/templates/default/front/assets/forms/css/modern.css'); ?>

<div class="form-container form-modern">
    <div class="form-card">
        <?php if (!empty($formDescription)) { ?>
            <div class="form-header">
                <h3><?php echo html($formName); ?></h3>
                <p><?php echo nl2br(html($formDescription)); ?></p>
            </div>
        <?php } ?>
        
        <form action="<?php echo html($actionUrl); ?>" method="POST" class="modern-form" enctype="multipart/form-data" id="form-<?php echo html($formSlug); ?>" data-ajax="true">
            
            <input type="hidden" name="form_id" value="<?php echo html($formId); ?>">
            <input type="hidden" name="form_slug" value="<?php echo html($formSlug); ?>">
            <?php if (!empty($csrfToken)) { ?>
                <input type="hidden" name="csrf_token" value="<?php echo html($csrfToken); ?>">
            <?php } ?>
            
            <div class="form-fields">
                <?php foreach ($structure as $field) { ?>
                    <?php if ($field['type'] === 'hidden') { ?>
                        <input type="hidden" name="<?php echo html($field['name']); ?>" value="<?php echo html($field['default_value'] ?? ''); ?>">
                    <?php } elseif ($field['type'] === 'submit') { ?>
                        <div class="form-group submit-group">
                            <?php if (!empty($captchaHtml)) { ?>
                                <div class="form-group captcha-group">
                                    <?php echo $captchaHtml; ?>
                                </div>
                            <?php } ?>
                            <button type="submit" class="btn-submit">
                                <?php echo html($field['label'] ?? 'Отправить'); ?>
                            </button>
                        </div>
                    <?php } else { ?>
                        <div class="form-group field-<?php echo html($field['type']); ?>">
                            <?php if (!empty($options['show_labels']) && !empty($field['label'])) { ?>
                                <label for="field-<?php echo html($field['name']); ?>" class="form-label">
                                    <?php echo html($field['label']); ?>
                                    <?php if (!empty($field['required'])) { ?>
                                        <span class="required">*</span>
                                    <?php } ?>
                                </label>
                            <?php } ?>
                            
                            <?php
                                $value = html($field['default_value'] ?? '');
                                $placeholder = html($field['placeholder'] ?? '');
                                $required = !empty($field['required']) ? 'required' : '';
                            ?>
                            
                            <?php if (in_array($field['type'], ['text', 'email', 'tel', 'number', 'password', 'date'])) { ?>
                                <input type="<?php echo html($field['type']); ?>" id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" class="form-input" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required; ?>>
                            <?php } elseif ($field['type'] === 'textarea') { ?>
                                <textarea id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" class="form-textarea" rows="<?php echo html($field['rows'] ?? 4); ?>" placeholder="<?php echo $placeholder; ?>" <?php echo $required; ?>></textarea>
                            <?php } elseif ($field['type'] === 'select') { ?>
                                <select id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" class="form-select" <?php echo $required; ?>>
                                    <option value=""><?php echo html($placeholder ?: 'Выберите...'); ?></option>
                                    <?php foreach ($field['options'] ?? [] as $option) { ?>
                                        <option value="<?php echo html($option['value'] ?? ''); ?>">
                                            <?php echo html($option['label'] ?? ''); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php } elseif ($field['type'] === 'checkbox') { ?>
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" class="form-checkbox" value="<?php echo html($field['checkbox_value'] ?? '1'); ?>" <?php echo $required; ?>>
                                    <label for="field-<?php echo html($field['name']); ?>" class="checkbox-label">
                                        <?php echo html($field['label']); ?>
                                    </label>
                                </div>
                            <?php } elseif ($field['type'] === 'radio') { ?>
                                <div class="radio-group">
                                    <?php foreach ($field['options'] ?? [] as $index => $option) { ?>
                                    <div class="radio-item">
                                        <input type="radio" id="field-<?php echo html($field['name']); ?>-<?php echo $index; ?>" name="<?php echo html($field['name']); ?>" class="form-radio" value="<?php echo html($option['value'] ?? ''); ?>" <?php echo $required; ?>>
                                        <label for="field-<?php echo html($field['name']); ?>-<?php echo $index; ?>">
                                            <?php echo html($option['label'] ?? ''); ?>
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                            <?php } elseif ($field['type'] === 'file') { ?>
                                <input type="file" id="field-<?php echo html($field['name']); ?>" name="<?php echo html($field['name']); ?>" class="form-file" <?php if (!empty($field['multiple'])) { ?>multiple<?php } ?> <?php if (!empty($field['accept'])) { ?>accept="<?php echo html($field['accept']); ?>"<?php } ?> <?php echo $required; ?>>
                            <?php } ?>
                            
                            <?php if (!empty($options['show_descriptions']) && !empty($field['description'])) { ?>
                                <small class="field-description"><?php echo html($field['description']); ?></small>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </form>
    </div>
</div>