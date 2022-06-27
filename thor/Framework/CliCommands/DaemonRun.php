<?php

namespace Thor\Framework\CliCommands;

use Thor\Globals;
use Thor\Process\CliCommand;
use Thor\Process\CommandError;
use Symfony\Component\Yaml\Yaml;
use Thor\Cli\{Console\Color,
    Console\Console,
    Console\Mode,
    DaemonState,
    DaemonScheduler};


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
final class DaemonRun extends CliCommand
{


    /**
     * @param string $daemonName
     *
     * @return array
     */
    private function loadDaemon(string $daemonName): array
    {
        $daemonFile = Globals::STATIC_DIR . "daemons/$daemonName.yml";
        if (!file_exists($daemonFile)) {

        }
        return Yaml::parseFile($daemonFile);
    }

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
            $console
                ->fColor(Color::RED)
                ->write("Daemon ")
                ->fColor(Color::BLUE)
                ->write($daemonName)
                ->fColor(Color::RED)
                ->writeln(" does not exist.")
                ->mode();
            return;
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
