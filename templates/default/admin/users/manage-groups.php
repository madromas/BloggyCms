<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'diagram-3', '24', '#000', 'me-2'); ?>
            Группы пользователя: <?php echo html($user['username']); ?>
        </h4>
        <a href="<?php echo ADMIN_URL; ?>/users/edit/<?php echo $user['id']; ?>" class="btn btn-outline-secondary btn-sm">
            <?php echo bloggy_icon('bs', 'arrow-left', '16', '#000', 'me-1'); ?> Назад к пользователю
        </a>
    </div>

    <form method="post">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label mb-3">Выберите группы для пользователя</label>
                            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <?php if (!empty($allGroups)) { ?>
                                    <?php foreach ($allGroups as $group) { ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                name="groups[]" value="<?php echo $group['id']; ?>"
                                                id="group_<?php echo $group['id']; ?>"
                                                <?php echo in_array($group['id'], $userGroups) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="group_<?php echo $group['id']; ?>">
                                                <strong><?php echo html($group['name']); ?></strong>
                                                <?php if ($group['is_default']) { ?>
                                                    <span class="badge bg-success ms-1">по умолчанию</span>
                                                <?php } ?>
                                                <?php if ($group['description']) { ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo html($group['description']); ?></small>
                                                <?php } ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="text-center text-muted py-3">
                                        <?php echo bloggy_icon('bs', 'diagram-3', '32', '#6C6C6C', 'mb-2'); ?>
                                        <p class="mt-2 mb-0">Группы не созданы</p>
                                        <a href="<?php echo ADMIN_URL; ?>/user-groups/create" class="btn btn-sm btn-outline-primary mt-2">
                                            <?php echo bloggy_icon('bs', 'plus', '14', '#0d6efd', 'me-1'); ?>Создать группу
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="form-text mt-2">
                                Пользователь может состоять в нескольких группах одновременно
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <?php echo bloggy_icon('bs', 'check-lg', '18', '#fff', 'me-1'); ?>Сохранить группы
                            </button>
                            <a href="<?php echo ADMIN_URL; ?>/users/edit/<?php echo $user['id']; ?>" class="btn btn-outline-secondary">
                                Отмена
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title border-bottom pb-2 mb-3">Информация о пользователе</h6>
                        <div class="d-flex align-items-center mb-3">
                            <?php if (!empty($user['avatar']) && $user['avatar'] !== 'default.jpg') { ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $user['avatar']; ?>" 
                                     class="rounded-circle me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;"
                                     alt="<?php echo html($user['username']); ?>">
                            <?php } else { ?>
                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-light" 
                                     style="width: 60px; height: 60px;">
                                    <?php echo bloggy_icon('bs', 'person', '24', '#6C6C6C'); ?>
                                </div>
                            <?php } ?>
                            <div>
                                <strong><?php echo html($user['username']); ?></strong>
                                <div class="text-muted small"><?php echo html($user['email']); ?></div>
                            </div>
                        </div>
                        
                        <div class="small text-muted">
                            <div class="mb-1">
                                <?php echo bloggy_icon('bs', 'circle-fill', '12', $user['status'] === 'active' ? '#198754' : '#dc3545', 'me-1'); ?>
                                Статус: <span class="fw-medium"><?php echo $user['status'] === 'active' ? 'Активен' : 'Заблокирован'; ?></span>
                            </div>
                            <div>
                                <?php echo bloggy_icon('bs', 'calendar', '14', '#6C6C6C', 'me-1'); ?>
                                Зарегистрирован: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('[type="submit"]');
        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';
        
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }, 5000);
    });
});
</script>
<?php admin_bottom_js(ob_get_clean()); ?>