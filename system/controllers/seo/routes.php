<?php
return [
    'admin/seo' => ['controller' => 'Seo', 'action' => 'adminIndex', 'admin' => true],
    'admin/seo/settings' => ['controller' => 'Seo', 'action' => 'adminSettings', 'admin' => true],
    'admin/seo/clear-cache' => ['controller' => 'Seo', 'action' => 'adminClearCache', 'admin' => true],
    'sitemap.xml' => ['controller' => 'Seo', 'action' => 'sitemap'],
    'robots.txt' => ['controller' => 'Seo', 'action' => 'robots'],
    'rss.xml' => ['controller' => 'Seo', 'action' => 'rss'],
    'rss/category/{slug}' => ['controller' => 'Seo', 'action' => 'rssCategory'],
    'rss/tag/{slug}' => ['controller' => 'Seo', 'action' => 'rssTag'],
];