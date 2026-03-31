<?php
    add_admin_js('templates/default/admin/assets/js/controllers/image-upload.js');
    $fieldModel = new FieldModel($this->db);
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', $isEdit ? 'pencil-square' : 'plus-circle', '24', '#000', 'me-2'); ?>
            <?php echo $isEdit ? 'Редактирование записи' : 'Создание записи'; ?>
        </h4>
        <div>
            <a href="<?php echo ADMIN_URL; ?>/fragments/entries/<?php echo $fragment['id']; ?>" class="btn btn-outline-secondary btn-sm">
                <?php echo bloggy_icon('bs', 'arrow-left', '16', '#000', 'me-1'); ?>
                Назад к записям
            </a>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">Поля фрагмента</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($fields)): ?>
                            <div class="alert alert-warning">
                                <?php echo bloggy_icon('bs', 'exclamation-triangle', '16', '#000', 'me-2'); ?>
                                У этого фрагмента нет полей. 
                                <a href="<?php echo ADMIN_URL; ?>/fragments/fields/<?php echo $fragment['id']; ?>" class="alert-link">
                                    Добавьте поля
                                </a> для создания записей.
                            </div>
                        <?php else: ?>
                            <?php foreach ($fields as $field): ?>
                                <div class="mb-4">
                                    <label class="form-label">
                                        <?php echo html($field['name']); ?>
                                        <?php if ($field['is_required']): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif; ?>
                                    </label>
                                    
                                    <?php
                                    $currentValue = $entry['data'][$field['system_name']] ?? null;
                                    $config = $field['config'] ?? [];
                                    ?>
                                    
                                    <?php echo $fieldModel->renderFieldInput(
                                        $field,
                                        $currentValue,
                                        'fragment_entry',
                                        $entry['id'] ?? 0
                                    ); ?>
                                    
                                    <?php if (!empty($field['description'])): ?>
                                        <div class="form-text"><?php echo html($field['description']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <?php echo bloggy_icon('bs', 'info-circle', '20', '#000', 'me-2'); ?>
                            Информация
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Статус</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo ($entry['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Активна</option>
                                <option value="inactive" <?php echo ($entry['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Неактивна</option>
                            </select>
                            <div class="form-text">
                                Неактивные записи не отображаются на сайте
                            </div>
                        </div>
                        
                        <?php if ($isEdit && isset($entry)): ?>
                            <div class="text-muted small">
                                <div class="mb-1">
                                    <strong>Создана:</strong> <?php echo date('d.m.Y H:i', strtotime($entry['created_at'])); ?>
                                </div>
                                <?php if ($entry['updated_at'] != $entry['created_at']): ?>
                                    <div class="mb-1">
                                        <strong>Обновлена:</strong> <?php echo date('d.m.Y H:i', strtotime($entry['updated_at'])); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <strong>ID записи:</strong> #<?php echo $entry['id']; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <?php echo bloggy_icon('bs', 'code', '20', '#000', 'me-2'); ?>
                            Шорткоды для вывода
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            При выводе фрагмента на сайте используйте:
                        </p>
                        
                        <?php foreach ($fields as $field): ?>
                            <div class="bg-light p-2 rounded mb-2">
                                <code class="small">{field:<?php echo html($field['system_name']); ?>}</code>
                                <div class="text-muted small mt-1">
                                    Вывод значения поля "<?php echo html($field['name']); ?>"
                                </div>
                                <code class="small d-block mt-1">{field_display:<?php echo html($field['system_name']); ?>}</code>
                                <div class="text-muted small">
                                    Вывод отрендеренного значения (изображения, ссылки и т.д.)
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($fields)): ?>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <?php echo bloggy_icon('bs', 'check-lg', '20', '#fff', 'me-2'); ?>
                    <?php echo $isEdit ? 'Сохранить изменения' : 'Создать запись'; ?>
                </button>
                <a href="<?php echo ADMIN_URL; ?>/fragments/entries/<?php echo $fragment['id']; ?>" class="btn btn-outline-secondary">
                    Отмена
                </a>
            </div>
        <?php endif; ?>
    </form>
</div>