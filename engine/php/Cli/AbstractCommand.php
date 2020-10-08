<?php

namespace Thor\Cli;

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
        string $description,
        array $arguments = []
    )
    {
        $this->command = $command;
        $this->description = $description;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

}
