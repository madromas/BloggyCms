<?php

return [
    'admin/fragments' => ['controller' => 'AdminFragment', 'action' => 'adminIndex', 'admin' => true],
    'admin/fragments/create' => ['controller' => 'AdminFragment', 'action' => 'create', 'admin' => true],
    'admin/fragments/edit/{id}' => ['controller' => 'AdminFragment', 'action' => 'edit', 'admin' => true],
    'admin/fragments/delete/{id}' => ['controller' => 'AdminFragment', 'action' => 'delete', 'admin' => true],
    'admin/fragments/fields/{id}' => ['controller' => 'AdminFragment', 'action' => 'fields', 'admin' => true],
    'admin/fragments/entries/{id}' => ['controller' => 'AdminFragment', 'action' => 'entries', 'admin' => true],
    'admin/fragments/entry/create/{id}' => ['controller' => 'AdminFragment', 'action' => 'entryCreate', 'admin' => true],
    'admin/fragments/entry/edit/{id}' => ['controller' => 'AdminFragment', 'action' => 'entryEdit', 'admin' => true],
    'admin/fragments/entry/delete/{id}' => ['controller' => 'AdminFragment', 'action' => 'entryDelete', 'admin' => true],
    'admin/fragments/reorder-entries' => ['controller' => 'AdminFragment', 'action' => 'reorderEntries', 'admin' => true],
    'fragment/{systemName}' => ['controller' => 'Fragment', 'action' => 'show'],
];