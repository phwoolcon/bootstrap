<?php

use Phalcon\Db\Adapter\Pdo as Adapter;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phwoolcon\Cli\Command\Migrate;

return [
    'up' => function (Adapter $db, Migrate $migrate) {
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
                    'notNull' => false,
                ]),
                new Column('email', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => false,
                ]),
                new Column('mobile', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 20,
                    'notNull' => false,
                ]),
                new Column('password', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 160,
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
                new Index('username', ['username'], 'UNIQUE'),
                new Index('email', ['email'], 'UNIQUE'),
                new Index('mobile', ['mobile'], 'UNIQUE'),
            ],
            'options' => [
                'TABLE_COLLATION' => $migrate->getDefaultTableCharset(),
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
                new Column('real_name', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 32,
                    'notNull' => false,
                ]),
                new Column('avatar', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => true,
                    'default' => '',
                ]),
                new Column('remember_token', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 52,
                    'notNull' => false,
                ]),
                new Column('extra_data', [
                    'type' => Column::TYPE_TEXT,
                    'notNull' => false,
                ]),
            ],
            'references' => [
                new Reference('user_profile_users_user_id', [
                    'referencedTable' => 'users',
                    'columns' => ['user_id'],
                    'referencedColumns' => ['id'],
                    'onDelete' => 'CASCADE',
                    'onUpdate' => 'CASCADE',
                ]),
            ],
            'options' => [
                'TABLE_COLLATION' => $migrate->getDefaultTableCharset(),
            ],
        ]);
    },
    'down' => function (Adapter $db, Migrate $migrate) {
        $db->tableExists('user_profile') and $db->dropTable('user_profile');
        $db->tableExists('users') and $db->dropTable('users');
    },
];
