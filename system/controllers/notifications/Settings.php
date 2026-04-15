<?php
namespace notifications;

class NotificationsSettings {
    public static function getForm($currentSettings) {
        $fieldsets = [
            new \Fieldset('Комментарии', [
                'icon' => 'bi bi-palette',
                'columns' => '12',
                'fields' => [
                    \FieldFactory::select('variables', [
                        'title' => 'Показывать уведомления',
                        'default' => 'pending',
                        'options' => [
                            'all' => 'Все без исключения',
                            'pending' => 'Только требующие модерации',
                        ],
                    ]),
                ]
            ]),
            
            new \Fieldset('Уведомления об ошибках', [
                'icon' => 'bi bi-bug',
                'columns' => '12',
                'fields' => [
                    \FieldFactory::checkbox('notify_on_new_error', [
                        'title' => 'Уведомлять о новых ошибках в системе',
                        'hint' => 'При появлении новой ошибки в логах (контроллер Debug) будет отправлено уведомление администраторам',
                        'default' => true,
                        'switch' => true
                    ]),
                    
                    \FieldFactory::select('notify_on_error_types', [
                        'title' => 'Типы ошибок для уведомления',
                        'hint' => 'Выберите типы ошибок, о которых нужно уведомлять',
                        'default' => 'error,exception',
                        'options' => [
                            'error' => 'Ошибки PHP',
                            'warning' => 'Предупреждения',
                            'notice' => 'Уведомления',
                            'exception' => 'Исключения'
                        ],
                        'attributes' => [
                            'multiple' => true,
                            'size' => 3
                        ],
                        'show' => 'field:notify_on_new_error'
                    ]),
                    
                    \FieldFactory::checkbox('notify_only_unfixed', [
                        'title' => 'Уведомлять только о неисправленных ошибках',
                        'hint' => 'Повторные уведомления об одной и той же ошибке не будут отправляться, пока она не будет отмечена как исправленная',
                        'default' => true,
                        'switch' => true,
                        'show' => 'field:notify_on_new_error'
                    ]),
                    
                    \FieldFactory::number('error_notification_throttle', [
                        'title' => 'Минимальный интервал между уведомлениями (минуты)',
                        'hint' => 'Чтобы не заспамливать уведомлениями при массовых ошибках',
                        'default' => 60,
                        'min' => 5,
                        'max' => 1440,
                        'show' => 'field:notify_on_new_error'
                    ]),
                ]
            ]),
        ];
        
        ob_start();
        ?>
        <div class="row">
            <?php foreach ($fieldsets as $fieldset) { ?>
            <div class="col-md-12">
                <?= $fieldset->render($currentSettings) ?>
            </div>
            <?php } ?>
        </div>
        <?php
        return ob_get_clean();
    }
}