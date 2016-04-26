<?php
return [
    'debug' => false,
    'autoload' => [
        'namespaces' => [
            'Admin' => ROOT_PATH . '/app/Admin',
            'Commands' => ROOT_PATH . '/bin/commands',
        ],
    ],
    'timezone' => 'UTC',
    'url' => 'http://localhost',
    'class_aliases' => [
        'Config' => 'Phwoolcon\Config',
        'Log' => 'Phwoolcon\Log',
        'Router' => 'Phwoolcon\Router',
        'View' => 'Phwoolcon\View',
        'User' => 'Phwoolcon\Model\User',
    ],
    'log' => [
        'adapter' => 'file',
        'file' => 'phwoolcon.log',
    ],
];
