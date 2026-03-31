<?php

/**
* Хелпер для работы с фрагментами
*/
class FragmentHelper {
    
    /**
    * @var FragmentModel
    */
    private static $fragmentModel;
    
    /**
    * @var FragmentEntryModel
    */
    private static $entryModel;
    
    /**
    * @var FieldManager
    */
    private static $fieldManager;
    
    /**
    * @var bool Флаг, что шорткоды уже зарегистрированы
    */
    private static $registered = false;
    
    /**
    * @var bool Флаг, что отложенная регистрация запланирована
    */
    private static $scheduled = false;
    
    /**
    * Инициализация
    */
    private static function init() {
        if (!self::$fragmentModel) {
            $db = Database::getInstance();
            self::$fragmentModel = new FragmentModel($db);
            self::$entryModel = new FragmentEntryModel($db);
            self::$fieldManager = new FieldManager($db);
        }
    }
    
    /**
    * Регистрация шорткодов фрагментов
    * Если модели еще не загружены, откладываем регистрацию
    */
    public static function registerShortcodes() {
        // Если класс ShortcodeRegistry не существует, откладываем
        if (!class_exists('ShortcodeRegistry')) {
            self::scheduleDelayedRegistration();
            return;
        }
        
        // Если FragmentModel не существует, откладываем
        if (!class_exists('FragmentModel')) {
            self::scheduleDelayedRegistration();
            return;
        }
        
        if (self::$registered) {
            return;
        }
        
        self::init();
        
        $fragments = self::$fragmentModel->getActive();
        
        foreach ($fragments as $fragment) {
            $systemName = $fragment['system_name'];

            ShortcodeRegistry::add($systemName, function($attrs) use ($systemName) {
                return self::renderFragment($systemName);
            });
            
            ShortcodeRegistry::add($systemName, function($attrs, $content = null) use ($systemName) {
                if ($content !== null) {
                    return self::renderFragmentLoop($systemName, $content);
                }
                return self::renderFragment($systemName);
            });
            
            ShortcodeRegistry::add('ctype:' . $systemName, function($attrs, $content = null) use ($systemName) {
                if ($content !== null) {
                    return self::renderFragmentLoop($systemName, $content);
                }
                return self::renderFragment($systemName);
            });
        }
        
        self::$registered = true;
    }
    
    /**
    * Планирует отложенную регистрацию шорткодов через событие app.init
    */
    private static function scheduleDelayedRegistration() {
        if (self::$scheduled) {
            return;
        }
        
        self::$scheduled = true;
        
        // Регистрируем обработчик события app.init
        if (class_exists('Event')) {
            Event::listen('app.init', function() {
                self::registerShortcodes();
            }, 100);
        }
    }
    
    /**
    * Рендеринг фрагмента
    * 
    * @param string $systemName
    * @return string
    */
    public static function renderFragment($systemName) {
        self::init();
        
        $fragment = self::$fragmentModel->getBySystemName($systemName);
        
        if (!$fragment || $fragment['status'] !== 'active') {
            return '<!-- Фрагмент "' . htmlspecialchars($systemName) . '" не найден -->';
        }
        
        self::loadFragmentAssets($fragment);
        
        $entries = self::$entryModel->getByFragment($fragment['id']);
        $fields = self::$fragmentModel->getFields($fragment['id']);
        
        ob_start();
        self::renderFragmentContent($fragment, $entries, $fields);
        return ob_get_clean();
    }
    
    /**
    * Рендеринг цикла фрагмента 
    * @param string $systemName
    * @param string $template
    * @return string
    */
    public static function renderFragmentLoop($systemName, $template) {
        self::init();
        
        $fragment = self::$fragmentModel->getBySystemName($systemName);
        
        if (!$fragment || $fragment['status'] !== 'active') {
            return '<!-- Фрагмент "' . htmlspecialchars($systemName) . '" не найден -->';
        }
        
        $entries = self::$entryModel->getByFragment($fragment['id']);
        $fields = self::$fragmentModel->getFields($fragment['id']);
        
        if (empty($entries)) {
            return '';
        }
        
        $output = '';
        $index = 0;
        $total = count($entries);
        
        foreach ($entries as $entry) {
            $index++;
            $itemOutput = $template;
            
            $itemOutput = str_replace('{index}', $index, $itemOutput);
            $itemOutput = str_replace('{total}', $total, $itemOutput);
            $itemOutput = str_replace('{is_first}', $index === 1 ? 'true' : 'false', $itemOutput);
            $itemOutput = str_replace('{is_last}', $index === $total ? 'true' : 'false', $itemOutput);
            $itemOutput = str_replace('{is_even}', $index % 2 === 0 ? 'true' : 'false', $itemOutput);
            $itemOutput = str_replace('{is_odd}', $index % 2 !== 0 ? 'true' : 'false', $itemOutput);
            
            foreach ($fields as $field) {
                $fieldName = $field['system_name'];
                $value = $entry['data'][$fieldName] ?? '';
                
                $placeholder = '{field:' . $fieldName . '}';
                $displayPlaceholder = '{field_display:' . $fieldName . '}';
                
                $itemOutput = str_replace($placeholder, htmlspecialchars($value), $itemOutput);
                $itemOutput = str_replace($displayPlaceholder, self::renderFieldValue($field, $value), $itemOutput);
            }
            
            $output .= $itemOutput;
        }
        
        return $output;
    }
    
    /**
    * Загрузка ресурсов фрагмента
    * @param array $fragment
    */
    private static function loadFragmentAssets($fragment) {
        if (!empty($fragment['css_files'])) {
            foreach ($fragment['css_files'] as $cssFile) {
                if (!empty($cssFile)) {
                    front_css($cssFile);
                }
            }
        }
        
        if (!empty($fragment['js_files'])) {
            foreach ($fragment['js_files'] as $jsFile) {
                if (!empty($jsFile)) {
                    front_js($jsFile);
                }
            }
        }
        
        if (!empty($fragment['inline_css'])) {
            front_inline_css($fragment['inline_css']);
        }
        
        if (!empty($fragment['inline_js'])) {
            front_inline_js($fragment['inline_js']);
        }
    }
    
    /**
     * Рендеринг контента фрагмента
     * 
     * @param array $fragment
     * @param array $entries
     * @param array $fields
     */
    private static function renderFragmentContent($fragment, $entries, $fields) {
        ?>
        <div class="fragment fragment-<?php echo htmlspecialchars($fragment['system_name']); ?>">
            <?php if (!empty($fragment['description'])) { ?>
                <div class="fragment-description">
                    <?php echo htmlspecialchars($fragment['description']); ?>
                </div>
            <?php } ?>
            
            <?php if (empty($entries)) { ?>
                <div class="fragment-empty">
                    <p>Нет записей в этом фрагменте</p>
                </div>
            <?php } else { ?>
                <div class="fragment-entries">
                    <?php foreach ($entries as $index => $entry) { ?>
                        <div class="fragment-entry">
                            <?php foreach ($fields as $field) { ?>
                                <?php $value = $entry['data'][$field['system_name']] ?? null; ?>
                                <?php if ($value !== null && $value !== '') { ?>
                                    <div class="fragment-field fragment-field-<?php echo htmlspecialchars($field['system_name']); ?>">
                                        <div class="field-label"><?php echo htmlspecialchars($field['name']); ?>:</div>
                                        <div class="field-value">
                                            <?php echo self::renderFieldValue($field, $value); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }
    
    /**
    * Рендеринг значения поля
    * 
    * @param array $field
    * @param mixed $value
    * @return string
    */
    private static function renderFieldValue($field, $value) {
        return self::$fieldManager->renderFieldDisplay(
            $field['type'],
            $value,
            $field['config'],
            'fragment_entry',
            0
        );
    }

}