<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'ui-sortable', '24', '#000', 'me-2'); ?>
            Поля фрагмента: <?php echo html($fragment['name']); ?>
        </h4>
        <div class="d-flex gap-2">
            <a href="<?php echo ADMIN_URL; ?>/fragments/field/create/<?php echo $fragment['id']; ?>" class="btn btn-primary">
                <?php echo bloggy_icon('bs', 'plus-circle', '16', '#fff', 'me-2'); ?>
                Создать поле
            </a>
            <a href="<?php echo ADMIN_URL; ?>/fragments/edit/<?php echo $fragment['id']; ?>" class="btn btn-outline-secondary">
                <?php echo bloggy_icon('bs', 'arrow-left', '16', '#000', 'me-1'); ?>
                Назад к фрагменту
            </a>
        </div>
    </div>
    
    <?php if (empty($fields)) { ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <?php echo bloggy_icon('bs', 'ui-sortable', '48', '#6C6C6C'); ?>
                </div>
                <h5 class="text-muted mb-2">Нет созданных полей</h5>
                <p class="text-muted mb-3">Создайте первое поле для этого фрагмента</p>
                <a href="<?php echo ADMIN_URL; ?>/fragments/field/create/<?php echo $fragment['id']; ?>" class="btn btn-primary">
                    <?php echo bloggy_icon('bs', 'plus-circle', '16', '#fff', 'me-2'); ?>
                    Создать поле
                </a>
            </div>
        </div>
    <?php } else { ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <?php echo bloggy_icon('bs', 'info-circle', '16', '#5AAFC9', 'me-2'); ?>
                    <strong>Подсказка:</strong> Перетаскивайте поля за иконку <?php echo bloggy_icon('bs', 'grip-vertical', '16', '#2c2c2c', 'me-2'); ?> для изменения порядка отображения
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Название</th>
                                <th>Системное имя</th>
                                <th>Тип</th>
                                <th>Статус</th>
                                <th>В списке</th>
                                <th class="text-end">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-fields">
                            <?php foreach ($fields as $field) { ?>
                                <tr data-id="<?php echo $field['id']; ?>">
                                    <td class="text-center">
                                        <span class="field-handle" style="cursor: grab;">
                                            <?php echo bloggy_icon('bs', 'grip-vertical', '18', '#6C6C6C'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo html($field['name']); ?></strong>
                                        <?php if (!empty($field['description'])) { ?>
                                            <br><small class="text-muted"><?php echo html(mb_substr($field['description'], 0, 50)); ?></small>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <code><?php echo html($field['system_name']); ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo html($field['type']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($field['is_active']) { ?>
                                            <span class="badge bg-success">Активно</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Отключено</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($field['show_in_list']) { ?>
                                            <span class="badge bg-info">Да</span>
                                        <?php } else { ?>
                                            <span class="badge bg-light text-dark">Нет</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo ADMIN_URL; ?>/fragments/field/edit/<?php echo $field['id']; ?>" 
                                            class="btn btn-outline-primary"
                                            title="Редактировать">
                                                <?php echo bloggy_icon('bs', 'pencil', '16', '#000'); ?>
                                            </a>
                                            <a href="<?php echo ADMIN_URL; ?>/fragments/field/delete/<?php echo $field['id']; ?>" 
                                            class="btn btn-outline-danger"
                                            onclick="return confirm('Вы уверены, что хотите удалить поле «<?php echo html($field['name']); ?>»?')"
                                            title="Удалить">
                                                <?php echo bloggy_icon('bs', 'trash', '16', '#000'); ?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (!empty($fieldsForDisplay)) { ?>
                    <div class="mt-4">
                        <h6 class="mb-3">Поля, отображаемые в списке записей:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($fieldsForDisplay as $field) { ?>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <?php echo html($field['name']); ?>
                                    <code class="ms-1"><?php echo html($field['system_name']); ?></code>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                
                <?php if ($total > $perPage) { ?>
                <div class="mt-4">
                    <?php echo \PaginationHelper::simple($current_page, $pages, ADMIN_URL . '/fragments/fields/' . $fragment['id'] . '?page='); ?>
                </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php ob_start(); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('sortable-fields');
            if (tbody && typeof Sortable !== 'undefined') {
                new Sortable(tbody, {
                    handle: '.field-handle',
                    animation: 150,
                    onEnd: function() {
                        const items = [];
                        document.querySelectorAll('#sortable-fields tr').forEach((row, index) => {
                            items.push({
                                id: row.dataset.id,
                                order: index
                            });
                        });
                        
                        fetch('<?php echo ADMIN_URL; ?>/fragments/field/reorder', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ order: items })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                console.error('Reorder error:', data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    }
                });
            }
        });
    </script>
<?php admin_bottom_js(ob_get_clean()); ?>