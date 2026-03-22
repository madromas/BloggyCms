<?php
/**
 * Template Name: Редактирование профиля
 */
?>

<div class="tg-profile-edit">
    <div class="tg-container">
        
        <div class="tg-card">
            <div class="tg-card-header">
                <h1 class="tg-card-title">
                    <?php echo bloggy_icon('bs', 'pencil-square', '24', 'currentColor', 'tg-mr-2'); ?>
                    Редактирование профиля
                </h1>
                <a href="<?php echo BASE_URL; ?>/profile/<?php echo html($user['username']); ?>" class="btn btn-outline-secondary btn-sm">
                    <?php echo bloggy_icon('bs', 'arrow-left', '14', 'currentColor', 'me-1'); ?>
                    Вернуться к профилю
                </a>
            </div>
            
            <div class="tg-card-body">
                <?php if (isset($_SESSION['error_message'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo html($_SESSION['error_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php } ?>
                
                <?php if (isset($_SESSION['success_message'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo html($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php } ?>

                <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab" data-bs-target="#profile-info" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'person', '16', 'currentColor', 'me-1'); ?>
                            Основная информация
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-password-tab" data-bs-toggle="tab" data-bs-target="#profile-password" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'key', '16', 'currentColor', 'me-1'); ?>
                            Смена пароля
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-sessions-tab" data-bs-toggle="tab" data-bs-target="#profile-sessions" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'laptop', '16', 'currentColor', 'me-1'); ?>
                            Сессии
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-additional-tab" data-bs-toggle="tab" data-bs-target="#profile-additional" type="button" role="tab">
                            <?php echo bloggy_icon('bs', 'gear', '16', 'currentColor', 'me-1'); ?>
                            Дополнительно
                        </button>
                    </li>
                </ul>
                
                <form method="POST" action="<?php echo BASE_URL; ?>/profile/update" enctype="multipart/form-data" id="profile-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action_type" id="action_type" value="update_profile">
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="profile-info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="tg-profile-avatar-section text-center">
                                        <div class="tg-profile-avatar-preview">
                                            <?php if (!empty($user['avatar']) && $user['avatar'] !== 'default.jpg') { ?>
                                                <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo html($user['avatar']); ?>" 
                                                     alt="Аватар" 
                                                     id="avatar-preview"
                                                     class="avatar-preview-img">
                                            <?php } else { ?>
                                                <div class="avatar-placeholder avatar-preview-placeholder" id="avatar-preview">
                                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <label class="btn btn-outline-primary btn-sm">
                                                <?php echo bloggy_icon('bs', 'cloud-upload', '14', 'currentColor', 'me-1'); ?>
                                                Загрузить аватар
                                                <input type="file" 
                                                       name="avatar" 
                                                       accept="image/jpeg,image/png,image/gif,image/webp"
                                                       class="d-none"
                                                       onchange="previewAvatar(this)">
                                            </label>
                                            <p class="text-muted small mt-2">
                                                Максимальный размер: 5MB<br>
                                                Допустимые форматы: JPG, PNG, GIF, WebP
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Имя пользователя</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="<?php echo html($user['username']); ?>" 
                                               disabled>
                                        <div class="form-text text-muted">
                                            Имя пользователя нельзя изменить
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Отображаемое имя</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="display_name" 
                                               value="<?php echo html($user['display_name'] ?? ''); ?>"
                                               placeholder="Как вас называть">
                                        <div class="form-text text-muted">
                                            Имя, которое будет отображаться на сайте
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control" 
                                               name="email" 
                                               value="<?php echo html($user['email']); ?>" 
                                               required>
                                        <div class="form-text text-muted">
                                            Ваш email будет использоваться для входа и уведомлений
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Веб-сайт</label>
                                        <input type="url" 
                                               class="form-control" 
                                               name="website" 
                                               value="<?php echo html($user['website'] ?? ''); ?>"
                                               placeholder="https://example.com">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">О себе</label>
                                        <textarea class="form-control" 
                                                  name="bio" 
                                                  rows="4" 
                                                  placeholder="Расскажите о себе"><?php echo html($user['bio'] ?? ''); ?></textarea>
                                        <div class="form-text text-muted">
                                            Максимум 500 символов
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($customFields)) { ?>
                                        <hr class="my-4">
                                        <h4 class="mb-3">Дополнительная информация</h4>
                                        
                                        <?php foreach ($customFields as $field) { 
                                            $config = json_decode($field['config'] ?? '{}', true);
                                            $isRequired = (bool)$field['is_required'];
                                            $requiredMark = $isRequired ? ' <span class="text-danger">*</span>' : '';
                                        ?>
                                            <div class="mb-3">
                                                <label class="form-label"><?php echo html($field['name']); ?><?php echo $requiredMark; ?></label>
                                                <?php 
                                                echo $fieldManager->renderFieldInput(
                                                    $field['type'],
                                                    $field['system_name'],
                                                    $field['value'],
                                                    $config,
                                                    'user',
                                                    $user['id']
                                                );
                                                ?>
                                                <?php if (!empty($field['description'])) { ?>
                                                    <div class="form-text text-muted"><?php echo html($field['description']); ?></div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="profile-password" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <?php echo bloggy_icon('bs', 'info-circle', '16', 'currentColor', 'me-1'); ?>
                                        Для смены пароля заполните все поля ниже. Если вы не хотите менять пароль, оставьте их пустыми.
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Текущий пароль</label>
                                        <input type="password" 
                                               class="form-control" 
                                               name="current_password" 
                                               autocomplete="current-password">
                                        <div class="form-text text-muted">
                                            Введите текущий пароль для подтверждения
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Новый пароль</label>
                                                <input type="password" 
                                                       class="form-control" 
                                                       name="new_password" 
                                                       id="new_password"
                                                       autocomplete="new-password">
                                                <div class="form-text text-muted">
                                                    Минимум 6 символов
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Подтверждение пароля</label>
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="confirm_password" 
                                                       autocomplete="new-password">
                                                <div class="invalid-feedback">
                                                    Пароли не совпадают
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="profile-sessions" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <?php echo bloggy_icon('bs', 'info-circle', '16', 'currentColor', 'me-1'); ?>
                                        Здесь отображаются все активные сессии вашего аккаунта. Вы можете завершить любую сессию, кроме текущей.
                                    </div>
                                    
                                    <div class="sessions-list" id="sessions-list">
                                        <div class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Загрузка...</span>
                                            </div>
                                            <p class="mt-2">Загрузка сессий...</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="terminate-all-sessions">
                                            <?php echo bloggy_icon('bs', 'x-circle', '14', 'currentColor', 'me-1'); ?>
                                            Завершить все сессии (кроме текущей)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="profile-additional" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-danger">
                                        <?php echo bloggy_icon('bs', 'exclamation-triangle', '16', 'currentColor', 'me-1'); ?>
                                        <strong>Внимание!</strong> Удаление профиля — необратимое действие. Все ваши данные будут удалены.
                                    </div>
                                    
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <strong>Удаление аккаунта</strong>
                                        </div>
                                        <div class="card-body">
                                            <p>При удалении аккаунта будут удалены:</p>
                                            <ul>
                                                <li>Ваш профиль и все личные данные</li>
                                                <li>Все ваши комментарии</li>
                                                <li>Все ваши закладки и лайки</li>
                                                <li>Ваши достижения (ачивки)</li>
                                            </ul>
                                            <p class="text-muted small">
                                                <strong>Примечание:</strong> Ваши посты и страницы не будут удалены, но будут привязаны к анонимному пользователю.
                                            </p>
                                            
                                            <div class="mt-4">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" id="confirm-delete">
                                                    <label class="form-check-label" for="confirm-delete">
                                                        Я понимаю, что это действие необратимо, и хочу удалить свой аккаунт
                                                    </label>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Введите пароль для подтверждения</label>
                                                    <input type="password" 
                                                           class="form-control" 
                                                           id="delete-password" 
                                                           placeholder="Ваш пароль">
                                                </div>
                                                
                                                <button type="button" class="btn btn-danger" id="delete-account-btn" disabled>
                                                    <?php echo bloggy_icon('bs', 'trash', '16', 'currentColor', 'me-1'); ?>
                                                    Удалить аккаунт
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tg-form-actions mt-4">
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <?php echo bloggy_icon('bs', 'check-lg', '16', 'currentColor', 'me-1'); ?>
                            Сохранить изменения
                        </button>
                        <a href="<?php echo BASE_URL; ?>/profile/<?php echo html($user['username']); ?>" class="btn btn-outline-secondary">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.id = 'avatar-preview';
                img.className = 'avatar-preview-img';
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submit-btn');
    const actionType = document.getElementById('action_type');
    
    function validatePassword() {
        if (newPassword && confirmPassword) {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.classList.add('is-invalid');
                return false;
            } else {
                confirmPassword.classList.remove('is-invalid');
                return true;
            }
        }
        return true;
    }
    
    if (newPassword && confirmPassword) {
        newPassword.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);
        
        submitBtn.addEventListener('click', function(e) {
            if (newPassword.value && newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                confirmPassword.classList.add('is-invalid');
                alert('Пароли не совпадают');
            }
        });
    }
    
    function loadSessions() {
        fetch('<?php echo BASE_URL; ?>/profile/sessions')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderSessions(data.sessions);
                }
            })
            .catch(error => {
                document.getElementById('sessions-list').innerHTML = `
                    <div class="alert alert-warning">
                        Не удалось загрузить список сессий
                    </div>
                `;
            });
    }
    
    function renderSessions(sessions) {
        const container = document.getElementById('sessions-list');
        if (!sessions || sessions.length === 0) {
            container.innerHTML = '<div class="text-center py-4 text-muted">Нет активных сессий</div>';
            return;
        }
        
        let html = '';
        sessions.forEach(session => {
            const isCurrent = session.is_current;
            html += `
                <div class="session-item ${isCurrent ? 'session-current' : ''}">
                    <div class="session-info">
                        <div class="session-icon">
                            <?php echo bloggy_icon('bs', 'laptop', '20', 'var(--tg-text-secondary)'); ?>
                        </div>
                        <div class="session-details">
                            <div class="session-device">
                                ${session.device || 'Неизвестное устройство'}
                                ${isCurrent ? '<span class="current-badge ms-2">Текущая</span>' : ''}
                            </div>
                            <div class="session-meta">
                                IP: ${session.ip} • Последняя активность: ${session.last_activity}
                            </div>
                        </div>
                    </div>
                    ${!isCurrent ? `
                        <button type="button" class="btn btn-sm btn-outline-danger terminate-session" data-session-id="${session.id}">
                            Завершить
                        </button>
                    ` : ''}
                </div>
            `;
        });
        
        container.innerHTML = html;
        
        document.querySelectorAll('.terminate-session').forEach(btn => {
            btn.addEventListener('click', function() {
                const sessionId = this.dataset.sessionId;
                if (confirm('Завершить эту сессию?')) {
                    terminateSession(sessionId);
                }
            });
        });
    }
    
    function terminateSession(sessionId) {
        fetch('<?php echo BASE_URL; ?>/profile/terminate-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ session_id: sessionId, csrf_token: '<?php echo $csrf_token; ?>' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadSessions();
            } else {
                alert(data.message || 'Ошибка при завершении сессии');
            }
        })
        .catch(error => {
            alert('Ошибка при завершении сессии');
        });
    }
    
    document.getElementById('terminate-all-sessions')?.addEventListener('click', function() {
        if (confirm('Завершить все сессии кроме текущей? Вам придется войти снова на других устройствах.')) {
            fetch('<?php echo BASE_URL; ?>/profile/terminate-all-sessions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ csrf_token: '<?php echo $csrf_token; ?>' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadSessions();
                } else {
                    alert(data.message || 'Ошибка при завершении сессий');
                }
            })
            .catch(error => {
                alert('Ошибка при завершении сессий');
            });
        }
    });
    
    const deleteCheckbox = document.getElementById('confirm-delete');
    const deletePassword = document.getElementById('delete-password');
    const deleteBtn = document.getElementById('delete-account-btn');
    
    if (deleteCheckbox && deletePassword && deleteBtn) {
        function checkDeleteForm() {
            deleteBtn.disabled = !(deleteCheckbox.checked && deletePassword.value.length > 0);
        }
        
        deleteCheckbox.addEventListener('change', checkDeleteForm);
        deletePassword.addEventListener('input', checkDeleteForm);
        
        deleteBtn.addEventListener('click', function() {
            if (confirm('ВНИМАНИЕ! Это действие необратимо. Вы уверены, что хотите удалить свой аккаунт?')) {
                fetch('<?php echo BASE_URL; ?>/profile/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        password: deletePassword.value, 
                        csrf_token: '<?php echo $csrf_token; ?>' 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Ваш аккаунт удален. Вы будете перенаправлены на главную страницу.');
                        window.location.href = '<?php echo BASE_URL; ?>';
                    } else {
                        alert(data.message || 'Ошибка при удалении аккаунта');
                    }
                })
                .catch(error => {
                    alert('Ошибка при удалении аккаунта');
                });
            }
        });
    }
    
    const sessionsTab = document.getElementById('profile-sessions-tab');
    if (sessionsTab) {
        sessionsTab.addEventListener('shown.bs.tab', function() {
            loadSessions();
        });
    }
    
    loadSessions();
});
</script>