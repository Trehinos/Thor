<?php

namespace Thor\Cli;

use Symfony\Component\Yaml\Yaml;
use Thor\Application;
use Thor\Debug\Logger;
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

    public function execute(): void
    {
        global $argv;

        $execute = $argv[1] ?? null;
        Logger::write("Executing DaemonScheduler...");

        foreach ($this->daemons as $daemon) {
            $state = new DaemonState($daemon);
            $state->load();
            if (!$state->isRunning() && $daemon->isNowRunnable($state->getLastRun())) {
                $logPath = Globals::VAR_DIR . (Thor::getInstance()->loadConfig('config')['log_path'] ?? '');
                if (null === $execute) {
                    CliKernel::executeBackgroundProgram(
                        'php ' . Globals::BIN_DIR . "daemon.php {$daemon->getName()}",
                        "$logPath{$daemon->getName()}/output.log"
                    );
                } elseif ($daemon->getName() === $execute) {
                    Application::setLoggerLevel(Thor::getInstance()->getEnv(), "$logPath{$daemon->getName()}/");
                    Logger::write("Start {$daemon->getName()} daemon");
                    $daemon->executeIfRunnable($state);
                    return;
                }
            }
        }
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
