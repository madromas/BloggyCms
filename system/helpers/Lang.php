<?php

/**
* Хелпер для работы с языковыми константами
*/

if (!function_exists('lang_load')) {
    function lang_load($class) {
        Language::loadForClass($class);
    }
}

if (!function_exists('lang')) {
    function lang($constant, $default = null) {
        return defined($constant) ? constant($constant) : ($default ?? $constant);
    }
}

if (!function_exists('lang_auto')) {
    function lang_auto() {
        static $loaded = false;
        if ($loaded) return;
        
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $caller = $trace[0]['file'] ?? '';
        
        if (preg_match('#/templates/[^/]+/(.+?)\.php$#', $caller, $matches)) {
            $templatePath = $matches[1];
            $currentTemplate = defined('DEFAULT_TEMPLATE') ? DEFAULT_TEMPLATE : 'default';
            Language::load('templates/' . $currentTemplate . '/' . $templatePath);
        }
        
        $loaded = true;
    }
}

if (!function_exists('__')) {
    function __($constant, $default = null) {
        static $autoLoaded = false;
        if (!$autoLoaded) {
            lang_auto();
            $autoLoaded = true;
        }
        return defined($constant) ? constant($constant) : ($default ?? $constant);
    }
}