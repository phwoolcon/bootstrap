<?php
return [
    'debug' => false,
    'cache_config' => true,
    'autoload' => [
        'namespaces' => [
            'Admin' => ROOT_PATH . '/app/Admin',
            'Auth' => ROOT_PATH . '/app/Auth',
            'Commands' => ROOT_PATH . '/bin/commands',
        ],
    ],
    'timezone' => 'UTC',
    'url' => 'http://localhost',
    'class_aliases' => [
        'Config' => 'Phwoolcon\Config',
        'Log' => 'Phwoolcon\Log',
        'Router' => 'Phwoolcon\Router',
        'Session' => 'Phwoolcon\Session',
        'View' => 'Phwoolcon\View',
        'User' => 'Phwoolcon\Model\User',
        'DisableSessionFilter' => 'Phwoolcon\Router\Filter\DisableSessionFilter',
    ],
    'log' => [
        'adapter' => 'file',
        'file' => 'phwoolcon.log',
    ],
];
