<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phwoolcon\Aliases;
use Phwoolcon\Cache;
use Phwoolcon\Config;
use Phwoolcon\Cookie;
use Phwoolcon\Db;
use Phwoolcon\I18n;
use Phwoolcon\Log;
use Phwoolcon\Router;
use Phwoolcon\Session;
use Phwoolcon\View;

if (!extension_loaded('phalcon')) {
    echo $error = 'Extension "phalcon" not detected, please install or activate it.';
    throw new RuntimeException($error);
}

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));

// The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
$di = new FactoryDefault();
$di->setShared('ROOT_PATH', function () {
    return ROOT_PATH;
});
$di->setShared('CONFIG_PATH', function () {
    static $configPath;
    $configPath or $configPath = ROOT_PATH . '/config';
    return $configPath;
});

include ROOT_PATH . '/vendor/phwoolcon/phwoolcon/src/functions.php';

// Register class loader
if (!$classMap = include ROOT_PATH . '/vendor/composer/autoload_classmap.php') {
    echo $error = sprintf('Autoload class map not available, please run command "%s/bin/dump-autoload" first.', ROOT_PATH);
    throw new UnexpectedValueException($error);
}
$loader = new Loader;
$loader->registerClasses($classMap)
    ->register();
$di->setShared('loader', $loader);

// Register components
Db::register($di);
Cache::register($di);
Log::register($di);
Config::register($di);
Aliases::register($di);
Router::register($di);
I18n::register($di);
Session::register($di);
View::register($di);

$loader->registerNamespaces(Config::get('app.autoload.namespaces'), true);

return $di;
