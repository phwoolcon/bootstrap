<?php
namespace Commands;

use Phwoolcon\Cache;
use Phwoolcon\Cli\Command;
use Phwoolcon\Config;
use Symfony\Component\Console\Input\InputOption;

class ClearCacheCommand extends Command
{

    protected function configure()
    {
        $this->setDefinition([
            new InputOption('config-only', 'c', InputOption::VALUE_NONE, 'Clear config cache only'),
        ])->setDescription('Clears cache');
    }

    public function fire()
    {
        if ($this->input->getOption('config-only')) {
            Config::clearCache();
            $this->info('Config cache cleared.');
            return;
        }
        Cache::flush();
        Config::clearCache();
        $this->info('Cache cleared.');
    }
}
