<?php

define('ROOT_PATH', dirname(__DIR__));
if (empty($_FILES) && !empty($_SERVER['USE_SERVICE'])) {
    require ROOT_PATH . '/bootstrap/service.php';
}

error_reporting(-1);

include ROOT_PATH . '/bootstrap/functions.php';

//$_SERVER['ENABLE_PROFILER']=1;
profilerStart();
include ROOT_PATH . '/bootstrap/di.php';

set_exception_handler('exceptionHandler');
set_error_handler('errorHandler');

$app = new Phalcon\Mvc\Application($di);
$di->setShared('app', $app);

Router::dispatch()->send();

profilerStop();
