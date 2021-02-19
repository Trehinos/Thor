<?php

namespace Thor\Api\Commands;

use Symfony\Component\Yaml\Yaml;
use Thor\Cli\CliKernel;
use Thor\Cli\Command;
use Thor\Cli\Console;
use Thor\Cli\Daemon;
use Thor\Cli\DaemonScheduler;
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
        $all = $this->get('all') ?? false;
        if (!$all && null === $daemonName) {
            $this->error("Usage error\n", 'Daemon name is required.', true);
        }

        if (!$all) {
            $daemonInfo = $this->loadDaemon($daemonName);
            $state = new DaemonState(Daemon::instantiate($daemonInfo));
            $state->load();
            dump($state);
            return;
        }
        $daemons = DaemonScheduler::getDaemonsFromConfig();
        foreach ($daemons as $daemon) {
            $state = new DaemonState($daemon);
            $state->load();
            $this->console
                ->writeFix('Is running ? ', 16, STR_PAD_LEFT)
                ->writeFix('Daemon', 32)
                ->writeFix('Status', 10)
                ->writeln('Last run');
            $this->console->fColor(
                $state->isRunning() ?
                    Console::COLOR_GREEN :
                    Console::COLOR_YELLOW
            )
                ->writeFix(
                    $state->isRunning() ?
                        "yes ➤  " :
                        "no ⊡  ",
                    18,
                    STR_PAD_LEFT
                )
                ->mode()
                ->writeFix("{$daemon->getName()}", 32)
                ->fColor(
                    $daemon->isActive() ?
                        Console::COLOR_CYAN :
                        ($daemon->isEnabled() ?
                            Console::COLOR_GREEN :
                            Console::COLOR_RED)
                )->writeFix(
                    $daemon->isActive() ?
                        'ACTIVE' :
                        ($daemon->isEnabled() ?
                            'ENABLED' :
                            'DISABLED'
                        ),
                    10
                )
                ->mode()
                ->writeln($state->getLastRun()?->format('Y-m-d H:i') ?? 'never');
        }
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
