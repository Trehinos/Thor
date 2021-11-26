<?php

/**
 * @package          Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Cli;

use Thor\Application;
use Thor\Configuration;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\FileSystem\Folder;
use Thor\Globals;
use Thor\KernelInterface;
use Thor\Thor;
use Throwable;

final class DaemonScheduler implements KernelInterface
{

    /**
     * @param Daemon[] $daemons
     */
    public function __construct(
        private array $daemons
    ) {
    }

    /**
     * Executed by the entry point thor/bin/daemons.php.
     *
     * Usage : php thor/bin/daemons.php [daemonName]
     *
     * @throws Throwable
     */
    public function execute(): void
    {
        global $argv;
        $execute = $argv[1] ?? null;

        Logger::write("Executing DaemonScheduler...");
        if (null !== $execute) {
            $this->executeDaemon($this->daemons[$execute] ?? null);
            return;
        }

        foreach ($this->daemons as $daemon) {
            $this->cycleDaemonIfRunnable($daemon);
        }
    }

    /**
     * @param Daemon|null $daemon
     *
     * @return bool false if ($daemon === null)
     *
     * @throws Throwable
     */
    private function executeDaemon(?Daemon $daemon): bool
    {
        if (null === $daemon) {
            return false;
        }

        Logger::write("DaemonScheduler execute-> {$daemon->getName()}");
        $state = new DaemonState($daemon);
        $state->load();

        $logPath = Globals::VAR_DIR . (Thor::config('config')['log_path'] ?? '');
        Application::setLoggerLevel(LogLevel::fromEnv(Thor::getEnv()), "$logPath{$daemon->getName()}/");
        Logger::write("Start {$daemon->getName()} daemon");

        $daemon->executeIfRunnable($state);
        return true;
    }

    private function cycleDaemonIfRunnable(Daemon $daemon): void
    {
        Logger::write("Cycle {$daemon->getName()}");
        $state = new DaemonState($daemon);
        $state->load();
        if (!$state->isRunning() && $daemon->isNowRunnable($state->getLastRun())) {
            $logPath = Globals::VAR_DIR . (Thor::config('config')['log_path'] ?? '');
            Folder::createIfNotExists($logPath . $daemon->getName());
            CliKernel::executeBackgroundProgram(
                'php ' . Globals::BIN_DIR . "daemon.php {$daemon->getName()}",
                "$logPath{$daemon->getName()}/output.log"
            );
        }
    }

    public static function create(): static
    {
        CliKernel::guardCli();
        return self::createFromConfiguration(Configuration::getDaemonsConfig());
    }

    public static function createFromConfiguration(array $config = []): static
    {
        $daemons = [];
        foreach ($config as $info) {
            $daemons[$info['name']] = Daemon::instantiate($info);
        }
        return new self($daemons);
    }

    public function getDaemons(): array
    {
        return $this->daemons;
    }
}
