<?php

namespace Thor\Framework\Commands;

use Thor\Globals;
use Symfony\Component\Yaml\Yaml;
use Thor\Framework\Factories\Configurations;
use Thor\Cli\{Console\Color,
    Console\FixedOutput,
    Console\Mode,
    Daemon,
    Command,
    CliKernel,
    DaemonState,
    DaemonScheduler
};


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

    public function daemonRun()
    {
        $daemonName = $this->get('name');
        $scheduler = DaemonScheduler::create();
        $daemon = $scheduler->getDaemon($daemonName);
        $this->console->mode(Mode::BRIGHT);
        if (null === $daemon) {
            $this->console
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
            $this->console
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
        $this->console
            ->mode(Mode::BRIGHT)
            ->fColor(Color::GREEN)
            ->write("Daemon ")
            ->fColor(Color::RED)
            ->write($daemonName)
            ->fColor(Color::GREEN)
            ->writeln(" executed.")
            ->mode();
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
        $state->setNextRun(null);
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
        $daemons = DaemonScheduler::createFromConfiguration(Configurations::getDaemonsConfig())->getDaemons();
        if (empty($daemons)) {
            $this->error('No daemon found', ' : Verify the static files in ' . Globals::STATIC_DIR . 'daemons/');
        }

        $this->console
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
            ->writeln();
        foreach ($daemons as $daemon) {
            $state = new DaemonState($daemon);
            $state->load();

            $status_color = Color::YELLOW;
            $status = 'DISABLED';
            if ($state->getError() !== null) {
                $status_color = Color::RED;
                $status = 'ERROR';
            } elseif ($daemon->isActive()) {
                $status_color = Color::CYAN;
                $status = 'ACTIVE';
            } elseif ($daemon->isEnabled()) {
                $status_color = Color::GREEN;
                $status = 'ENABLED';
            }

            $this->console
                ->fColor($status_color)
                ->writeFix($status, 10)
                ->mode()
                ->writeFix($state->getLastRun()?->format('Y-m-d H:i') ?? 'never', 17)
                ->writeFix($state->getNextRun()?->format('Y-m-d H:i') ?? 'never', 17)
                ->fColor(
                    $state->getError()
                        ? Color::RED
                        :
                        (
                        $state->isRunning()
                            ? Color::CYAN
                            : Color::YELLOW
                        )
                )
                ->writeFix(
                    $state->getError()
                        ? "  E "
                        :
                        (
                        $state->isRunning()
                            ? "  > "
                            : "  • "
                        ),
                    4,
                    STR_PAD_LEFT
                )
                ->writeFix(substr("{$daemon->getName()}", 0, 24), 24);

            if ($state->getError() ?? false) {
                $this->console
                    ->fColor(Color::RED)
                    ->writeln(substr($state->getError(), 0, 32))
                    ->mode();
                continue;
            }
            if ($state->isRunning()) {
                $this->console
                    ->fColor(Color::CYAN)
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
            $this->error("Not running\n", 'Daemon is not running');
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
