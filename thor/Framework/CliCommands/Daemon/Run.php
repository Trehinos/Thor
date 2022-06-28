<?php

namespace Thor\Framework\CliCommands\Daemon;

use Thor\Globals;
use Thor\Process\CliCommand;
use Thor\Process\CommandError;
use Symfony\Component\Yaml\Yaml;
use Thor\Cli\{DaemonState, Console\Mode, Console\Color, Console\Console, DaemonScheduler};


/**
 * This Command contains all daemons related Thor-Api commands :
 *  - daemon/start
 *  - daemon/stop
 *  - damon/reset
 *  - daemon/status
 *  - daemon/kill
 *
 * @package          Thor/Framework
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Run extends CliCommand
{

    /**
     * @return void
     * @throws \Throwable
     */
    public function execute(): void
    {
        $console = new Console();

        $daemonName = $this->get('name');
        if ($daemonName === null) {
            throw CommandError::misusage($this);
        }

        $scheduler = DaemonScheduler::create();
        $daemon = $scheduler->getDaemon($daemonName);
        $console->mode(Mode::BRIGHT);
        if (null === $daemon) {
            $this->error("The daemon $daemonName does not exist", false);
        }
        $state = new DaemonState($daemon);
        $state->load();
        if ($state->isRunning()) {
            $console
                ->fColor(Color::RED)
                ->write("Daemon ")
                ->fColor(Color::BLUE)
                ->write($daemonName)
                ->fColor(Color::RED)
                ->writeln(" already running.")
                ->mode();
            return;
        }
        $scheduler->executeDaemon($daemon, true);
        $console
            ->mode(Mode::BRIGHT)
            ->fColor(Color::GREEN)
            ->write("Daemon ")
            ->fColor(Color::RED)
            ->write($daemonName)
            ->fColor(Color::GREEN)
            ->writeln(" executed.")
            ->mode();
    }

}
