<?php
            define('BASE_PATH', dirname(dirname(__DIR__)));
            define('SYSTEM_PATH', BASE_PATH.'/system');
            define('TEMPLATES_PATH', BASE_PATH.'/templates');
            define('UPLOADS_PATH', BASE_PATH.'/uploads');
            define('BASE_URL', 'http://bloggy4');
            define('ADMIN_URL', BASE_URL.'/admin');
            define('DEFAULT_TEMPLATE', 'default');
            define('USER_ONLINE_INTERVAL', 300);
            define('CACHE_DIR', BASE_PATH.'/cache');
            if(!is_dir(CACHE_DIR)) @mkdir(CACHE_DIR,0755,true);