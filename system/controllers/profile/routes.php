<?php
return [
    'profile/edit' => ['controller' => 'Profile', 'action' => 'edit'],
    'profile/update' => ['controller' => 'Profile', 'action' => 'update'],
    'profile/sessions' => ['controller' => 'Profile', 'action' => 'sessions'],
    'profile/terminate-session' => ['controller' => 'Profile', 'action' => 'terminateSession'],
    'profile/terminate-all-sessions' => ['controller' => 'Profile', 'action' => 'terminateAllSessions'],
    'profile/delete' => ['controller' => 'Profile', 'action' => 'delete'],
    'profile' => ['controller' => 'Profile', 'action' => 'index'],
    'profile/{username}' => ['controller' => 'Profile', 'action' => 'show'],
];