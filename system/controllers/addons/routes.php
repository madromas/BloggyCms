<?php
return [
    'admin/addons' => ['controller' => 'AdminAddons', 'action' => 'adminIndex', 'admin' => true],
    'admin/addons/install' => ['controller' => 'AdminAddons', 'action' => 'install', 'admin' => true],
    'admin/addons/upload' => ['controller' => 'AdminAddons', 'action' => 'upload', 'admin' => true],
    'admin/addons/analyze' => ['controller' => 'AdminAddons', 'action' => 'analyze', 'admin' => true],
    'admin/addons/delete/{id}' => ['controller' => 'AdminAddons', 'action' => 'delete', 'admin' => true],
    'admin/addons/info/{id}' => ['controller' => 'AdminAddons', 'action' => 'info', 'admin' => true],
    'admin/addons/check-updates' => ['controller' => 'AdminAddons', 'action' => 'checkUpdates', 'admin' => true],
];