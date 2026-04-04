<?php
    add_admin_js('templates/default/admin/assets/js/controllers/fields-form.js');
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'input-cursor-text', '24', '#000', 'me-2'); ?>
            Поля фрагмента: <?php echo html($fragment['name']); ?>
        </h4>
        <div>
            <a href="<?php echo ADMIN_URL; ?>/fragments/edit/<?php echo $fragment['id']; ?>" class="btn btn-outline-secondary btn-sm me-2">
                <?php echo bloggy_icon('bs', 'arrow-left', '16', '#000', 'me-1'); ?>
                Назад к фрагменту
            </a>
            <a href="<?php echo ADMIN_URL; ?>/fragments/entries/<?php echo $fragment['id']; ?>" class="btn btn-outline-info btn-sm">
                <?php echo bloggy_icon('bs', 'list-ul', '16', '#000', 'me-1'); ?>
                Записи
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        <div class="d-flex">
            <?php echo bloggy_icon('bs', 'info-circle', '16', '#000', 'me-2 mt-1'); ?>
            <div>
                <strong>Как это работает</strong><br>
                Созданные здесь поля будут использоваться при добавлении записей во фрагмент.
                При выводе фрагмента на сайте, значения полей можно получить через шорткоды:
                <code>{field:имя_поля}</code> и <code>{field_display:имя_поля}</code>
            </div>
        </div>
    </div>

    <form method="POST">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Список полей</h5>
                <button type="button" class="btn btn-primary btn-sm" id="add-field-btn">
                    <?php echo bloggy_icon('bs', 'plus-lg', '16', '#fff', 'me-1'); ?>
                    Добавить поле
                </button>
            </div>
            <div class="card-body">
                <div id="fields-container" class="sortable-fields">
                    <?php if (empty($fields)): ?>
                        <div class="text-center text-muted py-5" id="empty-fields-message">
                            <?php echo bloggy_icon('bs', 'inbox', '48', '#6C6C6C', 'mb-3'); ?>
                            <p>Нет созданных полей</p>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField()">
                                <?php echo bloggy_icon('bs', 'plus-lg', '16', '#000', 'me-1'); ?>
                                Добавить поле
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($fields as $index => $field): ?>
                            <div class="field-item card mb-3" data-index="<?php echo $index; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h6 class="mb-0">
                                            <?php echo bloggy_icon('bs', 'input-cursor', '16', '#000', 'me-2'); ?>
                                            <?php echo html($field['name']); ?>
                                            <span class="badge bg-secondary ms-2"><?php echo get_field_type_name($field['type']); ?></span>
                                        </h6>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary field-handle">
                                                <?php echo bloggy_icon('bs', 'arrows-move', '16', '#000'); ?>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger remove-field">
                                                <?php echo bloggy_icon('bs', 'trash', '16', '#000'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <label class="form-label small">Название поля</label>
                                                <input type="text" 
                                                    name="fields[<?php echo $index; ?>][name]" 
                                                    class="form-control form-control-sm field-name"
                                                    value="<?php echo html($field['name']); ?>"
                                                    placeholder="Название поля">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <label class="form-label small">Системное имя</label>
                                                <input type="text" 
                                                    name="fields[<?php echo $index; ?>][system_name]" 
                                                    class="form-control form-control-sm field-system-name"
                                                    value="<?php echo html($field['system_name']); ?>"
                                                    placeholder="system_name"
                                                    pattern="[a-z0-9_]+">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <label class="form-label small">Тип поля</label>
                                                <select name="fields[<?php echo $index; ?>][type]" 
                                                        class="form-select form-select-sm field-type"
                                                        data-index="<?php echo $index; ?>">
                                                    <?php foreach ($fieldTypes as $type => $typeName): ?>
                                                        <option value="<?php echo $type; ?>" <?php echo $field['type'] === $type ? 'selected' : ''; ?>>
                                                            <?php echo html($typeName); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="form-label small">Описание</label>
                                                <textarea name="fields[<?php echo $index; ?>][description]" 
                                                        class="form-control form-control-sm" 
                                                        rows="2"><?php echo html($field['description'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                        name="fields[<?php echo $index; ?>][is_required]" 
                                                        class="form-check-input"
                                                        id="required_<?php echo $index; ?>"
                                                        value="1"
                                                        <?php echo ($field['is_required'] ?? 0) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label small" for="required_<?php echo $index; ?>">
                                                        Обязательное поле
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                        name="fields[<?php echo $index; ?>][show_in_list]" 
                                                        class="form-check-input"
                                                        id="show_list_<?php echo $index; ?>"
                                                        value="1"
                                                        <?php echo ($field['show_in_list'] ?? 0) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label small" for="show_list_<?php echo $index; ?>">
                                                        Показывать в списке записей
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="fields[<?php echo $index; ?>][sort_order]" class="field-order" value="<?php echo $field['sort_order'] ?? $index; ?>">
                                    <input type="hidden" name="fields[<?php echo $index; ?>][config]" class="field-config" value='<?php echo html(json_encode($field['config'] ?? [])); ?>'>
                                    
                                    <!-- ИЗМЕНЕНИЕ ЗДЕСЬ: Убираем прямой PHP вызов getSettingsForm() -->
                                    <div class="field-settings mt-3" data-index="<?php echo $index; ?>" data-type="<?php echo $field['type']; ?>">
                                        <div class="alert alert-info small">Загрузка настроек поля...</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <span id="fields-count"><?php echo count($fields); ?></span> полей
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <?php echo bloggy_icon('bs', 'check-lg', '20', '#fff', 'me-2'); ?>
                        Сохранить поля
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php ob_start(); ?>
<script>
let fieldIndex = <?php echo count($fields); ?>;

// НОВАЯ ФУНКЦИЯ: Загрузка настроек для всех существующих полей
async function loadAllFieldSettings() {
    const fieldItems = document.querySelectorAll('.field-item');
    
    for (const fieldItem of fieldItems) {
        const typeSelect = fieldItem.querySelector('.field-type');
        const settingsContainer = fieldItem.querySelector('.field-settings');
        const configInput = fieldItem.querySelector('.field-config');
        
        if (typeSelect && settingsContainer) {
            const type = typeSelect.value;
            let currentConfig = {};
            
            try {
                currentConfig = JSON.parse(configInput.value);
            } catch(e) {
                console.error('Error parsing config:', e);
            }
            
            try {
                const response = await fetch('<?php echo ADMIN_URL; ?>/fields/get-settings/' + type, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'config=' + encodeURIComponent(JSON.stringify(currentConfig))
                });
                
                if (response.ok) {
                    const html = await response.text();
                    settingsContainer.innerHTML = html;
                    settingsContainer.setAttribute('data-type', type);
                } else {
                    settingsContainer.innerHTML = '<div class="alert alert-danger small">Ошибка загрузки настроек поля</div>';
                }
            } catch (error) {
                console.error('Error loading settings for field:', error);
                settingsContainer.innerHTML = '<div class="alert alert-danger small">Ошибка загрузки настроек поля</div>';
            }
        }
    }
}

function addField() {
    const container = document.getElementById('fields-container');
    const emptyMessage = document.getElementById('empty-fields-message');
    
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    const newField = document.createElement('div');
    newField.className = 'field-item card mb-3';
    newField.setAttribute('data-index', fieldIndex);
    newField.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <?php echo bloggy_icon('bs', 'input-cursor', '16', '#000', 'me-2'); ?>
                    Новое поле
                    <span class="badge bg-secondary ms-2">Текст</span>
                </h6>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary field-handle">
                        <?php echo bloggy_icon('bs', 'arrows-move', '16', '#000'); ?>
                    </button>
                    <button type="button" class="btn btn-outline-danger remove-field">
                        <?php echo bloggy_icon('bs', 'trash', '16', '#000'); ?>
                    </button>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">Название поля</label>
                        <input type="text" 
                               name="fields[${fieldIndex}][name]" 
                               class="form-control form-control-sm field-name"
                               placeholder="Название поля">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">Системное имя</label>
                        <input type="text" 
                               name="fields[${fieldIndex}][system_name]" 
                               class="form-control form-control-sm field-system-name"
                               placeholder="system_name"
                               pattern="[a-z0-9_]+">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">Тип поля</label>
                        <select name="fields[${fieldIndex}][type]" 
                                class="form-select form-select-sm field-type"
                                data-index="${fieldIndex}">
                            <?php foreach ($fieldTypes as $type => $typeName): ?>
                                <option value="<?php echo $type; ?>"><?php echo html($typeName); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label small">Описание</label>
                        <textarea name="fields[${fieldIndex}][description]" 
                                  class="form-control form-control-sm" 
                                  rows="2"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="fields[${fieldIndex}][is_required]" 
                                   class="form-check-input"
                                   id="required_${fieldIndex}"
                                   value="1">
                            <label class="form-check-label small" for="required_${fieldIndex}">
                                Обязательное поле
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="fields[${fieldIndex}][show_in_list]" 
                                   class="form-check-input"
                                   id="show_list_${fieldIndex}"
                                   value="1">
                            <label class="form-check-label small" for="show_list_${fieldIndex}">
                                Показывать в списке записей
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="fields[${fieldIndex}][sort_order]" class="field-order" value="${fieldIndex}">
            <input type="hidden" name="fields[${fieldIndex}][config]" class="field-config" value='{}'>
            
            <div class="field-settings mt-3" data-index="${fieldIndex}" data-type="string">
                <div class="alert alert-info small">Выберите тип поля для настройки</div>
            </div>
        </div>
    `;
    
    container.appendChild(newField);
    
    // Инициализация обработчиков
    initFieldHandlers(newField);
    
    fieldIndex++;
    updateFieldsCount();
}

function initFieldHandlers(fieldElement) {
    // Удаление поля
    const removeBtn = fieldElement.querySelector('.remove-field');
    removeBtn.addEventListener('click', function() {
        if (confirm('Удалить это поле?')) {
            fieldElement.remove();
            updateFieldsCount();
        }
    });
    
    // Изменение типа поля
    const typeSelect = fieldElement.querySelector('.field-type');
    const settingsContainer = fieldElement.querySelector('.field-settings');
    const index = typeSelect.dataset.index;
    const configInput = fieldElement.querySelector('.field-config');
    
    typeSelect.addEventListener('change', async function() {
        const type = this.value;
        let currentConfig = {};
        
        try {
            if (configInput && configInput.value) {
                currentConfig = JSON.parse(configInput.value);
            }
        } catch(e) {
            console.error('Error parsing config:', e);
        }
        
        settingsContainer.innerHTML = '<div class="alert alert-info small">Загрузка настроек...</div>';
        
        try {
            const response = await fetch('<?php echo ADMIN_URL; ?>/fields/get-settings/' + type, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'config=' + encodeURIComponent(JSON.stringify(currentConfig))
            });
            
            if (response.ok) {
                const html = await response.text();
                settingsContainer.innerHTML = html;
                settingsContainer.setAttribute('data-type', type);
            } else {
                settingsContainer.innerHTML = '<div class="alert alert-danger small">Ошибка загрузки настроек поля</div>';
            }
        } catch (error) {
            console.error('Error loading settings:', error);
            settingsContainer.innerHTML = '<div class="alert alert-danger small">Ошибка загрузки настроек поля</div>';
        }
    });
    
    // Не вызываем автоматически change для существующих полей, так как они загружаются через loadAllFieldSettings
    if (!fieldElement.getAttribute('data-loaded')) {
        // Для новых полей вызываем change
        typeSelect.dispatchEvent(new Event('change'));
        fieldElement.setAttribute('data-loaded', 'true');
    }
}

function updateFieldsCount() {
    const count = document.querySelectorAll('.field-item').length;
    document.getElementById('fields-count').textContent = count;
}

document.addEventListener('DOMContentLoaded', async function() {
    // Инициализация обработчиков для существующих полей
    document.querySelectorAll('.field-item').forEach(fieldElement => {
        initFieldHandlers(fieldElement);
        // Отмечаем как загруженные, чтобы не вызывать change автоматически
        fieldElement.setAttribute('data-loaded', 'true');
    });
    
    // Загружаем настройки для всех существующих полей
    await loadAllFieldSettings();
    
    document.getElementById('add-field-btn').addEventListener('click', addField);
    
    // Сортировка полей
    if (typeof Sortable !== 'undefined') {
        const container = document.getElementById('fields-container');
        Sortable.create(container, {
            handle: '.field-handle',
            animation: 150,
            onEnd: function() {
                document.querySelectorAll('.field-item').forEach((item, idx) => {
                    const orderInput = item.querySelector('.field-order');
                    if (orderInput) {
                        orderInput.value = idx;
                    }
                });
            }
        });
    }
});
</script>
<?php admin_bottom_js(ob_get_clean()); ?>