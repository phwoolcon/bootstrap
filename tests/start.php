<?php

define('ROOT_PATH', dirname(__DIR__));

error_reporting(-1);

include ROOT_PATH . '/bootstrap/functions.php';
include ROOT_PATH . '/bootstrap/di.php';

$loader->registerNamespaces([
    'Tests' => ROOT_PATH . '/tests',
], true);
