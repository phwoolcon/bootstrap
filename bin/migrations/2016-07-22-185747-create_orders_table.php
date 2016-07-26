<?php

use Phalcon\Db\Adapter\Pdo as Adapter;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phwoolcon\Cli\Command\Migrate;

return [
    'up' => function (Adapter $db, Migrate $migrate) {
        // Create orders table
        $db->createTable('orders', null, [
            'columns' => [
                new Column('id', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => true,
                    'primary' => true,
                ]),
                new Column('prefixed_order_id', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => true,
                ]),
                new Column('trade_id', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => true,
                ]),
                new Column('txn_id', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 100,
                    'notNull' => false,
                ]),
                new Column('product_name', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => true,
                ]),
                new Column('user_identifier', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => false,
                ]),
                new Column('username', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => false,
                ]),
                new Column('client_id', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => true,
                ]),
                new Column('payment_gateway', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => false,
                ]),
                new Column('payment_method', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => false,
                ]),
                new Column('amount', [
                    'type' => Column::TYPE_DECIMAL,
                    'size' => 10,
                    'scale' => 2,
                    'unsigned' => true,
                    'notNull' => true,
                    'default' => 0,
                ]),
                new Column('discount_amount', [
                    'type' => Column::TYPE_DECIMAL,
                    'size' => 10,
                    'scale' => 2,
                    'unsigned' => true,
                    'notNull' => true,
                    'default' => 0,
                ]),
                new Column('cash_to_pay', [
                    'type' => Column::TYPE_DECIMAL,
                    'size' => 10,
                    'scale' => 2,
                    'unsigned' => true,
                    'notNull' => true,
                    'default' => 0,
                ]),
                new Column('cash_paid', [
                    'type' => Column::TYPE_DECIMAL,
                    'size' => 10,
                    'scale' => 2,
                    'unsigned' => true,
                    'notNull' => true,
                    'default' => 0,
                ]),
                new Column('currency', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 10,
                    'notNull' => false,
                ]),
                new Column('amount_in_currency', [
                    'type' => Column::TYPE_DECIMAL,
                    'size' => 10,
                    'scale' => 2,
                    'unsigned' => true,
                    'notNull' => true,
                    'default' => 0,
                ]),
                new Column('status', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => false,
                ]),
                new Column('created_at', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => false,
                ]),
                new Column('completed_at', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => false,
                ]),
                new Column('canceled_at', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => false,
                ]),
                new Column('failed_at', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => false,
                ]),
                new Column('callback_url', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 255,
                    'notNull' => false,
                ]),
                new Column('callback_status', [
                    'type' => Column::TYPE_VARCHAR,
                    'size' => 50,
                    'notNull' => false,
                ]),
                new Column('callback_next_retry', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => false,
                ]),
            ],
            'indexes' => [
                new Index('trade_id', ['trade_id', 'client_id'], 'UNIQUE'),
                new Index('prefixed_order_id', ['prefixed_order_id'], 'UNIQUE'),
                new Index('user_identifier', ['user_identifier', 'client_id']),
            ],
            'options' => [
                'TABLE_COLLATION' => $migrate->getDefaultTableCharset(),
            ],
        ]);

        // Create order_data table
        $db->createTable('order_data', null, [
            'columns' => [
                new Column('order_id', [
                    'type' => Column::TYPE_BIGINTEGER,
                    'size' => 20,
                    'unsigned' => true,
                    'notNull' => true,
                    'primary' => true,
                ]),
                new Column('request_data', [
                    'type' => Column::TYPE_TEXT,
                    'notNull' => false,
                ]),
                new Column('data', [
                    'type' => Column::TYPE_TEXT,
                    'notNull' => false,
                ]),
                new Column('status_history', [
                    'type' => Column::TYPE_TEXT,
                    'notNull' => false,
                ]),
            ],
            'options' => [
                'TABLE_COLLATION' => $migrate->getDefaultTableCharset(),
            ],
        ]);
    },
    'down' => function (Adapter $db, Migrate $migrate) {
        $db->tableExists('order_data') and $db->dropTable('order_data');
        $db->tableExists('orders') and $db->dropTable('orders');
    },
];
