(function() {
    'use strict';
    
    const elements = {
        uploadArea: null,
        fileInput: null,
        installBtn: null,
        uploadDefault: null,
        uploadPreview: null,
        packagePreview: null,
        uploadProgress: null,
        uploadResult: null,
        resultMessage: null,
        fileName: null,
        progressBar: null,
        progressText: null,
        checkUpdatesBtn: null,
        infoButtons: null
    };
    
    let selectedFile = null;
    let packageInfo = null;
    
    function init() {

        if (document.getElementById('uploadArea')) {
            initInstallPage();
        }
        
        initCheckUpdates();
        initInfoButtons();
    }
    
    function initInstallPage() {
        elements.uploadArea = document.getElementById('uploadArea');
        elements.fileInput = document.getElementById('addon-file-input');
        elements.installBtn = document.getElementById('install-btn');
        elements.uploadDefault = document.getElementById('uploadDefault');
        elements.uploadPreview = document.getElementById('uploadPreview');
        elements.packagePreview = document.getElementById('packagePreview');
        elements.uploadProgress = document.getElementById('uploadProgress');
        elements.uploadResult = document.getElementById('uploadResult');
        elements.resultMessage = document.getElementById('resultMessage');
        elements.fileName = document.getElementById('fileName');
        elements.progressBar = document.getElementById('progressBar');
        elements.progressText = document.getElementById('progressText');
        
        if (!elements.uploadArea) return;
        
        if (elements.fileInput) {
            elements.fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    handleFileSelect(this.files[0]);
                }
            });
        }
        
        elements.uploadArea.addEventListener('dragover', handleDragOver);
        elements.uploadArea.addEventListener('dragleave', handleDragLeave);
        elements.uploadArea.addEventListener('drop', handleDrop);
        
        if (elements.installBtn) {
            elements.installBtn.addEventListener('click', handleInstall);
        }
    }
    
    function handleDragOver(e) {
        e.preventDefault();
        elements.uploadArea.style.borderColor = '#0d6efd';
        elements.uploadArea.style.backgroundColor = '#e7f1ff';
    }
    
    function handleDragLeave(e) {
        e.preventDefault();
        elements.uploadArea.style.borderColor = '#dee2e6';
        elements.uploadArea.style.backgroundColor = '#f8f9fa';
    }
    
    function handleDrop(e) {
        e.preventDefault();
        elements.uploadArea.style.borderColor = '#dee2e6';
        elements.uploadArea.style.backgroundColor = '#f8f9fa';
        
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            const file = e.dataTransfer.files[0];
            if (file.name.toLowerCase().endsWith('.zip')) {
                handleFileSelect(file);
            } else {
                showResult('error', 'Пожалуйста, выберите ZIP-архив');
            }
        }
    }
    
    function handleFileSelect(file) {
        if (!file.name.toLowerCase().endsWith('.zip')) {
            showResult('error', 'Файл должен быть в формате ZIP');
            return;
        }
        
        if (file.size > 50 * 1024 * 1024) {
            showResult('error', 'Размер файла не должен превышать 50MB');
            return;
        }
        
        selectedFile = file;
        elements.fileName.textContent = file.name;
        
        elements.uploadDefault.classList.add('d-none');
        elements.uploadPreview.classList.remove('d-none');
        elements.packagePreview.classList.add('d-none');
        elements.uploadProgress.classList.add('d-none');
        elements.uploadResult.classList.add('d-none');
        
        if (elements.installBtn) {
            elements.installBtn.disabled = true;
        }

        analyzePackage(file);
    }
    
    function analyzePackage(file) {
        const formData = new FormData();
        formData.append('analyze_only', '1');
        formData.append('addon_file', file);
        
        showLoading('Анализ пакета...');
        
        fetch(window.ADMIN_URL + '/addons/analyze', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                packageInfo = data.package;
                showPackageInfo(packageInfo);
                if (elements.installBtn) {
                    elements.installBtn.disabled = false;
                }
            } else {
                showResult('error', 'Ошибка анализа пакета: ' + data.message);
                if (elements.installBtn) {
                    elements.installBtn.disabled = true;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showResult('error', 'Ошибка при анализе пакета: ' + error.message);
            if (elements.installBtn) {
                elements.installBtn.disabled = true;
            }
        });
    }
    
    function showPackageInfo(info) {
        const titleEl = document.getElementById('preview-title');
        const versionEl = document.getElementById('preview-version');
        const typeEl = document.getElementById('preview-type');
        const authorEl = document.getElementById('preview-author');
        const emailEl = document.getElementById('preview-email');
        const urlEl = document.getElementById('preview-url');
        const descEl = document.getElementById('preview-description');
        const emailRow = document.getElementById('preview-email-row');
        const urlRow = document.getElementById('preview-url-row');
        const descRow = document.getElementById('preview-description-row');
        const statusDiv = document.getElementById('preview-status');
        
        if (titleEl) titleEl.innerHTML = escapeHtml(info.title);
        if (versionEl) versionEl.innerHTML = 'v' + escapeHtml(info.version_string);
        
        if (typeEl) {
            if (info.type === 'install') {
                typeEl.innerHTML = '<span class="badge bg-success">Установка</span>';
            } else {
                typeEl.innerHTML = '<span class="badge bg-warning">Обновление</span>';
            }
        }
        
        if (authorEl) {
            if (info.author_name) {
                authorEl.innerHTML = escapeHtml(info.author_name);
            } else {
                authorEl.innerHTML = '<span class="text-muted">—</span>';
            }
        }
        
        if (emailRow && emailEl) {
            if (info.author_email) {
                emailEl.innerHTML = escapeHtml(info.author_email);
                emailRow.style.display = 'table-row';
            } else {
                emailRow.style.display = 'none';
            }
        }
        
        if (urlRow && urlEl) {
            if (info.author_url) {
                urlEl.innerHTML = '<a href="' + escapeHtml(info.author_url) + '" target="_blank">' + escapeHtml(info.author_url) + '</a>';
                urlRow.style.display = 'table-row';
            } else {
                urlRow.style.display = 'none';
            }
        }

        if (descRow && descEl) {
            if (info.description) {
                descEl.innerHTML = nl2br(escapeHtml(info.description));
                descRow.style.display = 'table-row';
            } else {
                descRow.style.display = 'none';
            }
        }
        
        if (statusDiv) {
            if (info.type === 'update') {
                statusDiv.className = 'alert alert-warning mb-0';
                statusDiv.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Обновление существующего пакета';
            } else {
                statusDiv.className = 'alert alert-success mb-0';
                statusDiv.innerHTML = '<i class="bi bi-check-circle me-1"></i> Готов к установке';
            }
        }

        elements.uploadPreview.classList.add('d-none');
        elements.packagePreview.classList.remove('d-none');
    }
    
    function showLoading(message) {
        elements.uploadPreview.classList.add('d-none');
        elements.packagePreview.classList.add('d-none');
        elements.uploadProgress.classList.remove('d-none');
        if (elements.progressBar) elements.progressBar.style.width = '30%';
        if (elements.progressText) elements.progressText.textContent = message;
    }
    
    function resetUpload() {
        selectedFile = null;
        packageInfo = null;
        if (elements.fileInput) elements.fileInput.value = '';
        elements.uploadDefault.classList.remove('d-none');
        elements.uploadPreview.classList.add('d-none');
        elements.packagePreview.classList.add('d-none');
        elements.uploadProgress.classList.add('d-none');
        elements.uploadResult.classList.add('d-none');
        if (elements.installBtn) elements.installBtn.disabled = true;
    }
    
    function showResult(type, message) {
        elements.uploadPreview.classList.add('d-none');
        elements.packagePreview.classList.add('d-none');
        elements.uploadProgress.classList.add('d-none');
        if (elements.resultMessage) {
            elements.resultMessage.className = 'alert alert-' + type;
            elements.resultMessage.innerHTML = message;
        }
        elements.uploadResult.classList.remove('d-none');
    }
    
    function handleInstall() {
        if (!selectedFile) return;
        
        const formData = new FormData();
        formData.append('addon_file', selectedFile);
        
        if (elements.installBtn) elements.installBtn.disabled = true;
        elements.packagePreview.classList.add('d-none');
        elements.uploadProgress.classList.remove('d-none');
        elements.uploadResult.classList.add('d-none');
        
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            if (progress <= 90) {
                if (elements.progressBar) elements.progressBar.style.width = progress + '%';
                if (elements.progressText) elements.progressText.textContent = `Установка... ${progress}%`;
            }
        }, 300);
        
        fetch(window.ADMIN_URL + '/addons/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(interval);
            if (elements.progressBar) elements.progressBar.style.width = '100%';
            if (elements.progressText) elements.progressText.textContent = 'Готово';
            
            setTimeout(() => {
                if (data.success) {
                    showResult('success', `
                        <strong>✅ Пакет успешно установлен!</strong><br>
                        <strong>Название:</strong> ${escapeHtml(data.package.title)}<br>
                        <strong>Версия:</strong> v${escapeHtml(data.package.version_string)}<br>
                        <strong>Тип:</strong> ${data.package.type === 'install' ? 'Установка' : 'Обновление'}<br>
                        ${data.package.description ? `<strong>Описание:</strong> ${escapeHtml(data.package.description)}` : ''}
                        <hr>
                        <button type="button" class="btn btn-primary btn-sm mt-2" onclick="window.location.href='${window.ADMIN_URL}/addons'">
                            Перейти к списку пакетов
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2 ms-2" onclick="location.reload()">
                            Установить другой пакет
                        </button>
                    `);
                    if (elements.installBtn) elements.installBtn.disabled = true;
                } else {
                    showResult('danger', '❌ Ошибка: ' + escapeHtml(data.message));
                    resetUpload();
                }
            }, 500);
        })
        .catch(error => {
            clearInterval(interval);
            console.error('Error:', error);
            showResult('danger', 'Ошибка при загрузке пакета: ' + error.message);
            resetUpload();
        });
    }
    
    function initCheckUpdates() {
        const checkUpdatesBtn = document.getElementById('check-updates-btn');
        if (!checkUpdatesBtn) return;
        
        checkUpdatesBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Проверка...';
            
            fetch(window.ADMIN_URL + '/addons/check-updates', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.has_updates) {
                        let message = 'Найдены обновления:\n';
                        data.updates.forEach(update => {
                            message += `\n- ${update.title}: ${update.current_version} → ${update.new_version}`;
                        });
                        alert(message);
                    } else {
                        alert('Обновлений не найдено');
                    }
                } else {
                    alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при проверке обновлений');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }
    
    function initInfoButtons() {
        const infoButtons = document.querySelectorAll('.info-addon');
        if (!infoButtons.length) return;
        
        infoButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const addonId = this.dataset.id;
                const modalBody = document.getElementById('addon-info-content');
                if (!modalBody) return;
                
                modalBody.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Загрузка...</p>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('addonInfoModal'));
                modal.show();
                
                fetch(window.ADMIN_URL + '/addons/info/' + addonId, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.addon) {
                        const addon = data.addon;
                        modalBody.innerHTML = `
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-3">${escapeHtml(addon.title)}</h5>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong class="text-muted">Версия:</strong>
                                                <span class="badge bg-info ms-2">v${escapeHtml(addon.version_string)}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong class="text-muted">Дата релиза:</strong>
                                                ${escapeHtml(addon.version_date || '—')}
                                            </div>
                                            <div class="mb-2">
                                                <strong class="text-muted">Тип:</strong>
                                                <span class="badge bg-${addon.type === 'install' ? 'success' : 'warning'} ms-2">
                                                    ${addon.type === 'install' ? 'Установка' : 'Обновление'}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <strong class="text-muted">Установлен:</strong>
                                                ${formatDate(addon.installed_at)}
                                            </div>
                                            ${addon.updated_at ? `
                                            <div class="mb-2">
                                                <strong class="text-muted">Обновлен:</strong>
                                                ${formatDate(addon.updated_at)}
                                            </div>
                                            ` : ''}
                                            <div class="mb-2">
                                                <strong class="text-muted">Системное имя:</strong>
                                                <code>${escapeHtml(addon.system_name)}</code>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    ${addon.description ? `
                                    <div class="mb-3">
                                        <strong class="text-muted">Описание:</strong>
                                        <div class="p-3 bg-light rounded mt-2">
                                            ${nl2br(escapeHtml(addon.description))}
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                                    ${addon.author_name ? `
                                    <div class="mb-3">
                                        <strong class="text-muted">Автор:</strong>
                                        <div class="mt-1">
                                            <strong>${escapeHtml(addon.author_name)}</strong>
                                            ${addon.author_url ? `<br><a href="${escapeHtml(addon.author_url)}" target="_blank">${escapeHtml(addon.author_url)}</a>` : ''}
                                            ${addon.author_email ? `<br>${escapeHtml(addon.author_email)}` : ''}
                                        </div>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `
                            <div class="alert alert-danger">
                                ${escapeHtml(data.message || 'Ошибка загрузки информации')}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            Ошибка загрузки информации о пакете
                        </div>
                    `;
                });
            });
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const date = new Date(dateStr);
        return date.toLocaleString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    function nl2br(str) {
        if (!str) return '';
        return str.replace(/\n/g, '<br>');
    }
    
    window.resetUpload = resetUpload;
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();