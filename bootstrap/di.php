<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phwoolcon\Aliases;
use Phwoolcon\Cache;
use Phwoolcon\Config;
use Phwoolcon\Cookies;
use Phwoolcon\Db;
use Phwoolcon\Events;
use Phwoolcon\I18n;
use Phwoolcon\Log;
use Phwoolcon\Router;
use Phwoolcon\Session;
use Phwoolcon\View;
use Phwoolcon\Queue;
use Phwoolcon\Auth\Auth;

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
    $configPath or $configPath = ROOT_PATH . '/app/config';
    return $configPath;
});

if (!is_file($includes = ROOT_PATH . '/vendor/composer/autoload_phalcon_files.php')) {
    echo $error = sprintf('Autoload not ready, please run command "%s/bin/dump-autoload" first.', ROOT_PATH);
    throw new UnexpectedValueException($error);
}
include $includes;

// Register class loader
$loader = new Loader;
$loader->registerClasses(include ROOT_PATH . '/vendor/composer/autoload_classmap.php')
    ->registerNamespaces(include ROOT_PATH . '/vendor/composer/autoload_phalcon_psr4.php')
    ->register();
$di->setShared('loader', $loader);

// Register components
Events::register($di);
Db::register($di);
Cache::register($di);
Log::register($di);
Config::register($di);
Aliases::register($di);
Router::register($di);
I18n::register($di);
Cookies::register($di);
Session::register($di);
View::register($di);
Auth::register($di);
Queue::register($di);

$loader->registerNamespaces(Config::get('app.autoload.namespaces'), true);

return $di;
