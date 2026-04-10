<?php

/**
* Функция для перенаправления на главную страницу фронта.
* @return void
*/
function go_home(): void {
    header('Location: /');
    exit;
}

/**
* Функция для получения URL главной страницы.
* @return string URL главной страницы.
*/
function get_home_url(): string {
    return BASE_URL;
}

/**
* Функция для генерации пути к изображению в шаблоне.
* @param string $file Имя файла изображения (например, "logo.png").
* @param string $subpath Подпапка внутри assets/img/ (например, "logo/").
* @return string Полный URL к изображению.
*/
function front_image(string $file, string $subpath = ''): string {

    $subpath = trim($subpath, '/');
    
    if (!empty($subpath)) {
        $subpath .= '/';
    }
    
    return BASE_URL . '/templates/' . DEFAULT_TEMPLATE . '/front/assets/img/' . $subpath . $file;
}

/**
* Выводит содержимое HTML-блока по его slug.
* @param string $slug Уникальный идентификатор блока.
* @return void
*/
function render_html_block(string $slug): void {
    static $assets_loaded = [];
    static $loaded_blocks = [];

    $db = Database::getInstance();
    
    $block = $db->fetch("
        SELECT 
            hb.*, 
            COALESCE(hbt.system_name, 'DefaultBlock') as block_type,
            hb.template as block_template
        FROM html_blocks hb 
        LEFT JOIN html_block_types hbt ON hb.type_id = hbt.id 
        WHERE hb.slug = ?
    ", [$slug]);

    if ($block) {
        if (!isset($loaded_blocks[$slug])) {
            if (!empty($block['css_files'])) {
                $cssFiles = json_decode($block['css_files'], true);
                if (is_array($cssFiles)) {
                    foreach ($cssFiles as $cssFile) {
                        add_frontend_css($cssFile);
                    }
                }
            }
            
            if (!empty($block['js_files'])) {
                $jsFiles = json_decode($block['js_files'], true);
                if (is_array($jsFiles)) {
                    foreach ($jsFiles as $jsFile) {
                        add_frontend_js($jsFile);
                    }
                }
            }
            
            if (!empty($block['inline_css'])) {
                add_inline_css($block['inline_css']);
            }
            
            if (!empty($block['inline_js'])) {
                add_inline_js($block['inline_js']);
            }

            if (!empty($block['block_type']) && $block['block_type'] !== 'DefaultBlock') {
                $blockTypeManager = new HtmlBlockTypeManager($db);
                $blockTypeManager->loadBlockFrontendAssets($block['block_type']);
            }
            
            $loaded_blocks[$slug] = true;
        }
        
        $content = '';
        
        $settings = [];
        if (!empty($block['settings'])) {
            $settings = json_decode($block['settings'], true);
        }
        
        $blockType = $block['block_type'] ?? 'DefaultBlock';
        
        if ($blockType === 'DefaultBlock') {
            $content = $settings['html'] ?? '';
            
            if (function_exists('process_shortcodes')) {
                $content = process_shortcodes($content);
            }
            
            if (empty(trim($content))) {
                $content = '<div class="alert alert-info">Блок "' . htmlspecialchars($block['name'] ?? '') . '" не имеет содержимого.</div>';
            }
        } elseif (!empty($blockType)) {
            $blockTypeManager = new HtmlBlockTypeManager($db);
            $templateToUse = $block['block_template'] ?? 'default';
            
            $content = $blockTypeManager->renderBlockFront($blockType, $settings, $templateToUse);
        } else {
            $content = '<div class="alert alert-warning">Блок "' . htmlspecialchars($block['name'] ?? '') . '" имеет неопределенный тип.</div>';
        }
        
        echo $content;
    } else {
        echo '<!-- HTML блок с slug "' . htmlspecialchars($slug) . '" не найден -->';
    }
}

/**
* Предзагружает ассеты HTML-блоков без вывода содержимого.
* @param array $slugs Массив слагов блоков для предзагрузки
* @return void
*/
function preload_html_block_assets(array $slugs): void {
    static $preloaded = [];
    
    foreach ($slugs as $slug) {
        if (isset($preloaded[$slug])) {
            continue;
        }
        
        $db = Database::getInstance();
        $block = $db->fetch("
            SELECT hb.*, hbt.system_name as block_type 
            FROM html_blocks hb 
            LEFT JOIN html_block_types hbt ON hb.type_id = hbt.id 
            WHERE hb.slug = ?
        ", [$slug]);
        
        if ($block) {
            load_html_block_assets($block);
            $preloaded[$slug] = true;
        }
    }
}

/**
* Загружает CSS и JS файлы для HTML-блока.
* @param array $block Данные блока
* @return void
*/
function load_html_block_assets($block): void {

    if (!empty($block['css_files'])) {
        $cssFiles = json_decode($block['css_files'], true);
        foreach ($cssFiles as $cssFile) {
            if (!empty(trim($cssFile))) {
                front_css($cssFile);
            }
        }
    }
    
    if (!empty($block['js_files'])) {
        $jsFiles = json_decode($block['js_files'], true);
        foreach ($jsFiles as $jsFile) {
            if (!empty(trim($jsFile))) {
                front_js($jsFile);
            }
        }
    }
    
    if (!empty($block['inline_css'])) {
        front_inline_css($block['inline_css']);
    }
    
    if (!empty($block['inline_js'])) {
        front_inline_js($block['inline_js']);
    }
    
    if (!empty($block['block_type']) && $block['block_type'] !== 'DefaultBlock') {
        $db = Database::getInstance();
        $blockTypeManager = new HtmlBlockTypeManager($db);
        $blockTypeManager->loadBlockFrontendAssets($block['block_type']);
    }
}

/**
* Форматирует дату в формате "23 июня 2025".
* @param string $date Дата в формате, понятном для strtotime
* @return string Отформатированная дата
*/
function format_date($date) {
    if (!$date) return '';
    
    $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day $month $year";
}

/**
* Возвращает время, прошедшее с указанной даты в человекочитаемом формате
* @param string $date Дата в формате, понятном для strtotime
* @return string Отформатированное время
*/
function time_ago($date) {
    if (!$date) return '';
    
    $timestamp = strtotime($date);
    $current = time();
    $diff = $current - $timestamp;
    
    $intervals = [
        'year'   => 31536000,
        'month'  => 2592000,
        'week'   => 604800,
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60
    ];
    
    $forms = [
        'year'   => ['год', 'года', 'лет'],
        'month'  => ['месяц', 'месяца', 'месяцев'],
        'week'   => ['неделя', 'недели', 'недель'],
        'day'    => ['день', 'дня', 'дней'],
        'hour'   => ['час', 'часа', 'часов'],
        'minute' => ['минута', 'минуты', 'минут']
    ];
    
    foreach ($intervals as $interval => $seconds) {
        $count = floor($diff / $seconds);
        if ($count > 0) {
            $form = function($n, $forms) {
                return $n%10==1 && $n%100!=11 ? $forms[0] : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? $forms[1] : $forms[2]);
            };
            
            return $count . ' ' . $form($count, $forms[$interval]) . ' назад';
        }
    }
    
    return 'только что';
}

/**
* Форматирует число в сокращенном виде (например, 1.2K, 1.5M) 
* @param int $number Число для форматирования.
* @return string Отформатированное число.
*/
function format_number(int $number): string
{
    if ($number < 1000) {
        return (string)$number;
    } elseif ($number < 1000000) {
        return number_format($number / 1000, 1) . 'K';
    } else {
        return number_format($number / 1000000, 1) . 'M';
    }
}

/**
* Функция для вывода selected в select-опциях
* @param mixed $value Значение опции
* @param mixed $current Текущее значение
* @param bool $echo Выводить или возвращать
* @return string HTML-атрибут selected
*/
function selected($value, $current, $echo = true) {
    $result = $value == $current ? 'selected="selected"' : '';
    if ($echo) {
        echo $result;
    }
    return $result;
}

/**
* Функция для вывода checked в checkbox-опциях
* @param mixed $value Значение опции
* @param mixed $current Текущее значение
* @param bool $echo Выводить или возвращать
* @return string HTML-атрибут checked
*/
function checked($value, $current, $echo = true) {
    $result = $value == $current ? 'checked="checked"' : '';
    if ($echo) {
        echo $result;
    }
    return $result;
}

/**
* Подключает фавиконку
* @param string|null $path Путь к фавиконке
* @return string HTML-тег link для фавиконки
*/
function favicon($path = null) {
    if ($path === null) {
        $path = BASE_URL . '/templates/default/admin/assets/img/favicon.png';
    }
    
    return '<link rel="icon" type="image/png" href="' . htmlspecialchars($path) . '">' . "\n";
}

/**
* Возвращает название текущего активного шаблона
* @return string Название шаблона
*/
function get_current_template(): string {
    return defined('CURRENT_TEMPLATE') ? CURRENT_TEMPLATE : 'default';
}

/**
* Проверяет, доступен ли блок для текущего шаблона
* @param mixed $blockTemplate Шаблон блока
* @return bool Всегда true (заглушка)
*/
function is_block_available_for_template($blockTemplate): bool {
    return true;
}

/**
* Склонение числительных в русском языке.
* @param int $number Число
* @param array $titles Массив форм слова [именительный, родительный, множественный]
* @return string Правильная форма слова 
* @example plural(5, ['комментарий', 'комментария', 'комментариев']) // "комментариев"
* @example plural(21, ['день', 'дня', 'дней']) // "день"
*/
function plural($number, $titles) {
    $lastTwoDigits = abs($number) % 100;
    $lastDigit = $lastTwoDigits % 10;
    
    if ($lastTwoDigits >= 11 && $lastTwoDigits <= 14) {
        return $titles[2];
    }
    
    if ($lastDigit == 1) {
        return $titles[0];
    } elseif ($lastDigit >= 2 && $lastDigit <= 4) {
        return $titles[1];
    } else {
        return $titles[2];
    }
}
