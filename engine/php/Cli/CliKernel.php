<?php

namespace Thor\Cli;

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
        return new self($commands, ...$argv);
    }

}
