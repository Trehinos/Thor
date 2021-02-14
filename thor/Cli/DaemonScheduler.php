<?php

namespace Thor\Cli;

use Symfony\Component\Yaml\Yaml;
use Thor\Globals;
use Thor\KernelInterface;

final class DaemonScheduler implements KernelInterface
{

    /**
     * @param Daemon[] $daemons
     */
    public function __construct(
        private array $daemons
    ) {
    }

    public function execute(): void
    {
        foreach ($this->daemons as $daemon) {
            $state = new DaemonState($daemon);
            $daemon->executeIfRunnable($state, $state->getLastRun());
        }
    }

    public static function create(): self
    {
        CliKernel::guardCli();
        $files = glob(Globals::STATIC_DIR . 'daemons/*.yml');
        $daemons = [];
        foreach ($files as $file) {
            $info = Yaml::parseFile($file);
            $daemons[] = Daemon::instantiate($info);
        }
        return new self($daemons);
    }

}
