<?php

namespace Thor\Framework\CliCommands\Daemon;

use Thor\Cli\Daemon;
use Thor\Cli\CliKernel;
use Thor\Cli\DaemonState;
use Thor\Process\Command;
use Thor\Configuration\ConfigurationFromFile;

final class Kill extends Command
{

    /**
     * @return void
     * @throws \Throwable
     */
    public function execute(): void
    {
        $daemonName = $this->get('name');
        if (null === $daemonName) {
            $this->usage();
            return;
        }

        $daemonInfo = ConfigurationFromFile::fromFile("daemons/$daemonName", true)->getArrayCopy();
        $state = new DaemonState(Daemon::instantiate($daemonInfo));
        $state->load();
        if (!$state->isRunning()) {
            echo "The daemon $daemonName is not running...\n";
            return;
        }
        $pid = $state->getPid();
        $state->setPid(null);
        if (str_starts_with(php_uname(), "Windows")) {
            CliKernel::executeProgram("taskkill /F /PID $pid");
        } else {
            CliKernel::executeProgram("kill -15 $pid");
        }
        $state->error('KILLED BY USER');
        $state->setRunning(false);
        $state->write();
    }

}
