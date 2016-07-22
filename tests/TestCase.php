<?php

namespace Tests;

use PHPUnit_Framework_TestCase;
use Phalcon\Di;
use Phwoolcon\Cache;
use Phwoolcon\Config;
use Phwoolcon\Log;
use Phwoolcon\Tests\Helper\TestCase as PhwoolconTestCase;

class TestCase extends PhwoolconTestCase
{

    public function setUp()
    {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $this->di = Di::getDefault();
        Cache::flush();
        Config::clearCache();
        PHPUnit_Framework_TestCase::setUp();

        $class = get_class($this);
        Log::debug("Running {$class}::{$this->getName()}() ...");
    }
}
