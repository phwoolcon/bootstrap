<?php

use Phalcon\Db\Adapter\Pdo as Adapter;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phwoolcon\Cli\Command\Migrate;

return [
    'up' => function (Adapter $db, Migrate $migrate) {
        $db->createTable('sso_sites', null, [
            'columns' => [
                new Column('id', [
                    'type' => Column::TYPE_INTEGER,
                    'size' => 10,
                    'unsigned' => true,
                    'notNull' => true,
                    'primary' => true,
                    'autoIncrement' => true,
                ]),
                new Column('site_name', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => true,
                ]),
                new Column('site_url', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => true,
                ]),
                new Column('site_secret', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => true,
                ]),
                new Column('created_at', [
                    'type' => Column::TYPE_TIMESTAMP,
                    'notNull' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                ]),
                new Column('updated_at', [
                    'type' => Column::TYPE_TIMESTAMP,
                    'notNull' => false,
                    'default' => 'CURRENT_TIMESTAMP',
                ]),
            ],
            'options' => [
                'TABLE_COLLATION' => $migrate->getDefaultTableCharset(),
            ],
        ]);
    },
    'down' => function (Adapter $db, Migrate $migrate) {
        $db->tableExists('sso_sites') and $db->dropTable('sso_sites');
    },
];
