<?php

namespace Thor\Cli;

use Thor\Thor;
use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Cli\Console\CursorControl;

/**
 *
 */

/**
 *
 */
final class Repl extends Command
{

    private bool $continue = true;

    /**
     * @var Command[]
     */
    private array $commands;

    public string $prompt;

    /**
     * @param string    $command
     * @param array     $args
     * @param CliKernel $kernel
     */
    public function __construct(string $command, array $args, CliKernel $kernel)
    {
        parent::__construct($command, $args, $kernel);
        $this->commands = $kernel->commands;
        $this->prompt = '[Thor ' . Thor::version() . '] Enter command :';
    }

    /**
     * @return void
     */
    public function repl(): void
    {
        while ($this->continue) {
            $command = $this->read();
            if ($command === 'exit') {
                $this->continue = false;
            } else {
                $arguments = preg_split(
                           '/("[^"]*")|\h+/',
                           $command,
                    flags: PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
                );
                $command = trim(array_shift($arguments));
                $commandArgs = Command::getArgs($arguments, $this->commands[$command]['arguments'] ?? []);
                CliKernel::executeCommand($command, $commandArgs);
            }
        }

        $this->console->writeln("Exiting...");
    }

    /**
     * @return string
     */
    public function read(): string
    {
        $this->console->fColor(Color::BLACK, Mode::BRIGHT)->writeln("\n" . $this->prompt)->mode();
        readline_add_history($line = readline(""));
        $this->console->echoes(
            CursorControl::moveUp(1),
            Mode::BRIGHT,
            Color::FG_YELLOW,
            "Run > ",
            Color::FG_BLUE,
            "$line\n"
        );
        return trim($line);
    }

}
