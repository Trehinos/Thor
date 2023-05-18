<?php

namespace Thor\Cli;

use Thor\Thor;
use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Cli\Command\Command;
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
    public string $prompt;

    public function __construct(string $command, string $description, array $args, array $options, ?CliKernel $kernel = null)
    {
        parent::__construct($command, $description, $args, $options, $kernel);
        $this->prompt = '[Thor ' . Thor::version() . '] Enter command :';
    }

    /**
     * @return void
     */
    public function execute(): void
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
                CliKernel::executeCommand($command, $arguments);
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
