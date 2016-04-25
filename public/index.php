<?php

define('ROOT_PATH', dirname(__DIR__));
if (empty($_FILES) && !empty($_SERVER['USE_SERVICE'])) {
    require ROOT_PATH . '/bootstrap/service.php';
}

error_reporting(-1);

/**
 * Read function
 */
include ROOT_PATH . '/bootstrap/functions.php';

profilerStart();
/**
 * Read services
 */
include ROOT_PATH . '/bootstrap/di.php';

set_exception_handler('exceptionHandler');
set_error_handler('errorHandler');

/**
 * Handle the request
 */
$app = new Phalcon\Mvc\Application($di);
$di->setShared('app', $app);

Router::dispatch();

profilerStop();
