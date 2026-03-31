<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <?php echo bloggy_icon('bs', 'puzzle', '24', '#000', 'me-2'); ?>
            Фрагменты
        </h4>
        <div class="d-flex gap-2">
            <a href="<?php echo ADMIN_URL; ?>/fragments/create" class="btn btn-primary">
                <?php echo bloggy_icon('bs', 'plus-lg', '20', '#fff', 'me-2'); ?>
                Создать фрагмент
            </a>
        </div>
    </div>

    <?php if (!empty($randomHint)): ?>
        <div class="alert alert-info d-flex align-items-center mb-4">
            <?php echo bloggy_icon('bs', 'info-circle', '16', '#5AAFC9', 'me-2'); ?>
            <span><?php echo html($randomHint); ?></span>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($fragments)): ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <?php echo bloggy_icon('bs', 'puzzle', '48', '#6C6C6C'); ?>
                    </div>
                    <h5 class="text-muted">Фрагменты не созданы</h5>
                    <p class="text-muted">Создайте первый фрагмент для вашего сайта</p>
                    <a href="<?php echo ADMIN_URL; ?>/fragments/create" class="btn btn-primary">
                        <?php echo bloggy_icon('bs', 'plus-lg', '20', '#fff', 'me-2'); ?>
                        Создать фрагмент
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                             <tr>
                                <th>Название</th>
                                <th>Системное имя</th>
                                <th>Описание</th>
                                <th>Записей</th>
                                <th>Статус</th>
                                <th class="text-end">Действия</th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fragments as $fragment): 
                                $stats = $this->fragmentModel->getStats($fragment['id']);
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo html($fragment['name']); ?></strong>
                                    </td>
                                    <td>
                                        <code class="text-muted"><?php echo html($fragment['system_name']); ?></code>
                                    </td>
                                    <td>
                                        <?php echo html(mb_substr($fragment['description'] ?? '', 0, 100)); ?>
                                        <?php if (mb_strlen($fragment['description'] ?? '') > 100): ?>...<?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo ADMIN_URL; ?>/fragments/entries/<?php echo $fragment['id']; ?>" 
                                           class="badge bg-info text-decoration-none">
                                            <?php echo $stats['total']; ?> записей
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $fragment['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo $fragment['status'] === 'active' ? 'Активен' : 'Неактивен'; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo ADMIN_URL; ?>/fragments/entries/<?php echo $fragment['id']; ?>" 
                                               class="btn btn-outline-secondary"
                                               title="Записи">
                                                <?php echo bloggy_icon('bs', 'list-ul', '16', '#000'); ?>
                                            </a>
                                            <a href="<?php echo ADMIN_URL; ?>/fragments/fields/<?php echo $fragment['id']; ?>" 
                                               class="btn btn-outline-info"
                                               title="Поля">
                                                <?php echo bloggy_icon('bs', 'input-cursor-text', '16', '#000'); ?>
                                            </a>
                                            <a href="<?php echo ADMIN_URL; ?>/fragments/edit/<?php echo $fragment['id']; ?>" 
                                               class="btn btn-outline-primary"
                                               title="Редактировать">
                                                <?php echo bloggy_icon('bs', 'pencil', '16', '#000'); ?>
                                            </a>
                                            <a href="<?php echo ADMIN_URL; ?>/fragments/delete/<?php echo $fragment['id']; ?>" 
                                               class="btn btn-outline-danger"
                                               onclick="return confirm('Вы уверены, что хотите удалить этот фрагмент? Все записи будут удалены.')"
                                               title="Удалить">
                                                <?php echo bloggy_icon('bs', 'trash', '16', '#000'); ?>
                                            </a>
                                        </div>
                                    </td>
                                 </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>