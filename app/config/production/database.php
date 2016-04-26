<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'host'       => 'localhost',
            'dbname'   => 'glife',
            'username'   => 'root',
            'password'   => '',
            'charset'    => 'utf8mb4',
            'options'    => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "utf8mb4" COLLATE "utf8mb4_unicode_ci"',
            ],
            'persistent' => true,
        ],
    ],
];
