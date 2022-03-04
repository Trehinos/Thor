<?php

namespace Thor\Cli;

// TODO
use Thor\Thor;
use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;

final class Repl extends Command
{

    private bool $continue = true;

    /**
     * @var Command[]
     */
    private array $commands = [];

    public string $prompt;

    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
        $this->commands = $kernel->commands;
        $this->prompt = 'Thor ' . Thor::version() . ' > ';
    }

    public function repl(): void
    {
        while ($this->continue) {
            $command = $this->read();
            if ($command === 'exit') {
                return;
            } else {
                $arguments = preg_split('/("[^"]*")|\h+/', $command, flags: PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
                $command = trim(array_shift($arguments));
                $commandArgs = Command::getArgs($arguments, $this->commands[$command]['arguments'] ?? []);
                CliKernel::executeCommand($command, $commandArgs); // TODO : obtains output
            }
        }
    }

    public function read(): string
    {
        $this->console->fColor(Color::BLUE, Mode::BRIGHT)->write($this->prompt)->fColor(mode: Mode::DIM);
        readline_add_history($line = readline());
        $this->console->mode();
        return $line;
    }


}
