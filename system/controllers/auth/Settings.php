<?php
namespace auth;

class AdminSettings {
    public static function getForm($currentSettings) {
        $fieldsets = [
            new \Fieldset(LANG_CONTROLLER_AUTH_SETTINGS_FIELDSET_AUTH, [
                'icon' => 'bi bi-person',
                'columns' => '12',
                'fields' => [
                    \FieldFactory::checkbox('disable_restore', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_DISABLE_RESTORE,
                        'hint' => LANG_CONTROLLER_AUTH_SETTINGS_DISABLE_RESTORE_HINT,
                        'default' => false,
                        'switch' => true
                    ]),
                    \FieldFactory::select('auth_redirect', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_AUTH_REDIRECT,
                        'default' => 'show_profile',
                        'options' => [
                            'show_profile' => LANG_CONTROLLER_AUTH_SETTINGS_OPTION_SHOW_PROFILE,
                            'show_index' => LANG_CONTROLLER_AUTH_SETTINGS_OPTION_SHOW_INDEX,
                        ],
                    ]),
                ]
            ]),

            new \Fieldset(LANG_CONTROLLER_AUTH_SETTINGS_FIELDSET_REGISTER, [
                'icon' => 'bi bi-person-plus',
                'columns' => '12',
                'fields' => [
                    \FieldFactory::checkbox('enable_register', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_ENABLE_REGISTER,
                        'default' => true,
                        'switch' => true
                    ]),
                    \FieldFactory::string('disable_register_reason', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_DISABLE_REGISTER_REASON,
                        'default' => LANG_CONTROLLER_AUTH_SETTINGS_DISABLE_REGISTER_REASON_DEFAULT,
                        'show' => 'field:enable_register',
                    ]),
                ]
            ]),

            new \Fieldset(LANG_CONTROLLER_AUTH_SETTINGS_FIELDSET_ADMIN_ACCESS, [
                'icon' => 'bi bi-gear',
                'columns' => '12',
                'fields' => [
                    \FieldFactory::checkbox('show_qa', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_SHOW_QA,
                        'default' => false,
                        'switch' => true
                    ]),
                    \FieldFactory::select('qa_param', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_QA_PARAM,
                        'hint' => LANG_CONTROLLER_AUTH_SETTINGS_QA_PARAM_HINT,
                        'default' => 'option2',
                        'options' => [
                            'opt1' => LANG_CONTROLLER_AUTH_SETTINGS_QA_OPTION1,
                            'opt2' => LANG_CONTROLLER_AUTH_SETTINGS_QA_OPTION2,
                            'opt3' => LANG_CONTROLLER_AUTH_SETTINGS_QA_OPTION3
                        ],
                        'required' => true,
                        'show' => 'field:show_qa'
                    ]),
                    \FieldFactory::repeater('words_array', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_WORDS_ARRAY,
                        'hint' => LANG_CONTROLLER_AUTH_SETTINGS_WORDS_ARRAY_HINT,
                        'fields' => [
                            [
                                'name' => 'question',
                                'title' => LANG_CONTROLLER_AUTH_SETTINGS_QUESTION,
                                'type' => 'string',
                                'hint' => LANG_CONTROLLER_AUTH_SETTINGS_QUESTION_HINT,
                            ],
                            [
                                'name' => 'answer',
                                'title' => LANG_CONTROLLER_AUTH_SETTINGS_ANSWER,
                                'type' => 'string',
                                'hint' => LANG_CONTROLLER_AUTH_SETTINGS_ANSWER_HINT,
                            ],
                        ],
                        'show' => 'field:show_qa'
                    ]),
                    \FieldFactory::number('count_auth', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_COUNT_AUTH,
                        'hint' => LANG_CONTROLLER_AUTH_SETTINGS_COUNT_AUTH_HINT,
                        'default' => '3',
                        'required' => true,
                        'max' => 5,
                        'show' => 'field:show_qa'
                    ]),
                    \FieldFactory::number('count_time', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_COUNT_TIME,
                        'hint' => LANG_CONTROLLER_AUTH_SETTINGS_COUNT_TIME_HINT,
                        'default' => '20',
                        'required' => true,
                        'show' => 'field:show_qa'
                    ]),
                    \FieldFactory::alert('auth_info', [
                        'title' => LANG_CONTROLLER_AUTH_SETTINGS_ALERT_TITLE,
                        'hint' => LANG_CONTROLLER_AUTH_SETTINGS_ALERT_HINT,
                        'type' => 'danger',
                        'icon' => 'info-circle',
                        'show' => 'field:show_qa',
                        'full_width' => true
                    ])
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