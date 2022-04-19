<?php

namespace Thor\Cli;

use Throwable;
use Thor\{Configuration\ThorConfiguration,
    Thor,
    Globals,
    Application,
    Debug\Logger,
    Debug\LogLevel,
    KernelInterface,
    FileSystem\Folder,
    Factories\Configurations,
    Configuration\Configuration};

/**
 * This class is the Kernel of thor daemons.
 *
 * Its entry point is `thor/bin/daemon.php`.
 *
 * @package          Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
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
     * Creates the DaemonScheduler with the daemons' configuration.
     */
    public static function create(): static
    {
        CliKernel::guardCli();
        return self::createFromConfiguration(Configurations::getDaemonsConfig());
    }

    /**
     * Creates the DaemonScheduler with the specified configuration.
     */
    public static function createFromConfiguration(Configuration $config): static
    {
        $daemons = [];
        foreach ($config as $info) {
            $daemons[$info['name']] = Daemon::instantiate($info);
        }
        return new self($daemons);
    }

    /**
     * Executed by the entry point `thor/bin/daemons.php`.
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
     * Executes the specified daemon.
     *
     * @param Daemon|null $daemon
     * @param bool        $force
     *
     * @return bool false if ($daemon === null)
     *
     * @throws Throwable
     */
    public function executeDaemon(?Daemon $daemon, bool $force = false): bool
    {
        if (null === $daemon) {
            return false;
        }

        Logger::write("DaemonScheduler execute-> {$daemon->getName()}");
        $state = new DaemonState($daemon);
        $state->load();

        $logPath = Globals::VAR_DIR . ThorConfiguration::get()->logPath();
        Application::setLoggerLevel(LogLevel::fromEnv(Thor::getEnv()), "{$logPath}daemon/{$daemon->getName()}/");
        Logger::write("Start {$daemon->getName()} daemon");

        $daemon->executeIfRunnable($state, $force);
        return true;
    }

    /**
     * Executes the DaemonScheduler in a new process with the specified daemon as argument.
     */
    private function cycleDaemonIfRunnable(Daemon $daemon): void
    {
        Logger::write("Cycle {$daemon->getName()}");
        $state = new DaemonState($daemon);
        $state->load();
        if (!$state->isRunning() && $daemon->isNowRunnable($state->getLastRun())) {
            $logPath = Globals::VAR_DIR . ThorConfiguration::get()->logPath();
            Folder::createIfNotExists($logPath . "{$logPath}daemon/{$daemon->getName()}/");
            CliKernel::executeBackgroundProgram(
                'php ' . Globals::BIN_DIR . "daemon.php {$daemon->getName()}",
                "$logPath{$daemon->getName()}/output.log"
            );
        }
    }

    /**
     * Gets daemons of this DaemonScheduler.
     *
     * @return Daemon[]
     */
    public function getDaemons(): array
    {
        return $this->daemons;
    }

    public function getDaemon(string $daemonName): ?Daemon
    {
        foreach ($this->daemons as $daemon) {
            if ($daemonName === $daemon->getName()) {
                return $daemon;
            }
        }
        return null;
    }
}
