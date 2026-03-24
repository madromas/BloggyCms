<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'box', '24', '#000', 'me-2'); ?>
            Управление пакетами
        </h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" id="check-updates-btn">
                <?php echo bloggy_icon('bs', 'arrow-repeat', '16', '#000', 'me-2'); ?>
                Проверить обновления
            </button>
            <a href="<?php echo ADMIN_URL; ?>/addons/install" class="btn btn-primary">
                <?php echo bloggy_icon('bs', 'cloud-upload', '16', '#fff', 'me-2'); ?>
                Установить пакет
            </a>
        </div>
    </div>

    <?php if (!empty($randomHint)) { ?>
        <div class="alert alert-info d-flex align-items-center mb-4">
            <?php echo bloggy_icon('bs', 'info-circle', '16', '#5AAFC9', 'me-2'); ?>
            <span><?php echo html($randomHint); ?></span>
        </div>
    <?php } ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($addons)) { ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <?php echo bloggy_icon('bs', 'box', '48', '#6C6C6C'); ?>
                    </div>
                    <h5 class="text-muted">Нет установленных пакетов</h5>
                    <p class="text-muted">Установите первый пакет расширений</p>
                    <a href="<?php echo ADMIN_URL; ?>/addons/install" class="btn btn-primary">
                        <?php echo bloggy_icon('bs', 'cloud-upload', '16', '#fff', 'me-2'); ?>
                        Установить пакет
                    </a>
                </div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Пакет</th>
                                <th>Версия</th>
                                <th>Тип</th>
                                <th>Автор</th>
                                <th>Дата установки</th>
                                <th class="text-end">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($addons as $addon) { ?>
                            <tr id="addon-<?php echo $addon['id']; ?>">
                                <td>
                                    <div>
                                        <strong><?php echo html($addon['title']); ?></strong>
                                        <?php if (!empty($addon['description'])) { ?>
                                            <br><small class="text-muted"><?php echo html(mb_substr($addon['description'], 0, 100)); ?>...</small>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">v<?php echo html($addon['version_string']); ?></span>
                                    <br>
                                    <small class="text-muted" title="<?php echo date('d.m.Y H:i:s', strtotime($addon['installed_at'])); ?>">
                                        <?php echo time_ago($addon['installed_at']); ?>
                                    </small>
                                    <?php if (!empty($addon['updated_at']) && $addon['updated_at'] != $addon['installed_at']) { ?>
                                        <br>
                                        <small class="text-muted" title="<?php echo date('d.m.Y H:i:s', strtotime($addon['updated_at'])); ?>">
                                            обновлен: <?php echo time_ago($addon['updated_at']); ?>
                                        </small>
                                    <?php } ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $addon['type'] === 'install' ? 'success' : 'warning'; ?>">
                                        <?php echo $addon['type'] === 'install' ? 'Установка' : 'Обновление'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($addon['author_name'])) { ?>
                                        <div><?php echo html($addon['author_name']); ?></div>
                                        <?php if (!empty($addon['author_email'])) { ?>
                                            <small class="text-muted"><?php echo html($addon['author_email']); ?></small>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="text-muted">—</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo date('d.m.Y H:i', strtotime($addon['installed_at'])); ?>
                                    </small>
                                    <?php if (!empty($addon['updated_at'])) { ?>
                                        <br>
                                        <small class="text-muted">обновлен: <?php echo date('d.m.Y', strtotime($addon['updated_at'])); ?></small>
                                    <?php } ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-secondary info-addon"
                                                data-id="<?php echo $addon['id']; ?>"
                                                title="Информация">
                                            <?php echo bloggy_icon('bs', 'info-circle', '16', '#000'); ?>
                                        </button>
                                        <a href="<?php echo ADMIN_URL; ?>/addons/delete/<?php echo $addon['id']; ?>" 
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Вы уверены, что хотите удалить пакет «<?php echo html($addon['title']); ?>»?')"
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
                
                <div class="mt-3 text-muted small">
                    <div class="d-flex justify-content-between">
                        <span>Всего пакетов: <?php echo $addonCount; ?></span>
                        <span>
                            <?php echo bloggy_icon('bs', 'info-circle', '14', '#6C6C6C', 'me-1'); ?>
                            При удалении пакета файлы остаются на сервере
                        </span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addonInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Информация о пакете</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="addon-info-content">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Загрузка...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php add_admin_js('templates/default/admin/assets/js/controllers/addons.js'); ?>