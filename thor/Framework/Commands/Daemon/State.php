<?php

namespace Thor\Framework\Commands\Daemon;

use Throwable;
use Thor\Globals;
use Thor\Cli\Daemon;
use Thor\Process\Command;
use Symfony\Component\Yaml\Yaml;
use Thor\Cli\DaemonState;
use Thor\Configuration\ConfigurationFromFile;

final class State extends Command
{

    /**
     * @return void
     * @throws Throwable
     */
    public function execute(): void
    {
        $name = $this->get('name', '');
        $enable = $this->get('enable');
        $disable = $this->get('disable');
        $reset = $this->get('reset', false);

        if ($name === '') {
            $this->usage();
            return;
        }

        $daemonInfo = ConfigurationFromFile::fromFile("daemons/$name", true)->getArrayCopy();
        if ($enable !== null || $disable !== null) {
            $daemonInfo['enabled'] = $enable === true && $disable !== true;
        }
        if ($reset) {
            $state = new DaemonState(Daemon::instantiate($daemonInfo));
            $state->load();
            $state->setLastRun(null);
            $state->setNextRun(null);
            $state->setRunning(false);
            $state->error(null);
            $state->write();
        }
        $daemonFile = Globals::STATIC_DIR . "daemons/$name.yml";
        file_put_contents($daemonFile, Yaml::dump($daemonInfo));
    }

}
