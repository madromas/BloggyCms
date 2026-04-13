<?php
namespace posts;

class PostSettings {
    public static function getForm($currentSettings) {
        $fieldsets = [
            new \Fieldset('Просмотр записи', [
                'icon' => 'bi bi-eye',
                'columns' => 'custom',
                'fields' => [
                    \FieldFactory::alert('alert', [
                        'title' => 'Пока настроек нет...',
                        'hint' => 'Их так много, что возможно для них понадобится отдельный контроллер....',
                        'type' => 'info',
                        'icon' => 'info-circle',
                        'dismissible' => false, 
                        'column' => '12'
                    ]),
                ]
            ]),
        ];
        
        ob_start();
        ?>
        <div class="row">
            <?php foreach ($fieldsets as $fieldset): ?>
            <div class="col-md-12">
                <?= $fieldset->render($currentSettings) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}