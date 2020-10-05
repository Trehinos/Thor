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
     * @param array $commands
     * @param string ...$commandLineArguments
     */
    public function __construct(array $commands, string ...$commandLineArguments)
    {
        $this->commands = $commands;
        $this->commandLineArguments = $commandLineArguments ?? [];
    }

    public function execute()
    {
        // @TODO parse command line arguments and execute the right command from commands array
    }
}
