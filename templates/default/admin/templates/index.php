<div class="template-designer">
    <div class="designer-header">
        <div class="header-left">
            <div class="header-icon">
                <?php echo bloggy_icon('bs', 'palette', '24', '#0d6efd'); ?>
            </div>
            <div class="header-info">
                <h1>Управление шаблонами</h1>
                <p>Редактирование файлов шаблонов в реальном времени</p>
            </div>
        </div>
        <div class="header-right">
            <a href="<?php echo ADMIN_URL; ?>/settings/cleanup-backups" class="btn-header btn-danger">
                <?php echo bloggy_icon('bs', 'trash', '14', '', 'me-1'); ?>
                Очистить бэкапы
            </a>
            <a href="<?php echo ADMIN_URL; ?>/settings?tab=site" class="btn-header btn-secondary">
                <?php echo bloggy_icon('bs', 'gear', '14', '', 'me-1'); ?>
                Настройки
            </a>
        </div>
    </div>

    <div class="designer-toolbar">
        <div class="toolbar-section">
            <div class="template-badge">
                <span class="badge-label">Активный шаблон:</span>
                <div class="template-selector-group">
                    <?php foreach ($templates as $template): ?>
                        <button class="template-pill template-selector <?php echo $template['name'] === $currentTemplate ? 'active' : ''; ?>" 
                                data-template="<?php echo $template['name']; ?>">
                            <?php echo bloggy_icon('bs', 'folder', '12', '', 'me-1'); ?>
                            <?php echo html(ucfirst($template['name'])); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="toolbar-section">
            <div class="search-box">
                <?php echo bloggy_icon('bs', 'search', '14', '#9ca3af', 'search-icon'); ?>
                <input type="text" id="searchFiles" placeholder="Поиск файлов...">
                <span class="search-shortcut">Ctrl+F</span>
            </div>
            <div class="view-switcher">
                <button class="view-switch active" id="switchTree" title="Дерево">
                    <?php echo bloggy_icon('bs', 'diagram-3', '16'); ?>
                </button>
                <button class="view-switch" id="switchList" title="Список">
                    <?php echo bloggy_icon('bs', 'list-ul', '16'); ?>
                </button>
            </div>
            <button class="toolbar-action" id="uploadFileBtn">
                <?php echo bloggy_icon('bs', 'upload', '14', '', 'me-1'); ?>
                Загрузить
            </button>
            <input type="file" id="fileUpload" style="display: none;">
            <button class="toolbar-action" id="refreshFilesBtn">
                <?php echo bloggy_icon('bs', 'arrow-clockwise', '14'); ?>
            </button>
        </div>
    </div>

    <div class="designer-main">
        <div class="explorer-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <?php echo bloggy_icon('bs', 'folder2-open', '14', '#0d6efd'); ?>
                    <span>Файлы шаблона</span>
                    <span class="file-counter" id="fileCounter">0</span>
                </div>
                <div class="panel-actions">
                    <button class="panel-action" id="collapseAllFolders" title="Свернуть всё">
                        <?php echo bloggy_icon('bs', 'chevron-bar-contract', '12'); ?>
                    </button>
                    <button class="panel-action" id="expandAllFolders" title="Развернуть всё">
                        <?php echo bloggy_icon('bs', 'chevron-bar-expand', '12'); ?>
                    </button>
                </div>
            </div>
            <div class="explorer-content" id="fileListContainer">
                <div id="fileList" class="file-list">
                    <div class="loading-state">
                        <div class="spinner"></div>
                        <p>Загрузка файлов...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="editor-panel">
            <div class="panel-header">
                <div class="panel-title" id="currentFileTitle">
                    <?php echo bloggy_icon('bs', 'file-code', '14', '#0d6efd'); ?>
                    <span>Редактор файлов</span>
                </div>
                <div class="panel-actions" id="editorActions" style="display: none;">
                    <button class="panel-action" id="refreshFile" title="Обновить">
                        <?php echo bloggy_icon('bs', 'arrow-clockwise', '14'); ?>
                    </button>
                    <button class="panel-action btn-save" id="saveFile" disabled title="Сохранить">
                        <?php echo bloggy_icon('bs', 'check-lg', '14'); ?>
                        <span>Сохранить</span>
                    </button>
                </div>
            </div>
            <div class="editor-content">
                <div id="editorContainer" style="display: none;">
                    <div id="codeEditor"></div>
                </div>
                <div id="editorPlaceholder" class="editor-placeholder">
                    <div class="placeholder-icon">
                        <?php echo bloggy_icon('bs', 'code-slash', '64', '#dee2e6'); ?>
                    </div>
                    <h4>Выберите файл для редактирования</h4>
                    <p>Нажмите на любой файл в левой панели, чтобы открыть его</p>
                </div>
            </div>
        </div>

        <div class="info-panel" id="fileInfoPanel" style="display: none;">
            <div class="panel-header">
                <div class="panel-title">
                    <?php echo bloggy_icon('bs', 'info-circle', '14', '#0d6efd'); ?>
                    <span>Информация</span>
                </div>
                <button class="panel-action" id="closeInfoPanel">
                    <?php echo bloggy_icon('bs', 'x', '12'); ?>
                </button>
            </div>
            <div class="info-content">
                <div class="info-section">
                    <div class="info-row">
                        <div class="info-label">Имя файла</div>
                        <div class="info-value" id="infoFileName">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Размер</div>
                        <div class="info-value" id="infoFileSize">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Путь</div>
                        <div class="info-value mono" id="infoFilePath">—</div>
                    </div>
                    <div class="info-row" id="infoDescRow" style="display: none;">
                        <div class="info-label">Описание</div>
                        <div class="info-value" id="infoFileDescription">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Последнее изменение</div>
                        <div class="info-value" id="infoFileUpdated">—</div>
                    </div>
                </div>
                <div class="info-actions">
                    <button class="btn-download" id="downloadFileBtn">
                        <?php echo bloggy_icon('bs', 'download', '14', '', 'me-1'); ?>
                        Скачать
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
    <script>
        const CURRENT_TEMPLATE = '<?php echo $currentTemplate; ?>';
    </script>
<?php admin_bottom_js(ob_get_clean()); ?>

<?php
    add_admin_js('templates/default/admin/assets/js/controllers/ace.js');
    add_admin_js('templates/default/admin/assets/js/controllers/ext-language_tools.js');
    add_admin_js('templates/default/admin/assets/js/controllers/mode-php.js');
    add_admin_js('templates/default/admin/assets/js/controllers/mode-html.js');
    add_admin_js('templates/default/admin/assets/js/controllers/mode-css.js');
    add_admin_js('templates/default/admin/assets/js/controllers/mode-javascript.js');
    add_admin_js('templates/default/admin/assets/js/controllers/templates-manager.js');
    add_admin_css('templates/default/admin/assets/css/templates.css');
?>