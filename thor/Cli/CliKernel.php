<?php

namespace Thor\Cli;

use Thor\Debug\Logger;
use Thor\KernelInterface;

/**
 * Class CliKernel: kernel for command line interface
 * @package Thor\Cli
 *
 * @since 2020-09
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
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

    /**
     * execute(): executes the command on the command line with provided arguments.
     */
    public function execute()
    {
        $command = $this->commandLineArguments[0] ?? '';
        if ($command === '') {
            echo "Please enter a command...\n";
            return;
        }

        foreach ($this->commands as $command) {
            if ($command->getCommand() === $command) {
                $arguments = $this->commandLineArguments;
                array_shift($arguments);
                $command->compileArguments($arguments);
                $command->execute();
                break;
            }
        }
    }

    /**
     * createCli(): creates a CliKernel with $argv as arguments.
     *
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
