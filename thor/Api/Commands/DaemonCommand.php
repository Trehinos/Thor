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
        if (empty($daemons)) {
            return;
        }

        $this->console
            ->fColor(Console::COLOR_BLACK)
            ->bColor(Console::COLOR_GRAY)
            ->writeFix('Status', 10)
            ->writeFix('Last start', 17)
            ->writeFix('', 4, STR_PAD_LEFT)
            ->writeFix('Daemon', 24)
            ->writeFix('Active period', 25)
            ->mode()
            ->writeln();
        foreach ($daemons as $daemon) {
            $state = new DaemonState($daemon);
            $state->load();
            $this->console
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
                ->writeFix($state->getLastRun()?->format('Y-m-d H:i') ?? 'never', 17)->fColor(
                    $state->isRunning() ?
                        Console::COLOR_GREEN :
                        Console::COLOR_YELLOW
                )
                ->writeFix(
                    $state->isRunning() ?
                        " ➤  " :
                        " ⊡  ",
                    6,
                    STR_PAD_LEFT
                )
                ->writeFix(substr("{$daemon->getName()}", 0 , 24), 24)
                ->mode()
                ->writeFix("[{$daemon->getStartToday()->format('H:i')} - {$daemon->getEndToday()->format('H:i')}]", 15)
                ->writeln(" / {$daemon->getPeriodicity()} min")
            ;
        }
        $this->console->writeln();
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
