document.addEventListener('DOMContentLoaded', function() {
    initHtmlBlockForm();
});

function initHtmlBlockForm() {
    initAceEditors();
    initAssetHandlers();
    initAssetSelector();
    initFormSubmit();
    initTooltips();
    focusNameField();
}

function initAceEditors() {
    if (typeof ace === 'undefined') {
        console.error('Ace editor not loaded!');
        return;
    }
    
    initCssEditor();
    initJsEditor();
    initHtmlEditor();
}

function initCssEditor() {
    const cssEditorElement = document.getElementById('inline-css-editor');
    if (!cssEditorElement) return;
    
    window.inlineCssEditor = ace.edit("inline-css-editor", {
        theme: "ace/theme/monokai",
        mode: "ace/mode/css",
        showPrintMargin: false,
        fontSize: "14px",
        tabSize: 4,
        useSoftTabs: true,
        wrap: true,
        minLines: 8,
        maxLines: 20
    });
    
    const inlineCssField = document.getElementById('inline_css');
    if (inlineCssField) {
        window.inlineCssEditor.setValue(inlineCssField.value || '', -1);
    }
    
    configureAceEditor(window.inlineCssEditor, false);
}

function initJsEditor() {
    const jsEditorElement = document.getElementById('inline-js-editor');
    if (!jsEditorElement) return;
    
    window.inlineJsEditor = ace.edit("inline-js-editor", {
        theme: "ace/theme/monokai",
        mode: "ace/mode/javascript",
        showPrintMargin: false,
        fontSize: "14px",
        tabSize: 4,
        useSoftTabs: true,
        wrap: true,
        minLines: 8,
        maxLines: 20
    });
    
    const inlineJsField = document.getElementById('inline_js');
    if (inlineJsField) {
        window.inlineJsEditor.setValue(inlineJsField.value || '', -1);
    }
    
    configureAceEditor(window.inlineJsEditor, false);
}

function initHtmlEditor() {
    const htmlEditorElement = document.getElementById('default-block-html-editor');
    if (!htmlEditorElement) return;
    
    window.defaultBlockHtmlEditor = ace.edit("default-block-html-editor", {
        theme: "ace/theme/monokai",
        mode: "ace/mode/html",
        showPrintMargin: false,
        fontSize: "14px",
        tabSize: 4,
        useSoftTabs: true,
        wrap: true,
        minLines: 20,
        maxLines: 40
    });
    
    window.defaultBlockHtmlEditor.session.setUseWrapMode(true);
    
    const htmlTextarea = document.getElementById('default-block-html');
    if (htmlTextarea) {
        window.defaultBlockHtmlEditor.setValue(htmlTextarea.value || '', -1);
        window.defaultBlockHtmlEditor.session.getUndoManager().reset();
    }
    
    configureAceEditor(window.defaultBlockHtmlEditor, true);
}

function configureAceEditor(editor, enableCompletions = false) {
    if (!editor) return;
    
    editor.setOptions({
        enableBasicAutocompletion: enableCompletions,
        enableLiveAutocompletion: enableCompletions,
        enableSnippets: enableCompletions,
        behavioursEnabled: true,
        wrapBehavioursEnabled: true
    });
    
    editor.session.setUseWrapMode(true);
    editor.session.setTabSize(4);
    editor.session.setUseSoftTabs(true);
    editor.session.getUndoManager().reset();
}

function initAssetHandlers() {
    document.getElementById('add-css-file')?.addEventListener('click', () => addAssetRow('css'));
    document.getElementById('add-js-file')?.addEventListener('click', () => addAssetRow('js'));
    attachRemoveHandlers();
}

function addAssetRow(type) {
    const container = document.getElementById(`${type}-files-container`);
    if (!container) return;
    
    const newRow = document.createElement('div');
    newRow.className = `input-group mb-2 ${type}-file-row`;
    newRow.innerHTML = `
        <input type="text" name="${type}_files[]" class="form-control asset-path-input" value="" placeholder="templates/default/front/assets/${type}/my-block.${type}">
        <button type="button" class="btn btn-outline-primary select-asset-btn" 
                data-asset-type="${type}"
                data-bs-toggle="tooltip"
                title="Выбрать из папки блока">
            <svg class="icon icon-folder" width="16" height="16" style="fill: #000">
                <use href="/templates/default/admin/icons/bs.svg#folder2-open"></use>
            </svg>
        </button>
        <button type="button" class="btn btn-outline-danger remove-asset" data-type="${type}">
            <svg class="icon icon-trash" width="16" height="16" style="fill: #000">
                <use href="/templates/default/admin/icons/bs.svg#trash"></use>
            </svg>
        </button>
    `;
    container.appendChild(newRow);
    
    const selectBtn = newRow.querySelector('.select-asset-btn');
    if (selectBtn) {
        selectBtn.addEventListener('click', handleAssetSelectClick);
    }
    
    attachRemoveHandlers();
    initTooltips();
}

function attachRemoveHandlers() {
    document.querySelectorAll('.remove-asset').forEach(button => {
        button.removeEventListener('click', handleRemoveAsset);
        button.addEventListener('click', handleRemoveAsset);
    });
}

function handleRemoveAsset(e) {
    const button = e.currentTarget;
    const type = button.getAttribute('data-type');
    const row = button.closest(`.${type}-file-row`);
    const container = document.getElementById(`${type}-files-container`);
    
    if (!row || !container) return;
    
    if (container.querySelectorAll(`.${type}-file-row`).length > 1) {
        row.remove();
    } else {
        const input = row.querySelector('input');
        if (input) input.value = '';
    }
}

let assetSelectorModal = null;
let currentAssetInput = null;
let currentAssetType = 'css';

function initAssetSelector() {
    const modalElement = document.getElementById('assetSelectorModal');
    if (modalElement) {
        assetSelectorModal = new bootstrap.Modal(modalElement);
    }
    
    document.querySelectorAll('.select-asset-btn').forEach(btn => {
        btn.addEventListener('click', handleAssetSelectClick);
    });
    
    const manualPathBtn = document.getElementById('asset-manual-path');
    if (manualPathBtn) {
        manualPathBtn.addEventListener('click', () => {
            if (assetSelectorModal) {
                assetSelectorModal.hide();
            }
            if (currentAssetInput) {
                currentAssetInput.focus();
            }
        });
    }
    
    const filesList = document.getElementById('asset-files-list');
    if (filesList) {
        filesList.addEventListener('click', handleFileSelect);
    }
}

function handleAssetSelectClick(e) {
    const btn = e.currentTarget;
    currentAssetType = btn.getAttribute('data-asset-type');
    currentAssetInput = btn.closest('.input-group').querySelector('.asset-path-input');
    const blockType = document.querySelector('input[name="block_type"]')?.value || 'DefaultBlock';
    const typeDisplay = document.getElementById('asset-type-display');
    const blockTypeDisplay = document.getElementById('asset-block-type');
    const modalTitle = document.getElementById('asset-modal-title');
    
    if (typeDisplay) typeDisplay.value = currentAssetType.toUpperCase();
    if (blockTypeDisplay) blockTypeDisplay.value = blockType;
    if (modalTitle) {
        modalTitle.textContent = `Выберите ${currentAssetType.toUpperCase()} файл для блока ${blockType}`;
    }
    
    if (assetSelectorModal) {
        assetSelectorModal.show();
    }
    
    loadAssetFiles(blockType, currentAssetType);
}

async function loadAssetFiles(blockType, assetType) {
    const filesList = document.getElementById('asset-files-list');
    const noFilesAlert = document.getElementById('asset-no-files');
    
    if (!filesList) return;
    
    filesList.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Загрузка файлов...</p>
        </div>
    `;
    
    if (noFilesAlert) {
        noFilesAlert.style.display = 'none';
    }
    
    try {
        if (typeof window.ADMIN_URL === 'undefined') {
            const path = window.location.pathname;
            if (path.includes('/admin/')) {
                const base = path.split('/admin/')[0];
                window.ADMIN_URL = base + '/admin';
            } else {
                window.ADMIN_URL = '/admin';
            }
        }
        
        const response = await fetch(`${window.ADMIN_URL}/html-blocks/get-block-assets?block_type=${encodeURIComponent(blockType)}&asset_type=${assetType}`);
        const data = await response.json();
        
        if (data.success && data.files && data.files.length > 0) {
            if (noFilesAlert) {
                noFilesAlert.style.display = 'none';
            }
            filesList.innerHTML = '';
            
            data.files.forEach(file => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                item.setAttribute('data-file-path', file.path);
                item.innerHTML = `
                    <div>
                        <svg class="icon icon-file me-2" width="16" height="16" style="fill: #0d6efd">
                            <use href="/templates/default/admin/icons/bs.svg#filetype-${assetType === 'css' ? 'scss' : 'code'}"></use>
                        </svg>
                        <strong>${file.name}</strong>
                        <div class="small text-muted">${file.path}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-secondary">${formatFileSize(file.size)}</span>
                        <div class="small text-muted mt-1">${file.modified}</div>
                    </div>
                `;
                filesList.appendChild(item);
            });
        } else {
            filesList.innerHTML = '';
            if (noFilesAlert) {
                noFilesAlert.style.display = 'block';
                noFilesAlert.innerHTML = `
                    <svg class="icon icon-warning me-2" width="16" height="16" style="fill: #856404">
                        <use href="/templates/default/admin/icons/bs.svg#exclamation-triangle"></use>
                    </svg>
                    ${data.message || 'Файлы не найдены. Вы можете указать путь вручную.'}
                `;
            }
        }
    } catch (error) {
        console.error('Error loading assets:', error);
        filesList.innerHTML = `
            <div class="alert alert-danger">
                <svg class="icon icon-error me-2" width="16" height="16" style="fill: #721c24">
                    <use href="/templates/default/admin/icons/bs.svg#exclamation-circle"></use>
                </svg>
                Ошибка загрузки: ${error.message}
            </div>
        `;
    }
}

function handleFileSelect(e) {
    const item = e.target.closest('[data-file-path]');
    if (!item) return;
    
    const filePath = item.getAttribute('data-file-path');
    
    if (currentAssetInput) {
        currentAssetInput.value = filePath;
        currentAssetInput.classList.add('border-success');
        setTimeout(() => {
            currentAssetInput.classList.remove('border-success');
        }, 2000);
    }
    
    if (assetSelectorModal) {
        assetSelectorModal.hide();
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function initFormSubmit() {
    const form = document.getElementById("blockForm");
    if (!form) return;
    
    form.addEventListener("submit", function(e) {
        saveEditorValues();
    });
}

function saveEditorValues() {
    if (window.inlineCssEditor) {
        const inlineCssField = document.getElementById('inline_css');
        if (inlineCssField) {
            inlineCssField.value = window.inlineCssEditor.getValue();
        }
    }
    
    if (window.inlineJsEditor) {
        const inlineJsField = document.getElementById('inline_js');
        if (inlineJsField) {
            inlineJsField.value = window.inlineJsEditor.getValue();
        }
    }
    
    if (window.defaultBlockHtmlEditor) {
        const defaultBlockHtmlField = document.getElementById('default-block-html');
        if (defaultBlockHtmlField) {
            defaultBlockHtmlField.value = window.defaultBlockHtmlEditor.getValue();
        }
    }
}

function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        const existingTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (existingTooltip) {
            existingTooltip.dispose();
        }
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function focusNameField() {
    document.querySelector('input[name="name"]')?.focus();
}

window.HtmlBlockForm = {
    init: initHtmlBlockForm,
    addAssetRow: addAssetRow,
    loadAssetFiles: loadAssetFiles
};