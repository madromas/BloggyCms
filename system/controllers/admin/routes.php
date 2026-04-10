<?php
return [
    'admin' => ['controller' => 'Admin', 'action' => 'index', 'admin' => true],
    'admin/login' => ['controller' => 'Admin', 'action' => 'login'],
    'admin/logout' => ['controller' => 'Admin', 'action' => 'logout', 'admin' => true],
    'admin/templates' => ['controller' => 'Admin', 'action' => 'templates', 'admin' => true],
    'admin/templates/get-files' => ['controller' => 'Admin', 'action' => 'getTemplateFiles', 'admin' => true],
    'admin/templates/get-file' => ['controller' => 'Admin', 'action' => 'getTemplateFile', 'admin' => true],
    'admin/templates/save' => ['controller' => 'Admin', 'action' => 'saveTemplateFile', 'admin' => true],
    'admin/templates/download-file' => ['controller' => 'Admin', 'action' => 'downloadFile', 'admin' => true],
    'admin/templates/upload-file' => ['controller' => 'Admin', 'action' => 'uploadFile', 'admin' => true],
    'admin/controllers' => ['controller' => 'Controllers', 'action' => 'adminIndex', 'admin' => true],
    'admin/templates/create-front-folder' => ['controller' => 'Admin', 'action' => 'createFrontFolder', 'admin' => true],
    'admin/delete-install-folder' => ['controller' => 'Admin', 'action' => 'deleteInstallFolder', 'admin' => true]
];