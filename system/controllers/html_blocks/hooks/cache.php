<?php

/**
* Хуки для очистки кеша CSS блоков
*/

Event::listen('html_block.saved', function() {
    if (function_exists('clear_blocks_assets_cache')) {
        clear_blocks_assets_cache();
    }
    if (function_exists('regenerate_blocks_css')) {
        regenerate_blocks_css();
    }
}, 10, 0);

Event::listen('html_block.deleted', function() {
    if (function_exists('clear_blocks_assets_cache')) {
        clear_blocks_assets_cache();
    }
    if (function_exists('regenerate_blocks_css')) {
        regenerate_blocks_css();
    }
}, 10, 0);