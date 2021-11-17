<?php

/**
 * This Command contains all daemons related Thor-Api commands :
 *  - daemon/start
 *  - daemon/stop
 *  - damon/reset
 *  - daemon/status
 *  - daemon/kill
 *
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Api\Commands;

use Symfony\Component\Yaml\Yaml;
use Thor\Cli\{CliKernel, Command, Console, Daemon, DaemonScheduler, DaemonState};
use Thor\Configuration;
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

    public function daemonReset()
    {
        $daemonName = $this->get('name');
        $this->daemonResetState($daemonName);
    }

    private function daemonResetState(?string $daemonName)
    {
        if (null === $daemonName) {
            $this->error("Usage error\n", 'Daemon name is required.', true);
        }

        $daemonInfo = $this->loadDaemon($daemonName);
        $state = new DaemonState(Daemon::instantiate($daemonInfo));
        $state->load();
        $state->setLastRun(null);
        $state->setRunning(false);
        $state->error(null);
        $state->write();
        $daemonFile = Globals::STATIC_DIR . "daemons/$daemonName.yml";
        file_put_contents($daemonFile, Yaml::dump($daemonInfo));
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
        $daemons = DaemonScheduler::createFromConfiguration(Configuration::getDaemonsConfig())->getDaemons();
        if (empty($daemons)) {
            $this->error('No daemon found', ' : Verify the static files in ' . Globals::STATIC_DIR . 'daemons/');
        }

        $this->console
            ->fColor(Console::COLOR_BLACK)
            ->bColor(Console::COLOR_GRAY)
            ->writeFix('Status', 10)
            ->writeFix('Last period run', 17)
            ->writeFix('', 4, STR_PAD_LEFT)
            ->writeFix('Daemon', 24)
            ->writeFix('Active period / Info', 32)
            ->mode()
            ->writeln();
        foreach ($daemons as $daemon) {
            $state = new DaemonState($daemon);
            $state->load();

            $status_color = Console::COLOR_YELLOW;
            $status = 'DISABLED';
            if ($state->getError() !== null) {
                $status_color = Console::COLOR_RED;
                $status = 'ERROR';
            } elseif ($daemon->isActive()) {
                $status_color = Console::COLOR_CYAN;
                $status = 'ACTIVE';
            } elseif ($daemon->isEnabled()) {
                $status_color = Console::COLOR_GREEN;
                $status = 'ENABLED';
            }

            $this->console
                ->fColor($status_color)
                ->writeFix($status, 10)
                ->mode()
                ->writeFix($state->getLastRun()?->format('Y-m-d H:i') ?? 'never', 17)->fColor(
                    $state->getError()
                        ? Console::COLOR_RED
                        :
                        ($state->isRunning()
                            ?
                            Console::COLOR_CYAN
                            :
                            Console::COLOR_YELLOW)
                )
                ->writeFix(
                    $state->getError()
                        ? "  E "
                        :
                        (
                        $state->isRunning()
                            ?
                            "  > "
                            :
                            "  • "
                        ),
                    4,
                    STR_PAD_LEFT
                )
                ->writeFix(substr("{$daemon->getName()}", 0, 24), 24);

            if ($state->getError() ?? false) {
                $this->console
                    ->fColor(Console::COLOR_RED)
                    ->writeln(substr($state->getError(), 0, 32))
                    ->mode();
                continue;
            }
            if ($state->isRunning()) {
                $this->console
                    ->fColor(Console::COLOR_CYAN)
                    ->writeln("PID : {$state->getPid()}")
                    ->mode();
            } else {
                $this->console
                    ->mode()
                    ->writeFix(
                        "[{$daemon->getStartToday()->format('H:i')} - {$daemon->getEndToday()->format('H:i')}]",
                        15
                    )
                    ->writeln(" / {$daemon->getPeriodicity()} min");
            }
        }
        $this->console->writeln();
    }

    public function daemonKill()
    {
        $daemonName = $this->get('name');
        if (null === $daemonName) {
            $this->error("Usage error\n", 'Daemon name is required.', true);
        }

        $daemonInfo = $this->loadDaemon($daemonName);
        $state = new DaemonState(Daemon::instantiate($daemonInfo));
        $state->load();
        if (!$state->isRunning()) {
            $this->error("Not running\n", 'Daemon is not running', false);
        }
        $pid = $state->getPid();
        $state->setPid(null);
        if (substr(php_uname(), 0, 7) === "Windows") {
            CliKernel::executeProgram("taskkill /F /PID $pid");
        } else {
            CliKernel::executeProgram("kill -15 $pid");
        }
        $state->error('KILLED BY USER');
        $state->setRunning(false);
        $state->write();
    }

}
