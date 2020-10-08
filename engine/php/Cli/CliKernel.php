<?php

namespace Thor\Cli;

use Thor\Debug\Logger;
use Thor\KernelInterface;

final class CliKernel implements KernelInterface
{
    private array $commands;

    private array $commandLineArguments;

    /**
     * CliKernel constructor.
     *
     * @param CommandInterface[] $commands
     * @param string ...$commandLineArguments
     */
    public function __construct(array $commands, string ...$commandLineArguments)
    {
        if ('cli' !== php_sapi_name()) {
            Logger::write(
                "PANIC ABORT : CLI kernel tried to be executed from another context than CLI.",
                Logger::PROD,
                Logger::ERROR
            );
            exit;
        }
        Logger::write('Start CLI context');
        $this->commands = $commands;
        $this->commandLineArguments = $commandLineArguments ?? [];
    }

    public function execute()
    {

    }

    /**
     * @param CommandInterface[] $commands
     *
     * @return self
     */
    public static function createCli(array $commands): self
    {
        global $argv;
        $argsCopy = $argv;
        array_shift($argsCopy);
        return new self($commands, ...$argsCopy);
    }

}
