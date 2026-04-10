<?php
return [
    'admin/debug' => ['controller' => 'Debug', 'action' => 'adminIndex', 'admin' => true],
    'admin/debug/logs' => ['controller' => 'Debug', 'action' => 'adminGetLogs', 'admin' => true],
    'admin/debug/log/{id}' => ['controller' => 'Debug', 'action' => 'adminGetLog', 'admin' => true],
    'admin/debug/delete/{id}' => ['controller' => 'Debug', 'action' => 'adminDelete', 'admin' => true],
    'admin/debug/delete-all' => ['controller' => 'Debug', 'action' => 'adminDeleteAll', 'admin' => true],
    'admin/debug/mark-fixed/{id}' => ['controller' => 'Debug', 'action' => 'adminMarkFixed', 'admin' => true],
    'admin/debug/stats' => ['controller' => 'Debug', 'action' => 'adminStats', 'admin' => true],
    'admin/debug/toggle' => ['controller' => 'Debug', 'action' => 'adminToggle', 'admin' => true]
];