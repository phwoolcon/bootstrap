<?php
namespace Commands;

use Phwoolcon\Cache;
use Phwoolcon\Cli\Command;
use Phwoolcon\Config;

class ClearCacheCommand extends Command
{

    public function fire()
    {
        Cache::flush();
        Config::clearCache();
        $this->info('Cache cleared.');
    }
}
