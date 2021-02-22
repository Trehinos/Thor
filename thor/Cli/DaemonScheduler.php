<?php

namespace Thor\Cli;

use Symfony\Component\Yaml\Yaml;
use Thor\Application;
use Thor\Debug\Logger;
use Thor\FileSystem\Folder;
use Thor\Globals;
use Thor\KernelInterface;
use Thor\Thor;

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
     */
    public function execute(): void
    {
        global $argv;
        $execute = $argv[1] ?? null;

        Logger::write("Executing DaemonScheduler...");
        if (null !== $execute && $this->executeDaemon($this->daemons[$execute] ?? null)) {
            return;
        }

        foreach ($this->daemons as $daemon) {
            $this->cycleDaemonIfRunnable($daemon);
        }
    }

    private function cycleDaemonIfRunnable(Daemon $daemon): void
    {
        $state = new DaemonState($daemon);
        $state->load();
        if (!$state->isRunning() && $daemon->isNowRunnable($state->getLastRun())) {
            $logPath = Globals::VAR_DIR . (Thor::getInstance()->loadConfig('config')['log_path'] ?? '');
            Folder::createIfNotExists($logPath . $daemon->getName());
            CliKernel::executeBackgroundProgram(
                'php ' . Globals::BIN_DIR . "daemon.php {$daemon->getName()}",
                "$logPath{$daemon->getName()}/output.log"
            );
        }
    }

    /**
     * @param Daemon|null $daemon
     *
     * @return bool false if ($daemon === null)
     */
    private function executeDaemon(?Daemon $daemon): bool
    {
        if (null === $daemon) {
            return false;
        }

        $state = new DaemonState($daemon);
        $state->load();

        $logPath = Globals::VAR_DIR . (Thor::getInstance()->loadConfig('config')['log_path'] ?? '');
        Application::setLoggerLevel(Thor::getInstance()->getEnv(), "$logPath{$daemon->getName()}/");
        Logger::write("Start {$daemon->getName()} daemon");

        $daemon->executeIfRunnable($state);
        return true;
    }

    public static function create(): self
    {
        CliKernel::guardCli();
        return new self(self::getDaemonsFromConfig());
    }

    /**
     * @return Daemon[]
     */
    public static function getDaemonsFromConfig(): array
    {
        $files = glob(Globals::STATIC_DIR . 'daemons/*.yml');
        $daemons = [];
        foreach ($files as $file) {
            $info = Yaml::parseFile($file);
            $daemons[] = Daemon::instantiate($info);
        }
        return $daemons;
    }

}
