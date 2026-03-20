<?php
/**
 * @name С Bootstrap иконками
 * @description Шаблон формы с SVG иконками из набора Bootstrap
 * @version 1.0.0
 */
?>

<?php echo add_frontend_css('/templates/default/front/assets/forms/css/iconic.css'); ?>

<div class="form-iconic">
    <div class="form-wrapper">
        <?php if (!empty($formName)) { ?>
        <div class="form-title-section">
            <div class="title-icon">
                <?php echo bloggy_icon('bs', 'pencil-square', '24 24'); ?>
            </div>
            <h2><?php echo html($formName); ?></h2>
        </div>
        <?php } ?>
        
        <form action="<?php echo html($actionUrl); ?>" method="POST" class="modern-form" enctype="multipart/form-data" id="form-<?php echo html($formSlug); ?>" data-ajax="true">
            
            <input type="hidden" name="form_id" value="<?php echo html($formId); ?>">
            <input type="hidden" name="form_slug" value="<?php echo html($formSlug); ?>">
            <?php if (!empty($csrfToken)) { ?>
                <input type="hidden" name="csrf_token" value="<?php echo html($csrfToken); ?>">
            <?php } ?>
            
            <?php 
            $fieldIcons = [
                'text' => 'file-earmark',
                'email' => 'envelope',
                'tel' => 'telephone',
                'number' => '123',
                'date' => 'calendar',
                'textarea' => 'pencil-square',
                'select' => 'list',
                'checkbox' => 'check-square',
                'radio' => 'radio-button',
                'file' => 'paperclip',
                'password' => 'lock'
            ];
            ?>
            
            <?php foreach ($structure as $field) { ?>
                <?php if ($field['type'] === 'hidden') { ?>
                    <input type="hidden" 
                           name="<?php echo html($field['name']); ?>" 
                           value="<?php echo html($field['default_value'] ?? ''); ?>">
                <?php } elseif ($field['type'] === 'submit') { ?>
                    <?php if (!empty($captchaHtml)) { ?>
                        <div class="form-group captcha-group">
                            <?php echo $captchaHtml; ?>
                        </div>
                    <?php } ?>
                        <button type="submit" class="iconic-submit">
                            <?php echo bloggy_icon('bs', 'send', '18 18', null, 'submit-icon'); ?>
                            <?php echo html($field['label'] ?? 'Отправить'); ?>
                        </button>
                <?php } else { ?>
                    <div class="iconic-field">
                        <?php if (!empty($options['show_labels']) && !empty($field['label'])) { ?>
                            <label for="field-<?php echo html($field['name']); ?>">
                                <span class="field-icon">
                                    <?php echo bloggy_icon('bs', $fieldIcons[$field['type']] ?? 'pin', '18 18'); ?>
                                </span>
                                <span class="field-label">
                                    <?php echo html($field['label']); ?>
                                        <?php if (!empty($field['required'])) { ?>
                                            <span class="required">*</span>
                                        <?php } ?>
                                </span>
                            </label>
                        <?php } ?>
                        
                        <div class="field-input-wrapper">
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
                                    <option value=""><?php echo html($placeholder ?: 'Выберите'); ?></option>
                                    <?php foreach ($field['options'] ?? [] as $option) { ?>
                                        <option value="<?php echo html($option['value'] ?? ''); ?>">
                                            <?php echo html($option['label'] ?? ''); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php } elseif ($field['type'] === 'checkbox') { ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="<?php echo html($field['name']); ?>" value="<?php echo html($field['checkbox_value'] ?? '1'); ?>" <?php echo $required; ?>>
                                    <span><?php echo html($field['label']); ?></span>
                                </label>
                            <?php } elseif ($field['type'] === 'file') { ?>
                                <input type="file" name="<?php echo html($field['name']); ?>" <?php echo $required; ?>>
                            <?php } ?>
                        </div>
                        
                        <?php if (!empty($options['show_descriptions']) && !empty($field['description'])) { ?>
                            <small class="field-hint"><?php echo html($field['description']); ?></small>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </form>
    </div>
</div>