<?php

namespace Thor\Framework\CliCommands\Daemon;

use Thor\Globals;
use Thor\Process\Command;
use Thor\Process\CommandError;
use Symfony\Component\Yaml\Yaml;
use Thor\Framework\Factories\Configurations;
use Thor\Cli\{Daemon, DaemonState, Console\Mode, Console\Color, Console\Console, DaemonScheduler, Console\FixedOutput};


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
final class Status extends Command
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
        $all = $this->get('all', false);

        if (!$all && null === $daemonName) {
            throw CommandError::misusage($this);
        }

        if (!$all) {
            $daemonInfo = $this->loadDaemon($daemonName);
            $state = new DaemonState(Daemon::instantiate($daemonInfo));
            $state->load();
            dump($state);
            return;
        }
        $daemons = DaemonScheduler::createFromConfiguration(Configurations::getDaemonsConfig())->getDaemons();
        if (empty($daemons)) {
            throw CommandError::misusage($this);
        }

        $console
            ->echoes(
                Color::FG_BLACK,
                Color::BG_GRAY,
                new FixedOutput('Status', 10),
                new FixedOutput('Last run', 17),
                new FixedOutput('Next run', 17),
                new FixedOutput('', 4, STR_PAD_LEFT),
                new FixedOutput('Daemon', 24),
                new FixedOutput('Active period / Info', 32)
            )
            ->writeln()
        ;
        foreach ($daemons as $daemon) {
            $state = new DaemonState($daemon);
            $state->load();

            [$status_color, $status] = match (true) {
                $state->getError() !== null => [Color::RED, 'ERROR'],
                $daemon->isActive() => [Color::CYAN, 'ACTIVE'],
                $daemon->isEnabled() => [Color::GREEN, 'ENABLED'],
                default => [Color::YELLOW, 'DISABLED'],
            };

            $console->echoes(
                $status_color->fg(),
                new FixedOutput($status, 10),
                Color::FG_GRAY,
                new FixedOutput($state->getLastRun()?->format('y-m-d H:i') ?? 'never', 17),
                new FixedOutput($state->getNextRun()?->format('y-m-d H:i') ?? 'never', 17),
                $state->getError()
                    ? Color::FG_RED
                    :
                    (
                    $state->isRunning()
                        ? Color::FG_CYAN
                        : Color::FG_YELLOW
                    ),
                new FixedOutput(
                    $state->getError()
                        ? "  E "
                        :
                        (
                        $state->isRunning()
                            ? "  > "
                            : "  • "
                        ),
                    4
                ),
                new FixedOutput(substr("{$daemon->getName()}", 0, 24), 24)
            );

            if ($state->getError() ?? false) {
                $console
                    ->fColor(Color::RED)
                    ->writeln(substr($state->getError(), 0, 32))
                    ->mode()
                ;
                continue;
            }
            if ($state->isRunning()) {
                $console
                    ->fColor(Color::CYAN)
                    ->writeln("PID : {$state->getPid()}")
                    ->mode()
                ;
            } else {
                $console
                    ->mode()
                    ->writeFix(
                        "[{$daemon->getStartToday()->format('H:i')} - {$daemon->getEndToday()->format('H:i')}]",
                        15
                    )
                    ->writeln(" / {$daemon->getPeriodicity()} min")
                ;
            }
        }
        $console->writeln();

        $console->echoes(Color::FG_GRAY, Mode::REVERSE, "Status\n");
        foreach (
            [
                [Color::CYAN, 'ACTIVE', 'Daemon is in its active period and is enabled'],
                [Color::GREEN, 'ENABLED', 'Daemon is NOT in its active period but is enabled'],
                [Color::YELLOW, 'DISABLED', 'The daemon is disabled'],
                [Color::RED, 'ERROR', 'The last run threw an error.'],
            ] as [$color, $legend, $desc]
        ) {
            $console->echoes($color->fg(), "$legend ", Color::FG_GRAY, Mode::UNDERSCORE, ": $desc\n");
        }

        $console->writeln();
    }

}
