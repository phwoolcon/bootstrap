<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));
$_SERVER['PHWOOLCON_ROOT_PATH'] = ROOT_PATH;
$_SERVER['PHWOOLCON_CONFIG_PATH'] = ROOT_PATH . '/app/config';

if (!extension_loaded('phalcon')) {
    echo $error = 'Extension "phalcon" not detected, please install or activate it.';
    throw new RuntimeException($error);
}

// The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
$di = new FactoryDefault();

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
