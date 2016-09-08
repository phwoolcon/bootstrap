<?php

define('ROOT_PATH', dirname(__DIR__));
$_SERVER['PHWOOLCON_ENV'] = 'testing';

error_reporting(-1);

include ROOT_PATH . '/bootstrap/functions.php';
include ROOT_PATH . '/bootstrap/start.php';
include ROOT_PATH . '/vendor/phwoolcon/di.php';

$loader->registerNamespaces([
    'Tests' => ROOT_PATH . '/tests',
], true);

Config::set('app.log.file', 'phwoolcon-test.log');

is_file($logFile = ROOT_PATH . '/storage/logs/phwoolcon-test.log') and file_put_contents($logFile, '');
