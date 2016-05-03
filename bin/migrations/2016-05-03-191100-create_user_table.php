<?php

use Phalcon\Db\Adapter\Pdo as Adapter;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;

/* @var \Phwoolcon\Cli\Command\Migrate $this */
return [
    'up' => function (Adapter $db) {
        $db->createTable('users', null, [
            'columns' => [
                new Column('id', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => true,
                    'primary' => true,
                ]),
                new Column('username', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 20,
                    'notNull' => true,
                ]),
                new Column('email', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => true,
                    'default' => '',
                ]),
                new Column('mobile', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 20,
                    'notNull' => true,
                    'default' => '',
                ]),
                new Column('password', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => true,
                ]),
                new Column('confirmed', [
                    'type' => Column::TYPE_BOOLEAN,
                    'size' => 1,
                    'unsigned' => true,
                    'notNull' => true,
                    'default' => 0,
                ]),
                new Column('created_at', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => false,
                ]),
                new Column('updated_at', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => false,
                ]),
            ],
            'indexes' => [
                new Index('username', ['username']),
                new Index('email', ['email']),
                new Index('mobile', ['mobile']),
            ],
        ]);
        $db->createTable('user_profile', null, [
            'columns' => [
                new Column('user_id', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => true,
                    'primary' => true,
                ]),
                new Column('name', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 32,
                    'notNull' => true,
                    'default' => '',
                ]),
                new Column('avatar', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => true,
                    'default' => '',
                ]),
                new Column('confirmation_code', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 32,
                    'notNull' => true,
                    'default' => '',
                ]),
                new Column('remember_token', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 32,
                    'notNull' => true,
                    'default' => '',
                ]),
                new Column('extra_data', [
                    'type' => Column::TYPE_TEXT,
                    'notNull' => false,
                ]),
            ],
        ]);
    },
    'down' => function (Adapter $db) {
        $db->dropTable('users');
        $db->dropTable('user_profile');
    },
];
