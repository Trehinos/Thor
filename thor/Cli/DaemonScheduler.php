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

        foreach ($this->daemons as $daemon) {
            $state = new DaemonState($daemon);
            $state->load();
            if (!$state->isRunning() && $daemon->isNowRunnable($state->getLastRun())) {
                if (null === $execute) {
                    CliKernel::executeBackgroundProgram(
                        'php ' . Globals::BIN_DIR . "daemon.php {$daemon->getName()}",
                        Globals::VAR_DIR . "{$daemon->getName()}.output.log"
                    );
                } elseif ($daemon->getName() === $execute) {
                    $logPath = Globals::VAR_DIR . (Thor::getInstance()->loadConfig('config')['log_path'] ?? '');
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
        $files = glob(Globals::STATIC_DIR . 'daemons/*.yml');
        $daemons = [];
        foreach ($files as $file) {
            $info = Yaml::parseFile($file);
            $daemons[] = Daemon::instantiate($info);
        }
        return new self($daemons);
    }

}
