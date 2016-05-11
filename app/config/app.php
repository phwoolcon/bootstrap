<?php
return [
    'debug' => false,
    'cache_config' => true,
    'enable_https' => false,
    'secure_routes' => [],
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
        'Auth' => 'Phwoolcon\Auth\Auth',
        'Config' => 'Phwoolcon\Config',
        'Log' => 'Phwoolcon\Log',
        'Router' => 'Phwoolcon\Router',
        'Session' => 'Phwoolcon\Session',
        'View' => 'Phwoolcon\View',
        'User' => 'Phwoolcon\Model\User',
        'DisableSessionFilter' => 'Phwoolcon\Router\Filter\DisableSessionFilter',
        'DisableCsrfFilter' => 'Phwoolcon\Router\Filter\DisableCsrfFilter',
        'MultiFilter' => 'Phwoolcon\Router\Filter\MultiFilter',
    ],
    'log' => [
        'adapter' => 'file',
        'file' => 'phwoolcon.log',
    ],
];
