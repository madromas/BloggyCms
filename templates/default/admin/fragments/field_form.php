<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', $isEdit ? 'pencil-square' : 'plus-circle', '24', '#000', 'me-2'); ?>
            <?php echo $isEdit ? 'Редактирование поля' : 'Создание поля'; ?>
        </h4>
        <a href="<?php echo ADMIN_URL; ?>/fragments/fields/<?php echo $fragment['id']; ?>" class="btn btn-outline-secondary btn-sm">
            <?php echo bloggy_icon('bs', 'arrow-left', '16', '#000', 'me-1'); ?>
            Назад к полям
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Название поля <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control" 
                                   value="<?php echo html($field['name'] ?? ''); ?>"
                                   required>
                            <div class="form-text">Отображаемое название поля в форме</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Системное имя <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="system_name" 
                                   class="form-control" 
                                   value="<?php echo html($field['system_name'] ?? ''); ?>"
                                   pattern="[a-z0-9_]+"
                                   required>
                            <div class="form-text">Только латиница, цифры и подчеркивание. Используется в шорткодах</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Тип поля <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" id="field-type-select">
                                <?php foreach ($fieldTypes as $type => $typeName) { ?>
                                    <option value="<?php echo html($type); ?>" 
                                            <?php echo (($field['type'] ?? 'string') == $type) ? 'selected' : ''; ?>>
                                        <?php echo html($typeName); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <input type="text" 
                                   name="description" 
                                   class="form-control" 
                                   value="<?php echo html($field['description'] ?? ''); ?>"
                                   placeholder="Необязательное описание поля">
                            <div class="form-text">Пояснение к полю для пользователя</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="is_required" 
                                       class="form-check-input" 
                                       id="is_required"
                                       value="1"
                                       <?php echo (!empty($field['is_required'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_required">Обязательное поле</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="is_active" 
                                       class="form-check-input" 
                                       id="is_active"
                                       value="1"
                                       <?php echo (!isset($field['is_active']) || $field['is_active'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Активно</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="show_in_list" 
                                       class="form-check-input" 
                                       id="show_in_list"
                                       value="1"
                                       <?php echo (!empty($field['show_in_list'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="show_in_list">Показывать в списке</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3" id="field-config-container">
                    <label class="form-label">Настройки поля</label>
                    <div id="field-config-content">
                        <?php 
                        $fieldManager = new \FieldManager($this->db);
                        $currentConfig = isset($field['config']) ? $field['config'] : [];
                        echo $fieldManager->getFieldSettingsForm($field['type'] ?? 'string', $currentConfig);
                        ?>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="<?php echo ADMIN_URL; ?>/fragments/fields/<?php echo $fragment['id']; ?>" class="btn btn-secondary">
                        Отмена
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <?php echo bloggy_icon('bs', 'check-lg', '16', '#fff', 'me-2'); ?>
                        <?php echo $isEdit ? 'Сохранить' : 'Создать'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php add_admin_js('templates/default/admin/assets/js/controllers/fields-form.js'); ?>