<?php

namespace Thor\Process;

use Thor\Cli\CliKernel;
use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\Cli\Console\Console;
use Thor\Cli\Console\FixedOutput;
use JetBrains\PhpStorm\ArrayShape;

abstract class Command implements Executable
{

    public array $context;
    public Console $console;

    /**
     * @param string         $command
     * @param string         $description
     * @param Argument[]     $arguments
     * @param Option[]       $options
     * @param CliKernel|null $kernel
     */
    public function __construct(
        public readonly string $command,
        public readonly string $description = '',
        public readonly array $arguments = [],
        public readonly array $options = [],
        public ?CliKernel $kernel = null
    ) {
        $this->console = new Console();
        $this->context = [];
    }

    public function matches(array $commandLineArguments): bool
    {
        $command = array_shift($commandLineArguments);
        return $command === $this->command;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->context[$name] ?? $default;
    }

    public function error(string $message, bool $displayUsage = true): never
    {
        $this->console->echoes(Mode::BRIGHT, Color::FG_RED, 'ERROR', "\n");
        $this->console->echoes(Mode::BRIGHT, Color::FG_YELLOW, $message, "\n");
        if ($displayUsage) {
            $this->usage();
        }
        exit;
    }

    public function usage(bool $full = true): void
    {
        $console = new Console();
        if ($full) {
            $console->echoes(Mode::BRIGHT, 'Usage of ', Color::FG_BLUE, $this->command, Color::FG_GRAY, ":\n\n");
            $console->echoes('    ');
            $console->echoes(Mode::BRIGHT, Color::FG_BLUE, "{$this->command} ");
        } else {
            $console->echoes(Mode::BRIGHT, Color::FG_BLUE, new FixedOutput($this->command, 20, STR_PAD_LEFT));
        }
        if ($full && !empty($this->options)) {
            $console->echoes('[');
            $console->echoes(Mode::BRIGHT, Color::FG_GREEN, 'OPTIONS');
            $console->echoes(']');
        }
        if ($full) {
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
            $console->echoes(Mode::BRIGHT, Mode::UNDERSCORE, "\n    {$this->description}\n");
            $console->writeln();
        } else {
            $console->echoes(Mode::BRIGHT, Mode::UNDERSCORE, ": {$this->description}\n");
        }

        if ($full && !empty($this->options)) {
            $console->writeln("    List of options ");
            foreach ($this->options as $option) {
                $console->color(Color::FG_GREEN, Mode::BRIGHT);
                $console->writeFix("-{$option->short}", 14, STR_PAD_LEFT);
                $console->mode();
                $console->echoes(
                    Mode::BRIGHT,
                    Color::FG_RED,
                    new FixedOutput(
                        $option->cumulative
                            ? '+'
                            : ($option->hasValue
                            ? ' ' . strtoupper($option->name)
                            : ''),
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
                if ($option->cumulative) {
                    $console->echoes(Mode::BRIGHT, Color::FG_RED, '+');
                } elseif ($option->hasValue) {
                    $console->write('=');
                    $console->echoes(Mode::BRIGHT, Color::FG_RED, '', strtoupper($option->name));
                }
                $console->writeln();
            }
            $console->writeln();
        }

        if ($full && !empty($this->arguments)) {
            $console->writeln("    List of arguments ");
            foreach ($this->arguments as $argument) {
                $console->color(Color::FG_YELLOW, Mode::BRIGHT);
                $console->writeFix("{$argument->name} ", 23, STR_PAD_LEFT);
                $console->echoes(
                    Mode::UNDERSCORE,
                    Color::FG_GRAY,
                    $argument->description
                )->writeln();
            }

            $console->writeln();
        }
    }

    /**
     * @throws CommandError
     *
     * TODO : refactor in CommandParser
     * @see CommandParser
     */
    #[ArrayShape(['command' => "mixed|null|string", 'arguments' => "array", 'options' => "array"])]
    public function parse(array $commandLineArguments): array
    {
        $parser = CommandParser::with($this);
        $commandFromLine = array_shift($commandLineArguments);
        if ($commandFromLine !== $this->command) {
            throw CommandError::mismatch($this, $commandFromLine);
        }

        $waitingForValue = false;
        $options = [];
        $arguments = [];
        $nextArg = 0;
        foreach ($commandLineArguments as $argumentFromLine) {
            if ($waitingForValue !== false) {
                $options[$waitingForValue->name] = $argumentFromLine;
                $waitingForValue = false;
                continue;
            }

            if ($parser->isOption($argumentFromLine)) {
                $option = $parser->parseOption($argumentFromLine);
                foreach (
                match ($option['type'] ?? '') {
                    'short' => $option['option'],
                    'long' => [$option['option']]
                } as $opt
                ) {
                    if ($opt === null) {
                        continue;
                    }
                    if ($opt?->cumulative) {
                        $options[$opt->name] = ($options[$opt->name] ?? 0) + 1;
                    } else {
                        $options[$opt->name] = true;
                    }
                }
                if (array_key_exists('value', $option) && is_string($option['value'] ?? null)) {
                    $options[$option['option']?->name] = $option['value'];
                } elseif ($option['waiting'] ?? false) {
                    $waitingForValue = $option['for'];
                }
                continue;
            }

            if ($nextArg !== null) {
                $arguments[$this->arguments[$nextArg++]->name] = $argumentFromLine;
            }
        }

        return [
            'command'   => $commandFromLine,
            'arguments' => $arguments,
            'options'   => $options,
        ];
    }

}
