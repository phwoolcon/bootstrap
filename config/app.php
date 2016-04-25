<?php
return [
    'debug' => false,
    'autoload' => [
        'namespaces' => [
            'Commands' => ROOT_PATH . '/commands',
        ],
    ],
    'load_config_from_db' => false,
    'timezone' => 'UTC',
    'i18n' => [
        'locale' => 'zh_CN',
        'use_browser' => false,
    ],
    'url' => 'http://localhost',
    'class_aliases' => [
        'Config' => 'Phwoolcon\Config',
        'Log' => 'Phwoolcon\Log',
        'Router' => 'Phwoolcon\Router',
        'User' => 'Phwoolcon\Model\User',
    ],
    'log' => [
        'adapter' => 'file',
        'file' => 'phwoolcon.log',
    ],
];
