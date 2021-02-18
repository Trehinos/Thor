<?php

namespace Thor\Api\Commands;

use Symfony\Component\Yaml\Yaml;
use Thor\Cli\CliKernel;
use Thor\Cli\Command;
use Thor\Cli\Daemon;
use Thor\Cli\DaemonState;
use Thor\Globals;

final class DaemonCommand extends Command
{

    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
    }

    public function daemonStart()
    {
        $daemonName = $this->get('name');
        $this->daemonEnable($daemonName);
    }

    public function daemonStop()
    {
        $daemonName = $this->get('name');
        $this->daemonEnable($daemonName, false);
    }

    public function daemonStatus()
    {
        $daemonName = $this->get('name');
        if (null === $daemonName) {
            $this->error("Usage error\n", 'Daemon name is required.', true);
        }
        $daemonInfo = $this->loadDaemon($daemonName);
        $state = new DaemonState(Daemon::instantiate($daemonInfo));
        $state->load();
        dump($state);
    }

    private function daemonEnable(?string $daemonName, bool $enable = true)
    {
        if (null === $daemonName) {
            $this->error("Usage error\n", 'Daemon name is required.', true);
        }

        $daemonInfo = $this->loadDaemon($daemonName);
        $daemonInfo['enabled'] = $enable;
        $daemonFile = Globals::STATIC_DIR . "daemons/$daemonName.yml";
        file_put_contents($daemonFile, Yaml::dump($daemonInfo));
    }

    private function loadDaemon(string $daemonName): array
    {
        $daemonFile = Globals::STATIC_DIR . "daemons/$daemonName.yml";
        if (!file_exists($daemonFile)) {
            $this->error("File error\n", 'Daemon does not exist.', true);
        }
        return Yaml::parseFile($daemonFile);
    }

}
