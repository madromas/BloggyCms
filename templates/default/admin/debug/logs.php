<?php
add_admin_js('templates/default/admin/assets/js/controllers/debug-logs.js');
add_admin_css('templates/default/admin/assets/css/controllers/debug.css');
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'file-text', '24', '#000', 'me-2'); ?>
            Системные логи
        </h4>
        <div>
            <a href="<?php echo ADMIN_URL; ?>/debug" class="btn btn-outline-secondary btn-sm">
                <?php echo bloggy_icon('bs', 'arrow-left', '16', '#000', 'me-1'); ?>
                Назад к отладке
            </a>
            <button class="btn btn-outline-danger btn-sm ms-2" id="clear-error-logs">
                <?php echo bloggy_icon('bs', 'trash', '16', '#000', 'me-1'); ?>
                Очистить лог
            </button>
        </div>
    </div>

    <!-- Информация о файле лога -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <?php echo bloggy_icon('bs', 'info-circle', '18', '#0dcaf0', 'me-2'); ?>
            <div>
                <strong>Файл системного лога:</strong> 
                <code id="log-file-path"><?php echo html($logFilePath); ?></code>
                <span class="ms-2 text-muted" id="log-file-size"><?php echo html($logFileSize); ?></span>
            </div>
            <button class="btn btn-sm btn-outline-secondary ms-auto" id="refresh-logs" title="Обновить">
                <?php echo bloggy_icon('bs', 'arrow-clockwise', '14', '#000', 'me-1'); ?>
                Обновить
            </button>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Тип лога</label>
                    <select class="form-select" id="log-type-filter">
                        <option value="all">Все типы</option>
                        <option value="error">Ошибки</option>
                        <option value="warning">Предупреждения</option>
                        <option value="info">Информационные</option>
                        <option value="debug">Отладочные</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Поиск</label>
                    <input type="text" class="form-control" id="log-search" placeholder="Поиск по сообщению...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Сортировка</label>
                    <select class="form-select" id="log-sort">
                        <option value="desc">Новые сверху</option>
                        <option value="asc">Старые сверху</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" id="apply-log-filters">
                        <?php echo bloggy_icon('bs', 'funnel', '16', '#fff', 'me-1'); ?>
                        Применить фильтр
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Список логов -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <?php echo bloggy_icon('bs', 'list-ul', '20', '#000', 'me-2'); ?>
                Записи лога
            </h5>
            <span class="badge bg-secondary" id="logs-count">0</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="logs-table">
                    <thead class="table-light">
                        <tr>
                            <th width="180">Дата/время</th>
                            <th width="100">Тип</th>
                            <th>Сообщение</th>
                        </tr>
                    </thead>
                    <tbody id="logs-tbody">
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-muted">Загрузка логов...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            <nav id="pagination-container"></nav>
        </div>
    </div>
</div>

<script>
// Передаем ADMIN_URL в глобальную переменную, если её нет
if (typeof ADMIN_URL === 'undefined') {
    window.ADMIN_URL = '<?php echo ADMIN_URL; ?>';
}

// Ждём загрузки DOM и инициализируем
document.addEventListener('DOMContentLoaded', function() {
    // Загружаем логи через AJAX
    loadErrorLogs();
    
    // Обработчики событий
    document.getElementById('apply-log-filters').addEventListener('click', function() {
        if (typeof applyFiltersAndRender === 'function') {
            window.currentPage = 1;
            applyFiltersAndRender();
        }
    });
    
    document.getElementById('refresh-logs').addEventListener('click', function() {
        loadErrorLogs();
    });
    
    document.getElementById('clear-error-logs').addEventListener('click', function() {
        if (confirm('Вы уверены, что хотите очистить системный лог? Это действие нельзя отменить.')) {
            clearErrorLogs();
        }
    });
    
    document.getElementById('log-search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            if (typeof applyFiltersAndRender === 'function') {
                window.currentPage = 1;
                applyFiltersAndRender();
            }
        }
    });
});

// Глобальные переменные
window.currentPage = 1;
window.totalPages = 1;
window.allLogs = [];

function loadErrorLogs() {
    const tbody = document.getElementById('logs-tbody');
    tbody.innerHTML = `
        <tr><td colspan="3" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Загрузка логов...</p>
        </td></tr>
    `;
    
    fetch(ADMIN_URL + '/debug/get-error-logs', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.allLogs = data.logs;
            document.getElementById('log-file-path').textContent = data.log_file_path;
            document.getElementById('log-file-size').textContent = `(${data.log_file_size})`;
            applyFiltersAndRender();
        } else {
            showError(data.message || 'Ошибка загрузки системных логов');
        }
    })
    .catch(error => {
        console.error('Error loading logs:', error);
        showError('Ошибка загрузки системных логов');
    });
}

function applyFiltersAndRender() {
    const typeFilter = document.getElementById('log-type-filter').value;
    const searchQuery = document.getElementById('log-search').value.toLowerCase();
    const sortOrder = document.getElementById('log-sort').value;
    
    let filteredLogs = [...window.allLogs];
    
    // Фильтр по типу
    if (typeFilter !== 'all') {
        filteredLogs = filteredLogs.filter(log => log.type === typeFilter);
    }
    
    // Поиск
    if (searchQuery) {
        filteredLogs = filteredLogs.filter(log => 
            log.message.toLowerCase().includes(searchQuery)
        );
    }
    
    // Сортировка
    if (sortOrder === 'asc') {
        filteredLogs = [...filteredLogs].reverse();
    }
    
    // Пагинация
    const perPage = 50;
    window.totalPages = Math.ceil(filteredLogs.length / perPage);
    const start = (window.currentPage - 1) * perPage;
    const paginatedLogs = filteredLogs.slice(start, start + perPage);
    
    renderLogsTable(paginatedLogs);
    renderPagination();
    document.getElementById('logs-count').textContent = filteredLogs.length;
}

function renderLogsTable(logs) {
    const tbody = document.getElementById('logs-tbody');
    
    if (!logs || logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-5 text-muted">Нет записей</td></tr>';
        return;
    }
    
    let html = '';
    logs.forEach(log => {
        const typeClass = getTypeClass(log.type);
        const typeLabel = log.type.toUpperCase();
        
        html += `
            <tr>
                <td><small class="text-muted">${escapeHtml(log.date)}</small></td>
                <td><span class="badge bg-${typeClass}">${typeLabel}</span></td>
                <td><code class="small" style="white-space: pre-wrap; word-break: break-all;">${escapeHtml(log.message)}</code></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function renderPagination() {
    const container = document.getElementById('pagination-container');
    if (!container || window.totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '<ul class="pagination justify-content-center mb-0">';
    
    // Previous
    if (window.currentPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${window.currentPage - 1}">«</a></li>`;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">«</span></li>';
    }
    
    // Pages
    let start = Math.max(1, window.currentPage - 2);
    let end = Math.min(window.totalPages, start + 4);
    if (end - start < 4) start = Math.max(1, end - 4);
    
    for (let i = start; i <= end; i++) {
        const activeClass = i === window.currentPage ? 'active' : '';
        html += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    
    // Next
    if (window.currentPage < window.totalPages) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${window.currentPage + 1}">»</a></li>`;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">»</span></li>';
    }
    
    html += '</ul>';
    container.innerHTML = html;
    
    // Привязываем обработчики
    container.querySelectorAll('.page-link[data-page]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            window.currentPage = parseInt(link.dataset.page);
            applyFiltersAndRender();
        });
    });
}

function getTypeClass(type) {
    switch(type) {
        case 'error': return 'danger';
        case 'warning': return 'warning';
        case 'debug': return 'info';
        default: return 'secondary';
    }
}

function clearErrorLogs() {
    fetch(ADMIN_URL + '/debug/clear-error-logs', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof showNotification === 'function') {
                showNotification('Системный лог очищен', 'success');
            } else {
                alert('Системный лог очищен');
            }
            loadErrorLogs();
        } else {
            const msg = data.message || 'Ошибка при очистке лога';
            if (typeof showNotification === 'function') {
                showNotification(msg, 'error');
            } else {
                alert(msg);
            }
        }
    })
    .catch(error => {
        console.error('Error clearing logs:', error);
        if (typeof showNotification === 'function') {
            showNotification('Ошибка сети при очистке лога', 'error');
        } else {
            alert('Ошибка сети при очистке лога');
        }
    });
}

function showError(message) {
    const tbody = document.getElementById('logs-tbody');
    tbody.innerHTML = `<tr><td colspan="3" class="text-center py-5 text-danger">${escapeHtml(message)}</td></tr>`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>