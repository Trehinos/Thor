<?php

namespace Thor\Process;

use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Cli\Console\Console;
use Thor\Cli\Console\FixedOutput;
use PhpParser\Node\Expr\AssignOp\Mod;

class CliCommand implements Executable
{

    public readonly array $context;

    /**
     * @param string     $command
     * @param Argument[] $arguments
     * @param Option[]   $options
     */
    public function __construct(
        public readonly string $command,
        public readonly array $arguments = [],
        public readonly array $options = []
    ) {
    }

    public static function test(): void
    {
        $test = new self(
            'daemon/run',
            [
                new Argument('NAME', 'The daemon to run', true),
                new Argument('TIME', 'HH:MM the moment when to run the daemon (default : now)'),
                new Argument('USERNAME', 'Sends a different username to the command'),
            ],
            [
                new Option('env', 'The environnement on which to run the daemon'),
                new Option('verbose', 'Set this option to show the daemon\'s output', hasValue: false),
            ]
        );
        $test->usage();

        global $argv;
        $d = $test->parse($argv);
        dump($d);
        $test->execute();
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->context[$name] ?? $default;
    }

    public function usage(): void
    {
        $console = new Console();
        $console->echoes(Mode::BRIGHT, 'Usage of ', Color::FG_BLUE, $this->command, Color::FG_GRAY, ":\n\n");
        $console->echoes(Mode::BRIGHT, Color::FG_BLUE, "    {$this->command} ");
        if (!empty($this->options)) {
            $console->echoes('[');
            $console->echoes(Mode::BRIGHT, Color::FG_GREEN, 'OPTIONS');
            $console->echoes(']');
        }
        foreach ($this->arguments as $argument) {
            $console->echoes(' ');
            if (!$argument->required) {
                $console->echoes('[');
            }
            $console->echoes(Mode::BRIGHT, Color::FG_YELLOW, "{$argument->name}");
            if (!$argument->required) {
                $console->echoes(']');
            }
        }
        $console->writeln()->writeln();

        if (!empty($this->options)) {
            $console->writeFix("List of options ", 23, STR_PAD_LEFT)->writeln();
            foreach ($this->options as $option) {
                $console->color(Color::FG_GREEN, Mode::BRIGHT);
                $console->writeFix("-{$option->short}", 14, STR_PAD_LEFT);
                $console->mode();
                $console->echoes(
                    Mode::BRIGHT,
                    Color::FG_RED,
                    ' ',
                    new FixedOutput(
                        $option->hasValue
                            ? strtoupper($option->name)
                            : '',
                        7
                    )
                );
                $console->echoes(
                    Mode::BRIGHT,
                    Mode::UNDERSCORE,
                    Color::FG_GRAY,
                    ' ',
                    $option->description
                )->writeln();
                $console->color(Color::FG_GREEN, Mode::BRIGHT);
                $console->writeFix("--{$option->long}", 14, STR_PAD_LEFT);
                $console->mode();
                if ($option->hasValue) {
                    $console->echoes(Mode::BRIGHT, Color::FG_RED, ' ', strtoupper($option->name));
                }
                $console->writeln();
            }
            $console->writeln();
        }

        if (!empty($this->arguments)) {
            $console->writeFix("List of arguments ", 23, STR_PAD_LEFT)->writeln();
            foreach ($this->arguments as $argument) {
                $console->color(Color::FG_YELLOW, Mode::BRIGHT);
                $console->writeFix("{$argument->name} ", 23, STR_PAD_LEFT);
                $console->echoes(
                    Mode::UNDERSCORE,
                    Color::FG_GRAY,
                    $argument->description
                )->writeln();
            }
        }

        $console->writeln()->writeln();
    }

    public function parse(array $commandLineArguments): array
    {
        array_shift($commandLineArguments);
        $command = array_shift($commandLineArguments);
        if ($command !== $this->command) {
            throw CommandError::mismatch($this, $command);
        }
        $options = [];
        $arguments = [];
        $optionBuffer = '';
        $inOption = false;
        foreach ($commandLineArguments as $argument) {
            if ($inOption) {
                if (is_array($optionBuffer)) {
                    foreach ($optionBuffer as $flag) {
                        $options[$flag] = true;
                    }
                } elseif (array_key_exists($optionBuffer, $options)) {
                    if (!is_array($options[$optionBuffer])) {
                        $options[$optionBuffer] = [$options[$optionBuffer]];
                    }
                    $options[$optionBuffer][] = $argument;
                } else {
                    $options[$optionBuffer] = $argument;
                }
                $inOption = false;
                $optionBuffer = '';
            } else {
                if (str_starts_with($argument, '-')) {
                    $inOption = true;
                    if (str_starts_with($argument, '--')) {
                        $optionBuffer = trim($argument, '-');
                    }
                    $optionBuffer = str_split(trim($argument, '-'));
                } else {
                    $arguments[] = $argument;
                }
            }
        }

        return [
            'command'   => $command,
            'arguments' => $arguments,
            'options'   => $options,
        ];
    }

    public function execute(): void
    {
        // TODO: Implement execute() method.
    }
}