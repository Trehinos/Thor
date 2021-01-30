<?php

namespace Thor\Cli;

/**
 * Class AbstractCommand
 * @package Thor\Cli
 *
 * @since 2020-09
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
abstract class AbstractCommand implements CommandInterface
{

    private string $command;
    private string $description;
    private array $arguments;

    /**
     * AbstractCommand constructor.
     * @param string $command
     * @param string $description
     * @param CommandArgument[] $arguments
     */
    public function __construct(
        string $command,
        string $description = '',
        array $arguments = []
    ) {
        $this->command = $command;
        $this->description = $description;
        $this->arguments = $arguments;
    }

    /**
     * @return string the command name.
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string an optional description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return CommandArgument[] every possible arguments of this command.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

}
