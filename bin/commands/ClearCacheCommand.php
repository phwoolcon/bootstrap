<?php
namespace Commands;

use Phwoolcon\Cache;
use Phwoolcon\Cli\Command;

class ClearCacheCommand extends Command
{

    public function fire()
    {
        Cache::flush();
        $this->info('Cache cleared.');
    }
}
