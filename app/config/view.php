<?php

return [
    'debug' => true,
    'path' => ROOT_PATH . '/app/views/',
    'theme' => 'default',
    'top_level' => 'html',
    'default_layout' => 'default',
    'title_separator' => ' - ',
    'title_suffix' => 'Phwoolcon',
    'assets' => [
        'head-css' => [
            'css/styles.css',
        ],
        'head-js' => [
            'js/js.js',
        ],
        'body-js' => [
            'js/js.js',
        ],
        'ie-hack-css' => [
            'css/styles.css',
        ],
        'ie-hack-js' => [
            'js/js.js',
        ],
    ],
    'admin' => [
        'title_suffix' => 'Admin',
        'theme' => 'default',
        'layout' => 'default',
        'assets' => [
            'head-css' => [
                'css/styles.css',
            ],
            'head-js' => [
                'js/js.js',
            ],
            'body-js' => [
                'js/js.js',
            ],
            'ie-hack-css' => [
                'css/styles.css',
            ],
            'ie-hack-js' => [
                'js/js.js',
            ],
        ],
    ],
    'options' => [
        'assets_options' => [
            'base_path' => ROOT_PATH . '/public',
            'assets_dir' => 'assets',
            'cache_assets' => true,
        ],
    ],
];
